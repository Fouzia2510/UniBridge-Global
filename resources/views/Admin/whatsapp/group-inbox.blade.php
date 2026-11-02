<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    {{ $group->name }} - Group Inbox
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Internal group message timeline
                </p>
            </div>

            <a href="{{ route('admin.whatsapp.groups') }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Back to Groups
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-bold">Please correct the following:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <strong>Important:</strong>
                Internal group messages are saved only inside UniBridge.
                They are not sent to WhatsApp.
            </div>

            {{-- Internal UniBridge group timeline --}}
            <section class="overflow-hidden rounded-2xl border border-indigo-200 bg-white shadow-sm">
                <div class="border-b border-indigo-100 bg-indigo-50 p-5">
                    <h3 class="text-lg font-bold text-indigo-950">
                        Internal Group Messages
                    </h3>
                    <p class="mt-1 text-sm text-indigo-800">
                        A shared message timeline for this saved UniBridge group.
                        Messages are local-only and are not delivered to WhatsApp.
                    </p>
                </div>

                <div class="space-y-4 p-4 sm:p-6">
                    @forelse ($groupMessages as $groupMessage)
                        <article class="rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div>
                                    <p class="font-semibold text-slate-900">
                                        {{ $groupMessage->sender_name ?: ($groupMessage->sender?->name ?? 'Admin') }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $groupMessage->sender_type === 'admin' ? 'UniBridge Admin' : ucfirst($groupMessage->sender_type ?: 'Member') }}
                                    </p>
                                </div>

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    Internal · Not sent to WhatsApp
                                </span>
                            </div>

                            <p class="mt-3 whitespace-pre-wrap break-words text-sm leading-6 text-slate-800">{{ $groupMessage->message_text }}</p>

                            <p class="mt-3 text-xs text-slate-500">
                                {{ $groupMessage->sent_at?->format('d M Y, h:i A') ?? $groupMessage->created_at?->format('d M Y, h:i A') ?? 'Time unavailable' }}
                            </p>
                        </article>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center">
                            <p class="font-semibold text-slate-700">No internal messages yet</p>
                            <p class="mt-1 text-sm text-slate-500">
                                Write the first message below. It will be saved in UniBridge only.
                            </p>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-200 bg-slate-50 p-4 sm:p-6">
                    <form method="POST"
                          action="{{ route('admin.whatsapp.group-messages.store', ['group' => $group->id]) }}"
                          class="space-y-3">
                        @csrf

                        <label for="group-message" class="block text-sm font-semibold text-slate-800">
                            Write an internal group message
                        </label>

                        <textarea
                            id="group-message"
                            name="message"
                            rows="4"
                            maxlength="4096"
                            required
                            placeholder="Write a note or update for this UniBridge group..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                        >{{ old('message') }}</textarea>

                        @error('message')
                            <p class="text-sm text-red-700">{{ $message }}</p>
                        @enderror

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs text-slate-500">
                                Maximum 4096 characters. Saved internally only.
                            </p>

                            <button
                                type="submit"
                                class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                            >
                                Save Internal Message
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            </x-app-layout>