<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppBroadcastRecipient;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\services\WhatsAppPhoneNormalizer;
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

        $jsonBody = preg_replace('/^\xEF\xBB\xBF/', '', $rawBody) ?? $rawBody;
        $payload = json_decode($jsonBody, true);

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
                $incoming = $this->storeIncomingMessage($event, $message);

                if (is_array($incoming)) {
                    $this->processAIReply($incoming);
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
                $this->updateOutgoingStatus($currentEvent, $event, $message);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Webhook processed.',
        ]);
    }

    /** Generate a safe AI reply draft and only send when all live guards pass. */
    private function processAIReply(array $incoming): void
    {
        $dryRun = config('services.kapso.ai_dry_run') === true;
        $autoSend = ! $dryRun
            && config('services.kapso.ai_auto_reply') === true
            && config('services.kapso.ai_live_send_enabled') === true;

        if (! $dryRun && ! $autoSend) {
            return;
        }

        $conversationId = (int) $incoming['conversation_id'];
        $incomingMessageId = (string) $incoming['message_id'];

        try {
            $reply = ($incoming['reply_mode'] ?? null) === 'media_fallback'
                ? $this->unsupportedMediaReply((string) ($incoming['message_type'] ?? 'media'))
                : app(WhatsAppAIResponder::class)->generateReply($incoming['message_text']);

            if (trim($reply) === '') {
                throw new \RuntimeException('AI generated an empty reply.');
            }

            $draftId = DB::transaction(function () use (
                $conversationId,
                $incomingMessageId,
                $reply
            ): ?int {
                $conversation = WhatsAppConversation::query()
                    ->whereKey($conversationId)
                    ->lockForUpdate()
                    ->first();

                if (! $conversation) {
                    throw new \RuntimeException('Conversation not found for AI reply.');
                }

                $existingReply = WhatsAppMessage::query()
                    ->where('conversation_id', $conversationId)
                    ->where('direction', 'outbound')
                    ->where('payload->ai_source', 'kapso_webhook')
                    ->where('payload->in_reply_to_message_id', $incomingMessageId)
                    ->exists();

                if ($existingReply) {
                    return null;
                }

                $draft = WhatsAppMessage::create([
                    'conversation_id' => $conversationId,
                    'provider_message_id' => null,
                    'direction' => 'outbound',
                    'message_type' => 'text',
                    'message_text' => $reply,
                    'status' => 'draft',
                    'payload' => [
                        'ai_source' => 'kapso_webhook',
                        'in_reply_to_message_id' => $incomingMessageId,
                    ],
                    'sent_at' => null,
                ]);

                return (int) $draft->id;
            });

            if ($draftId === null) {
                Log::info('Kapso AI reply already exists', [
                    'conversation_id' => $conversationId,
                ]);

                return;
            }

            $draft = WhatsAppMessage::findOrFail($draftId);

            if (! $autoSend) {
                Log::info('Kapso AI dry-run draft saved', [
                    'conversation_id' => $conversationId,
                    'reply_length' => mb_strlen($reply),
                ]);

                return;
            }

            $conversation = WhatsAppConversation::find($conversationId);
            $inboundMessage = WhatsAppMessage::query()
                ->where('conversation_id', $conversationId)
                ->where('provider_message_id', $incomingMessageId)
                ->where('direction', 'inbound')
                ->first();

            $blockReason = null;

            if (! $conversation || ! $inboundMessage || ! $inboundMessage->sent_at) {
                $blockReason = 'missing_valid_inbound_message';
            } elseif (
                $inboundMessage->sent_at->isFuture() ||
                $inboundMessage->sent_at->lt(now()->subHours(24))
            ) {
                $blockReason = 'outside_24_hour_reply_window';
            }

            if (
                $blockReason === null &&
                config('services.kapso.sandbox_mode') === true
            ) {
                $allowedRecipient = WhatsAppPhoneNormalizer::normalize(
                    (string) config('services.kapso.sandbox_recipient', '')
                );
                $actualRecipient = WhatsAppPhoneNormalizer::normalize(
                    (string) $conversation->phone_number
                );

                if ($allowedRecipient === '' || $actualRecipient !== $allowedRecipient) {
                    $blockReason = 'sandbox_recipient_mismatch';
                }
            }

            if ($blockReason !== null) {
                $payload = (array) $draft->payload;
                $payload['auto_send_block_reason'] = $blockReason;
                $draft->update(['payload' => $payload]);

                Log::warning('Kapso AI reply retained as draft', [
                    'conversation_id' => $conversationId,
                    'reason' => $blockReason,
                ]);

                return;
            }

            $providerResponse = app(\App\Services\KapsoWhatsAppService::class)
                ->sendText($conversation->phone_number, $reply);
            $providerMessageId = data_get($providerResponse, 'messages.0.id');
            $providerStatus = data_get($providerResponse, 'messages.0.message_status') ?? 'accepted';
            $payload = (array) $draft->payload;
            $payload['auto_send'] = 'accepted_by_provider';

            $draft->update([
                'provider_message_id' => $providerMessageId,
                'status' => $providerStatus,
                'payload' => $payload,
                'sent_at' => now(),
            ]);

            Log::info('Kapso AI reply accepted by provider', [
                'conversation_id' => $conversationId,
                'provider_message_id_present' => is_string($providerMessageId)
                    && $providerMessageId !== '',
                'provider_status' => $providerStatus,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Kapso AI reply processing failed', [
                'conversation_id' => $conversationId,
                'error_type' => get_class($exception),
            ]);
        }
    }

    private function storeIncomingMessage(array $event, array $message): ?array
    {
        $phone = WhatsAppPhoneNormalizer::normalize(
            (string) (data_get($event, 'conversation.phone_number') ?? ($message['from'] ?? ''))
        );
        $messageId = $message['id'] ?? null;

        if ($phone === '' || ! is_string($messageId) || $messageId === '') {
            Log::warning('Kapso incoming message is missing its ID or phone.');

            return null;
        }

        $timestamp = $message['timestamp'] ?? null;
        $sentAt = null;

        if (is_numeric($timestamp) && (int) $timestamp > 0) {
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

        $contactName = data_get($event, 'conversation.contact_name')
            ?? data_get($event, 'contacts.0.profile.name')
            ?? data_get($event, 'contact.profile.name');
        $messageType = $message['type'] ?? 'unknown';
        $transcription = data_get($message, 'audio.transcription')
            ?? data_get($message, 'transcription')
            ?? data_get($message, 'kapso.transcription');
        $messageText = $messageType === 'text'
            ? (data_get($message, 'text.body') ?? data_get($message, 'kapso.content'))
            : ($messageType === 'audio' && is_string($transcription)
                ? $transcription
                : null);

        return DB::transaction(function () use (
            $phone,
            $messageId,
            $contactName,
            $messageType,
            $messageText,
            $event,
            $sentAt
        ): ?array {
            $conversation = WhatsAppConversation::query()
                ->get()
                ->filter(fn (WhatsAppConversation $item) =>
                    WhatsAppPhoneNormalizer::normalize((string) $item->phone_number) === $phone
                )
                ->sortByDesc(fn (WhatsAppConversation $item) =>
                    $item->last_message_at?->timestamp ?? $item->updated_at?->timestamp ?? 0
                )
                ->first();

            if (! $conversation) {
                $conversation = WhatsAppConversation::create([
                    'phone_number' => $phone,
                    'contact_name' => $contactName,
                    'status' => 'open',
                    'last_message_at' => $sentAt,
                ]);
            }

            if ($contactName && ! $conversation->contact_name) {
                $conversation->contact_name = $contactName;
            }

            $centralContact = WhatsAppContact::firstOrCreate(
                ['phone_number' => $phone],
                [
                    'contact_name' => $contactName,
                    'contact_type' => 'incoming',
                ]
            );

            if ($contactName && ! $centralContact->contact_name) {
                $centralContact->contact_name = $contactName;
                $centralContact->save();
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

            if (in_array($messageType, ['text', 'audio'], true)
                && is_string($messageText)
                && trim($messageText) !== ''
            ) {
                return [
                    'conversation_id' => $conversation->id,
                    'message_id' => $messageId,
                    'message_type' => $messageType,
                    'message_text' => trim($messageText),
                ];
            }

            if (in_array($messageType, ['audio', 'image', 'video', 'document', 'sticker'], true)) {
                return [
                    'conversation_id' => $conversation->id,
                    'message_id' => $messageId,
                    'message_type' => $messageType,
                    'message_text' => '[No transcription available]',
                    'reply_mode' => 'media_fallback',
                ];
            }

            return null;
        });
    }

    private function unsupportedMediaReply(string $messageType): string
    {
        if ($messageType === 'audio') {
            return 'I received your voice message, but automatic voice transcription is not available yet. Please type your question in English or Urdu so I can help. For personal application, payment, visa or document details, sign in to your UniBridge account or contact your assigned consultant. Do not send passwords, OTPs, passport/ID numbers or bank details here.';
        }

        return 'I received your attachment, but automatic document/image review is not available here. For personal application, payment, visa or document details, use your secure UniBridge account or contact your assigned consultant. Please type a general question if you need study-abroad guidance. Do not send passwords, OTPs, passport/ID numbers or bank details here.';
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

        if (! in_array($status, ['sent', 'delivered', 'read', 'failed'], true)) {
            return;
        }

        DB::transaction(function () use ($messageId, $eventName, $event, $status): void {
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
                Log::warning('Kapso status arrived before its outgoing record.', [
                    'event' => $eventName,
                    'message_id' => $messageId,
                ]);

                return;
            }

            if ($outgoing) {
                $this->applyMessageStatus($outgoing, $status, $eventName, $event);
            }

            if ($recipient) {
                $this->applyRecipientStatus($recipient, $status, $eventName, $event);
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

        if (isset($priority[$oldStatus]) && $priority[$oldStatus] > $priority[$status]) {
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

        if (isset($priority[$oldStatus]) && $priority[$oldStatus] > $priority[$status]) {
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
            $updates['error_message'] = 'Kapso reported message delivery failure.';
        } else {
            $updates['error_message'] = null;
        }

        $recipient->update($updates);
        $broadcast = $recipient->broadcast()->lockForUpdate()->first();

        if (! $broadcast) {
            return;
        }

        $failedCount = $broadcast->recipients()
            ->where('status', 'failed')
            ->count();
        $broadcast->update(['failed_count' => $failedCount]);
    }
}
