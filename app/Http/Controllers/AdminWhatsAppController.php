<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppBroadcastRecipient;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppGroup;
use App\Models\WhatsAppGroupMember;
use App\Models\WhatsAppGroupMessage;
use App\Models\WhatsAppMessage;
use App\Services\KapsoWhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class AdminWhatsAppController extends Controller
{
    /**
     * Display conversations, messages, groups, and broadcast drafts.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Open a selected contact directly in its individual chat page.
        $conversationId = $request->query('conversation');

        if ($conversationId !== null) {
            $conversation = WhatsAppConversation::find($conversationId);

            if ($conversation) {
                return redirect()->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ]);
            }
        }

        // Send normal inbox visits to the dedicated Chats page.
        if (
            $request->query('group') === null &&
            $request->query('new_chat') === null &&
            $request->query('broadcast') === null
        ) {
            return redirect()->route('admin.whatsapp.chats');
        }
        $conversations = WhatsAppConversation::query()
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        $selectedConversation = null;
        $messages = collect();

        $groups = WhatsAppGroup::withCount('members')
            ->with('members')
            ->orderBy('name')
            ->get();
        $selectedGroup = null;
        $groupId = $request->query('group');

        if ($groupId !== null) {
            $selectedGroup = $groups->firstWhere('id', (int) $groupId);
        }
        // Open a saved group member's individual conversation.
        $newChatMemberId = $request->query('new_chat');

        if ($newChatMemberId !== null && $selectedGroup) {
            $member = $selectedGroup->members->firstWhere(
                'id',
                (int) $newChatMemberId
            );

            if ($member) {
                $phone = preg_replace(
                    '/[^0-9]/',
                    '',
                    $member->phone_number
                );

                $conversation = WhatsAppConversation::firstOrCreate(
                    ['phone_number' => $phone],
                    [
                        'contact_name' => $member->contact_name,
                        'status' => 'open',
                        'last_message_at' => now(),
                    ]
                );

                if (! $conversation->contact_name) {
                    $conversation->update([
                        'contact_name' => $member->contact_name,
                    ]);
                }

                return redirect()->route('admin.whatsapp.index', [
                    'conversation' => $conversation->id,
                ]);
            }
        }
        $conversationId = $request->query('conversation');

        if ($conversationId !== null) {
            $selectedConversation = WhatsAppConversation::find(
                $conversationId
            );

            if ($selectedConversation) {
                $messages = $selectedConversation->messages()
                    ->orderByRaw('COALESCE(sent_at, created_at) ASC')
                    ->orderBy('id')
                    ->paginate(30, ['*'], 'messages_page')
                    ->withQueryString();
            }
        }

        // Load the 10 most recent broadcast drafts for history.
        $broadcasts = WhatsAppBroadcast::with('group')
            ->withCount('recipients')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Load a selected broadcast draft and its saved recipients.
        $selectedBroadcast = null;
        $broadcastId = $request->query('broadcast');

        if ($broadcastId !== null) {
            $selectedBroadcast = WhatsAppBroadcast::with([
                'group',
                'recipients' => function ($query) {
                    $query->orderBy('id');
                },
            ])->find($broadcastId);
        }

        return view('admin.whatsapp.index', [
            'conversations' => $conversations,
            'selectedConversation' => $selectedConversation,
            'messages' => $messages,
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'selectedBroadcast' => $selectedBroadcast,
            'broadcasts' => $broadcasts,
        ]);
    }

    /**
     * Start an individual WhatsApp conversation.
     */
    public function newChat(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone_number' => [
                'required',
                'string',
                'max:30',
                'regex:/^\+?[0-9\s\-()]{8,25}$/',
            ],
            'contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        // Keep digits only for storage and matching.
        $phone = preg_replace(
            '/\D+/',
            '',
            $validated['phone_number']
        );

        if (
            ! is_string($phone) ||
            strlen($phone) < 8 ||
            strlen($phone) > 15
        ) {
            return back()
                ->withErrors([
                    'phone_number' => 'Enter a valid international phone number.',
                ])
                ->withInput();
        }

        $contactName = trim($validated['contact_name'] ?? '');

        $conversation = WhatsAppConversation::firstOrCreate(
            ['phone_number' => $phone],
            [
                'contact_name' => $contactName !== ''
                    ? $contactName
                    : null,
                'status' => 'open',
                'last_message_at' => now(),
            ]
        );

        if (
            $contactName !== '' &&
            ! $conversation->contact_name
        ) {
            $conversation->update([
                'contact_name' => $contactName,
            ]);
        }

        return redirect()
            ->route('admin.whatsapp.chat', [
                'conversation' => $conversation->id,
            ])
            ->with(
                'success',
                'Conversation opened. You can now message this contact.'
            );
    }

    /**
     * Send an individual text reply through Kapso.
     *
     * This is an individual-message action, not a broadcast action.
     */
    public function reply(
        Request $request,
        WhatsAppConversation $conversation,
        KapsoWhatsAppService $kapso
    ): RedirectResponse {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4096'],
        ]);

        $text = trim($validated['message']);

        if ($text === '') {
            return back()
                ->withErrors([
                    'message' => 'Please enter a message.',
                ])
                ->withInput();
        }

        // Prevent sending to unauthorized recipients in sandbox mode.
        if (config('services.kapso.sandbox_mode', true)) {
            $recipient = preg_replace(
                '/\D+/',
                '',
                (string) $conversation->phone_number
            );

            $sandboxRecipient = preg_replace(
                '/\D+/',
                '',
                (string) config('services.kapso.sandbox_recipient', '')
            );

            if (
                $sandboxRecipient === '' ||
                $recipient !== $sandboxRecipient
            ) {
                return redirect()
                    ->route('admin.whatsapp.chat', [
                        'conversation' => $conversation->id,
                    ])
                    ->with(
                        'error',
                        'Sandbox mode is enabled. This contact is not the authorized sandbox recipient.'
                    );
            }
        }

        // Check the latest valid incoming customer message.
        // Outgoing messages must never extend the 24-hour window.
        $latestInboundAt = WhatsAppMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('direction', 'inbound')
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now())
            ->orderByDesc('sent_at')
            ->value('sent_at');

        $windowExpired = $latestInboundAt === null
            || Carbon::parse($latestInboundAt)
                ->lt(now()->subHours(24));

        if ($windowExpired) {
            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'error',
                    'The 24-hour customer service window has expired or no valid incoming message timestamp is available. Use an approved WhatsApp template.'
                )
                ->withInput();
        }

        try {
            $response = $kapso->sendText(
                $conversation->phone_number,
                $text
            );

            $providerMessageId = data_get(
                $response,
                'messages.0.id'
            );

            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'provider_message_id' => $providerMessageId,
                'direction' => 'outbound',
                'message_type' => 'text',
                'message_text' => $text,
                'status' => data_get(
                    $response,
                    'messages.0.message_status',
                    'accepted'
                ),
                'payload' => $response,
                'sent_at' => now(),
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'success',
                    'Kapso accepted the message request. Delivery is not yet confirmed.'
                );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'error',
                    'The message could not be sent. Check the Laravel log for details.'
                )
                ->withInput();
        }
    }

    /**
    /**
     * Send an attachment to an individual WhatsApp contact.
     */

    /**
     * Send the approved UniBridge application-update template.
     */
    public function sendTemplate(
        Request $request,
        WhatsAppConversation $conversation,
        KapsoWhatsAppService $kapso
    ): RedirectResponse {
        $validated = $request->validate([
            'template_name' => [
                'required',
                'in:unibridge_application_update',
            ],
            'student_name' => ['required', 'string', 'max:100'],
            'application_status' => ['required', 'string', 'max:100'],
        ]);

        $returnToChat = fn () => redirect()->route(
            'admin.whatsapp.chat',
            ['conversation' => $conversation->id]
        );

        if (! config('services.kapso.application_template_approved', false)) {
            return $returnToChat()->with(
                'error',
                'Template sending is disabled until the template is approved in Kapso.'
            );
        }

        if (config('services.kapso.sandbox_mode', true)) {
            return $returnToChat()->with(
                'error',
                'Sandbox mode is enabled. Disable it only when the real Kapso number is correctly configured.'
            );
        }

        $studentName = trim($validated['student_name']);
        $applicationStatus = trim($validated['application_status']);

        try {
            $response = $kapso->sendTemplate(
                $conversation->phone_number,
                $validated['template_name'],
                [$studentName, $applicationStatus],
                'en_US'
            );

            $messageText = "Hello {$studentName}, your UniBridge application status has been updated to {$applicationStatus}. Please log in to your UniBridge account for details.";

            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'provider_message_id' => data_get($response, 'messages.0.id'),
                'direction' => 'outbound',
                'message_type' => 'template',
                'message_text' => $messageText,
                'status' => data_get(
                    $response,
                    'messages.0.message_status',
                    'accepted'
                ),
                'payload' => $response,
                'sent_at' => now(),
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            return $returnToChat()->with(
                'success',
                'Kapso accepted the template request. Delivery is not yet confirmed.'
            );
        } catch (Throwable $exception) {
            report($exception);

            return $returnToChat()->with(
                'error',
                'The template could not be sent. Check the Laravel log for details.'
            )->withInput();
        }
    }

    public function sendMedia(
        Request $request,
        WhatsAppConversation $conversation,
        KapsoWhatsAppService $kapso
    ): RedirectResponse {
        $validated = $request->validate([
            'attachment' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,mp4,3gp,mp3,m4a,ogg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
                'max:40960',
            ],
            'caption' => ['nullable', 'string', 'max:1024'],
        ]);

        $file = $request->file('attachment');
        $caption = trim((string) ($validated['caption'] ?? ''));

        if (config('services.kapso.sandbox_mode', true)) {
            $recipient = preg_replace(
                '/\D+/',
                '',
                (string) $conversation->phone_number
            );

            $sandboxRecipient = preg_replace(
                '/\D+/',
                '',
                (string) config('services.kapso.sandbox_recipient', '')
            );

            if (
                $sandboxRecipient === '' ||
                $recipient !== $sandboxRecipient
            ) {
                return redirect()
                    ->route('admin.whatsapp.chat', [
                        'conversation' => $conversation->id,
                    ])
                    ->with(
                        'error',
                        'Sandbox mode is enabled. This contact is not the authorized sandbox recipient.'
                    );
            }
        }
        // Enforce the 24-hour customer service window before uploading media.
        $latestInboundAt = WhatsAppMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('direction', 'inbound')
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now())
            ->orderByDesc('sent_at')
            ->value('sent_at');

        $windowExpired = $latestInboundAt === null
            || Carbon::parse($latestInboundAt)
                ->lt(now()->subHours(24));

        if ($windowExpired) {
            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'error',
                    'The 24-hour customer service window has expired or no valid incoming message timestamp is available. Use an approved WhatsApp template.'
                )
                ->withInput();
        }
        $mime = strtolower((string) $file->getMimeType());

        if (str_starts_with($mime, 'image/')) {
            $type = 'image';
        } elseif (str_starts_with($mime, 'video/')) {
            $type = 'video';
        } elseif (str_starts_with($mime, 'audio/')) {
            $type = 'audio';
        } else {
            $type = 'document';
        }

        try {
            $mediaId = $kapso->uploadMedia($file);

            $response = $kapso->sendMedia(
                $conversation->phone_number,
                $type,
                $mediaId,
                $caption !== '' ? $caption : null,
                $type === 'document'
                    ? $file->getClientOriginalName()
                    : null
            );

            $providerMessageId = data_get(
                $response,
                'messages.0.id'
            );

            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'provider_message_id' => $providerMessageId,
                'direction' => 'outbound',
                'message_type' => $type,
                'message_text' => $caption !== ''
                    ? $caption
                    : '['.ucfirst($type).' attachment: '
                        .$file->getClientOriginalName().']',
                'status' => data_get(
                    $response,
                    'messages.0.message_status',
                    'accepted'
                ),
                'payload' => [
                    'provider_response' => $response,
                    'media_id' => $mediaId,
                    'filename' => $file->getClientOriginalName(),
                ],
                'sent_at' => now(),
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'success',
                    'Kapso accepted the attachment request. Delivery is not yet confirmed.'
                );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'error',
                    'The attachment could not be sent. Check the Laravel log for details.'
                )
                ->withInput();
        }
    }

    /**
    /**
     * Share a location explicitly provided by the admin.
     */
    public function sendLocation(
        Request $request,
        WhatsAppConversation $conversation,
        KapsoWhatsAppService $kapso
    ): RedirectResponse {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'location_address' => ['nullable', 'string', 'max:1000'],
            'location_confirmed' => ['required', 'accepted'],
        ]);

        if (config('services.kapso.sandbox_mode', true)) {
            $recipient = preg_replace(
                '/\D+/',
                '',
                (string) $conversation->phone_number
            );

            $sandboxRecipient = preg_replace(
                '/\D+/',
                '',
                (string) config('services.kapso.sandbox_recipient', '')
            );

            if (
                $sandboxRecipient === '' ||
                $recipient !== $sandboxRecipient
            ) {
                return redirect()
                    ->route('admin.whatsapp.chat', [
                        'conversation' => $conversation->id,
                    ])
                    ->with(
                        'error',
                        'Sandbox mode is enabled. This contact is not the authorized sandbox recipient.'
                    );
            }
        }
        // Enforce the 24-hour customer service window before sharing location.
        $latestInboundAt = WhatsAppMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('direction', 'inbound')
            ->whereNotNull('sent_at')
            ->where('sent_at', '<=', now())
            ->orderByDesc('sent_at')
            ->value('sent_at');

        $windowExpired = $latestInboundAt === null
            || Carbon::parse($latestInboundAt)
                ->lt(now()->subHours(24));

        if ($windowExpired) {
            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'error',
                    'The 24-hour customer service window has expired or no valid incoming message timestamp is available. Use an approved WhatsApp template.'
                )
                ->withInput();
        }
        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];
        $name = trim((string) ($validated['location_name'] ?? ''));
        $address = trim((string) ($validated['location_address'] ?? ''));

        try {
            $response = $kapso->sendLocation(
                $conversation->phone_number,
                $latitude,
                $longitude,
                $name !== '' ? $name : null,
                $address !== '' ? $address : null
            );

            WhatsAppMessage::create([
                'conversation_id' => $conversation->id,
                'provider_message_id' => data_get(
                    $response,
                    'messages.0.id'
                ),
                'direction' => 'outbound',
                'message_type' => 'location',
                'message_text' => ($name !== '' ? $name : 'Shared location')
                    .' ('.$latitude.', '.$longitude.')',
                'status' => data_get(
                    $response,
                    'messages.0.message_status',
                    'accepted'
                ),
                'payload' => [
                    'provider_response' => $response,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'name' => $name,
                    'address' => $address,
                ],
                'sent_at' => now(),
            ]);

            $conversation->update([
                'last_message_at' => now(),
            ]);

            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'success',
                    'Kapso accepted the location request. Delivery is not yet confirmed.'
                );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.whatsapp.chat', [
                    'conversation' => $conversation->id,
                ])
                ->with(
                    'error',
                    'The location could not be shared. Check the Laravel log for details.'
                )
                ->withInput();
        }
    }

    /**
     * Create a WhatsApp contact group.
     */
    public function createGroup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => [
                'required',
                'in:students,consultants,general',
            ],
        ]);

        WhatsAppGroup::create([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.whatsapp.groups')
            ->with('success', 'WhatsApp group created successfully.');
    }

    /**
     * Delete a group and its saved memberships.
     */
    public function deleteGroup(
        WhatsAppGroup $group
    ): RedirectResponse {
        $hasBroadcastHistory = WhatsAppBroadcast::query()
            ->where('whatsapp_group_id', $group->id)
            ->exists();

        if ($hasBroadcastHistory) {
            return redirect()
                ->route('admin.whatsapp.groups')
                ->with(
                    'error',
                    'This group cannot be deleted because it has saved broadcast history. Keep the group to preserve its history.'
                );
        }

        $group->delete();

        return redirect()
            ->route('admin.whatsapp.groups')
            ->with('success', 'WhatsApp group deleted successfully.');
    }

    /**
     * Add a saved contact and record consent only when confirmed.
     */
    public function addMember(
        Request $request,
        WhatsAppGroup $group
    ): RedirectResponse {
        $validated = $request->validate([
            'contact_name' => [
                'required',
                'string',
                'max:255',
            ],
            'phone_number' => [
                'required',
                'string',
                'max:30',
                'regex:/^\+?[0-9\s\-()]{8,25}$/',
            ],
            'contact_type' => [
                'required',
                'in:student,consultant,manual',
            ],
            'whatsapp_opt_in' => [
                'sometimes',
                'accepted',
            ],
        ]);

        $phone = preg_replace(
            '/\D+/',
            '',
            $validated['phone_number']
        );

        if (
            ! is_string($phone) ||
            strlen($phone) < 8 ||
            strlen($phone) > 15
        ) {
            return back()
                ->withErrors([
                    'phone_number' => 'Enter a valid international phone number.',
                ])
                ->withInput();
        }

        $exists = $group->members()
            ->where('phone_number', $phone)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'phone_number' => 'This phone number is already in this group.',
                ])
                ->withInput();
        }

        $optedIn = $request->boolean('whatsapp_opt_in');

        $group->members()->create([
            'contact_name' => trim($validated['contact_name']),
            'phone_number' => $phone,
            'contact_type' => $validated['contact_type'],
            'user_id' => null,
            'whatsapp_opt_in' => $optedIn,
            'opted_in_at' => $optedIn ? now() : null,
        ]);

        return redirect()
            ->route('admin.whatsapp.groups')
            ->with(
                'success',
                $optedIn
                    ? 'Contact added and consent recorded.'
                    : 'Contact added. WhatsApp messaging consent is not recorded.'
            );
    }

    /**
     * Remove a contact from a group.
     */
    public function removeMember(
        WhatsAppGroup $group,
        WhatsAppGroupMember $member
    ): RedirectResponse {
        abort_unless(
            (int) $member->whatsapp_group_id === (int) $group->id,
            404
        );

        $member->delete();

        return redirect()
            ->route('admin.whatsapp.groups')
            ->with('success', 'Contact removed from the group.');
    }

    /**
     * Save a broadcast draft and eligible recipient snapshots.
     *
     * This method does not call Kapso and does not send messages.
     */
    public function broadcastPreview(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'group_id' => [
                'required',
                'integer',
                'exists:whatsapp_groups,id',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'message_text' => [
                'required',
                'string',
                'max:4096',
            ],
        ]);

        $name = trim($validated['name']);
        $messageText = trim($validated['message_text']);

        if ($name === '' || $messageText === '') {
            return back()
                ->withErrors([
                    'message_text' => 'Broadcast name and message cannot be empty.',
                ])
                ->withInput();
        }

        $group = WhatsAppGroup::with('members')
            ->findOrFail($validated['group_id']);

        // Include only contacts with recorded opt-in and a timestamp.
        $eligible = $group->members->filter(
            fn ($member) => (bool) $member->whatsapp_opt_in
                && ! empty($member->phone_number)
                && ! empty($member->opted_in_at)
        );

        $broadcast = DB::transaction(function () use (
            $request,
            $group,
            $eligible,
            $name,
            $messageText
        ) {
            $draft = WhatsAppBroadcast::create([
                'whatsapp_group_id' => $group->id,
                'created_by' => $request->user()->id,
                'name' => $name,
                'message_text' => $messageText,
                'status' => 'draft',
                'recipient_count' => $eligible->count(),
                'accepted_count' => 0,
                'failed_count' => 0,
            ]);

            foreach ($eligible as $member) {
                WhatsAppBroadcastRecipient::create([
                    'whatsapp_broadcast_id' => $draft->id,
                    'whatsapp_group_member_id' => $member->id,
                    'contact_name' => $member->contact_name,
                    'phone_number' => $member->phone_number,
                    'status' => 'pending',
                ]);
            }

            return $draft;
        });

        return redirect()
            ->route('admin.whatsapp.broadcasts', [
                'broadcast' => $broadcast->id,
            ])
            ->with(
                'success',
                'Draft saved. Eligible recipients: '
                .$eligible->count()
                .'. No WhatsApp messages were sent.'
            );
    }

    /**
     * Send one explicitly confirmed sandbox test message.
     * Never sends a multi-recipient broadcast.
     */
    public function sendTestBroadcast(
        Request $request,
        WhatsAppBroadcast $broadcast,
        KapsoWhatsAppService $kapso
    ): RedirectResponse {
        $returnToPreview = fn () => redirect()
            ->route('admin.whatsapp.broadcasts', [
                'broadcast' => $broadcast->id,
            ]);

        // This endpoint is only for sandbox testing.
        if (! config('services.kapso.sandbox_mode', true)) {
            return $returnToPreview()->with(
                'error',
                'Test-send blocked: sandbox mode must remain enabled.'
            );
        }

        if (! $request->boolean('confirm_test_send')) {
            return $returnToPreview()->with(
                'error',
                'Please confirm the sandbox test send first.'
            );
        }

        $recipientId = null;

        try {
            $claimed = DB::transaction(function () use (
                $broadcast,
                &$recipientId
            ) {
                $lockedBroadcast = WhatsAppBroadcast::query()
                    ->whereKey($broadcast->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedBroadcast->status !== 'draft') {
                    return 'not_draft';
                }

                $recipients = $lockedBroadcast->recipients()
                    ->lockForUpdate()
                    ->get();

                // Absolutely no group/mass sending from this endpoint.
                if ($recipients->count() !== 1) {
                    return 'recipient_count';
                }

                $recipient = $recipients->first();

                if ($recipient->status !== 'pending') {
                    return 'recipient_status';
                }

                $member = $recipient->groupMember;

                // Confirm the contact still exists and has current consent.
                if (
                    ! $member ||
                    (int) $member->whatsapp_group_id !==
                        (int) $lockedBroadcast->whatsapp_group_id ||
                    ! $member->whatsapp_opt_in ||
                    ! $member->opted_in_at ||
                    ! $member->phone_number
                ) {
                    return 'consent';
                }

                $digits = fn ($phone) => preg_replace(
                    '/\D+/',
                    '',
                    (string) $phone
                );

                $sandboxNumber = $digits(
                    config('services.kapso.sandbox_recipient', '')
                );

                if (
                    $sandboxNumber === '' ||
                    $digits($recipient->phone_number) !== $sandboxNumber ||
                    $digits($member->phone_number) !== $sandboxNumber
                ) {
                    return 'wrong_recipient';
                }
                // Find the existing conversation for the sandbox recipient.
                $conversation = WhatsAppConversation::query()
                    ->get()
                    ->first(
                        fn ($item) => $digits($item->phone_number)
                            === $sandboxNumber
                    );

                if (! $conversation) {
                    return 'window_expired';
                }

                // Only a valid incoming customer message opens the 24-hour window.
                $latestInboundAt = WhatsAppMessage::query()
                    ->where('conversation_id', $conversation->id)
                    ->where('direction', 'inbound')
                    ->whereNotNull('sent_at')
                    ->where('sent_at', '<=', now())
                    ->orderByDesc('sent_at')
                    ->value('sent_at');

                $windowExpired = $latestInboundAt === null
                    || Carbon::parse($latestInboundAt)
                        ->lt(now()->subHours(24));

                if ($windowExpired) {
                    return 'window_expired';
                }

                // Claim before calling the external API to block repeat clicks.
                $recipient->update([
                    'status' => 'sending',
                    'attempted_at' => now(),
                ]);

                $lockedBroadcast->update([
                    'status' => 'sending',
                    'started_at' => now(),
                ]);

                $recipientId = $recipient->id;

                return 'claimed';
            });
        } catch (Throwable $exception) {
            report($exception);

            return $returnToPreview()->with(
                'error',
                'Could not prepare the test send. No send was intentionally started.'
            );
        }

        if ($claimed !== 'claimed') {
            $messages = [
                'not_draft' => 'This draft has already been processed or claimed.',
                'recipient_count' => 'Test-send requires exactly one saved recipient.',
                'recipient_status' => 'The recipient is not pending; no retry was made.',
                'consent' => 'The contact no longer has valid recorded opt-in.',
                'wrong_recipient' => 'The saved contact does not match the configured sandbox number.',
                'window_expired' => 'Test-send blocked: no valid incoming customer message within the last 24 hours.',
            ];

            return $returnToPreview()->with(
                'error',
                $messages[$claimed] ?? 'Test-send blocked.'
            );
        }

        try {
            $recipient = WhatsAppBroadcastRecipient::findOrFail(
                $recipientId
            );

            $response = $kapso->sendText(
                $recipient->phone_number,
                $broadcast->message_text
            );

            DB::transaction(function () use (
                $broadcast,
                $recipient,
                $response
            ) {
                $recipient->update([
                    'status' => 'accepted',
                    'provider_message_id' => data_get(
                        $response,
                        'messages.0.id'
                    ),
                    'provider_payload' => $response,
                    'error_message' => null,
                ]);

                $broadcast->update([
                    'status' => 'accepted',
                    'accepted_count' => 1,
                    'failed_count' => 0,
                    'completed_at' => now(),
                ]);
            });

            return $returnToPreview()->with(
                'success',
                'Kapso accepted the sandbox test request. Delivery is not yet confirmed.'
            );
        } catch (Throwable $exception) {
            report($exception);

            // The request may have reached Kapso even if a timeout occurred.
            // Mark unknown and do not automatically retry.
            try {
                DB::transaction(function () use (
                    $broadcast,
                    $recipientId,
                    $exception
                ) {
                    $recipient = WhatsAppBroadcastRecipient::find(
                        $recipientId
                    );

                    if ($recipient) {
                        $recipient->update([
                            'status' => 'unknown',
                            'error_message' => substr(
                                $exception->getMessage(),
                                0,
                                2000
                            ),
                        ]);
                    }

                    $broadcast->update([
                        'status' => 'unknown',
                        'completed_at' => now(),
                    ]);
                });
            } catch (Throwable $recordingException) {
                report($recordingException);
            }

            return $returnToPreview()->with(
                'error',
                'The outcome is uncertain. Do not retry this draft until its status is checked in Kapso.'
            );
        }
    }

    public function chatPage(WhatsAppConversation $conversation): View
    {
        $messages = $conversation->messages()
            ->orderByRaw('COALESCE(sent_at, created_at) ASC')
            ->orderBy('id')
            ->paginate(30);

        return view('admin.whatsapp.chat', [
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function chatsPage(): View
    {
        $conversations = WhatsAppConversation::query()
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.whatsapp.chats', [
            'conversations' => $conversations,
        ]);
    }

    public function groupsPage(): View
    {
        $groups = WhatsAppGroup::query()
            ->withCount('members')
            ->with('members')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.whatsapp.groups', [
            'groups' => $groups,
        ]);
    }

    public function storeGroupMessage(Request $request, int $group): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4096'],
        ]);

        $text = trim($validated['message']);

        if ($text === '') {
            return back()->withErrors([
                'message' => 'Please enter a message.',
            ]);
        }

        $whatsappGroup = WhatsAppGroup::findOrFail($group);
        $user = $request->user();

        WhatsAppGroupMessage::create([
            'whatsapp_group_id' => $whatsappGroup->id,
            'sender_user_id' => $user->id,
            'sender_type' => 'admin',
            'sender_name' => $user->name,
            'message_text' => $text,
            'message_type' => 'text',
            'status' => 'local',
            'sent_at' => now(),
        ]);

        return redirect()
            ->route('admin.whatsapp.group-inbox', ['group' => $whatsappGroup->id])
            ->with('success', 'Internal group message saved in UniBridge.');
    }

    public function groupInbox(int $group): View
    {
        $group = WhatsAppGroup::query()
            ->withCount('members')
            ->findOrFail($group);

        $members = $group->members()->get();

        $normalizePhone = static function (?string $phone): string {
            return preg_replace('/\D+/', '', $phone ?? '');
        };

        // Read conversation metadata first; do not load unrelated message history.
        $allConversations = WhatsAppConversation::query()->get();

        $conversationByPhone = $allConversations
            ->filter(function ($conversation) use ($normalizePhone) {
                return $normalizePhone($conversation->phone_number) !== '';
            })
            ->keyBy(function ($conversation) use ($normalizePhone) {
                return $normalizePhone($conversation->phone_number);
            });

        $matchingIds = [];

        foreach ($members as $member) {
            $phone = $normalizePhone($member->phone_number);
            $conversation = $phone !== ''
                ? $conversationByPhone->get($phone)
                : null;

            $member->setRelation('conversation', $conversation);

            if ($conversation) {
                $matchingIds[] = $conversation->id;
            }
        }

        // Load messages only for conversations belonging to saved group members.
        $matchedConversations = WhatsAppConversation::query()
            ->whereIn('id', array_values(array_unique($matchingIds)))
            ->with([
                'messages' => function ($query) {
                    $query->orderByRaw('COALESCE(sent_at, created_at) ASC')
                        ->orderBy('id');
                },
            ])
            ->get()
            ->keyBy(function ($conversation) use ($normalizePhone) {
                return $normalizePhone($conversation->phone_number);
            });

        foreach ($members as $member) {
            $phone = $normalizePhone($member->phone_number);
            $conversation = $phone !== ''
                ? $matchedConversations->get($phone)
                : null;

            $member->setRelation('conversation', $conversation);
        }

        $groupMessages = WhatsAppGroupMessage::query()
            ->where('whatsapp_group_id', $group->id)
            ->with('sender')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $broadcasts = WhatsAppBroadcast::query()
            ->where('whatsapp_group_id', $group->id)
            ->where('status', '!=', 'draft')
            ->with([
                'creator',
                'recipients.groupMember',
            ])
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.whatsapp.group-inbox', [
            'group' => $group,
            'members' => $members,
            'groupMessages' => $groupMessages,
            'broadcasts' => $broadcasts,
            'memberCount' => $group->members_count,
        ]);
    }

    public function broadcastsPage(): View
    {
        $broadcasts = WhatsAppBroadcast::query()
            ->with([
                'group',
                'recipients' => function ($query) {
                    $query->orderBy('id');
                },
            ])
            ->withCount('recipients')
            ->orderByDesc('id')
            ->paginate(20);

        $groups = WhatsAppGroup::query()
            ->withCount('members')
            ->orderBy('name')
            ->get();

        return view('admin.whatsapp.broadcasts', [
            'broadcasts' => $broadcasts,
            'groups' => $groups,
        ]);
    }

    /**
     * Rename a WhatsApp conversation's display name.
     */
    public function renameConversation(
        Request $request,
        WhatsAppConversation $conversation
    ): RedirectResponse {
        $validated = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
        ]);

        $conversation->update([
            'contact_name' => trim($validated['contact_name']),
        ]);

        return redirect()
            ->route('admin.whatsapp.index', [
                'conversation' => $conversation->id,
            ])
            ->with('success', 'Chat renamed successfully.');
    }

    /**
     * Delete a conversation and its related messages.
     */
    public function deleteConversation(
        WhatsAppConversation $conversation
    ): RedirectResponse {
        $conversation->delete();

        return redirect()
            ->route('admin.whatsapp.index')
            ->with('success', 'Chat and its messages deleted successfully.');
    }
}
