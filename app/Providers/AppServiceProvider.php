<?php

namespace App\Providers;

use App\services\GeminiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GeminiService::class, function (): GeminiService {

            $apiKey = config('gemini.api_key');

            if (! is_string($apiKey) || trim($apiKey) === '') {
                throw new \RuntimeException(
                    'GEMINI_API_KEY is not configured.'
                );
            }

            $client = \Gemini::client($apiKey);

            return new GeminiService(
                $client,
                (string) config(
                    'gemini.model',
                    'gemini-3.1-flash-lite'
                ),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}