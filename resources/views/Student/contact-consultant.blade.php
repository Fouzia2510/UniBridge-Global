<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    Contact Consultant
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Ask a consultant about your study abroad plans.
                </p>
            </div>

            <a
                href="{{ route('student.messages.index') }}"
                class="inline-flex w-fit items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
            >
                My Messages
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gray-50 py-8">

        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">

                    <p class="font-semibold text-red-800">
                        Please fix the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif


            <div class="overflow-hidden rounded-2xl bg-white shadow-md ring-1 ring-gray-200">


                {{-- HERO --}}
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-8 sm:px-8">

                    <div class="flex items-start gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/20 text-2xl">
                            💬
                        </div>

                        <div>

                            {{-- CHANGED TO DARK --}}
                            <h1 class="text-2xl font-bold text-gray-900">
                                Talk to a Consultant
                            </h1>

                            {{-- CHANGED TO DARK --}}
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-900">
                                Get guidance about universities, admissions and studying abroad.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- FORM --}}
                <form
                    method="POST"
                    action="{{ route('student.contact-consultant.store') }}"
                    class="p-6 sm:p-8"
                >

                    @csrf


                    {{-- SELECT CONSULTANT --}}
                    <div>

                        <div class="mb-4">

                            <h2 class="text-lg font-bold text-gray-900">
                                Select Consultant
                                <span class="text-red-500">*</span>
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                Choose the consultant you want to contact.
                            </p>

                        </div>


                        @if ($consultants->count() > 0)

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                                @foreach ($consultants as $consultant)

                                    @php
                                        $isSelected =
                                            old('consultant_id', $selectedConsultantId ?? '') == $consultant->id;

                                        $company = optional($consultant->consultantProfile)->company;
                                    @endphp


                                    <label
                                        for="consultant_{{ $consultant->id }}"
                                        class="group relative block cursor-pointer"
                                    >

                                        <input
                                            type="radio"
                                            id="consultant_{{ $consultant->id }}"
                                            name="consultant_id"
                                            value="{{ $consultant->id }}"
                                            class="peer sr-only"
                                            {{ $isSelected ? 'checked' : '' }}
                                            required
                                        >


                                        <div
                                            class="rounded-xl border-2 border-gray-200 bg-white p-5 shadow-sm transition
                                            hover:border-indigo-300 hover:shadow-md
                                            peer-checked:border-indigo-600
                                            peer-checked:bg-indigo-50"
                                        >

                                            <div class="flex items-start gap-4">


                                                <div
                                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-lg font-bold text-indigo-700"
                                                >
                                                    {{ strtoupper(substr($consultant->name, 0, 1)) }}
                                                </div>


                                                <div class="min-w-0 flex-1">

                                                    <h3 class="truncate text-base font-bold text-gray-900">
                                                        {{ $consultant->name }}
                                                    </h3>


                                                    @if ($company)

                                                        <p class="mt-1 truncate text-sm text-gray-500">
                                                            {{ $company }}
                                                        </p>

                                                    @else

                                                        <p class="mt-1 text-sm text-gray-500">
                                                            Study Abroad Consultant
                                                        </p>

                                                    @endif


                                                    <div class="mt-3 flex items-center gap-2">

                                                        <span
                                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700"
                                                        >
                                                            ● Available
                                                        </span>

                                                    </div>

                                                </div>


                                                <div
                                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-gray-300"
                                                >
                                                    <div
                                                        class="h-3 w-3 rounded-full bg-indigo-600"
                                                    ></div>
                                                </div>

                                            </div>


                                            <div
                                                class="mt-4 rounded-lg bg-indigo-100 px-3 py-2 text-center text-sm font-semibold text-indigo-700"
                                            >
                                                Select this consultant
                                            </div>

                                        </div>

                                    </label>

                                @endforeach

                            </div>


                            <div class="mt-5 flex items-center gap-2 rounded-lg bg-indigo-50 px-4 py-3">

                                <span class="text-lg">
                                    👥
                                </span>

                                <p class="text-sm font-medium text-indigo-800">
                                    {{ $consultants->count() }}
                                    consultants available.
                                </p>

                            </div>


                        @else

                            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4">

                                <p class="text-sm font-semibold text-red-700">
                                    No consultants are currently available.
                                </p>

                            </div>

                        @endif


                        @error('consultant_id')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>



                    {{-- SUBJECT --}}
                    <div class="mt-8">

                        <label
                            for="subject"
                            class="mb-2 block text-sm font-semibold text-gray-800"
                        >
                            Subject

                            <span class="font-normal text-gray-400">
                                (optional)
                            </span>
                        </label>


                        <input
                            id="subject"
                            name="subject"
                            type="text"
                            value="{{ old('subject') }}"
                            maxlength="255"
                            placeholder="e.g. Canada university admission guidance"
                            class="block h-12 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                        >


                        @error('subject')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>



                    {{-- MESSAGE --}}
                    <div class="mt-7">

                        <div class="mb-2 flex items-center justify-between">

                            <label
                                for="message"
                                class="block text-sm font-semibold text-gray-800"
                            >
                                Your Message
                                <span class="text-red-500">*</span>
                            </label>

                            <span class="text-xs text-gray-400">
                                Max 3000 characters
                            </span>

                        </div>


                        <textarea
                            id="message"
                            name="message"
                            rows="7"
                            maxlength="3000"
                            required
                            placeholder="Write your question or explain what guidance you need..."
                            class="block w-full resize-y rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm leading-6 text-gray-900 shadow-sm outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                        >{{ old('message') }}</textarea>


                        @error('message')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>



                    {{-- INFO --}}
                    <div class="mt-7 rounded-xl border border-blue-100 bg-blue-50 px-5 py-4">

                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                i
                            </div>

                            <div>

                                <h3 class="text-sm font-semibold text-blue-900">
                                    Before you send
                                </h3>

                                <p class="mt-1 text-sm leading-6 text-blue-800">
                                    Include your target country, intended program,
                                    intake and specific question so the consultant
                                    can understand your requirements.
                                </p>

                            </div>

                        </div>

                    </div>



                    {{-- BUTTONS --}}
                    <div class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">

                        <a
                            href="{{ route('student.dashboard') }}"
                            class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 px-6 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span>✉️</span>
                            Send Message
                        </button>

                    </div>

                </form>

            </div>



            {{-- FOOTER --}}
            <div class="mt-6 text-center">

                <p class="text-sm text-gray-500">

                    Want to see your previous conversations?

                    <a
                        href="{{ route('student.messages.index') }}"
                        class="font-semibold text-indigo-600 hover:text-indigo-700"
                    >
                        View My Messages
                    </a>

                </p>

            </div>

        </div>

    </div>

</x-app-layout>