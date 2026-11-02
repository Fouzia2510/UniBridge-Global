<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-slate-900">
                {{ __('Student Dashboard') }}
            </h2>

            <p class="text-sm text-slate-500">
                Your study-abroad journey starts here.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @php
                $profile = auth()->user()->studentProfile;
            @endphp

            {{-- ===================================================== --}}
            {{-- STUDENT PROFILE / READINESS --}}
            {{-- ===================================================== --}}

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">

                <div class="border-b border-slate-100 bg-gradient-to-r from-indigo-50 via-white to-blue-50 px-6 py-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <p class="text-sm font-bold text-indigo-600">
                                UniBridge
                            </p>

                            <h3 class="mt-1 text-xl font-extrabold text-slate-900">
                                Welcome, {{ auth()->user()->name }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Manage your profile and prepare for your study-abroad journey.
                            </p>
                        </div>

                        @if ($profile)
                            <div class="rounded-xl bg-white px-5 py-3 text-center shadow-sm ring-1 ring-slate-200">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Readiness Score
                                </p>

                                <p
                                    class="mt-1 text-3xl font-extrabold
                                    {{ ($profile->readiness_score ?? 0) >= 70
                                        ? 'text-emerald-600'
                                        : (($profile->readiness_score ?? 0) >= 40
                                            ? 'text-amber-600'
                                            : 'text-red-600') }}"
                                >
                                    {{ $profile->readiness_score ?? 0 }}%
                                </p>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="p-6">

                    @if ($profile)

                        {{-- Student Information --}}
                        <div class="mb-6">
                            <div class="mb-4">
                                <h4 class="text-lg font-bold text-slate-900">
                                    Your Profile
                                </h4>

                                <p class="text-sm text-slate-500">
                                    Your current academic and study preferences.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                                <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Degree
                                    </p>

                                    <p class="mt-2 font-semibold text-slate-900">
                                        {{ $profile->degree ?? 'Not added' }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        CGPA
                                    </p>

                                    <p class="mt-2 font-semibold text-slate-900">
                                        {{ $profile->cgpa ?? 'Not added' }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-slate-100">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Preferred Country
                                    </p>

                                    <p class="mt-2 font-semibold text-slate-900">
                                        {{ $profile->preferred_country ?? 'Not selected' }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        {{-- Dashboard Actions --}}
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">

                            <a
                                href="{{ route('student.profile.edit') }}"
                                class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:bg-indigo-50"
                            >
                                <p class="font-bold text-indigo-700">
                                    Edit Profile
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Update your information
                                </p>
                            </a>

                            <a
                                href="{{ route('student.eligibility') }}"
                                class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50"
                            >
                                <p class="font-bold text-blue-700">
                                    Eligibility
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Check your eligibility
                                </p>
                            </a>

                            <a
                                href="{{ route('student.universities.index') }}"
                                class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:bg-cyan-50"
                            >
                                <p class="font-bold text-cyan-700">
                                    Universities
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Find suitable universities
                                </p>
                            </a>

                            <a
                                href="{{ route('student.bookings.index') }}"
                                class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-emerald-50"
                            >
                                <p class="font-bold text-emerald-700">
                                    My Bookings
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    View consultant bookings
                                </p>
                            </a>

                            <a
                                href="{{ route('student.courses.index') }}"
                                class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-violet-300 hover:bg-violet-50"
                            >
                                <p class="font-bold text-violet-700">
                                    Academy
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    View short courses
                                </p>
                            </a>

                        </div>

                    @else

                        {{-- Profile Not Completed --}}
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-6 py-8 text-center">

                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-100">
                                <span class="text-xl font-extrabold text-indigo-600">
                                    U
                                </span>
                            </div>

                            <h3 class="mt-4 text-xl font-extrabold text-slate-900">
                                Complete Your Student Profile
                            </h3>

                            <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-600">
                                You haven't completed your profile yet.
                                Complete it to get your readiness score,
                                eligibility information and university recommendations.
                            </p>

                            <a
                                href="{{ route('student.profile.edit') }}"
                                class="mt-5 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                            >
                                Complete Your Profile
                            </a>

                        </div>

                    @endif

                </div>
            </div>


            {{-- ===================================================== --}}
            {{-- UNIBRIDGE AI ASSISTANT --}}
            {{-- ===================================================== --}}

            <div
                id="unibridge-ai"
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200"
            >

                {{-- AI HEADER --}}
                <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 px-5 py-5 sm:px-6">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex items-center gap-3">

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm">
                                <span class="text-lg font-black text-indigo-600">
                                    AI
                                </span>
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center gap-2">

                                    <h3 class="text-lg font-extrabold text-white">
                                        UniBridge AI Assistant
                                    </h3>

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-2.5 py-1 text-xs font-bold text-emerald-100">
                                        <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                                        Available
                                    </span>

                                </div>

                                <p class="mt-1 text-sm text-blue-100">
                                    Your AI study-abroad guide
                                </p>
                            </div>

                        </div>

                        <button
                            type="button"
                            id="open-ai-button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-sm transition hover:bg-indigo-50"
                        >
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-black text-indigo-700">
                                AI
                            </span>

                            <span id="open-ai-button-text">
                                Ask AI
                            </span>
                        </button>

                    </div>

                </div>


                {{-- AI PANEL --}}
                <div
                    id="ai-panel"
                    class="hidden border-t border-slate-200 bg-slate-50"
                >

                    <div class="p-5 sm:p-6">

                        {{-- Intro --}}
                        <div class="mb-6 flex items-start gap-3">

                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                <span class="text-xs font-black">
                                    AI
                                </span>
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-900">
                                    Ask UniBridge AI
                                </h4>

                                <p class="mt-1 text-sm text-slate-500">
                                    Ask about countries, universities, eligibility,
                                    documents or study-abroad preparation.
                                </p>
                            </div>

                        </div>


                        {{-- Quick Questions --}}
                        <div class="mb-6">

                            <p class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                                Quick Questions
                            </p>

                            <div class="flex flex-wrap gap-2">

                                <button
                                    type="button"
                                    data-ai-question="Based on my profile, what should I focus on to prepare for studying abroad?"
                                    class="ai-suggestion rounded-lg border border-indigo-200 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50"
                                >
                                    🎓 Analyze my profile
                                </button>

                                <button
                                    type="button"
                                    data-ai-question="What documents should a student generally prepare before applying to universities abroad?"
                                    class="ai-suggestion rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-50"
                                >
                                    📄 Required documents
                                </button>

                                <button
                                    type="button"
                                    data-ai-question="What are the important things I should consider when choosing a country and university for studying abroad?"
                                    class="ai-suggestion rounded-lg border border-cyan-200 bg-white px-3 py-2 text-xs font-semibold text-cyan-700 transition hover:bg-cyan-50"
                                >
                                    🌍 Choosing a country
                                </button>

                                <button
                                    type="button"
                                    data-ai-question="Give me practical advice to improve my study-abroad readiness."
                                    class="ai-suggestion rounded-lg border border-emerald-200 bg-white px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50"
                                >
                                    📈 Improve readiness
                                </button>

                            </div>

                        </div>


                        {{-- AI FORM --}}
                        <form id="gemini-form">

                            @csrf

                            <label
                                for="gemini-prompt"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Your Question
                            </label>

                            <textarea
                                id="gemini-prompt"
                                name="prompt"
                                rows="4"
                                maxlength="10000"
                                placeholder="Example: Which universities in my preferred country may match my profile?"
                                class="w-full rounded-xl border-slate-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            ></textarea>

                            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <p class="text-xs text-slate-500">
                                    Ask one question at a time for a clearer answer.
                                </p>

                                <button
                                    type="submit"
                                    id="gemini-submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/20 text-[9px] font-black">
                                        AI
                                    </span>

                                    <span id="gemini-submit-text">
                                        Ask AI
                                    </span>
                                </button>

                            </div>

                        </form>


                        {{-- Loading --}}
                        <div
                            id="gemini-loading"
                            class="mt-5 hidden rounded-xl border border-indigo-100 bg-indigo-50 px-5 py-4"
                        >
                            <div class="flex items-center gap-3">

                                <div class="h-5 w-5 animate-spin rounded-full border-2 border-indigo-200 border-t-indigo-600"></div>

                                <p class="text-sm font-semibold text-indigo-700">
                                    UniBridge AI is thinking...
                                </p>

                            </div>
                        </div>


                        {{-- Error --}}
                        <div
                            id="gemini-error"
                            class="mt-5 hidden rounded-xl border border-red-200 bg-red-50 px-5 py-4"
                        >
                            <p class="text-sm font-bold text-red-700">
                                AI request failed
                            </p>

                            <p
                                id="gemini-error-text"
                                class="mt-1 text-sm text-red-600"
                            ></p>
                        </div>


                        {{-- AI RESPONSE --}}
                        <div
                            id="gemini-response"
                            class="mt-5 hidden"
                        >

                            <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">

                                <div class="mb-4 flex items-center gap-3">

                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                                        <span class="text-xs font-black">
                                            AI
                                        </span>
                                    </div>

                                    <div>
                                        <p class="text-sm font-bold text-slate-900">
                                            UniBridge AI
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            AI-generated guidance
                                        </p>
                                    </div>

                                </div>

                                <div
                                    id="gemini-response-text"
                                    class="whitespace-pre-wrap text-sm leading-7 text-slate-700"
                                ></div>

                            </div>

                            <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">

                                <p class="text-xs leading-5 text-amber-800">
                                    <strong>Important:</strong>
                                    AI guidance is for informational purposes only.
                                    University admission and visa requirements can change.
                                    Always verify important requirements with the university,
                                    relevant authorities, or a UniBridge consultant.
                                </p>

                            </div>

                        </div>


                        {{-- Close --}}
                        <div class="mt-5 flex justify-end">

                            <button
                                type="button"
                                id="close-ai-button"
                                class="text-sm font-semibold text-slate-500 transition hover:text-slate-800"
                            >
                                Close AI Assistant
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- AI JAVASCRIPT --}}
    {{-- ===================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const openButton = document.getElementById('open-ai-button');
            const openButtonText = document.getElementById('open-ai-button-text');
            const closeButton = document.getElementById('close-ai-button');

            const aiPanel = document.getElementById('ai-panel');

            const form = document.getElementById('gemini-form');
            const promptInput = document.getElementById('gemini-prompt');

            const submitButton = document.getElementById('gemini-submit');
            const submitText = document.getElementById('gemini-submit-text');

            const loadingBox = document.getElementById('gemini-loading');

            const responseBox = document.getElementById('gemini-response');
            const responseText = document.getElementById('gemini-response-text');

            const errorBox = document.getElementById('gemini-error');
            const errorText = document.getElementById('gemini-error-text');

            const suggestionButtons =
                document.querySelectorAll('.ai-suggestion');


            /*
             * Open AI panel.
             */
            if (openButton && aiPanel) {

                openButton.addEventListener('click', function () {

                    aiPanel.classList.toggle('hidden');

                    if (aiPanel.classList.contains('hidden')) {

                        if (openButtonText) {
                            openButtonText.textContent = 'Ask AI';
                        }

                    } else {

                        if (openButtonText) {
                            openButtonText.textContent = 'Close AI';
                        }

                        setTimeout(function () {

                            if (promptInput) {
                                promptInput.focus();
                            }

                        }, 100);

                    }

                });

            }


            /*
             * Close AI panel.
             */
            if (closeButton && aiPanel) {

                closeButton.addEventListener('click', function () {

                    aiPanel.classList.add('hidden');

                    if (openButtonText) {
                        openButtonText.textContent = 'Ask AI';
                    }

                });

            }


            /*
             * Quick questions.
             */
            suggestionButtons.forEach(function (button) {

                button.addEventListener('click', function () {

                    const question =
                        button.getAttribute('data-ai-question');

                    if (promptInput) {
                        promptInput.value = question || '';
                        promptInput.focus();
                    }

                    if (responseBox) {
                        responseBox.classList.add('hidden');
                    }

                    if (errorBox) {
                        errorBox.classList.add('hidden');
                    }

                });

            });


            /*
             * AI request.
             */
            if (form) {

                form.addEventListener('submit', async function (event) {

                    event.preventDefault();

                    const prompt =
                        promptInput
                            ? promptInput.value.trim()
                            : '';


                    if (!prompt) {

                        if (errorText) {
                            errorText.textContent =
                                'Please enter a question first.';
                        }

                        if (errorBox) {
                            errorBox.classList.remove('hidden');
                        }

                        return;
                    }


                    if (responseBox) {
                        responseBox.classList.add('hidden');
                    }

                    if (errorBox) {
                        errorBox.classList.add('hidden');
                    }

                    if (loadingBox) {
                        loadingBox.classList.remove('hidden');
                    }

                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    if (submitText) {
                        submitText.textContent = 'Thinking...';
                    }


                    try {

                        const response = await fetch(
                            "{{ route('api.gemini.generate') }}",
                            {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },

                                body: JSON.stringify({
                                    prompt: prompt
                                })
                            }
                        );


                        let data = {};

                        try {
                            data = await response.json();
                        } catch (jsonError) {
                            data = {};
                        }


                        if (!response.ok) {

                            if (response.status === 419) {

                                throw new Error(
                                    'Your session has expired. Please refresh the page and try again.'
                                );

                            }

                            if (response.status === 422) {

                                throw new Error(
                                    'Please check your question and try again.'
                                );

                            }

                            if (response.status === 503) {

                                throw new Error(
                                    data.message ||
                                    'AI service is temporarily unavailable.'
                                );

                            }

                            throw new Error(
                                data.message ||
                                'Unable to get an AI response.'
                            );

                        }


                        if (!data.text) {

                            throw new Error(
                                'The AI returned an empty response.'
                            );

                        }


                        /*
                         * Safely display AI response as plain text.
                         * This avoids broken Markdown/regex code.
                         */
                        if (responseText) {

                            responseText.textContent =
                                data.text;

                        }


                        if (responseBox) {

                            responseBox.classList.remove('hidden');

                            responseBox.scrollIntoView({
                                behavior: 'smooth',
                                block: 'nearest'
                            });

                        }


                    } catch (error) {

                        console.error(
                            'UniBridge AI error:',
                            error
                        );


                        if (errorText) {

                            errorText.textContent =
                                error.message ||
                                'Something went wrong. Please try again.';

                        }


                        if (errorBox) {
                            errorBox.classList.remove('hidden');
                        }


                    } finally {

                        if (loadingBox) {
                            loadingBox.classList.add('hidden');
                        }

                        if (submitButton) {
                            submitButton.disabled = false;
                        }

                        if (submitText) {
                            submitText.textContent = 'Ask AI';
                        }

                    }

                });

            }

        });
    </script>

</x-app-layout>