<?php

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleMeetController extends Controller
{
    /**
     * Send the user to Google to connect their account.
     */
    public function connect(Request $request): RedirectResponse
    {
        $client = $this->googleClient();

        $client->setScopes([
            'https://www.googleapis.com/auth/meetings.space.created',
        ]);

        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);

        $state = bin2hex(random_bytes(32));

        $request->session()->put(
            'google_oauth_state',
            $state
        );

        $client->setState($state);

        return redirect()->away(
            $client->createAuthUrl()
        );
    }

    /**
     * Handle Google's OAuth callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        $expectedState = $request->session()->pull(
            'google_oauth_state'
        );

        $returnedState = (string) $request->query('state');

        if (
            ! $expectedState ||
            ! hash_equals(
                (string) $expectedState,
                $returnedState
            )
        ) {
            abort(
                403,
                'Invalid Google OAuth state.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Google cancelled or denied authorization
        |--------------------------------------------------------------------------
        */

        if ($request->filled('error')) {
            return $this->redirectWithError(
                $request,
                'Google connection was cancelled or denied.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Google did not return authorization code
        |--------------------------------------------------------------------------
        */

        if (! $request->filled('code')) {
            return $this->redirectWithError(
                $request,
                'Google did not return an authorization code.'
            );
        }

        $client = $this->googleClient();

        /*
        |--------------------------------------------------------------------------
        | Exchange authorization code for access token
        |--------------------------------------------------------------------------
        */

        $token = $client->fetchAccessTokenWithAuthCode(
            $request->query('code')
        );

        if (
            ! is_array($token) ||
            isset($token['error']) ||
            empty($token['access_token'])
        ) {
            return $this->redirectWithError(
                $request,
                'Google authorization failed. Please try again.'
            );
        }

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Preserve existing refresh token
        |--------------------------------------------------------------------------
        */

        $existingToken = $user->google_oauth_token ?? [];

        if (! is_array($existingToken)) {
            $existingToken = [];
        }

        if (
            empty($token['refresh_token']) &&
            ! empty($existingToken['refresh_token'])
        ) {
            $token['refresh_token'] = $existingToken['refresh_token'];
        }

        /*
        |--------------------------------------------------------------------------
        | Save Google OAuth token
        |--------------------------------------------------------------------------
        |
        | User model encrypts this field using:
        |
        | encrypted:array
        |
        */

        $user->google_oauth_token = $token;
        $user->save();

        /*
        |--------------------------------------------------------------------------
        | Redirect according to user role
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {
            return redirect()
                ->route('admin.dashboard')
                ->with(
                    'status',
                    'Google account connected successfully. Google Meet is now ready.'
                );
        }

        return redirect()
            ->route('consultant.bookings.index')
            ->with(
                'status',
                'Google account connected successfully. Google Meet is now ready.'
            );
    }

    /**
     * Redirect with an OAuth error according to user role.
     */
    private function redirectWithError(
        Request $request,
        string $message
    ): RedirectResponse {
        if ($request->user()?->role === 'admin') {
            return redirect()
                ->route('admin.dashboard')
                ->withErrors([
                    'google' => $message,
                ]);
        }

        return redirect()
            ->route('consultant.bookings.index')
            ->withErrors([
                'google' => $message,
            ]);
    }

    /**
     * Build the Google API client.
     */
    private function googleClient(): GoogleClient
    {
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

        $client->setRedirectUri(
            config('services.google.redirect')
        );

        return $client;
    }
}