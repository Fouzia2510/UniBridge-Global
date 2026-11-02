<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    My Messages
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    View your conversations with consultants.
                </p>
            </div>

            <a
                href="{{ route('student.contact-consultant.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
            >
                Contact Consultant
            </a>

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


            {{-- Main Messages Card --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-gray-200">


                {{-- Header --}}
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-8 sm:px-8">

                    <div class="flex items-center gap-4">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-2xl">
                            💬
                        </div>

                        <div>

                            <h1 class="text-2xl font-bold text-gray-900">
                                My Consultant Messages
                            </h1>

                            <p class="mt-1 text-sm font-medium text-gray-900">
                                Keep track of your questions and consultant replies.
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


                                    {{-- Consultant + Date --}}
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">


                                        <div class="flex items-center gap-3">

                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">
                                                {{ strtoupper(substr($message->consultant->name ?? 'C', 0, 1)) }}
                                            </div>

                                            <div>

                                                <h3 class="font-bold text-gray-900">
                                                    {{ $message->consultant->name ?? 'Consultant' }}
                                                </h3>

                                                <p class="text-xs text-gray-500">
                                                    Consultant
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
                                            Your Message
                                        </p>

                                        <p class="whitespace-pre-line text-sm leading-6 text-gray-700">
                                            {{ $message->message }}
                                        </p>

                                    </div>


                                    {{-- Consultant Reply --}}
                                    @if ($message->consultant_reply)


                                        <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4">


                                            <div class="flex items-center justify-between gap-3">

                                                <p class="text-xs font-semibold uppercase tracking-wide text-green-700">
                                                    Consultant Reply
                                                </p>


                                                @if ($message->replied_at)

                                                    <span class="text-xs text-green-600">
                                                        {{ $message->replied_at->format('d M Y, h:i A') }}
                                                    </span>

                                                @endif

                                            </div>


                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-green-900">
                                                {{ $message->consultant_reply }}
                                            </p>


                                        </div>


                                    @else


                                        <div class="mt-4 rounded-xl border border-yellow-200 bg-yellow-50 p-4">

                                            <p class="text-sm font-medium text-yellow-800">
                                                ⏳ Waiting for consultant's reply.
                                            </p>

                                        </div>


                                    @endif


                                </div>


                            @endforeach


                        </div>


                    @else


                        {{-- Empty Messages --}}
                        <div class="py-12 text-center">


                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-3xl">
                                💬
                            </div>


                            <h2 class="mt-5 text-xl font-bold text-gray-900">
                                No Messages Yet
                            </h2>


                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                                You haven't contacted any consultant yet.
                                Start a conversation to get guidance about your study abroad plans.
                            </p>


                            <a
                                href="{{ route('student.contact-consultant.create') }}"
                                class="mt-6 inline-flex items-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700"
                            >
                                💬 Contact a Consultant
                            </a>


                        </div>


                    @endif


                </div>

            </div>


            {{-- How to Contact Consultant --}}
            <div class="mt-6 overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-gray-200">


                <div class="border-b border-gray-200 px-6 py-5 sm:px-8">

                    <h2 class="text-xl font-bold text-gray-900">
                        How to Contact a Consultant
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Follow these simple steps to get guidance from an approved consultant.
                    </p>

                </div>


                <div class="p-6 sm:p-8">


                    <div class="grid gap-5 md:grid-cols-2">


                        {{-- Step 1 --}}
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                            <div class="flex items-start gap-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">
                                    1
                                </div>

                                <div>

                                    <h3 class="font-bold text-gray-900">
                                        Select a Consultant
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-gray-600">
                                        Click the "Contact Consultant" button and choose an approved consultant from the list.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Step 2 --}}
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                            <div class="flex items-start gap-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">
                                    2
                                </div>

                                <div>

                                    <h3 class="font-bold text-gray-900">
                                        Write Your Message
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-gray-600">
                                        Enter a subject and explain your question or study abroad requirement clearly.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Step 3 --}}
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                            <div class="flex items-start gap-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">
                                    3
                                </div>

                                <div>

                                    <h3 class="font-bold text-gray-900">
                                        Send Your Message
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-gray-600">
                                        Submit your message. It will be delivered directly to the selected consultant.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Step 4 --}}
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

                            <div class="flex items-start gap-4">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700">
                                    4
                                </div>

                                <div>

                                    <h3 class="font-bold text-gray-900">
                                        Check Consultant Reply
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-gray-600">
                                        Return to My Messages to see the consultant's response when they reply.
                                    </p>

                                </div>

                            </div>

                        </div>


                    </div>


                    {{-- Contact Button --}}
                    <div class="mt-6 rounded-xl border border-indigo-200 bg-indigo-50 p-5">


                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">


                            <div>

                                <h3 class="font-bold text-indigo-900">
                                    Need help with your study abroad plans?
                                </h3>

                                <p class="mt-1 text-sm text-indigo-700">
                                    Contact an approved consultant and ask your questions.
                                </p>

                            </div>


                            <a
                                href="{{ route('student.contact-consultant.create') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                            >
                                💬 Contact Consultant
                            </a>


                        </div>


                    </div>


                </div>

            </div>


            {{-- Back to Dashboard --}}
            <div class="mt-6 text-center">

                <a
                    href="{{ route('student.dashboard') }}"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-700"
                >
                    ← Back to Student Dashboard
                </a>

            </div>


        </div>

    </div>

</x-app-layout>