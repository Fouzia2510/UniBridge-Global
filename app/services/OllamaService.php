<?php

namespace App\services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OllamaService
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int $timeout
    ) {
    }

    public function generate(string $prompt): string
    {
        if (trim($prompt) === '') {
            throw new RuntimeException('A prompt is required.');
        }

        $response = Http::acceptJson()
            ->timeout($this->timeout)
            ->post(
                rtrim($this->baseUrl, '/') . '/api/generate',
                [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'num_predict' => 450,
                        'temperature' => 0.3,
                    ],
                ]
            )
            ->throw();

        $text = trim((string) $response->json('response'));

        if ($text === '') {
            throw new RuntimeException(
                'Ollama returned an empty response.'
            );
        }

        return $text;
    }
}