<?php

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleDriveController extends Controller
{
    /**
     * Start Google Drive OAuth.
     */
    public function connect(Request $request): RedirectResponse
    {
        $client = $this->googleClient();

        $client->setScopes([
            'https://www.googleapis.com/auth/drive.file',
        ]);

        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);

        $state = bin2hex(random_bytes(32));

        $request->session()->put(
            'google_drive_oauth_state',
            $state
        );

        $request->session()->save();

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
        $expectedState = $request->session()->get(
            'google_drive_oauth_state'
        );

        $returnedState = (string) $request->query('state');

        if (
            empty($expectedState) ||
            empty($returnedState) ||
            ! hash_equals(
                (string) $expectedState,
                $returnedState
            )
        ) {
            abort(
                403,
                'Invalid Google Drive OAuth state.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | State is valid, so remove it now.
        |--------------------------------------------------------------------------
        */

        $request->session()->forget(
            'google_drive_oauth_state'
        );

        if ($request->filled('error')) {
            return redirect()
                ->route('documents.index')
                ->withErrors([
                    'google_drive' =>
                        'Google Drive connection was cancelled or denied.',
                ]);
        }

        if (! $request->filled('code')) {
            return redirect()
                ->route('documents.index')
                ->withErrors([
                    'google_drive' =>
                        'Google did not return an authorization code.',
                ]);
        }

        $client = $this->googleClient();

        $token = $client->fetchAccessTokenWithAuthCode(
            $request->query('code')
        );

        if (
            ! is_array($token) ||
            isset($token['error']) ||
            empty($token['access_token'])
        ) {
            return redirect()
                ->route('documents.index')
                ->withErrors([
                    'google_drive' =>
                        'Google Drive authorization failed. Please try again.',
                ]);
        }

        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | Preserve existing refresh token
        |--------------------------------------------------------------------------
        */

        $existingToken =
            $user->google_drive_oauth_token ?? [];

        if (! is_array($existingToken)) {
            $existingToken = [];
        }

        if (
            empty($token['refresh_token']) &&
            ! empty($existingToken['refresh_token'])
        ) {
            $token['refresh_token'] =
                $existingToken['refresh_token'];
        }

        /*
        |--------------------------------------------------------------------------
        | Save encrypted Google Drive OAuth token
        |--------------------------------------------------------------------------
        */

        $user->google_drive_oauth_token = $token;
        $user->save();

        return redirect()
            ->route('documents.index')
            ->with(
                'status',
                'Google Drive connected successfully.'
            );
    }

    /**
     * Build Google API client.
     */
    private function googleClient(): GoogleClient
    {
        $client = new GoogleClient();

        $client->setApplicationName(
            'UniBridge'
        );

        $client->setClientId(
            config('services.google_drive.client_id')
        );

        $client->setClientSecret(
            config('services.google_drive.client_secret')
        );

        $client->setRedirectUri(
            config('services.google_drive.redirect')
        );

        return $client;
    }
}