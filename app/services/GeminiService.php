<?php

namespace App\services;

use RuntimeException;

class GeminiService
{
    public function __construct(
        private readonly object $client,
        private readonly string $model
    ) {
    }

    public function generate(string $prompt): string
    {
        if (trim($prompt) === '') {
            throw new RuntimeException('A prompt is required.');
        }

        $response = $this->client
            ->generativeModel($this->model)
            ->generateContent($prompt);

        $text = trim((string) $response->text());

        if ($text === '') {
            throw new RuntimeException(
                'Gemini returned an empty response.'
            );
        }

        return $text;
    }
}
