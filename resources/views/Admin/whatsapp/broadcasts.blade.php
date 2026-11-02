<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900">WhatsApp Broadcasts</h2>
            <p class="mt-1 text-sm text-slate-500">Review saved broadcast drafts, recipient counts and delivery status.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap gap-3">
                <a href="{{ route('admin.whatsapp.chats') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold text-slate-700">Individual Chats</a>
                <a href="{{ route('admin.whatsapp.groups') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold text-slate-700">Contact Groups</a>
                <a href="{{ route('admin.whatsapp.broadcasts') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Broadcasts</a>
                <a href="{{ route('admin.whatsapp.index') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold text-slate-700">Full WhatsApp Inbox</a>
            </nav>

            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <h3 class="font-bold text-amber-900">Sending and consent</h3>
                <p class="mt-1 text-sm leading-6 text-amber-800">
                    This page lists saved broadcasts and their recorded statuses only.
                    Previewing a broadcast does not send messages. API acceptance does not confirm delivery.
                    Existing consent and sandbox checks remain in place.
                </p>
            </section>
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error') || $errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    @if (session('error'))
                        <p>{{ session('error') }}</p>
                    @endif
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <section id="create-broadcast" class="overflow-hidden rounded-2xl border border-indigo-200 bg-white shadow-sm">
                <div class="border-b border-indigo-100 bg-indigo-50 px-5 py-4">
                    <h3 class="text-lg font-bold text-indigo-950">Create WhatsApp Broadcast Draft</h3>
                    <p class="mt-1 text-sm text-indigo-800">
                        Save a draft and preview eligible contacts. This form does not send WhatsApp messages.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.whatsapp.broadcast-preview') }}" class="space-y-4 p-5">
                    @csrf

                    <div>
                        <label for="broadcast_group_id" class="mb-2 block text-sm font-semibold text-slate-700">
                            Select Contact Group
                        </label>
                        <select id="broadcast_group_id" name="group_id" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                            <option value="">Choose a group</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group->id }}" {{ (string) old('group_id') === (string) $group->id ? 'selected' : '' }}>
                                    {{ $group->name }} ({{ $group->members_count }} members)
                                </option>
                            @endforeach
                        </select>
                        @if ($groups->isEmpty())
                            <p class="mt-2 text-sm text-amber-700">No contact groups exist. Create a group before preparing a draft.</p>
                        @endif
                    </div>

                    <div>
                        <label for="broadcast_name" class="mb-2 block text-sm font-semibold text-slate-700">Draft Name</label>
                        <input id="broadcast_name" name="name" type="text" maxlength="255" required
                               value="{{ old('name') }}" placeholder="e.g. Application Reminder"
                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label for="broadcast_message_text" class="mb-2 block text-sm font-semibold text-slate-700">Message Text</label>
                        <textarea id="broadcast_message_text" name="message_text" rows="5" maxlength="4096" required
                                  placeholder="Write your proposed WhatsApp message..."
                                  class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('message_text') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500">Maximum 4096 characters. Draft only.</p>
                    </div>

                    <button type="submit" @disabled($groups->isEmpty())
                            class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">
                        Save Draft &amp; Preview
                    </button>

                    <p class="text-xs leading-5 text-slate-500">
                        No WhatsApp API sending call is made. Only contacts with recorded opt-in consent are included.
                    </p>
                </form>
            </section>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Broadcast History</h3>
                    <p class="text-sm text-slate-500">Most recently created broadcasts appear first.</p>
                </div>
                <a href="#create-broadcast" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create Broadcast Draft</a>
            </div>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="divide-y divide-slate-100">
                    @forelse ($broadcasts as $broadcast)
                        @php
                            $broadcastStatus = strtolower((string) $broadcast->status);
                            $broadcastStatusClasses = match ($broadcastStatus) {
                                'completed', 'delivered', 'read' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'sending', 'processing' => 'bg-blue-100 text-blue-800',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp

                        <div id="broadcast-{{ $broadcast->id }}" class="space-y-5 p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-slate-900">{{ $broadcast->name }}</h4>
                                    <p class="mt-1 text-sm text-slate-600">
                                        Group: {{ $broadcast->group?->name ?? 'Group unavailable' }}
                                    </p>
                                    <p class="mt-2 whitespace-pre-line text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($broadcast->message_text, 300) }}</p>
                                    <p class="mt-2 text-xs text-slate-500">
                                        Created {{ $broadcast->created_at?->format('d M Y, h:i A') ?? 'Date unavailable' }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 flex-col items-start gap-3 sm:items-end">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $broadcastStatusClasses }}">
                                        {{ ucfirst(str_replace('_', ' ', $broadcastStatus ?: 'unknown')) }}
                                    </span>
                                    <a href="#broadcast-{{ $broadcast->id }}"
                                       class="rounded-lg border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                                        Review Draft
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div class="rounded-xl bg-slate-50 p-3">
                                    <p class="text-xs font-medium text-slate-500">Saved recipients</p>
                                    <p class="mt-1 text-xl font-bold text-slate-900">{{ $broadcast->recipients_count }}</p>
                                </div>
                                <div class="rounded-xl bg-blue-50 p-3">
                                    <p class="text-xs font-medium text-blue-700">API accepted</p>
                                    <p class="mt-1 text-xl font-bold text-blue-900">{{ $broadcast->accepted_count ?? 0 }}</p>
                                </div>
                                <div class="rounded-xl bg-red-50 p-3">
                                    <p class="text-xs font-medium text-red-700">Failed</p>
                                    <p class="mt-1 text-xl font-bold text-red-900">{{ $broadcast->failed_count ?? 0 }}</p>
                                </div>
                                <div class="rounded-xl bg-green-50 p-3">
                                    <p class="text-xs font-medium text-green-700">Completed</p>
                                    <p class="mt-1 text-sm font-bold text-green-900">
                                        {{ $broadcast->completed_at?->format('d M Y, h:i A') ?? 'Not recorded' }}
                                    </p>
                                </div>
                            </div>

                            <details class="rounded-xl border border-slate-200">
                                <summary class="cursor-pointer px-4 py-3 text-sm font-semibold text-slate-700">
                                    View saved recipients ({{ $broadcast->recipients->count() }})
                                </summary>

                                <div class="divide-y divide-slate-100 border-t border-slate-200">
                                    @forelse ($broadcast->recipients as $recipient)
                                        @php
                                            $recipientStatus = strtolower((string) $recipient->status);
                                            $recipientStatusClasses = match ($recipientStatus) {
                                                'delivered', 'read' => 'bg-green-100 text-green-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'accepted', 'sent' => 'bg-blue-100 text-blue-800',
                                                'pending' => 'bg-amber-100 text-amber-800',
                                                default => 'bg-slate-100 text-slate-700',
                                            };
                                        @endphp

                                        <div class="space-y-2 p-4">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900">
                                                        {{ $recipient->contact_name ?: 'Unnamed contact' }}
                                                    </p>
                                                    <p class="text-xs text-slate-500">{{ $recipient->phone_number }}</p>
                                                </div>
                                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $recipientStatusClasses }}">
                                                    {{ ucfirst(str_replace('_', ' ', $recipientStatus ?: 'unknown')) }}
                                                </span>
                                            </div>

                                            <div class="grid gap-1 text-xs text-slate-500 sm:grid-cols-2">
                                                <p>
                                                    Attempted:
                                                    {{ $recipient->attempted_at?->format('d M Y, h:i A') ?? 'Not recorded' }}
                                                </p>
                                                <p>
                                                    Delivery confirmed:
                                                    {{ $recipient->delivered_at?->format('d M Y, h:i A') ?? 'Not confirmed' }}
                                                </p>
                                            </div>

                                            @if ($recipientStatus === 'failed' && $recipient->error_message)
                                                <p class="rounded-lg bg-red-50 p-2 text-xs text-red-700">
                                                    {{ $recipient->error_message }}
                                                </p>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="p-4 text-sm text-slate-500">No recipient records found for this broadcast.</p>
                                    @endforelse
                                </div>
                            </details>
                        </div>
                    @empty
                        <div class="p-8 text-center text-sm text-slate-500">
                            No broadcasts saved yet. Create a draft from the full WhatsApp Inbox.
                        </div>
                    @endforelse
                </div>

                <div class="border-t p-4">{{ $broadcasts->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>