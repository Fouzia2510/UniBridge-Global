<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Groups & Conversations</h2>
                <p class="mt-1 text-sm text-slate-500">Private internal conversations for UniBridge members.</p>
            </div>

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.chat-groups.create') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    + Create Group
                </a>
            @endif
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($groups->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-2xl">💬</div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">No groups yet</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                        @if (auth()->user()->role === 'admin')
                            Create an internal group and add approved UniBridge users or phone-only contacts.
                        @else
                            Groups you belong to will appear here.
                        @endif
                    </p>
                </div>
            @else
                <div class="mb-5 flex items-center justify-between">
                    <p class="text-sm text-slate-500">{{ $groups->total() }} group(s)</p>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Internal chat</span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($groups as $group)
                        <a href="{{ route('chat-groups.show', $group) }}"
                           class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md">
                            <div class="flex items-start gap-4">
                                @if ($group->avatar_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($group->avatar_path) }}"
                                         alt="{{ $group->name }}"
                                         class="h-14 w-14 rounded-2xl object-cover">
                                @else
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-100 text-xl font-bold text-indigo-700">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($group->name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-base font-bold text-slate-900 group-hover:text-indigo-700">
                                        {{ $group->name }}
                                    </h3>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $group->members_count }} member(s)
                                    </p>
                                    @if ($group->latestMessage)
                                        <p class="mt-3 truncate text-sm text-slate-600">
                                            <span class="font-semibold">
                                                {{ $group->latestMessage->sender?->name ?? $group->latestMessage->sender_contact_name ?? 'Member' }}:
                                            </span>
                                            {{ $group->latestMessage->message_text }}
                                        </p>
                                    @else
                                        <p class="mt-3 text-sm italic text-slate-400">No messages yet — start the conversation.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                                <span class="text-xs text-slate-400">
                                    @if ($group->latestMessage)
                                        {{ $group->latestMessage->created_at->format('d M Y, h:i A') }}
                                    @else
                                        Created {{ $group->created_at->format('d M Y') }}
                                    @endif
                                </span>
                                <span class="text-sm font-semibold text-indigo-600">Open chat →</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $groups->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>