<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">WhatsApp Chats</h2>
                <p class="mt-1 text-sm text-slate-500">Manage contacts and open individual conversations.</p>
            </div>

            <a href="{{ route('admin.whatsapp.groups') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6M23 11h-6"/>
                </svg>
                Groups
            </a>
        </div>
    </x-slot>

    <div class="min-h-[70vh] bg-slate-100 py-6 sm:py-8">
        <div class="mx-auto max-w-5xl space-y-5 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative min-w-0 flex-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="10.8" cy="10.8" r="6.8"/>
                            <path stroke-linecap="round" d="m16 16 5 5"/>
                        </svg>
                        <input id="chat-search" type="search" autocomplete="off"
                               placeholder="Search name or phone number..."
                               class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-4 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <button type="button" id="toggle-new-chat"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                        </svg>
                        New Chat
                    </button>
                </div>

                <form id="new-chat-fields" method="POST" action="{{ route('admin.whatsapp.new-chat') }}"
                      class="mt-4 hidden space-y-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    @csrf
                    <h3 class="font-bold text-slate-900">Start a conversation</h3>
                    <p class="text-sm text-slate-600">Enter the contact's international phone number, including country code.</p>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="contact_name" class="mb-1 block text-sm font-semibold text-slate-700">Contact name (optional)</label>
                            <input id="contact_name" name="contact_name" type="text" maxlength="255"
                                   value="{{ old('contact_name') }}"
                                   placeholder="e.g. Ahmad"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label for="phone_number" class="mb-1 block text-sm font-semibold text-slate-700">Phone number</label>
                            <input id="phone_number" name="phone_number" type="tel" required maxlength="30"
                                   value="{{ old('phone_number') }}"
                                   placeholder="e.g. 923001234567"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                            Create / Open Chat
                        </button>
                        <button type="button" id="cancel-new-chat" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                    </div>
                </form>
            </section>

            <section class="overflow-visible rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 p-5">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">All conversations</h3>
                        <p class="mt-1 text-sm text-slate-500">Select a contact to open their individual chat.</p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">
                        {{ $conversations->total() }} contacts
                    </span>
                </div>

                @if ($conversations->count() > 0)
                    <div id="chat-list" class="divide-y divide-slate-100">
                        @foreach ($conversations as $conversation)
                            @php
                                $displayName = $conversation->contact_name ?: $conversation->phone_number;
                                $searchText = \Illuminate\Support\Str::lower($displayName . ' ' . $conversation->phone_number);
                            @endphp

                            <div class="chat-row flex items-center gap-3 p-4 transition hover:bg-emerald-50/60 sm:gap-4 sm:px-5"
                                 data-search="{{ $searchText }}">
                                <a href="{{ route('admin.whatsapp.chat', ['conversation' => $conversation->id]) }}"
                                   aria-label="Open chat with {{ $displayName }}"
                                   class="flex min-w-0 flex-1 items-center gap-3 sm:gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-base font-extrabold uppercase text-emerald-800 ring-1 ring-emerald-200">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($displayName, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                                            <p class="truncate font-bold text-slate-900">{{ $displayName }}</p>
                                            @if ($conversation->last_message_at)
                                                <span class="shrink-0 text-xs text-slate-500">
                                                    {{ $conversation->last_message_at->format('d M, h:i A') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-1 truncate text-sm text-slate-500">{{ $conversation->phone_number }}</p>
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $conversation->messages_count }} {{ \Illuminate\Support\Str::plural('message', $conversation->messages_count) }}
                                            @if ($conversation->status)
                                                <span class="mx-1">·</span>{{ ucfirst($conversation->status) }}
                                            @endif
                                        </p>
                                    </div>
                                    <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                                    </svg>
                                </a>

                                <details class="relative shrink-0">
                                    <summary aria-label="Contact actions"
                                             class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-full text-slate-500 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <circle cx="5" cy="12" r="1.8"/>
                                            <circle cx="12" cy="12" r="1.8"/>
                                            <circle cx="19" cy="12" r="1.8"/>
                                        </svg>
                                    </summary>

                                    <div class="absolute right-0 z-30 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-3 shadow-xl">
                                        <form method="POST" action="{{ route('admin.whatsapp.rename', ['conversation' => $conversation->id]) }}" class="space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="block text-xs font-bold text-slate-600">Rename contact</label>
                                            <input type="text" name="contact_name" maxlength="255" required
                                                   value="{{ $conversation->contact_name }}"
                                                   placeholder="Contact name"
                                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            <button type="submit" class="w-full rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                                                Save name
                                            </button>
                                        </form>

                                        <div class="my-3 border-t border-slate-200"></div>

                                        <form method="POST" action="{{ route('admin.whatsapp.delete', ['conversation' => $conversation->id]) }}"
                                              onsubmit="return confirm('Delete this conversation and its messages permanently? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                                                Delete chat and messages
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        @endforeach
                    </div>

                    <div id="no-search-results" class="hidden px-6 py-12 text-center text-sm text-slate-500">
                        No contacts match your search.
                    </div>
                @else
                    <div class="px-6 py-16 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-700">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.5 11.5a8.5 8.5 0 0 1-12.6 7.4L3 20l1.2-4.7A8.5 8.5 0 1 1 20.5 11.5Z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-bold text-slate-800">No conversations yet</h3>
                        <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                            Create a chat above or wait for a contact to message your WhatsApp account.
                        </p>
                    </div>
                @endif

                @if ($conversations->hasPages())
                    <div class="border-t border-slate-200 bg-white p-4 sm:px-6">
                        {{ $conversations->links() }}
                    </div>
                @endif
            </section>

            <p class="text-center text-xs text-slate-500">
                Opening a contact shows their separate conversation, message history and available messaging options.
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('chat-search');
            const rows = Array.from(document.querySelectorAll('.chat-row'));
            const noResults = document.getElementById('no-search-results');
            const toggleButton = document.getElementById('toggle-new-chat');
            const newChatForm = document.getElementById('new-chat-fields');
            const cancelButton = document.getElementById('cancel-new-chat');

            if (search) {
                search.addEventListener('input', function () {
                    const query = this.value.trim().toLowerCase();
                    let visibleCount = 0;

                    rows.forEach(function (row) {
                        const matches = (row.dataset.search || '').includes(query);
                        row.classList.toggle('hidden', !matches);
                        if (matches) visibleCount++;
                    });

                    if (noResults) {
                        noResults.classList.toggle('hidden', visibleCount !== 0 || rows.length === 0);
                    }
                });
            }

            if (toggleButton && newChatForm) {
                toggleButton.addEventListener('click', function () {
                    newChatForm.classList.toggle('hidden');
                    if (!newChatForm.classList.contains('hidden')) {
                        document.getElementById('phone_number')?.focus();
                    }
                });
            }

            if (cancelButton && newChatForm) {
                cancelButton.addEventListener('click', function () {
                    newChatForm.classList.add('hidden');
                });
            }

            document.querySelectorAll('details').forEach(function (details) {
                details.addEventListener('toggle', function () {
                    if (details.open) {
                        document.querySelectorAll('details').forEach(function (other) {
                            if (other !== details) other.open = false;
                        });
                    }
                });
            });
        });
    </script>
</x-app-layout>