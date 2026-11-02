<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GoogleMeetService
{
    /**
     * Create a Google Meet space for a consultant.
     *
     * @return array{
     *     name: string,
     *     meetingUri: string
     * }
     */
    public function createMeeting(User $consultant): array
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Check consultant role
        |--------------------------------------------------------------------------
        */

        if ($consultant->role !== User::ROLE_CONSULTANT) {
            throw new RuntimeException(
                'Only consultant accounts can create Google Meet meetings.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Get saved Google OAuth token
        |--------------------------------------------------------------------------
        */

        $token = $consultant->google_oauth_token;

        if (
            ! is_array($token) ||
            empty($token['access_token'])
        ) {
            throw new RuntimeException(
                'This consultant has not connected a Google account yet.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Build Google client
        |--------------------------------------------------------------------------
        */

        $client = $this->buildClient(
            $consultant,
            $token
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Refresh expired access token
        |--------------------------------------------------------------------------
        */

        if (
            $client->isAccessTokenExpired() &&
            ! empty($token['refresh_token'])
        ) {
            $newToken = $client->fetchAccessTokenWithRefreshToken(
                $token['refresh_token']
            );

            if (
                ! is_array($newToken) ||
                isset($newToken['error']) ||
                empty($newToken['access_token'])
            ) {
                Log::error(
                    'Google access token refresh failed.',
                    [
                        'consultant_id' => $consultant->id,
                        'error' => is_array($newToken)
                            ? ($newToken['error'] ?? 'unknown')
                            : 'invalid_token_response',
                        'error_description' => is_array($newToken)
                            ? ($newToken['error_description'] ?? null)
                            : null,
                    ]
                );

                throw new RuntimeException(
                    'Google access token could not be refreshed. Please reconnect Google.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Keep existing refresh token if Google does not return a new one
            |--------------------------------------------------------------------------
            */

            if (
                empty($newToken['refresh_token']) &&
                ! empty($token['refresh_token'])
            ) {
                $newToken['refresh_token'] = $token['refresh_token'];
            }

            $consultant->google_oauth_token = $newToken;
            $consultant->save();

            $token = $newToken;

            $client->setAccessToken($token);
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Authorized HTTP client
        |--------------------------------------------------------------------------
        */

        $httpClient = $client->authorize();

        /*
        |--------------------------------------------------------------------------
        | 6. Create Google Meet space
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Google Meet API expects a JSON object.
        | We therefore send an explicit JSON body of "{}"
        | instead of an empty array.
        |
        */

        $requestBody = '{}';

        try {
            $response = $httpClient->post(
                'https://meet.googleapis.com/v2/spaces',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],

                    'body' => $requestBody,
                ]
            );
        } catch (\Throwable $exception) {
            Log::error(
                'Google Meet HTTP request failed.',
                [
                    'consultant_id' => $consultant->id,
                    'exception_class' => get_class($exception),
                    'message' => $exception->getMessage(),
                ]
            );

            throw new RuntimeException(
                'Google Meet request failed: '
                . $exception->getMessage()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Read response
        |--------------------------------------------------------------------------
        */

        $statusCode = $response->getStatusCode();

        $responseBody = (string) $response->getBody();

        $data = json_decode(
            $responseBody,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | 8. Handle Google API errors
        |--------------------------------------------------------------------------
        */

        if (
            $statusCode < 200 ||
            $statusCode >= 300
        ) {
            Log::error(
                'Google Meet API returned an unexpected response.',
                [
                    'consultant_id' => $consultant->id,
                    'status_code' => $statusCode,
                    'response_body' => $responseBody,
                ]
            );

            $googleMessage = null;

            if (is_array($data)) {
                $googleMessage =
                    $data['error']['message']
                    ?? $data['message']
                    ?? null;
            }

            if (empty($googleMessage)) {
                $googleMessage =
                    'Google Meet API returned HTTP '
                    . $statusCode
                    . '.';
            }

            throw new RuntimeException(
                'Google Meet could not create the meeting. Google says: '
                . $googleMessage
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Validate JSON response
        |--------------------------------------------------------------------------
        */

        if (! is_array($data)) {
            Log::error(
                'Google Meet API returned invalid JSON.',
                [
                    'consultant_id' => $consultant->id,
                    'status_code' => $statusCode,
                    'response_body' => $responseBody,
                ]
            );

            throw new RuntimeException(
                'Google Meet returned an invalid response.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Get Meet space information
        |--------------------------------------------------------------------------
        */

        $spaceName = $data['name'] ?? null;

        $meetingUri = $data['meetingUri'] ?? null;

        if (
            empty($spaceName) ||
            empty($meetingUri)
        ) {
            Log::error(
                'Google Meet API response did not contain required meeting data.',
                [
                    'consultant_id' => $consultant->id,
                    'status_code' => $statusCode,
                    'response_body' => $responseBody,
                ]
            );

            throw new RuntimeException(
                'Google Meet was created but no meeting link was returned.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 11. Success
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Google Meet created successfully.',
            [
                'consultant_id' => $consultant->id,
                'space_name' => $spaceName,
                'status_code' => $statusCode,
            ]
        );

        return [
            'name' => $spaceName,
            'meetingUri' => $meetingUri,
        ];
    }

    /**
     * Build Google API client.
     */
    private function buildClient(
        User $consultant,
        array $token
    ): GoogleClient {
        $client = new GoogleClient();

        $client->setApplicationName(
            'UniBridge'
        );

        $client->setClientId(
            config('services.google.client_id')
        );

        $client->setClientSecret(
            config('services.google.client_secret')
        );

        $client->setScopes([
            'https://www.googleapis.com/auth/meetings.space.created',
        ]);

        $client->setAccessType(
            'offline'
        );

        $client->setAccessToken(
            $token
        );

        return $client;
    }
}