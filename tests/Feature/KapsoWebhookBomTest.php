<?php

namespace Tests\Feature;

use App\Models\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KapsoWebhookBomTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_json_payload_with_utf8_bom_is_processed(): void
    {
        $secret = 'test-webhook-secret';
        config([
            'services.kapso.webhook_secret' => $secret,
            'services.kapso.ai_dry_run' => false,
            'services.kapso.ai_auto_reply' => false,
            'services.kapso.ai_live_send_enabled' => false,
        ]);

        $payload = "\xEF\xBB\xBF".json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => ['phone_number' => '923001112266'],
            'message' => [
                'id' => 'bom-incoming-001',
                'from' => '923001112266',
                'type' => 'text',
                'timestamp' => time(),
                'text' => ['body' => 'Hello from a BOM payload'],
            ],
        ], JSON_THROW_ON_ERROR);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => hash_hmac('sha256', $payload, $secret),
            ],
            $payload
        );

        $response->assertOk();
        $this->assertDatabaseHas('whatsapp_messages', [
            'provider_message_id' => 'bom-incoming-001',
            'message_text' => 'Hello from a BOM payload',
        ]);
    }
}
