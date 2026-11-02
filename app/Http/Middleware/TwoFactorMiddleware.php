<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Accounts exempt from email OTP verification.
     */
    private const OTP_EXEMPT_EMAILS = [
        'abcd1234@gmail.com',
        'admin@project.com',
        'student@gmail.com',
        'sarah@project.com',
        'hina@project.com',
        'ali@project.com',
    ];

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (! auth()->check()) {
            return $next($request);
        }

        if ($request->routeIs('logout')) {
            return $next($request);
        }

        if (
            $request->routeIs('two-factor.verify') ||
            $request->routeIs('two-factor.verify.store') ||
            $request->routeIs('two-factor.resend') ||
            $request->routeIs('two-factor.cancel')
        ) {
            return $next($request);
        }

        $user = $request->user();

        if (
            $user &&
            in_array(
                strtolower(trim($user->email)),
                self::OTP_EXEMPT_EMAILS,
                true
            )
        ) {
            return $next($request);
        }

        if (
            $request->session()->get('two_factor_verified')
            !== true
        ) {
            return redirect()->route('two-factor.verify');
        }

        return $next($request);
    }
}
