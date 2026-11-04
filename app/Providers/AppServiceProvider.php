<?php

namespace App\Providers;

use App\services\GeminiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            GeminiService::class,
            function (): GeminiService {
                return new GeminiService(
                    (string) config(
                        'ollama.base_url',
                        'http://127.0.0.1:11434'
                    ),
                    (string) config('ollama.model', 'gemma3:1b'),
                    (int) config('ollama.timeout', 120),
                );
            }
        );
    }

    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised();
        });
    }
}