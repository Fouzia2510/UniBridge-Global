<x-app-layout>

```
<x-slot name="header">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h2 class="text-xl font-bold text-gray-900">
                Messages
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                View and reply to messages from students.
            </p>
        </div>

    </div>
</x-slot>

<div class="min-h-screen bg-gray-50 py-8">

    <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8">

        {{-- Success Message --}}
        @if (session('status'))

            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4">

                <p class="text-sm font-semibold text-green-800">
                    {{ session('status') }}
                </p>

            </div>

        @endif

        {{-- Main Card --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-gray-200">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-8 sm:px-8">

                <div class="flex items-center gap-4">

                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-2xl">
                        💬
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            Student Messages
                        </h1>

                        <p class="mt-1 text-sm text-white/90">
                            Respond to students and provide study abroad guidance.
                        </p>
                    </div>

                </div>

            </div>

            {{-- Messages --}}
            <div class="p-6 sm:p-8">

                @if ($messages->count() > 0)

                    <div class="space-y-6">

                        @foreach ($messages as $message)

                            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                                {{-- Student Information --}}
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">
                                            {{ strtoupper(substr($message->student->name ?? 'S', 0, 1)) }}
                                        </div>

                                        <div>

                                            <h3 class="font-bold text-gray-900">
                                                {{ $message->student->name ?? 'Student' }}
                                            </h3>

                                            <p class="text-xs text-gray-500">
                                                {{ $message->student->email ?? '' }}
                                            </p>

                                        </div>

                                    </div>

                                    <div class="text-sm text-gray-500">
                                        {{ $message->created_at->format('d M Y, h:i A') }}
                                    </div>

                                </div>

                                {{-- Subject --}}
                                @if ($message->subject)

                                    <div class="mt-5">

                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                            Subject
                                        </p>

                                        <p class="mt-1 text-base font-semibold text-gray-900">
                                            {{ $message->subject }}
                                        </p>

                                    </div>

                                @endif

                                {{-- Student Message --}}
                                <div class="mt-5 rounded-xl bg-gray-50 p-4">

                                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Student Message
                                    </p>

                                    <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                        {{ $message->message }}
                                    </p>

                                </div>

                                {{-- Existing Reply --}}
                                @if ($message->consultant_reply)

                                    <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4">

                                        <p class="text-xs font-semibold uppercase tracking-wide text-green-700">
                                            Your Reply
                                        </p>

                                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-green-900">
                                            {{ $message->consultant_reply }}
                                        </p>

                                        @if ($message->replied_at)

                                            <p class="mt-2 text-xs text-green-600">
                                                Replied:
                                                {{ $message->replied_at->format('d M Y, h:i A') }}
                                            </p>

                                        @endif

                                    </div>

                                @endif

                                {{-- Reply Form --}}
                                <div class="mt-5 border-t border-gray-200 pt-5">

                                    <form
                                        method="POST"
                                        action="{{ route('consultant.messages.reply', $message) }}"
                                    >

                                        @csrf

                                        <label
                                            for="consultant_reply_{{ $message->id }}"
                                            class="block text-sm font-semibold text-gray-700"
                                        >
                                            {{ $message->consultant_reply ? 'Update Reply' : 'Reply to Student' }}
                                        </label>

                                        <textarea
                                            id="consultant_reply_{{ $message->id }}"
                                            name="consultant_reply"
                                            rows="4"
                                            required
                                            maxlength="3000"
                                            placeholder="Write your response to the student..."
                                            class="mt-2 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >{{ old('consultant_reply', $message->consultant_reply) }}</textarea>

                                        @error('consultant_reply')

                                            <p class="mt-2 text-sm text-red-600">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                        <div class="mt-3 flex justify-end">

                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700"
                                            >
                                                ✉️ {{ $message->consultant_reply ? 'Update Reply' : 'Send Reply' }}
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    {{-- Empty State --}}
                    <div class="py-12 text-center">

                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-3xl">
                            💬
                        </div>

                        <h2 class="mt-5 text-xl font-bold text-gray-900">
                            No Student Messages
                        </h2>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                            When students contact you, their messages will appear here.
                        </p>

                    </div>

                @endif

            </div>

        </div>

        {{-- Back --}}
        <div class="mt-6 text-center">

            <a
                href="{{ route('consultant.dashboard') }}"
                class="text-sm font-semibold text-indigo-600 hover:text-indigo-700"
            >
                ← Back to Consultant Dashboard
            </a>

        </div>

    </div>

</div>
```

</x-app-layout>
