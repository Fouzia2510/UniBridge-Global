<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('chat-groups.index') }}"
               class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                ← All groups
            </a>

            <div class="min-w-0 flex-1">
                <h2 class="truncate text-2xl font-bold text-slate-900">
                    {{ $chatGroup->name }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Internal UniBridge group conversation
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $currentUserId = (int) auth()->id();

        $currentUserIsMember = $chatGroup->members
            ->contains(fn ($member) => (int) $member->user_id === $currentUserId);

        $lastMessageId = (int) ($messages->getCollection()->max('id') ?? 0);
    @endphp

    <div class="min-h-screen bg-slate-50 py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">

                {{-- Internal group conversation --}}
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">

                    {{-- Group header --}}
                    <div class="flex items-center gap-3 border-b border-slate-200 p-5">
                        @if ($chatGroup->avatar_path)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($chatGroup->avatar_path) }}"
                                alt="{{ $chatGroup->name }}"
                                class="h-12 w-12 rounded-xl object-cover"
                            >
                        @else
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-lg font-bold text-indigo-700">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($chatGroup->name, 0, 1)) }}
                            </div>
                        @endif

                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-900">
                                {{ $chatGroup->name }}
                            </h3>
                            <p class="text-xs text-slate-500">
                                {{ $chatGroup->members->count() }} member(s)
                                · Internal group chat
                            </p>
                        </div>

                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                            Internal
                        </span>
                    </div>

                    @if ($chatGroup->description)
                        <div class="border-b border-slate-100 bg-slate-50 px-5 py-3 text-sm text-slate-600">
                            {{ $chatGroup->description }}
                        </div>
                    @endif

                    {{-- Messages area --}}
                    <div
                        id="messages-list"
                        data-last-message-id="{{ $lastMessageId }}"
                        class="max-h-[65vh] min-h-[350px] space-y-4 overflow-y-auto bg-slate-50 p-4 sm:p-6"
                        aria-live="polite"
                        aria-label="Group messages"
                    >
                        <div
                            id="empty-state"
                            class="{{ $messages->isEmpty() ? '' : 'hidden' }} rounded-xl border border-dashed border-slate-300 bg-white px-4 py-12 text-center"
                        >
                            <div class="text-3xl">💬</div>
                            <p class="mt-3 font-semibold text-slate-800">
                                Start the conversation
                            </p>
                            <p class="mt-1 text-sm text-slate-500">
                                Your group's messages will appear here.
                            </p>
                        </div>

                        @foreach ($messages as $message)
                            @php
                                $isOwnMessage = (int) $message->sender_user_id === $currentUserId;
                                $senderName = $message->sender?->name
                                    ?? $message->sender_contact_name
                                    ?? 'Group member';
                            @endphp

                            <article
                                id="{{ $loop->last ? 'latest-message' : 'message-'.$message->id }}"
                                data-message-id="{{ $message->id }}"
                                class="flex w-full {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}"
                            >
                                <div class="max-w-[85%] sm:max-w-[75%]">
                                    <div class="mb-1 flex flex-wrap items-center gap-2 {{ $isOwnMessage ? 'justify-end' : 'justify-start' }}">
                                        <span class="text-xs font-semibold {{ $isOwnMessage ? 'text-indigo-700' : 'text-slate-600' }}">
                                            {{ $isOwnMessage ? 'You' : $senderName }}
                                        </span>

                                        <time
                                            class="text-xs text-slate-400"
                                            datetime="{{ $message->created_at?->toIso8601String() }}"
                                        >
                                            {{ $message->created_at?->format('d M Y, h:i A') }}
                                        </time>
                                    </div>

                                    <div class="rounded-2xl px-4 py-3 shadow-sm {{ $isOwnMessage ? 'rounded-tr-sm bg-indigo-600 text-white' : 'rounded-tl-sm border border-slate-200 bg-white text-slate-800' }}">
                                        <p class="whitespace-pre-wrap break-words text-sm leading-6">{{ $message->message_text }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach

                        @if ($messages->hasPages())
                            <div class="border-t border-slate-200 pt-4">
                                {{ $messages->links() }}
                            </div>
                        @endif
                    </div>

                    {{-- Message composer --}}
                    @if ($currentUserIsMember)
                        <form
                            id="message-form"
                            method="POST"
                            action="{{ route('chat-groups.messages.store', $chatGroup) }}"
                            class="border-t border-slate-200 bg-white p-4 sm:p-5"
                        >
                            @csrf

                            <label for="message_text" class="mb-2 block text-sm font-semibold text-slate-700">
                                Write a message
                            </label>

                            <textarea
                                id="message_text"
                                name="message_text"
                                rows="3"
                                maxlength="5000"
                                required
                                class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Type your message..."
                            >{{ old('message_text') }}</textarea>

                            <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
                                <p class="text-xs text-slate-400">
                                    Internal group chat · Not a WhatsApp message
                                </p>

                                <button
                                    id="send-button"
                                    type="submit"
                                    class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    Send message
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="border-t border-slate-200 bg-white p-4 text-sm text-slate-500">
                            You are not a member of this group, so you cannot send messages.
                        </div>
                    @endif
                </section>

                {{-- Members sidebar --}}
                <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">
                        Group members
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        People listed in this internal group.
                    </p>

                    <div class="mt-4 space-y-3">
                        @forelse ($chatGroup->members as $member)
                            <div class="flex items-start gap-3 rounded-xl border border-slate-100 p-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($member->user?->name ?? $member->contact_name ?? 'M', 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="break-words text-sm font-semibold text-slate-800">
                                        {{ $member->user?->name ?? $member->contact_name ?? 'Group member' }}
                                    </p>

                                    @if ($member->user)
                                        <p class="break-all text-xs text-slate-500">
                                            {{ $member->user->email }}
                                        </p>
                                    @elseif ($member->phone_number)
                                        <p class="break-words text-xs text-slate-500">
                                            {{ $member->phone_number }}
                                        </p>
                                        <span class="mt-1 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">
                                            Phone-only contact
                                        </span>
                                    @endif

                                    @if ($member->role === 'owner')
                                        <span class="mt-1 inline-block rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                            Group owner
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">
                                No members listed.
                            </p>
                        @endforelse
                    </div>
                </aside>

            </div>
        </div>
    </div>

    {{-- Poll the internal chat endpoint for newly saved messages. --}}
    <script>
        (() => {
            const messagesList = document.getElementById('messages-list');

            if (!messagesList) {
                return;
            }

            const messagesUrl = @json(route('chat-groups.messages.new', $chatGroup));
            const currentUserId = @json($currentUserId);
            const emptyState = document.getElementById('empty-state');

            let lastMessageId = Number(messagesList.dataset.lastMessageId || 0);
            let requestInProgress = false;

            function formatMessageTime(value) {
                if (!value) {
                    return '';
                }

                const date = new Date(value);

                if (Number.isNaN(date.getTime())) {
                    return '';
                }

                return new Intl.DateTimeFormat(undefined, {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(date);
            }

            function appendMessage(message) {
                if (messagesList.querySelector(`[data-message-id="${message.id}"]`)) {
                    return;
                }

                if (emptyState) {
                    emptyState.classList.add('hidden');
                }

                const isOwnMessage = Number(message.sender_user_id) === Number(currentUserId);

                const article = document.createElement('article');
                article.dataset.messageId = String(message.id);
                article.className = `flex w-full ${isOwnMessage ? 'justify-end' : 'justify-start'}`;

                const wrapper = document.createElement('div');
                wrapper.className = 'max-w-[85%] sm:max-w-[75%]';

                const meta = document.createElement('div');
                meta.className = `mb-1 flex flex-wrap items-center gap-2 ${isOwnMessage ? 'justify-end' : 'justify-start'}`;

                const sender = document.createElement('span');
                sender.className = `text-xs font-semibold ${isOwnMessage ? 'text-indigo-700' : 'text-slate-600'}`;
                sender.textContent = isOwnMessage ? 'You' : (message.sender || 'Group member');

                const time = document.createElement('time');
                time.className = 'text-xs text-slate-400';
                time.dateTime = message.created_at || '';
                time.textContent = formatMessageTime(message.created_at);

                meta.append(sender, time);

                const bubble = document.createElement('div');
                bubble.className = isOwnMessage
                    ? 'rounded-2xl rounded-tr-sm bg-indigo-600 px-4 py-3 text-white shadow-sm'
                    : 'rounded-2xl rounded-tl-sm border border-slate-200 bg-white px-4 py-3 text-slate-800 shadow-sm';

                const text = document.createElement('p');
                text.className = 'whitespace-pre-wrap break-words text-sm leading-6';
                text.textContent = message.message_text || '';

                bubble.appendChild(text);
                wrapper.append(meta, bubble);
                article.appendChild(wrapper);
                messagesList.appendChild(article);

                lastMessageId = Math.max(lastMessageId, Number(message.id));
                messagesList.dataset.lastMessageId = String(lastMessageId);
            }

            async function fetchNewMessages() {
                if (requestInProgress || document.hidden) {
                    return;
                }

                requestInProgress = true;

                try {
                    const url = new URL(messagesUrl, window.location.origin);
                    url.searchParams.set('after_id', String(lastMessageId));

                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();

                    if (!Array.isArray(data.messages)) {
                        return;
                    }

                    data.messages.forEach(appendMessage);

                    if (data.messages.length > 0) {
                        messagesList.scrollTop = messagesList.scrollHeight;
                    }
                } catch (error) {
                    console.error('Unable to refresh internal group messages.', error);
                } finally {
                    requestInProgress = false;
                }
            }

            messagesList.scrollTop = messagesList.scrollHeight;

            window.setInterval(fetchNewMessages, 3000);
            window.addEventListener('focus', fetchNewMessages);
        })();
    </script>
</x-app-layout>