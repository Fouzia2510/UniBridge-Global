<?php

namespace App\Providers;

use App\services\GeminiService;
use App\services\OllamaService;
use Gemini\Contracts\ClientContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Google Gemini: independent cloud AI service.
        $this->app->singleton(
            GeminiService::class,
            function ($app): GeminiService {
                $client = $app->make(ClientContract::class);

                return new GeminiService(
                    $client,
                    (string) config(
                        'gemini.model',
                        'gemini-3.1-flash-lite'
                    )
                );
            }
        );

        // Ollama: independent local AI service.
        $this->app->singleton(
            OllamaService::class,
            function (): OllamaService {
                return new OllamaService(
                    (string) config(
                        'ollama.base_url',
                        'http://127.0.0.1:11434'
                    ),
                    (string) config('ollama.model', 'gemma3:1b'),
                    (int) config('ollama.timeout', 120)
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
