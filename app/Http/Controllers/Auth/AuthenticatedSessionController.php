<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): View
    {
        // Preserve the portal selected in the URL or previous form.
        $portalRole = $this->normalizePortalRole(
            $request->query('role')
        );

        if (!$portalRole) {
            $portalRole = $this->normalizePortalRole(
                old('role')
            );
        }

        // Default to student only when no valid portal was selected.
        $portalRole = $portalRole ?? User::ROLE_STUDENT;

        return view('auth.login', compact('portalRole'));
    }

    /**
     * Handle an authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate using the submitted email and password.
        $request->authenticate();

        $user = $request->user();

        // Read the role submitted by the login form.
        $portalRole = $this->normalizePortalRole(
            $request->input('role')
        );

        /*
         * TEMPORARY DIAGNOSTIC:
         * Remove this log after we identify the problem.
         * Never log passwords, OTPs, or session tokens.
         */
        Log::info('UniBridge login role diagnostic', [
            'submitted_role' => $request->input('role'),
            'normalized_role' => $portalRole,
            'database_role' => $user->role,
            'email' => strtolower(trim($user->email)),
        ]);

        // Reject a missing or invalid portal selection.
        if (!$portalRole) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'role' => 'Please select a valid UniBridge portal.',
                ]);
        }

        // Prevent users from logging in through the wrong portal.
        if ($user->role !== $portalRole) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login', ['role' => $portalRole])
                ->withErrors([
                    'email' => 'This account does not belong to the selected portal. Please choose the correct portal.',
                ])
                ->withInput([
                    'email' => $request->input('email'),
                    'role' => $portalRole,
                ]);
        }

        // Regenerate the session after successful authentication.
        $request->session()->regenerate();

        // Save the authentication context.
        $request->session()->put([
            'auth_portal_role' => $user->role,
            'auth_flow' => 'login',
        ]);

        // Clear any previous OTP state.
        $request->session()->forget([
            'two_factor_verified',
            'two_factor_otp_hash',
            'two_factor_otp_expires_at',
            'two_factor_otp_sent_at',
            'two_factor_otp_attempts',
        ]);

        // The six specified accounts can log in without OTP.
        if ($this->isOtpExempt($user)) {
            $request->session()->put(
                'two_factor_verified',
                true
            );

            return redirect()->to(
                $user->dashboardRoute()
            );
        }

        // All other accounts must complete OTP verification.
        return redirect()->route('two-factor.verify');
    }

    /**
     * Log the user out.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Check whether this account is exempt from OTP.
     */
    private function isOtpExempt(User $user): bool
    {
        $exemptEmails = [
            'abcd1234@gmail.com',
            'admin@project.com',
            'student@gmail.com',
            'sarah@project.com',
            'hina@project.com',
            'ali@project.com',
        ];

        return in_array(
            strtolower(trim($user->email)),
            $exemptEmails,
            true
        );
    }

    /**
     * Normalize the selected portal role.
     */
    private function normalizePortalRole(?string $role): ?string
    {
        return match ($role) {
            User::ROLE_STUDENT => User::ROLE_STUDENT,
            User::ROLE_CONSULTANT => User::ROLE_CONSULTANT,
            User::ROLE_ADMIN => User::ROLE_ADMIN,
            default => null,
        };
    }
}
