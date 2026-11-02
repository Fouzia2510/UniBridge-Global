<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-lg font-bold text-emerald-800">
                    {{ strtoupper(substr($conversation->contact_name ?: $conversation->phone_number, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="truncate text-lg font-bold text-slate-900">
                        {{ $conversation->contact_name ?: $conversation->phone_number }}
                    </h2>
                    <p class="text-sm text-slate-500">{{ $conversation->phone_number }}</p>
                </div>
            </div>

            <div class="relative flex shrink-0 items-center gap-2">
                <a href="{{ route('admin.whatsapp.chats') }}"
                   class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    Chats
                </a>

                <button id="contact-menu-button" type="button"
                        aria-label="Contact options" aria-expanded="false"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-2xl text-slate-700 hover:bg-slate-100">
                    &#8942;
                </button>

                <div id="contact-menu"
                     class="absolute right-0 top-12 z-50 hidden w-64 rounded-xl border border-slate-200 bg-white p-2 shadow-xl">

                    <button type="button" data-contact-action="info"
                            class="w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-100">
                        Contact info
                    </button>

                    <button type="button" data-contact-action="search"
                            class="w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-100">
                        Search messages
                    </button>

                    <button type="button" data-contact-action="rename"
                            class="w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-100">
                        Rename contact
                    </button>

                    <div class="my-2 border-t border-slate-100"></div>

                    <form method="POST"
                          action="{{ route('admin.whatsapp.delete', ['conversation' => $conversation->id]) }}"
                          onsubmit="return confirm('Delete this chat and its saved messages? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-red-600 hover:bg-red-50">
                            Delete chat
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div id="contact-info-panel" class="mt-3 hidden rounded-xl border border-slate-200 bg-white p-4">
            <p class="mb-2 font-bold text-slate-800">Contact information</p>
            <p class="text-sm text-slate-600">
                Name: {{ $conversation->contact_name ?: 'No saved name' }}
            </p>
            <p class="mt-1 text-sm text-slate-600">
                Phone: {{ $conversation->phone_number }}
            </p>
            <button type="button" data-contact-close
                    class="mt-3 rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold hover:bg-slate-200">
                Close
            </button>
        </div>

        <div id="contact-search-panel" class="mt-3 hidden rounded-xl border border-slate-200 bg-white p-3">
            <label for="contact-message-search" class="mb-2 block text-sm font-semibold text-slate-700">
                Search loaded messages
            </label>
            <input id="contact-message-search" type="search"
                   placeholder="Type to search this page..."
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <p id="contact-search-count" class="mt-2 text-xs text-slate-500">
                Search covers messages on the current page.
            </p>
            <button type="button" data-contact-close
                    class="mt-2 rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold hover:bg-slate-200">
                Close search
            </button>
        </div>

        <div id="contact-rename-panel" class="mt-3 hidden rounded-xl border border-slate-200 bg-white p-4">
            <form method="POST"
                  action="{{ route('admin.whatsapp.rename', ['conversation' => $conversation->id]) }}"
                  class="flex flex-wrap items-end gap-3">
                @csrf
                @method('PATCH')
                <div class="min-w-0 flex-1">
                    <label for="new-contact-name" class="mb-1 block text-sm font-semibold text-slate-700">
                        Contact name
                    </label>
                    <input id="new-contact-name" name="contact_name" required maxlength="255"
                           value="{{ $conversation->contact_name }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>
                <button type="submit"
                        class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                    Save name
                </button>
                <button type="button" data-contact-close
                        class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold hover:bg-slate-200">
                    Cancel
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-bold">Please check the following:</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Conversation history --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 p-5">
                    <h3 class="font-bold text-slate-900">Conversation History</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        API acceptance does not guarantee message delivery.
                    </p>
                </div>

                <div class="max-h-[520px] space-y-4 overflow-y-auto bg-slate-50/70 p-5">
                    @forelse ($messages as $message)
                        <div class="chat-message flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] rounded-2xl border px-4 py-3 shadow-sm
                                {{ $message->direction === 'outbound' ? 'border-indigo-200 bg-indigo-50' : 'border-slate-200 bg-white' }}">

                                <div class="mb-2 flex flex-wrap items-center gap-2 text-xs">
                                    <span class="font-bold {{ $message->direction === 'outbound' ? 'text-indigo-700' : 'text-emerald-700' }}">
                                        {{ $message->direction === 'outbound' ? 'Sent' : 'Received' }}
                                    </span>
                                    <span class="text-slate-500">
                                        {{ ucfirst($message->message_type ?? 'text') }}
                                    </span>
                                    @if ($message->status)
                                        <span class="text-slate-500">
                                             -  {{ ucfirst($message->status) }}
                                        </span>
                                    @endif
                                </div>

                                <p class="whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">{{ $message->message_text ?: '[' . ucfirst($message->message_type ?? 'unknown') . ' message; no text available]' }}</p>

                                <p class="mt-2 text-right text-xs text-slate-400">
                                    {{ ($message->sent_at ?: $message->created_at)?->format('d M Y, h:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                            No messages in this conversation yet.
                        </p>
                    @endforelse
                </div>

                @if ($messages->hasPages())
                    <div class="border-t border-slate-200 p-4">
                        {{ $messages->links() }}
                    </div>
                @endif
            </section>

            {{-- Unified WhatsApp-style composer --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3">
                    <h3 class="font-bold text-slate-900">Message</h3>
                    <p class="text-xs text-slate-500">
                        Type a message or use + for more options.
                    </p>
                </div>

                {{-- Options menu --}}
                <div id="composer-menu"
                     class="mb-3 hidden rounded-xl border border-slate-200 bg-slate-50 p-2">
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        <button type="button" data-composer-option="attachment"
                                class="rounded-lg border border-slate-200 bg-white p-3 text-left text-sm font-semibold text-slate-700 hover:bg-indigo-50">
                            Attachment
                            <span class="mt-1 block text-xs font-normal text-slate-500">Photo, video, audio, document</span>
                        </button>

                        <button type="button" data-composer-option="template"
                                class="rounded-lg border border-slate-200 bg-white p-3 text-left text-sm font-semibold text-slate-700 hover:bg-indigo-50">
                            Template
                            <span class="mt-1 block text-xs font-normal text-slate-500">Application update</span>
                        </button>

                        <button type="button" data-composer-option="location"
                                class="rounded-lg border border-slate-200 bg-white p-3 text-left text-sm font-semibold text-slate-700 hover:bg-indigo-50">
                             Location
                            <span class="mt-1 block text-xs font-normal text-slate-500">Share a map location</span>
                        </button>
                    </div>
                </div>

                {{-- Attachment panel --}}
                <div id="panel-attachment" class="composer-panel mb-3 hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h4 class="font-bold text-slate-800">Send attachment</h4>
                        <button type="button" data-close-panel
                                class="rounded-lg px-3 py-1 text-sm text-slate-500 hover:bg-slate-200">
                            Close
                        </button>
                    </div>

                    <form method="POST" enctype="multipart/form-data"
                          action="{{ route('admin.whatsapp.media', ['conversation' => $conversation->id]) }}"
                          class="space-y-3">
                        @csrf

                        <label for="attachment" class="block text-sm font-medium text-slate-700">
                            Choose a file
                        </label>
                        <input id="attachment" type="file" name="attachment"
                               accept=".jpg,.jpeg,.png,.webp,.mp4,.3gp,.mp3,.m4a,.ogg,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                               required
                               class="block w-full rounded-xl border border-slate-300 bg-white p-3 text-sm">

                        <label for="caption" class="block text-sm font-medium text-slate-700">
                            Caption (optional)
                        </label>
                        <textarea id="caption" name="caption" rows="2" maxlength="1024"
                                  class="w-full rounded-xl border border-slate-300 bg-white p-3 text-sm">{{ old('caption') }}</textarea>

                        <button type="submit"
                                class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-indigo-700">
                            Send Attachment
                        </button>
                    </form>
                </div>

                {{-- Template panel --}}
                <div id="panel-template" class="composer-panel mb-3 hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <h4 class="font-bold text-slate-800">Application update</h4>
                        <button type="button" data-close-panel
                                class="rounded-lg px-3 py-1 text-sm text-slate-500 hover:bg-slate-200">
                            Close
                        </button>
                    </div>

                    <p class="mb-3 text-xs text-amber-800">
                        This option works only after the template is approved in Kapso and template sending is enabled in your configuration.
                    </p>

                    <form method="POST"
                          action="{{ route('admin.whatsapp.template', ['conversation' => $conversation->id]) }}"
                          class="space-y-3">
                        @csrf

                        <input type="hidden" name="template_name"
                               value="unibridge_application_update">

                        <div>
                            <label for="student_name" class="mb-1 block text-sm font-medium text-slate-700">
                                Student name
                            </label>
                            <input id="student_name" name="student_name" type="text"
                                   maxlength="100" required
                                   value="{{ old('student_name') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm">
                        </div>

                        <div>
                            <label for="application_status" class="mb-1 block text-sm font-medium text-slate-700">
                                Application status
                            </label>
                            <input id="application_status" name="application_status"
                                   type="text" maxlength="100" required
                                   placeholder="e.g. Under Review"
                                   value="{{ old('application_status') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm">
                        </div>

                        <button type="submit"
                                class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                            Send Template
                        </button>
                    </form>
                </div>

              
{{-- Location panel --}}
<div id="panel-location" class="composer-panel mb-3 hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <h4 class="font-bold text-slate-900">Share current location</h4>
        <button type="button" data-close-panel
            class="rounded-full px-3 py-1 text-sm text-slate-500 hover:bg-slate-100">
            
        </button>
    </div>

    <div class="flex flex-col items-center py-3 text-center">
        <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl">
            
        </div>

        <p class="mb-4 text-sm text-slate-600">
            Apni current location hasil karein aur map check karke WhatsApp par share karein.
        </p>

        <button id="get-current-location" type="button"
            class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-bold text-white hover:bg-emerald-700">
             Get Current Location
        </button>

        <p id="location-message" role="status" aria-live="polite"
            class="mt-3 text-sm text-slate-600">
            Current location hasil karne ke liye button dabayein.
        </p>
    </div>

    <div id="location-map-container"
        class="mb-4 hidden overflow-hidden rounded-xl border border-slate-200">
        <iframe id="location-map" title="Current location map preview"
            class="h-56 w-full border-0" loading="lazy"></iframe>
    </div>

    <a id="open-location-map" href="#" target="_blank" rel="noopener noreferrer"
        class="mb-4 hidden text-center text-sm font-semibold text-blue-600 hover:underline">
        Open map
    </a>

    <form id="share-location-form" method="POST"
        action="{{ route('admin.whatsapp.location', ['conversation' => $conversation->id]) }}"
        class="space-y-3">
        @csrf

        {{-- Values are filled automatically from browser location --}}
        <input id="latitude" type="hidden" name="latitude" value="{{ old('latitude') }}">
        <input id="longitude" type="hidden" name="longitude" value="{{ old('longitude') }}">
        <input type="hidden" name="location_name" value="Current Location">
        <input type="hidden" name="location_address" value="">
        <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
    <input id="confirm-location" type="checkbox"
        name="location_confirmed" value="1" required
        class="mt-1 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
    <span>
        I have checked the map and confirm that I want to share this location with the recipient.
    </span>
</label>
        <button id="location-submit" type="submit" disabled
            class="w-full rounded-full bg-emerald-600 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50">
            Confirm &amp; Share Location
        </button>
    </form>
</div>

                {{-- Main message bar --}}
                <form id="reply-form" method="POST"
                      action="{{ route('admin.whatsapp.reply', ['conversation' => $conversation->id]) }}">
                    @csrf

                    <div class="flex items-end gap-2 rounded-2xl border border-slate-300 bg-white p-2 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                        <button id="composer-plus" type="button"
                                aria-label="Open message options"
                                aria-expanded="false"
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-100 text-2xl font-medium text-slate-700 hover:bg-slate-200">
                            +
                        </button>

                        <textarea id="message" name="message" rows="1" maxlength="4096"
                                  required placeholder="Type a message..."
                                  class="max-h-36 min-h-11 flex-1 resize-y border-0 bg-transparent px-2 py-3 text-sm outline-none focus:ring-0">{{ old('message') }}</textarea>

                        <button type="submit"
                                class="flex h-11 shrink-0 items-center justify-center rounded-full bg-indigo-600 px-5 text-sm font-bold text-white hover:bg-indigo-700">
                            Send
                        </button>
                    </div>
                </form>

                <p class="mt-2 text-xs text-slate-500">
                    Use + to attach a file, send an approved template, or share a location.
                </p>
            </section>

        </div>
    </div>

    <script>
    (function () {
        const menuButton = document.getElementById('contact-menu-button');
        const contactMenu = document.getElementById('contact-menu');
        const infoPanel = document.getElementById('contact-info-panel');
        const searchPanel = document.getElementById('contact-search-panel');
        const renamePanel = document.getElementById('contact-rename-panel');
        const searchInput = document.getElementById('contact-message-search');
        const searchCount = document.getElementById('contact-search-count');

        function closeContactPanels() {
            [infoPanel, searchPanel, renamePanel].forEach(function (panel) {
                panel.classList.add('hidden');
            });
        }

        function closeContactMenu() {
            contactMenu.classList.add('hidden');
            menuButton.setAttribute('aria-expanded', 'false');
        }

        menuButton.addEventListener('click', function (event) {
            event.stopPropagation();
            const opening = contactMenu.classList.contains('hidden');
            contactMenu.classList.toggle('hidden');
            menuButton.setAttribute('aria-expanded', String(opening));
        });

        contactMenu.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function (event) {
            if (!contactMenu.contains(event.target) && !menuButton.contains(event.target)) {
                closeContactMenu();
            }
        });

        document.querySelectorAll('[data-contact-action]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeContactPanels();
                const action = button.getAttribute('data-contact-action');
                const panel = {
                    info: infoPanel,
                    search: searchPanel,
                    rename: renamePanel
                }[action];

                if (panel) {
                    panel.classList.remove('hidden');
                }

                closeContactMenu();

                if (action === 'search' && searchInput) {
                    searchInput.focus();
                }
                if (action === 'rename') {
                    const nameInput = document.getElementById('new-contact-name');
                    if (nameInput) nameInput.focus();
                }
            });
        });

        document.querySelectorAll('[data-contact-close]').forEach(function (button) {
            button.addEventListener('click', closeContactPanels);
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const term = searchInput.value.trim().toLocaleLowerCase();
                const rows = Array.from(document.querySelectorAll('.chat-message'));
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const matches = row.textContent.toLocaleLowerCase().includes(term);
                    row.classList.toggle('hidden', term !== '' && !matches);
                    if (term === '' || matches) visibleCount++;
                });

                searchCount.textContent = term === ''
                    ? 'Search covers messages on the current page.'
                    : visibleCount + ' matching message(s) on this page.';
            });
        }
    })();
    </script>
    <script>
    (function () {
        const plusButton = document.getElementById('composer-plus');
        const menu = document.getElementById('composer-menu');
        const panels = document.querySelectorAll('.composer-panel');

        function closePanels() {
            panels.forEach(function (panel) {
                panel.classList.add('hidden');
            });
        }

        function closeMenu() {
            menu.classList.add('hidden');
            plusButton.setAttribute('aria-expanded', 'false');
        }

        plusButton.addEventListener('click', function () {
            const isOpening = menu.classList.contains('hidden');

            if (isOpening) {
                menu.classList.remove('hidden');
                plusButton.setAttribute('aria-expanded', 'true');
            } else {
                closeMenu();
            }
        });

        document.querySelectorAll('[data-composer-option]').forEach(function (button) {
            button.addEventListener('click', function () {
                const selected = button.getAttribute('data-composer-option');
                const selectedPanel = document.getElementById('panel-' + selected);
                const wasVisible = selectedPanel && !selectedPanel.classList.contains('hidden');

                closePanels();
                closeMenu();

                if (selectedPanel && !wasVisible) {
                    selectedPanel.classList.remove('hidden');
                }
            });
        });

        document.querySelectorAll('[data-close-panel]').forEach(function (button) {
            button.addEventListener('click', function () {
                const panel = button.closest('.composer-panel');
                if (panel) panel.classList.add('hidden');
            });
        });

        // Keep the location map preview and sharing safeguards.
        const locationButton = document.getElementById('get-current-location');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const locationMessage = document.getElementById('location-message');
        const mapContainer = document.getElementById('location-map-container');
        const map = document.getElementById('location-map');
        const mapLink = document.getElementById('open-location-map');
        const locationForm = document.getElementById('share-location-form');
        const locationSubmit = document.getElementById('location-submit');

        if (!locationButton || !latInput || !lngInput || !locationForm) return;

        function showMap(lat, lng) {
            map.src = 'https://www.openstreetmap.org/export/embed.html?marker='
                + encodeURIComponent(lat + ',' + lng) + '&layer=mapnik';

            mapLink.href = 'https://www.openstreetmap.org/?mlat='
                + encodeURIComponent(lat) + '&mlon=' + encodeURIComponent(lng)
                + '#map=16/' + encodeURIComponent(lat) + '/' + encodeURIComponent(lng);

            mapContainer.classList.remove('hidden');
            mapLink.classList.remove('hidden');
        }

        function previewCoordinates() {
            if (latInput.value.trim() === '' || lngInput.value.trim() === '') {
                mapContainer.classList.add('hidden');
                mapLink.classList.add('hidden');
                return;
            }

            const lat = Number(latInput.value);
            const lng = Number(lngInput.value);

            if (!Number.isFinite(lat) || !Number.isFinite(lng)
                || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                mapContainer.classList.add('hidden');
                mapLink.classList.add('hidden');
                return;
            }

            showMap(lat, lng);
        }

        locationButton.addEventListener('click', function () {
            if (!navigator.geolocation) {
                locationMessage.textContent =
                    'Your browser does not support location sharing.';
                return;
            }

            locationButton.disabled = true;
            locationMessage.textContent = 'Waiting for browser location permission...';

            navigator.geolocation.getCurrentPosition(function (position) {
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;

                showMap(position.coords.latitude, position.coords.longitude);
                locationSubmit.disabled = false;
                locationMessage.textContent =
                    'Location found. Review the map before confirming.';
                locationButton.disabled = false;
            }, function (error) {
               const messages = {
    1: 'Location permission denied. Please allow location access in your browser.',
    2: 'Location is unavailable. Please try again.',
    3: 'Location request timed out. Please try again.'
};

                locationMessage.textContent =
                    messages[error.code] || 'Could not get location.';
                locationButton.disabled = false;
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });
        });

        latInput.addEventListener('change', previewCoordinates);
        lngInput.addEventListener('change', previewCoordinates);

        locationForm.addEventListener('submit', function (event) {
            const lat = Number(latInput.value);
            const lng = Number(lngInput.value);

            if (latInput.value.trim() === '' || lngInput.value.trim() === ''
                || !Number.isFinite(lat) || !Number.isFinite(lng)
                || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                event.preventDefault();
                locationMessage.textContent = 'Please enter valid coordinates.';
                return;
            }

            if (!document.getElementById('confirm-location').checked) {
                event.preventDefault();
                locationMessage.textContent =
                    'Please confirm that you are authorised to share this location.';
            }
        });

        previewCoordinates();
    })();
    </script>

    <script id="reply-submit-guard">
    (function () {
        const form = document.getElementById('reply-form');
        if (!form) return;

        const button = form.querySelector('button[type="submit"]');
        const message = document.getElementById('message');
        let submitting = false;

        form.addEventListener('submit', function (event) {
            if (submitting) {
                event.preventDefault();
                return;
            }

            const text = message ? message.value.trim() : '';

            if (!text) {
                event.preventDefault();
                if (message) message.focus();
                return;
            }

            submitting = true;

            if (button) {
                button.disabled = true;
                button.textContent = 'Sending...';
                button.classList.add(
                    'opacity-60',
                    'cursor-not-allowed'
                );
            }
        });
    })();
    </script>
</x-app-layout>


