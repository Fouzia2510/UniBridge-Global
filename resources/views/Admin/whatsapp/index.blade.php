<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">WhatsApp Inbox</h2>
                <p class="mt-1 text-sm text-slate-500">All conversations in one place</p>
            </div>

            <div class="flex flex-wrap gap-2 text-sm">
                <a href="{{ route('admin.whatsapp.groups') }}"
                   class="rounded-lg border px-3 py-2 hover:bg-slate-100">Groups</a>
                <a href="{{ route('admin.whatsapp.broadcasts') }}"
                   class="rounded-lg border px-3 py-2 hover:bg-slate-100">Broadcasts</a>
            </div>
        </div>
    </x-slot>
    <style>
        .chat-options > div {
            display: none;
        }

        .chat-options[open] > div {
            display: block;
        }

        .chat-options > summary {
            list-style: none;
        }

        .chat-options > summary::-webkit-details-marker {
            display: none;
        }
    </style>
    <div class="px-3 py-5 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (config('services.kapso.ai_dry_run'))
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <strong>AI reply dry-run is ON.</strong>
                    Incoming messages can create reply drafts, but UniBridge will not send automatic WhatsApp replies.
                    An active Kapso webhook and reachable HTTPS callback are also required for incoming messages.
                </div>
            @elseif (config('services.kapso.ai_auto_reply') && config('services.kapso.ai_live_send_enabled') && !config('services.kapso.sandbox_mode'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
                    <strong>Live AI auto-reply is enabled in app settings.</strong>
                    Replies are limited to the active WhatsApp customer-service window. Verify Kapso webhook delivery and monitor provider message status.
                </div>
            @elseif (config('services.kapso.sandbox_mode'))
                <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                    <strong>WhatsApp sandbox mode is ON.</strong>
                    Automatic replies are restricted to the configured test recipient and cannot serve all students or consultants.
                </div>
            @else
                <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    <strong>AI auto-reply is disabled.</strong>
                    Incoming messages will be saved in the inbox without automatic replies.
                </div>
            @endif

            <div class="grid min-h-[680px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:grid-cols-[320px_minmax(0,1fr)]">

                {{-- LEFT: SEARCH AND CHAT LIST --}}
                <aside class="border-b border-slate-200 lg:border-b-0 lg:border-r">
                    <div class="border-b border-slate-200 p-4">
                        <form method="POST" action="{{ route('admin.whatsapp.new-chat') }}"
                              class="mb-4" id="new-chat-form">
                            @csrf
                            <button type="button" id="toggle-new-chat"
                                    class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                                + New chat
                            </button>

                            <div id="new-chat-fields" class="mt-3 hidden space-y-2">
                                <input name="contact_name" type="text" maxlength="255"
                                       placeholder="Contact name (optional)"
                                       class="w-full rounded-lg border-slate-300 text-sm">
                                <input name="phone_number" type="text" required
                                       placeholder="Phone number with country code"
                                       class="w-full rounded-lg border-slate-300 text-sm">
                                <button type="submit"
                                        class="w-full rounded-lg bg-slate-800 px-3 py-2 text-sm font-semibold text-white">
                                    Create / Open chat
                                </button>
                            </div>
                        </form>

                        <label for="chat-search" class="sr-only">Search chats</label>
                        <div class="flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2">
                            <span aria-hidden="true">âŒ•</span>
                            <input id="chat-search" type="search"
                                   placeholder="Search chats or phone..."
                                   class="w-full border-0 bg-transparent text-sm outline-none focus:ring-0">
                        </div>
                    </div>
                  
                    <div id="chat-list" class="max-h-[540px] overflow-y-auto">
                        @forelse ($conversations as $item)
                            <div
                                data-chat-search="{{ strtolower(($item->contact_name ?? '') . ' ' . $item->phone_number) }}"
                                class="chat-row flex items-start gap-2 border-b border-slate-100 p-3 transition hover:bg-slate-50 {{ $selectedConversation && $selectedConversation->id === $item->id ? 'bg-emerald-50' : '' }}"
                            >
                                <a
                                    href="{{ route('admin.whatsapp.index', ['conversation' => $item->id]) }}"
                                    class="flex min-w-0 flex-1 gap-3 rounded-lg p-1"
                                >
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-800">
                                        {{ strtoupper(substr($item->contact_name ?: $item->phone_number, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="truncate text-sm font-bold text-slate-900">
                                                {{ $item->contact_name ?: $item->phone_number }}
                                            </p>
                                            <span class="shrink-0 text-xs text-slate-400">
                                                {{ $item->last_message_at?->format('d M') }}
                                            </span>
                                        </div>

                                        <p class="mt-1 truncate text-xs text-slate-500">
                                            {{ $item->phone_number }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->messages_count }} messages
                                        </p>
                                    </div>
                                </a>

                                <details class="chat-options relative shrink-0">
                                    <summary
                                        aria-label="Chat options"
                                        title="Chat options"
                                        class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-lg text-xl font-bold text-slate-600 hover:bg-slate-200"
                                    ><span aria-hidden="true">&#8942;</span></summary>

                                    <div class="absolute right-0 z-30 mt-1 w-56 rounded-xl border border-slate-200 bg-white p-3 shadow-xl">
                                        <p class="mb-2 truncate text-xs font-semibold text-slate-500">
                                            {{ $item->contact_name ?: $item->phone_number }}
                                        </p>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.whatsapp.rename', ['conversation' => $item->id]) }}"
                                            class="space-y-2"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <label class="block text-xs font-medium text-slate-700">
                                                Rename chat
                                            </label>

                                            <input
                                                type="text"
                                                name="contact_name"
                                                value="{{ $item->contact_name ?: $item->phone_number }}"
                                                maxlength="255"
                                                required
                                                class="w-full rounded-lg border-slate-300 text-sm"
                                            >

                                            <button
                                                type="submit"
                                                class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                                            >
                                                Save name
                                            </button>
                                        </form>

                                        <div class="my-3 border-t border-slate-200"></div>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.whatsapp.delete', ['conversation' => $item->id]) }}"
                                            onsubmit="return confirm('Delete this chat and all its messages? This cannot be undone.');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="w-full rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50"
                                            >
                                                Delete chat
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        @empty
                            <p class="p-6 text-center text-sm text-slate-500">
                                No chats yet. Select New chat to start.
                            </p>
                        @endforelse
                    </div>


                    <div class="border-t border-slate-100 p-3">
                        {{ $conversations->links() }}
                    </div>
                </aside>

                {{-- RIGHT: SELECTED CONVERSATION --}}
                <section class="flex min-w-0 flex-col">
                    @if ($selectedConversation)
                        <div class="flex items-center gap-3 border-b border-slate-200 p-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-800">
                                {{ strtoupper(substr($selectedConversation->contact_name ?: $selectedConversation->phone_number, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h3 class="truncate font-bold text-slate-900">
                                    {{ $selectedConversation->contact_name ?: $selectedConversation->phone_number }}
                                </h3>
                                <p class="text-xs text-slate-500">{{ $selectedConversation->phone_number }}</p>
                            </div>
                        </div>

                        <div id="message-history"
                             class="flex-1 space-y-3 overflow-y-auto bg-slate-50 p-4 sm:p-6"
                             style="min-height:360px; max-height:520px;">

                            <p class="mb-3 text-center text-xs text-slate-400">
                                API acceptance does not guarantee message delivery.
                            </p>

                            @forelse ($messages as $message)
                                <div class="flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[88%] rounded-2xl px-4 py-3 shadow-sm {{ $message->direction === 'outbound' ? 'rounded-br-sm bg-emerald-100 text-slate-900' : 'rounded-bl-sm border border-slate-200 bg-white text-slate-800' }}">
                                        @if ($message->message_type && $message->message_type !== 'text')
                                            <p class="mb-1 text-xs font-semibold text-slate-500">
                                                {{ ucfirst($message->message_type) }}
                                            </p>
                                        @endif

                                        <p class="whitespace-pre-wrap break-words text-sm">{{ $message->message_text ?: '[' . ucfirst($message->message_type ?? 'message') . ' message; no text available]' }}</p>

                                        <div class="mt-2 flex flex-wrap justify-end gap-2 text-[11px] text-slate-500">
                                            <span>{{ ($message->sent_at ?: $message->created_at)?->format('d M, h:i A') }}</span>
                                            @if ($message->status)
                                                <span>Â· {{ ucfirst($message->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="py-12 text-center text-sm text-slate-400">
                                    No messages yet. Send your first message below.
                                </p>
                            @endforelse
                        </div>

                        @if (is_object($messages) && method_exists($messages, 'hasPages') && $messages->hasPages())
                            <div class="border-t bg-white p-3">
                                {{ $messages->links() }}
                            </div>
                        @endif

                        {{-- ONE COMPOSER BAR --}}
                        <div class="border-t border-slate-200 bg-white p-3 sm:p-4">
                            <div id="attachment-preview" class="mb-3 hidden rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p id="attachment-filename" class="break-all text-sm text-slate-700"></p>
                                    <button type="button" id="cancel-attachment"
                                            class="rounded px-2 py-1 text-sm text-red-600">
                                        Remove
                                    </button>
                                </div>
                                <form method="POST" enctype="multipart/form-data"
                                      action="{{ route('admin.whatsapp.media', ['conversation' => $selectedConversation->id]) }}"
                                      id="attachment-form" class="mt-3 space-y-2">
                                    @csrf
                                    <input type="file" name="attachment" id="attachment-input" required
                                           accept=".jpg,.jpeg,.png,.webp,.mp4,.3gp,.mp3,.m4a,.ogg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                                           class="hidden">
                                    <input type="text" name="caption" maxlength="1024"
                                           placeholder="Add a caption (optional)"
                                           class="w-full rounded-lg border-slate-300 text-sm">
                                    <button type="submit"
                                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">
                                        Send attachment
                                    </button>
                                </form>
                            </div>

                            <form method="POST"
                                  action="{{ route('admin.whatsapp.reply', ['conversation' => $selectedConversation->id]) }}"
                                  id="reply-form">
                                @csrf
                                <div class="flex items-end gap-2">
                                    <button type="button" id="open-attachment"
                                            title="Attach a file"
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-2xl text-slate-600 hover:bg-slate-100">
                                        +
                                    </button>

                                    <button type="button" id="open-location"
                                            title="Share current location"
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-xl text-slate-600 hover:bg-slate-100">
                                        ðŸ“
                                    </button>

                                    <label for="message" class="sr-only">Type a message</label>
                                    <textarea id="message" name="message" rows="1" maxlength="4096"
                                              placeholder="Type a message..."
                                              class="max-h-32 min-h-11 flex-1 resize-y rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>

                                    <button type="submit"
                                            class="flex h-11 shrink-0 items-center justify-center rounded-full bg-emerald-600 px-4 font-bold text-white hover:bg-emerald-700">
                                        Send
                                    </button>
                                </div>
                            </form>

                            {{-- LOCATION OPENS INSIDE THE COMPOSER, NOT A SEPARATE PAGE --}}
                            <div id="location-panel" class="mt-3 hidden rounded-xl border border-slate-200 bg-slate-50 p-3 sm:p-4">
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    <h4 class="font-bold text-slate-800">Share current location</h4>
                                    <button type="button" id="close-location"
                                            class="rounded-lg px-3 py-1 text-sm text-slate-500 hover:bg-slate-200">
                                        Close âœ•
                                    </button>
                                </div>

                                <button type="button" id="get-current-location"
                                        class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">
                                    Get current location
                                </button>

                                <p id="location-message" role="status" aria-live="polite"
                                   class="my-3 text-sm text-slate-600">
                                    Your location will not be sent until you confirm.
                                </p>

                                <div id="location-map-container" class="mb-3 hidden overflow-hidden rounded-lg border">
                                    <iframe id="location-map" title="Location preview"
                                            class="h-48 w-full border-0" loading="lazy"></iframe>
                                </div>

                                <a id="open-location-map" href="#" target="_blank"
                                   rel="noopener noreferrer"
                                   class="mb-3 hidden text-sm font-semibold text-emerald-700 underline">
                                    Open map
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.whatsapp.location', ['conversation' => $selectedConversation->id]) }}"
                                      id="share-location-form" class="space-y-3">
                                    @csrf

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label for="latitude" class="mb-1 block text-xs font-semibold text-slate-600">Latitude</label>
                                            <input id="latitude" name="latitude" type="number" step="any"
                                                   min="-90" max="90" required
                                                   class="w-full rounded-lg border-slate-300 text-sm">
                                        </div>
                                        <div>
                                            <label for="longitude" class="mb-1 block text-xs font-semibold text-slate-600">Longitude</label>
                                            <input id="longitude" name="longitude" type="number" step="any"
                                                   min="-180" max="180" required
                                                   class="w-full rounded-lg border-slate-300 text-sm">
                                        </div>
                                    </div>

                                    <input name="location_name" type="text" maxlength="255"
                                           placeholder="Location name (optional)"
                                           class="w-full rounded-lg border-slate-300 text-sm">

                                    <input name="location_address" type="text" maxlength="1000"
                                           placeholder="Address (optional)"
                                           class="w-full rounded-lg border-slate-300 text-sm">

                                    <label class="flex items-start gap-2 text-xs text-slate-600">
                                        <input id="confirm-location" type="checkbox"
                                               name="location_confirmed" value="1" required class="mt-0.5 rounded">
                                        <span>I confirm I am authorised to share these coordinates with this contact.</span>
                                    </label>

                                    <button type="submit"
                                            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                                        Confirm &amp; Share Location
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-1 flex-col items-center justify-center bg-slate-50 p-8 text-center">
                            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-4xl">ðŸ’¬</div>
                            <h3 class="text-xl font-bold text-slate-800">Your WhatsApp inbox</h3>
                            <p class="mt-2 max-w-sm text-sm text-slate-500">
                                Search and select a chat on the left, or create a new chat to start a conversation.
                            </p>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const search = document.getElementById('chat-search');
        const chatRows = document.querySelectorAll('.chat-row');
        const replyForm = document.getElementById('reply-form');

if (replyForm) {
    let submitting = false;

    replyForm.addEventListener('submit', function (event) {
        const message = document.getElementById('message');
        const sendButton = replyForm.querySelector('button[type="submit"]');

        if (!message || !message.value.trim()) {
            event.preventDefault();
            message?.focus();
            return;
        }

        if (submitting) {
            event.preventDefault();
            return;
        }

        submitting = true;

        if (sendButton) {
            sendButton.disabled = true;
            sendButton.textContent = 'Sending...';
        }
    });
}

        if (search) {
            search.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                chatRows.forEach(function (row) {
                    row.style.display = row.dataset.chatSearch.includes(query) ? '' : 'none';
                });
            });
        }

        const toggleNewChat = document.getElementById('toggle-new-chat');
        const newChatFields = document.getElementById('new-chat-fields');

        if (toggleNewChat && newChatFields) {
            toggleNewChat.addEventListener('click', function () {
                newChatFields.classList.toggle('hidden');
            });
        }

        const openAttachment = document.getElementById('open-attachment');
        const attachmentInput = document.getElementById('attachment-input');
        const attachmentPreview = document.getElementById('attachment-preview');
        const attachmentFilename = document.getElementById('attachment-filename');
        const cancelAttachment = document.getElementById('cancel-attachment');

        if (openAttachment && attachmentInput) {
            openAttachment.addEventListener('click', function () {
                attachmentInput.click();
            });

            attachmentInput.addEventListener('change', function () {
                if (this.files && this.files.length) {
                    attachmentFilename.textContent = this.files[0].name;
                    attachmentPreview.classList.remove('hidden');
                }
            });

            cancelAttachment.addEventListener('click', function () {
                attachmentInput.value = '';
                attachmentPreview.classList.add('hidden');
            });
        }

        const locationPanel = document.getElementById('location-panel');
        const openLocation = document.getElementById('open-location');
        const closeLocation = document.getElementById('close-location');
        const getLocation = document.getElementById('get-current-location');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const locationMessage = document.getElementById('location-message');
        const mapContainer = document.getElementById('location-map-container');
        const mapFrame = document.getElementById('location-map');
        const mapLink = document.getElementById('open-location-map');
        const locationForm = document.getElementById('share-location-form');

        if (openLocation && locationPanel) {
            openLocation.addEventListener('click', function () {
                locationPanel.classList.toggle('hidden');
            });
        }

        if (closeLocation && locationPanel) {
            closeLocation.addEventListener('click', function () {
                locationPanel.classList.add('hidden');
            });
        }

        function previewMap(lat, lng) {
            mapFrame.src = 'https://www.openstreetmap.org/export/embed.html?marker='
                + encodeURIComponent(lat + ',' + lng) + '&layer=mapnik';

            mapLink.href = 'https://www.openstreetmap.org/?mlat='
                + encodeURIComponent(lat) + '&mlon=' + encodeURIComponent(lng)
                + '#map=16/' + encodeURIComponent(lat) + '/' + encodeURIComponent(lng);

            mapContainer.classList.remove('hidden');
            mapLink.classList.remove('hidden');
        }

        if (getLocation) {
            getLocation.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    locationMessage.textContent = 'This browser does not support location. You can enter coordinates manually.';
                    return;
                }

                getLocation.disabled = true;
                locationMessage.textContent = 'Getting location. Please wait...';

                navigator.geolocation.getCurrentPosition(function (position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    previewMap(position.coords.latitude, position.coords.longitude);
                    locationMessage.textContent = 'Location found. Review it and confirm before sharing.';
                    getLocation.disabled = false;
                }, function (error) {
                    const errors = {
                        1: 'Location permission denied. Allow location access in browser settings.',
                        2: 'Location is unavailable. Check Windows location settings or enter coordinates manually.',
                        3: 'Location timed out. Check Windows location settings, then try again.'
                    };

                    locationMessage.textContent = errors[error.code] || 'Could not get your location.';
                    getLocation.disabled = false;
                }, {
                    enableHighAccuracy: false,
                    timeout: 60000,
                    maximumAge: 60000
                });
            });
        }

        if (locationForm) {
            locationForm.addEventListener('submit', function (event) {
                const lat = Number(latInput.value);
                const lng = Number(lngInput.value);

                if (latInput.value.trim() === '' || lngInput.value.trim() === ''
                    || !Number.isFinite(lat) || !Number.isFinite(lng)
                    || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                    event.preventDefault();
                    locationMessage.textContent = 'Please enter valid latitude and longitude.';
                    return;
                }

                if (!document.getElementById('confirm-location').checked) {
                    event.preventDefault();
                    locationMessage.textContent = 'Please confirm before sharing this location.';
                }
            });
        }
    })();
    </script>
</x-app-layout>
