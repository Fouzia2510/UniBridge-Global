<?php

namespace Tests\Feature;

use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppBroadcastRecipient;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KapsoWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_request_without_signature(): void
    {
        config([
            'services.kapso.webhook_secret' => 'test-webhook-secret',
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'message' => [
                'id' => 'test-message-001',
                'from' => '923000000000',
                'type' => 'text',
                'text' => ['body' => 'Hello'],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $payload
        );

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Webhook signature is missing.',
        ]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config([
            'services.kapso.webhook_secret' => 'test-webhook-secret',
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'message' => [
                'id' => 'test-message-002',
                'from' => '923000000000',
                'type' => 'text',
                'text' => ['body' => 'Hello'],
            ],
        ]);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => 'invalid-signature',
            ],
            $payload
        );

        $response->assertUnauthorized();
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid webhook signature.',
        ]);
    }

    public function test_webhook_accepts_valid_signature(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = json_encode([
            'type' => 'unknown.test.event',
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $response->assertJson([
            'success' => true,
            'message' => 'Webhook processed.',
        ]);
    }

    public function test_webhook_stores_valid_incoming_message(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => [
                'phone_number' => '923000000000',
                'contact_name' => 'Test Contact',
            ],
            'message' => [
                'id' => 'test-incoming-001',
                'from' => '923000000000',
                'type' => 'text',
                'timestamp' => time(),
                'text' => [
                    'body' => 'Hello UniBridge',
                ],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_conversations', [
            'phone_number' => '923000000000',
            'contact_name' => 'Test Contact',
        ]);

        $this->assertDatabaseHas('whatsapp_messages', [
            'provider_message_id' => 'test-incoming-001',
            'direction' => 'inbound',
            'message_type' => 'text',
            'message_text' => 'Hello UniBridge',
            'status' => 'received',
        ]);
    }
    public function test_new_incoming_number_is_added_to_central_contacts(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => [
                'phone_number' => '923001234567',
                'contact_name' => 'New Incoming Contact',
            ],
            'message' => [
                'id' => 'central-contact-test-001',
                'from' => '923001234567',
                'type' => 'text',
                'timestamp' => time(),
                'text' => [
                    'body' => 'Hello UniBridge',
                ],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_contacts', [
            'phone_number' => '923001234567',
            'contact_name' => 'New Incoming Contact',
            'contact_type' => 'incoming',
        ]);

        $this->assertDatabaseCount('whatsapp_contact_consents', 0);
    }
    public function test_duplicate_incoming_message_is_not_stored_twice(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => [
                'phone_number' => '923000000001',
                'contact_name' => 'Duplicate Test',
            ],
            'message' => [
                'id' => 'duplicate-message-001',
                'from' => '923000000001',
                'type' => 'text',
                'timestamp' => time(),
                'text' => [
                    'body' => 'Testing duplicate prevention',
                ],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        for ($i = 0; $i < 2; $i++) {
            $response = $this->call(
                'POST',
                '/webhooks/kapso',
                [],
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
                ],
                $payload
            );

            $response->assertOk();
        }

        $this->assertDatabaseCount('whatsapp_messages', 1);
        $this->assertDatabaseCount('whatsapp_conversations', 1);
    }
    public function test_incoming_message_reuses_existing_local_format_conversation(): void
{
    $secret = 'test-webhook-secret';

    config([
        'services.kapso.webhook_secret' => $secret,
    ]);

    $conversation = WhatsAppConversation::create([
        'phone_number' => '03001234567',
        'contact_name' => 'Existing Local Contact',
        'status' => 'open',
        'last_message_at' => now()->subHour(),
    ]);

    $payload = json_encode([
        'type' => 'whatsapp.message.received',
        'conversation' => [
            'phone_number' => '03001234567',
            'contact_name' => 'Existing Local Contact',
        ],
        'message' => [
            'id' => 'local-duplicate-test-001',
            'from' => '03001234567',
            'type' => 'text',
            'timestamp' => time(),
            'text' => [
                'body' => 'Testing existing conversation reuse',
            ],
        ],
    ]);

    $signature = hash_hmac('sha256', $payload, $secret);

    $response = $this->call(
        'POST',
        '/webhooks/kapso',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
        ],
        $payload
    );

    $response->assertOk();

    $this->assertDatabaseCount('whatsapp_conversations', 1);

    $this->assertDatabaseHas('whatsapp_messages', [
        'conversation_id' => $conversation->id,
        'provider_message_id' => 'local-duplicate-test-001',
    ]);
}
public function test_incoming_message_uses_latest_matching_conversation(): void
{
    $secret = 'test-webhook-secret';

    config([
        'services.kapso.webhook_secret' => $secret,
    ]);

    $olderConversation = WhatsAppConversation::create([
        'phone_number' => '03001234567',
        'contact_name' => 'Older Contact',
        'status' => 'open',
        'last_message_at' => now()->subHours(2),
    ]);

    $latestConversation = WhatsAppConversation::create([
        'phone_number' => '923001234567',
        'contact_name' => 'Latest Contact',
        'status' => 'open',
        'last_message_at' => now()->subHour(),
    ]);

    $payload = json_encode([
        'type' => 'whatsapp.message.received',
        'conversation' => [
            'phone_number' => '03001234567',
            'contact_name' => 'Latest Contact',
        ],
        'message' => [
            'id' => 'latest-conversation-test-001',
            'from' => '03001234567',
            'type' => 'text',
            'timestamp' => time(),
            'text' => [
                'body' => 'Testing latest conversation selection',
            ],
        ],
    ]);

    $signature = hash_hmac('sha256', $payload, $secret);

    $response = $this->call(
        'POST',
        '/webhooks/kapso',
        [],
        [],
        [],
        [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
        ],
        $payload
    );

    $response->assertOk();

    $this->assertDatabaseCount('whatsapp_conversations', 2);

    $this->assertDatabaseHas('whatsapp_messages', [
        'conversation_id' => $latestConversation->id,
        'provider_message_id' => 'latest-conversation-test-001',
    ]);

    $this->assertDatabaseMissing('whatsapp_messages', [
        'conversation_id' => $olderConversation->id,
        'provider_message_id' => 'latest-conversation-test-001',
    ]);
}
    public function test_incoming_local_number_is_normalized_before_contact_creation(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => [
                'phone_number' => '03001234567',
                'contact_name' => 'Normalized Contact',
            ],
            'message' => [
                'id' => 'normalized-contact-test-001',
                'from' => '03001234567',
                'type' => 'text',
                'timestamp' => time(),
                'text' => [
                    'body' => 'Testing number normalization',
                ],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_contacts', [
            'phone_number' => '923001234567',
            'contact_name' => 'Normalized Contact',
        ]);

        $this->assertDatabaseMissing('whatsapp_contacts', [
            'phone_number' => '03001234567',
        ]);
    }
    public function test_webhook_rejects_invalid_json_payload(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = '{"type": invalid json}';

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertStatus(400);

        $response->assertJson([
            'success' => false,
            'message' => 'Invalid JSON payload.',
        ]);
    }

    public function test_webhook_stores_non_text_incoming_message(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => [
                'phone_number' => '923000000002',
                'contact_name' => 'Media Test',
            ],
            'message' => [
                'id' => 'media-message-001',
                'from' => '923000000002',
                'type' => 'image',
                'timestamp' => time(),
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'provider_message_id' => 'media-message-001',
            'direction' => 'inbound',
            'message_type' => 'image',
            'status' => 'received',
        ]);
    }

    public function test_webhook_updates_outgoing_message_status(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $conversation = WhatsAppConversation::create([
            'phone_number' => '923000000003',
            'contact_name' => 'Status Test',
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'provider_message_id' => 'outgoing-status-001',
            'direction' => 'outbound',
            'message_type' => 'text',
            'message_text' => 'Test message',
            'status' => 'accepted',
        ]);

        $events = [
            'whatsapp.message.sent' => 'sent',
            'whatsapp.message.delivered' => 'delivered',
            'whatsapp.message.read' => 'read',
        ];

        foreach ($events as $event => $expectedStatus) {
            $payload = json_encode([
                'type' => $event,
                'message' => [
                    'id' => 'outgoing-status-001',
                ],
            ]);

            $signature = hash_hmac('sha256', $payload, $secret);

            $response = $this->call(
                'POST',
                '/webhooks/kapso',
                [],
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
                ],
                $payload
            );

            $response->assertOk();

            $this->assertDatabaseHas('whatsapp_messages', [
                'provider_message_id' => 'outgoing-status-001',
                'status' => $expectedStatus,
            ]);
        }
    }

    public function test_old_status_event_does_not_replace_read_status(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $conversation = WhatsAppConversation::create([
            'phone_number' => '923000000004',
            'contact_name' => 'Status Priority Test',
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'provider_message_id' => 'status-priority-001',
            'direction' => 'outbound',
            'message_type' => 'text',
            'message_text' => 'Test message',
            'status' => 'read',
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.sent',
            'message' => [
                'id' => 'status-priority-001',
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'provider_message_id' => 'status-priority-001',
            'status' => 'read',
        ]);
    }

    public function test_status_event_does_not_create_unknown_outgoing_message(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.delivered',
            'message' => [
                'id' => 'unknown-outgoing-001',
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseMissing('whatsapp_messages', [
            'provider_message_id' => 'unknown-outgoing-001',
        ]);
    }

    public function test_delivered_webhook_updates_broadcast_recipient(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $broadcast = WhatsAppBroadcast::create([
            'name' => 'Webhook delivery test',
            'message_text' => 'Test broadcast',
            'status' => 'accepted',
            'recipient_count' => 1,
            'accepted_count' => 1,
            'failed_count' => 0,
        ]);

        WhatsAppBroadcastRecipient::create([
            'whatsapp_broadcast_id' => $broadcast->id,
            'contact_name' => 'Test Recipient',
            'phone_number' => '923000000005',
            'status' => 'accepted',
            'provider_message_id' => 'broadcast-delivered-001',
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.delivered',
            'message' => [
                'id' => 'broadcast-delivered-001',
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $recipient = WhatsAppBroadcastRecipient::where(
            'provider_message_id',
            'broadcast-delivered-001'
        )->firstOrFail();

        $this->assertSame('delivered', $recipient->status);
        $this->assertNotNull($recipient->delivered_at);
    }

    public function test_failed_broadcast_recipient_can_later_be_confirmed_delivered(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $broadcast = WhatsAppBroadcast::create([
            'name' => 'Webhook failure test',
            'message_text' => 'Test broadcast',
            'status' => 'accepted',
            'recipient_count' => 1,
            'accepted_count' => 1,
            'failed_count' => 0,
        ]);

        WhatsAppBroadcastRecipient::create([
            'whatsapp_broadcast_id' => $broadcast->id,
            'contact_name' => 'Failure Test',
            'phone_number' => '923000000006',
            'status' => 'accepted',
            'provider_message_id' => 'broadcast-failed-001',
        ]);

        foreach ([
            'whatsapp.message.failed',
            'whatsapp.message.delivered',
        ] as $event) {
            $payload = json_encode([
                'type' => $event,
                'message' => [
                    'id' => 'broadcast-failed-001',
                ],
            ]);

            $signature = hash_hmac('sha256', $payload, $secret);

            $response = $this->call(
                'POST',
                '/webhooks/kapso',
                [],
                [],
                [],
                [
                    'CONTENT_TYPE' => 'application/json',
                    'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
                ],
                $payload
            );

            $response->assertOk();
        }

        $recipient = WhatsAppBroadcastRecipient::where(
            'provider_message_id',
            'broadcast-failed-001'
        )->firstOrFail();

        $this->assertSame('delivered', $recipient->status);
        $this->assertNotNull($recipient->delivered_at);

        $this->assertDatabaseHas('whatsapp_broadcasts', [
            'id' => $broadcast->id,
            'failed_count' => 0,
        ]);
    }

    public function test_batch_item_event_takes_priority_over_header_event(): void
    {
        $secret = 'test-webhook-secret';

        config([
            'services.kapso.webhook_secret' => $secret,
        ]);

        $conversation = WhatsAppConversation::create([
            'phone_number' => '923000000007',
            'contact_name' => 'Batch Test',
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'provider_message_id' => 'batch-status-001',
            'direction' => 'outbound',
            'message_type' => 'text',
            'message_text' => 'Batch test message',
            'status' => 'accepted',
        ]);

        $payload = json_encode([
            'batch' => true,
            'data' => [
                [
                    'type' => 'whatsapp.message.delivered',
                    'request_body' => [
                        'message' => [
                            'id' => 'batch-status-001',
                        ],
                    ],
                ],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
                'HTTP_X_WEBHOOK_EVENT' => 'whatsapp.message.sent',
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'provider_message_id' => 'batch-status-001',
            'status' => 'delivered',
        ]);
    }

    public function test_missing_incoming_timestamp_is_saved_as_null(): void
    {
        $secret = 'test-webhook-secret';

        config(['services.kapso.webhook_secret' => $secret]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => ['phone_number' => '923000000010'],
            'message' => [
                'id' => 'missing-timestamp-001',
                'from' => '923000000010',
                'type' => 'text',
                'text' => ['body' => 'Missing timestamp test'],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'provider_message_id' => 'missing-timestamp-001',
            'sent_at' => null,
        ]);
    }

    public function test_future_incoming_timestamp_is_saved_as_null(): void
    {
        $secret = 'test-webhook-secret';

        config(['services.kapso.webhook_secret' => $secret]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => ['phone_number' => '923000000011'],
            'message' => [
                'id' => 'future-timestamp-001',
                'from' => '923000000011',
                'type' => 'text',
                'timestamp' => now()->addDay()->timestamp,
                'text' => ['body' => 'Future timestamp test'],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'provider_message_id' => 'future-timestamp-001',
            'sent_at' => null,
        ]);
    }

    public function test_missing_timestamp_does_not_overwrite_conversation_last_message_at(): void
    {
        $secret = 'test-webhook-secret';
        config(['services.kapso.webhook_secret' => $secret]);

        $lastMessageAt = now()->subHours(3)->startOfSecond();

        $conversation = WhatsAppConversation::create([
            'phone_number' => '923000000012',
            'contact_name' => 'Timestamp Test',
            'status' => 'open',
            'last_message_at' => $lastMessageAt,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => ['phone_number' => '923000000012'],
            'message' => [
                'id' => 'missing-last-message-time-001',
                'from' => '923000000012',
                'type' => 'text',
                'text' => ['body' => 'Missing timestamp'],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_conversations', [
            'id' => $conversation->id,
            'last_message_at' => $lastMessageAt->toDateTimeString(),
        ]);
    }

    public function test_older_incoming_timestamp_does_not_overwrite_newer_conversation_time(): void
    {
        $secret = 'test-webhook-secret';
        config(['services.kapso.webhook_secret' => $secret]);

        $lastMessageAt = now()->subHour()->startOfSecond();
        $olderTimestamp = now()->subHours(2)->timestamp;

        $conversation = WhatsAppConversation::create([
            'phone_number' => '923000000013',
            'contact_name' => 'Older Timestamp Test',
            'status' => 'open',
            'last_message_at' => $lastMessageAt,
        ]);

        $payload = json_encode([
            'type' => 'whatsapp.message.received',
            'conversation' => ['phone_number' => '923000000013'],
            'message' => [
                'id' => 'older-timestamp-001',
                'from' => '923000000013',
                'type' => 'text',
                'timestamp' => $olderTimestamp,
                'text' => ['body' => 'Older timestamp'],
            ],
        ]);

        $signature = hash_hmac('sha256', $payload, $secret);

        $response = $this->call(
            'POST',
            '/webhooks/kapso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('whatsapp_conversations', [
            'id' => $conversation->id,
            'last_message_at' => $lastMessageAt->toDateTimeString(),
        ]);
    }
}
