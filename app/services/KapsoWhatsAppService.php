<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class KapsoWhatsAppService
{
    /**
     * Send a normal text message.
     */
    public function sendText(string $to, string $message): array
    {
        $api = $this->apiConfig();

        $response = Http::withHeaders([
            'X-API-Key' => $api['api_key'],
        ])->acceptJson()->post(
            $this->messagesUrl($api),
            [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => $message,
                ],
            ]
        );

        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    /**
     * Send an approved WhatsApp template.
     *
     * Example for a template with {{1}} and {{2}}:
     * sendTemplate($to, 'unibridge_application_update',
     *     ['Student Name', 'Under Review']);
     */
    public function sendTemplate(
        string $to,
        string $templateName,
        array $bodyParameters = [],
        string $language = 'en_US'
    ): array {
        if (!preg_match('/^[a-z0-9_]+$/', $templateName)) {
            throw new RuntimeException(
                'Invalid WhatsApp template name.'
            );
        }

        if (trim($to) === '') {
            throw new RuntimeException(
                'A recipient phone number is required.'
            );
        }

        if (trim($language) === '') {
            throw new RuntimeException(
                'A template language is required.'
            );
        }

        $template = [
            'name' => $templateName,
            'language' => [
                'code' => $language,
            ],
        ];

        if ($bodyParameters !== []) {
            $parameters = [];

            foreach ($bodyParameters as $value) {
                $parameters[] = [
                    'type' => 'text',
                    'text' => (string) $value,
                ];
            }

            $template['components'] = [
                [
                    'type' => 'body',
                    'parameters' => $parameters,
                ],
            ];
        }

        $api = $this->apiConfig();

        $response = Http::withHeaders([
            'X-API-Key' => $api['api_key'],
        ])->acceptJson()->post(
            $this->messagesUrl($api),
            [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'template',
                'template' => $template,
            ]
        );

        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    /**
     * Upload a file to Kapso and return its media ID.
     */
    public function uploadMedia(UploadedFile $file): string
    {
        $api = $this->apiConfig();

        $url = $api['base_url'] . '/' . $api['version'] . '/' .
            $api['phone_number_id'] . '/media';

        $handle = fopen($file->getRealPath(), 'rb');

        if ($handle === false) {
            throw new RuntimeException(
                'The uploaded file could not be opened.'
            );
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $api['api_key'],
            ])
                ->acceptJson()
                ->timeout(120)
                ->attach(
                    'file',
                    $handle,
                    $file->getClientOriginalName()
                )
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                ]);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        $this->ensureSuccessful($response);

        $mediaId = $response->json('id');

        if (!is_string($mediaId) || $mediaId === '') {
            throw new RuntimeException(
                'Kapso did not return a media ID.'
            );
        }

        return $mediaId;
    }

    /**
     * Send an uploaded image, video, audio file or document.
     */
    public function sendMedia(
        string $to,
        string $type,
        string $mediaId,
        ?string $caption = null,
        ?string $filename = null
    ): array {
        $allowedTypes = [
            'image',
            'video',
            'audio',
            'document',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            throw new RuntimeException(
                'Unsupported WhatsApp media type.'
            );
        }

        if (trim($mediaId) === '') {
            throw new RuntimeException(
                'A valid media ID is required.'
            );
        }

        $media = [
            'id' => $mediaId,
        ];

        if (
            $caption !== null &&
            trim($caption) !== '' &&
            in_array($type, ['image', 'video', 'document'], true)
        ) {
            $media['caption'] = trim($caption);
        }

        if ($type === 'document' && $filename !== null) {
            $media['filename'] = $filename;
        }

        $api = $this->apiConfig();

        $response = Http::withHeaders([
            'X-API-Key' => $api['api_key'],
        ])->acceptJson()->post(
            $this->messagesUrl($api),
            [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => $type,
                $type => $media,
            ]
        );

        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    /**
     * Send a location explicitly provided by the admin.
     */
    public function sendLocation(
        string $to,
        float $latitude,
        float $longitude,
        ?string $name = null,
        ?string $address = null
    ): array {
        if (
            $latitude < -90 || $latitude > 90 ||
            $longitude < -180 || $longitude > 180
        ) {
            throw new RuntimeException(
                'The supplied location coordinates are invalid.'
            );
        }

        $api = $this->apiConfig();

        $location = [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if ($name !== null && trim($name) !== '') {
            $location['name'] = trim($name);
        }

        if ($address !== null && trim($address) !== '') {
            $location['address'] = trim($address);
        }

        $response = Http::withHeaders([
            'X-API-Key' => $api['api_key'],
        ])->acceptJson()->post(
            $this->messagesUrl($api),
            [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'location',
                'location' => $location,
            ]
        );

        $this->ensureSuccessful($response);

        return $response->json() ?? [];
    }

    /**
     * Build the Kapso messages endpoint.
     */
    private function messagesUrl(array $api): string
    {
        return $api['base_url'] . '/' . $api['version'] . '/' .
            $api['phone_number_id'] . '/messages';
    }

    /**
     * Read and validate Kapso configuration.
     */
    private function apiConfig(): array
    {
        $apiKey = config('services.kapso.api_key');

        $baseUrl = rtrim(
            config(
                'services.kapso.base_url',
                'https://api.kapso.ai/meta/whatsapp'
            ),
            '/'
        );

        $version = config('services.kapso.api_version', 'v24.0');
        $phoneNumberId = config('services.kapso.phone_number_id');

        if (!$apiKey || !$phoneNumberId) {
            throw new RuntimeException(
                'Kapso API key or phone number ID is missing.'
            );
        }

        return [
            'api_key' => $apiKey,
            'base_url' => $baseUrl,
            'version' => $version,
            'phone_number_id' => $phoneNumberId,
        ];
    }

    /**
     * Convert provider errors into an exception.
     */
    private function ensureSuccessful($response): void
    {
        if ($response->failed()) {
            throw new RuntimeException(
                'Kapso request failed with HTTP status ' .
                $response->status() .
                '. Response: ' .
                substr($response->body(), 0, 1500)
            );
        }
    }
}
