<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Notifications
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Your UniBridge updates and activity.
                </p>
            </div>

            <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">
                {{ $unreadCount }} unread
            </span>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-600">
                Your latest account notifications appear here.
            </p>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data ?? [];
                    $isUnread = is_null($notification->read_at);
                @endphp

                <article class="rounded-2xl border p-5 shadow-sm
                    {{ $isUnread
                        ? 'border-indigo-200 bg-indigo-50/60'
                        : 'border-slate-200 bg-white' }}">
                    <div class="flex items-start gap-3">
                        <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                            {{ $isUnread ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                            <span aria-hidden="true">🔔</span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-slate-900">
                                    {{ data_get($data, 'title', 'UniBridge Notification') }}
                                </h3>

                                @if($isUnread)
                                    <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                        New
                                    </span>
                                @endif
                            </div>

                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                                {{ data_get($data, 'message', 'You have a new notification.') }}
                            </p>

                            <p class="mt-3 text-xs text-slate-500">
                                {{ $notification->created_at?->format('d M Y, h:i A') }}
                            </p>

                            @if($isUnread)
                                <form
                                    method="POST"
                                    action="{{ route('notifications.read', ['id' => $notification->id]) }}"
                                    class="mt-4"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="rounded-lg border border-indigo-200 bg-white px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50"
                                    >
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                    <div class="text-3xl" aria-hidden="true">🔔</div>
                    <h3 class="mt-3 font-bold text-slate-900">
                        You're all caught up
                    </h3>
                    <p class="mt-2 text-sm text-slate-500">
                        New UniBridge notifications will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
