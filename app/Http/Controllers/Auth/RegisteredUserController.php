<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(
        Request $request
    ): View {
        $portalRole =
            $this->normalizePortalRole(
                $request->query('role')
            );

        return view(
            'auth.register',
            compact('portalRole')
        );
    }

    /**
     * Handle a registration request.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $portalRole =
            $this->normalizePortalRole(
                $request->input('role')
            );

        /*
         * Public registration is available only for:
         *
         * Student
         * Consultant
         *
         * Admin accounts must never be created through
         * public registration.
         */
        if (
            ! in_array(
                $portalRole,
                [
                    User::ROLE_STUDENT,
                    User::ROLE_CONSULTANT,
                ],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'role' =>
                    'Please select Student or Consultant registration.',
            ]);
        }

        $rules = [
            'role' => [
                'required',
                'in:' .
                    User::ROLE_STUDENT .
                    ',' .
                    User::ROLE_CONSULTANT,
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' .
                    User::class .
                    ',email',
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ];

        /*
         * Existing Student registration fields.
         *
         * These are validated for the registration interface
         * but are not written to users because the current
         * users table does not contain these columns.
         */
        if (
            $portalRole === User::ROLE_STUDENT
        ) {
            $rules['roll_number'] = [
                'required',
                'string',
                'max:100',
            ];

            $rules['department'] = [
                'required',
                'string',
                'max:255',
            ];
        }

        $validated =
            $request->validate($rules);

        /*
         * Student accounts are approved immediately.
         *
         * Consultant accounts require admin approval.
         */
        $isApproved =
            $portalRole === User::ROLE_STUDENT;

        $user = User::create([
            'name' =>
                $validated['name'],

            'email' =>
                strtolower(
                    $validated['email']
                ),

            'password' =>
                Hash::make(
                    $validated['password']
                ),

            'role' =>
                $portalRole,

            'is_approved' =>
                $isApproved,
        ]);

        /*
         * Fire Laravel's Registered event.
         */
        event(
            new Registered($user)
        );

        /*
         * Authenticate the newly registered user.
         */
        Auth::login($user);

        /*
         * Regenerate session ID.
         */
        $request->session()->regenerate();

        /*
         * Store authentication context.
         */
        $request->session()->put([
            'auth_portal_role' =>
                $portalRole,

            'auth_flow' =>
                'registration',
        ]);

        /*
         * Always start with a fresh OTP state.
         */
        $request->session()->forget([
            'two_factor_verified',
            'two_factor_otp_hash',
            'two_factor_otp_expires_at',
            'two_factor_otp_sent_at',
            'two_factor_otp_attempts',
        ]);

        /*
         * Send the new account to OTP verification.
         */
        return redirect()
            ->route(
                'two-factor.verify'
            );
    }

    /**
     * Normalize requested portal role.
     *
     * Admin is intentionally excluded from
     * public registration.
     */
    private function normalizePortalRole(
        ?string $role
    ): ?string {
        return match ($role) {

            User::ROLE_STUDENT =>
                User::ROLE_STUDENT,

            User::ROLE_CONSULTANT =>
                User::ROLE_CONSULTANT,

            default =>
                null,
        };
    }
}