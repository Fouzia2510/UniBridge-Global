<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class TwoFactorController extends Controller
{
    /**
     * OTP validity in minutes.
     */
    private const OTP_EXPIRY_MINUTES = 5;

    /**
     * Maximum wrong attempts.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Minimum seconds between resend requests.
     */
    private const RESEND_COOLDOWN_SECONDS = 60;

    /**
     * Display OTP verification page.
     */
    public function show(
        Request $request
    ): View|RedirectResponse {
        /*
         * A user who has already completed 2FA
         * should not see the OTP page again.
         */
        if (
            $request->session()->get(
                'two_factor_verified'
            ) === true
        ) {
            return redirect()->to(
                $request->user()->dashboardRoute()
            );
        }

        /*
         * User must be authenticated before receiving OTP.
         */
        if (! $request->user()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                        'Please sign in before verifying your account.',
                ]);
        }

        /*
         * Generate OTP only when there is no active OTP.
         */
        if (
            ! $request->session()->has(
                'two_factor_otp_hash'
            )
        ) {
            $this->generateAndSendOtp($request);
        }

        return view(
            'auth.two-factor'
        );
    }

    /**
     * Verify submitted OTP.
     */
    public function verify(
        Request $request
    ): RedirectResponse {
        if (
            $request->session()->get(
                'two_factor_verified'
            ) === true
        ) {
            return redirect()->to(
                $request->user()->dashboardRoute()
            );
        }

        /*
         * User must still be authenticated.
         */
        if (! $request->user()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'otp' =>
                        'Your login session has expired. Please sign in again.',
                ]);
        }

        $request->validate([
            'otp' => [
                'required',
                'digits:6',
            ],
        ]);

        $otpHash = $request->session()->get(
            'two_factor_otp_hash'
        );

        /*
         * No OTP means it has expired or was never generated.
         */
        if (! $otpHash) {
            return back()->withErrors([
                'otp' =>
                    'Your OTP has expired. Please request a new code.',
            ]);
        }

        $expiresAt = $request->session()->get(
            'two_factor_otp_expires_at'
        );

        /*
         * Laravel normally restores Carbon instances from
         * the database session. This also safely handles a
         * string timestamp if one is returned.
         */
        try {
            if (
                is_string($expiresAt)
            ) {
                $expiresAt = \Carbon\Carbon::parse(
                    $expiresAt
                );
            }
        } catch (Throwable) {
            $this->clearOtp($request);

            return back()->withErrors([
                'otp' =>
                    'Your OTP session is invalid. Please request a new code.',
            ]);
        }

        /*
         * Check expiration.
         */
        if (
            ! $expiresAt ||
            now()->greaterThan($expiresAt)
        ) {
            $this->clearOtp($request);

            return back()->withErrors([
                'otp' =>
                    'Your OTP has expired. Please request a new code.',
            ]);
        }

        $attempts = (int) $request->session()->get(
            'two_factor_otp_attempts',
            0
        );

        /*
         * Stop verification after maximum attempts.
         */
        if (
            $attempts >= self::MAX_ATTEMPTS
        ) {
            $this->clearOtp($request);

            return back()->withErrors([
                'otp' =>
                    'Too many incorrect attempts. Please request a new OTP.',
            ]);
        }

        /*
         * Compare entered OTP with the hashed OTP.
         */
        if (
            ! Hash::check(
                $request->input('otp'),
                $otpHash
            )
        ) {
            $attempts++;

            $request->session()->put(
                'two_factor_otp_attempts',
                $attempts
            );

            $remaining = max(
                0,
                self::MAX_ATTEMPTS - $attempts
            );

            if ($remaining === 0) {
                $this->clearOtp($request);

                return back()->withErrors([
                    'otp' =>
                        'Too many incorrect attempts. Please request a new OTP.',
                ]);
            }

            return back()->withErrors([
                'otp' =>
                    "Incorrect OTP. You have {$remaining} attempt(s) remaining.",
            ]);
        }

        /*
         * OTP successfully verified.
         */
        $request->session()->put(
            'two_factor_verified',
            true
        );

        /*
         * Remove the OTP itself immediately after successful
         * verification so it cannot be reused.
         */
        $this->clearOtp($request);

        /*
         * Regenerate the session ID after completing 2FA.
         */
        $request->session()->regenerate();

        return redirect()
            ->to(
                $request->user()->dashboardRoute()
            )
            ->with(
                'status',
                'Two-factor authentication verified successfully.'
            );
    }

    /**
     * Resend OTP.
     */
    public function resend(
        Request $request
    ): RedirectResponse {
        if (! $request->user()) {
            return redirect()
                ->route('login');
        }

        $lastSentAt = $request->session()->get(
            'two_factor_otp_sent_at'
        );

        /*
         * Safely convert stored timestamp if required.
         */
        try {
            if (
                is_string($lastSentAt)
            ) {
                $lastSentAt = \Carbon\Carbon::parse(
                    $lastSentAt
                );
            }
        } catch (Throwable) {
            $lastSentAt = null;
        }

        if ($lastSentAt) {
            $secondsPassed = now()->diffInSeconds(
                $lastSentAt
            );

            if (
                $secondsPassed <
                self::RESEND_COOLDOWN_SECONDS
            ) {
                $remaining =
                    self::RESEND_COOLDOWN_SECONDS
                    - $secondsPassed;

                return back()->withErrors([
                    'otp' =>
                        "Please wait {$remaining} seconds before requesting another OTP.",
                ]);
            }
        }

        $this->generateAndSendOtp(
            $request
        );

        return back()->with(
            'status',
            'A new verification code has been sent to your email.'
        );
    }

    /**
     * Cancel current authentication flow.
     */
    public function cancel(
        Request $request
    ): RedirectResponse {
        $this->clearOtp($request);

        $request->session()->forget(
            'two_factor_verified'
        );

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Login cancelled. Please sign in again.'
            );
    }

    /**
     * Generate and send a new OTP.
     */
    private function generateAndSendOtp(
        Request $request
    ): void {
        $user = $request->user();

        if (
            ! $user ||
            ! $user->email
        ) {
            abort(
                403,
                'A valid email address is required for two-factor authentication.'
            );
        }

        /*
         * Generate a secure six-digit OTP.
         */
        $otp = (string) random_int(
            100000,
            999999
        );

        /*
         * Store only the hashed OTP.
         */
        $request->session()->put([
            'two_factor_otp_hash' =>
                Hash::make($otp),

            'two_factor_otp_expires_at' =>
                now()->addMinutes(
                    self::OTP_EXPIRY_MINUTES
                ),

            'two_factor_otp_sent_at' =>
                now(),

            'two_factor_otp_attempts' =>
                0,

            'two_factor_verified' =>
                false,
        ]);

        $appName = config(
            'app.name',
            'UniBridge'
        );

        /*
         * Send OTP through the application's configured
         * mail service.
         */
        Mail::raw(
            "Your {$appName} verification code is: {$otp}\n\n"
            . "This code will expire in "
            . self::OTP_EXPIRY_MINUTES
            . " minutes.\n\n"
            . "If you did not attempt to sign in, "
            . "you can safely ignore this email.\n\n"
            . "Regards,\n"
            . $appName,
            function ($message) use (
                $user,
                $appName
            ) {
                $message
                    ->to($user->email)
                    ->subject(
                        "{$appName} Login Verification Code"
                    );
            }
        );
    }

    /**
     * Remove OTP-related session data.
     */
    private function clearOtp(
        Request $request
    ): void {
        $request->session()->forget([
            'two_factor_otp_hash',
            'two_factor_otp_expires_at',
            'two_factor_otp_sent_at',
            'two_factor_otp_attempts',
        ]);
    }
}