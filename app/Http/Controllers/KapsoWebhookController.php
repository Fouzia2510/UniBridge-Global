<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppBroadcastRecipient;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppAIResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KapsoWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $secret = config('services.kapso.webhook_secret');
        $signature = $request->header('X-Webhook-Signature');
        $rawBody = $request->getContent();

        Log::info('Kapso webhook received', [
            'signature_present' => is_string($signature)
                && $signature !== '',
            'secret_configured' => is_string($secret)
                && $secret !== '',
            'event_header' => $request->header('X-Webhook-Event'),
        ]);

        if (
            ! is_string($secret) ||
            $secret === '' ||
            ! is_string($signature) ||
            $signature === ''
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook signature is missing.',
            ], 401);
        }

        $expectedSignature = hash_hmac(
            'sha256',
            $rawBody,
            $secret
        );

        if (! hash_equals($expectedSignature, $signature)) {
            Log::warning('Kapso webhook signature verification failed.');

            return response()->json([
                'success' => false,
                'message' => 'Invalid webhook signature.',
            ], 401);
        }

        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON payload.',
            ], 400);
        }

        $headerEvent = $request->header('X-Webhook-Event');

        $isBatch = (
            ($payload['batch'] ?? false) === true &&
            is_array($payload['data'] ?? null)
        );

        $items = $isBatch ? $payload['data'] : [$payload];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $event = $item['request_body'] ?? $item;

            if (! is_array($event)) {
                continue;
            }

            // In a batch, each item's event takes priority.
            // The header is a fallback for items without an event name.
            $itemEvent = $item['type']
                ?? $item['event']
                ?? $event['type']
                ?? $event['event']
                ?? null;

            $currentEvent = $isBatch
                ? ($itemEvent ?? $headerEvent)
                : ($headerEvent ?? $itemEvent);

            Log::info('Kapso webhook event detected', [
                'event_name' => $currentEvent,
                'has_message' => is_array($event['message'] ?? null),
            ]);

            $message = $event['message'] ?? null;

            if (! is_array($message)) {
                continue;
            }

            if ($currentEvent === 'whatsapp.message.received') {
                $incoming = $this->storeIncomingMessage(
                    $event,
                    $message
                );

                // Dry-run only. This block never sends a WhatsApp reply.
                if (
                    is_array($incoming) &&
                    config('services.kapso.ai_dry_run') === true &&
                    config('services.kapso.ai_auto_reply') !== true &&
                    config('services.kapso.ai_live_send_enabled') !== true
                ) {
                    try {
                        $reply = app(
                            WhatsAppAIResponder::class
                        )->generateReply($incoming['message_text']);

                        Log::info('Kapso AI dry-run completed', [
                            'message_id_present' => true,
                            'reply_length' => mb_strlen($reply),
                        ]);
                    } catch (\Throwable $exception) {
                        Log::warning('Kapso AI dry-run failed', [
                            'error_type' => get_class($exception),
                        ]);
                    }
                }

                continue;
            }

            $statusEvents = [
                'whatsapp.message.sent',
                'whatsapp.message.delivered',
                'whatsapp.message.read',
                'whatsapp.message.failed',
            ];

            if (in_array($currentEvent, $statusEvents, true)) {
                $this->updateOutgoingStatus(
                    $currentEvent,
                    $event,
                    $message
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook processed.',
        ]);
    }

    private function storeIncomingMessage(
        array $event,
        array $message
    ): ?array {
        $phone = preg_replace(
            '/\D+/',
            '',
            (string) (
                data_get($event, 'conversation.phone_number')
                ?? ($message['from'] ?? '')
            )
        );

        $messageId = $message['id'] ?? null;

        if (
            $phone === '' ||
            ! is_string($messageId) ||
            $messageId === ''
        ) {
            Log::warning(
                'Kapso incoming message is missing its ID or phone.'
            );

            return null;
        }

        $timestamp = $message['timestamp'] ?? null;

        $sentAt = null;

        if (
            is_numeric($timestamp) &&
            (int) $timestamp > 0
        ) {
            try {
                $candidate = Carbon::createFromTimestamp((int) $timestamp);

                if ($candidate->lessThanOrEqualTo(now())) {
                    $sentAt = $candidate;
                }
            } catch (\Throwable $exception) {
                Log::warning('Kapso incoming message has an invalid timestamp.', [
                    'message_id' => $messageId,
                ]);
            }
        }

        $contactName = data_get(
            $event,
            'conversation.contact_name'
        )
            ?? data_get($event, 'contacts.0.profile.name')
            ?? data_get($event, 'contact.profile.name');

        $messageType = $message['type'] ?? 'unknown';

        $messageText = data_get($message, 'text.body')
            ?? data_get($message, 'kapso.content');

        return DB::transaction(function () use (
            $phone,
            $messageId,
            $contactName,
            $messageType,
            $messageText,
            $event,
            $sentAt
        ): ?array {
            $conversation = WhatsAppConversation::firstOrCreate(
                ['phone_number' => $phone],
                [
                    'contact_name' => $contactName,
                    'status' => 'open',
                    'last_message_at' => $sentAt,
                ]
            );

            if ($contactName && ! $conversation->contact_name) {
                $conversation->contact_name = $contactName;
            }

            $saved = WhatsAppMessage::firstOrCreate(
                ['provider_message_id' => $messageId],
                [
                    'conversation_id' => $conversation->id,
                    'direction' => 'inbound',
                    'message_type' => $messageType,
                    'message_text' => $messageText,
                    'status' => 'received',
                    'payload' => $event,
                    'sent_at' => $sentAt,
                ]
            );

            if (! $saved->wasRecentlyCreated) {
                $conversation->save();

                return null;
            }

            if (
                $sentAt !== null &&
                (
                    $conversation->last_message_at === null ||
                    $sentAt->greaterThan($conversation->last_message_at)
                )
            ) {
                $conversation->last_message_at = $sentAt;
                $conversation->save();
            }

            if (
                $messageType !== 'text' ||
                ! is_string($messageText) ||
                trim($messageText) === ''
            ) {
                return null;
            }

            return [
                'conversation_id' => $conversation->id,
                'message_id' => $messageId,
                'message_text' => trim($messageText),
            ];
        });
    }

    private function updateOutgoingStatus(
        string $eventName,
        array $event,
        array $message
    ): void {
        $messageId = $message['id'] ?? null;

        if (! is_string($messageId) || $messageId === '') {
            Log::warning('Kapso status event has no message ID.', [
                'event' => $eventName,
            ]);

            return;
        }

        $status = match ($eventName) {
            'whatsapp.message.sent' => 'sent',
            'whatsapp.message.delivered' => 'delivered',
            'whatsapp.message.read' => 'read',
            'whatsapp.message.failed' => 'failed',
            default => data_get($message, 'kapso.status'),
        };

        if (! in_array(
            $status,
            ['sent', 'delivered', 'read', 'failed'],
            true
        )) {
            return;
        }

        DB::transaction(function () use (
            $messageId,
            $eventName,
            $event,
            $status
        ): void {
            $outgoing = WhatsAppMessage::query()
                ->where('provider_message_id', $messageId)
                ->where('direction', 'outbound')
                ->lockForUpdate()
                ->first();

            $recipient = WhatsAppBroadcastRecipient::query()
                ->where('provider_message_id', $messageId)
                ->lockForUpdate()
                ->first();

            if (! $outgoing && ! $recipient) {
                Log::warning(
                    'Kapso status arrived before its outgoing record.',
                    [
                        'event' => $eventName,
                        'message_id' => $messageId,
                    ]
                );

                return;
            }

            if ($outgoing) {
                $this->applyMessageStatus(
                    $outgoing,
                    $status,
                    $eventName,
                    $event
                );
            }

            if ($recipient) {
                $this->applyRecipientStatus(
                    $recipient,
                    $status,
                    $eventName,
                    $event
                );
            }
        });
    }

    private function applyMessageStatus(
        WhatsAppMessage $outgoing,
        string $status,
        string $eventName,
        array $event
    ): void {
        $priority = [
            'accepted' => 0,
            'sent' => 1,
            'failed' => 2,
            'delivered' => 3,
            'read' => 4,
        ];

        $oldStatus = strtolower((string) $outgoing->status);

        if (
            isset($priority[$oldStatus]) &&
            $priority[$oldStatus] > $priority[$status]
        ) {
            return;
        }

        $storedPayload = $outgoing->payload ?? [];

        if (! is_array($storedPayload)) {
            $storedPayload = [];
        }

        $storedPayload['last_status_webhook'] = [
            'event' => $eventName,
            'payload' => $event,
            'received_at' => now()->toIso8601String(),
        ];

        $outgoing->status = $status;
        $outgoing->payload = $storedPayload;
        $outgoing->save();
    }

    private function applyRecipientStatus(
        WhatsAppBroadcastRecipient $recipient,
        string $status,
        string $eventName,
        array $event
    ): void {
        $priority = [
            'pending' => -1,
            'unknown' => -1,
            'accepted' => 0,
            'sent' => 1,
            'failed' => 2,
            'delivered' => 3,
            'read' => 4,
        ];

        $oldStatus = strtolower((string) $recipient->status);

        if (
            isset($priority[$oldStatus]) &&
            $priority[$oldStatus] > $priority[$status]
        ) {
            return;
        }

        $providerPayload = $recipient->provider_payload ?? [];

        if (! is_array($providerPayload)) {
            $providerPayload = [];
        }

        $providerPayload['last_status_webhook'] = [
            'event' => $eventName,
            'payload' => $event,
            'received_at' => now()->toIso8601String(),
        ];

        $updates = [
            'status' => $status,
            'provider_payload' => $providerPayload,
        ];

        if ($status === 'delivered' && ! $recipient->delivered_at) {
            $updates['delivered_at'] = now();
        }

        if ($status === 'failed') {
            $updates['error_message'] =
                'Kapso reported message delivery failure.';
        } else {
            $updates['error_message'] = null;
        }

        $recipient->update($updates);

        $broadcast = $recipient->broadcast()
            ->lockForUpdate()
            ->first();

        if (! $broadcast) {
            return;
        }

        // accepted_count remains the historical API-acceptance count.
        // failed_count reflects currently confirmed failed recipients.
        $failedCount = $broadcast->recipients()
            ->where('status', 'failed')
            ->count();

        $broadcast->update([
            'failed_count' => $failedCount,
        ]);
    }
}
