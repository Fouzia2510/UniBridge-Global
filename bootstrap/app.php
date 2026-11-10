<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(
        function (Middleware $middleware): void {

            $middleware->trustProxies(
               at: '*',
                headers: Request::HEADER_X_FORWARDED_FOR
                    | Request::HEADER_X_FORWARDED_HOST
                    | Request::HEADER_X_FORWARDED_PORT
                    | Request::HEADER_X_FORWARDED_PROTO
            );

            /*
            |--------------------------------------------------------------------------
            | CSRF Exceptions
            |--------------------------------------------------------------------------
            */

            $middleware->validateCsrfTokens(except: [
                'webhooks/kapso',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Middleware Aliases
            |--------------------------------------------------------------------------
            */

            $middleware->alias([
                'role' =>
                    \App\Http\Middleware\EnsureUserHasRole::class,

                'approved' =>
                    \App\Http\Middleware\EnsureUserIsApproved::class,

                'two.factor' =>
                    \App\Http\Middleware\TwoFactorMiddleware::class,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Two-Factor Authentication
            |--------------------------------------------------------------------------
            */

$middleware->web(
    append: [
        \App\Http\Middleware\TwoFactorMiddleware::class,
    ]
);

$middleware->authenticateSessions();
        }
    )
    ->withExceptions(
        function (Exceptions $exceptions): void {
            //
        }
    )
    ->create();