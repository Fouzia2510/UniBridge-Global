<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
        }
    )
    ->withExceptions(
        function (Exceptions $exceptions): void {
            //
        }
    )
    ->create();