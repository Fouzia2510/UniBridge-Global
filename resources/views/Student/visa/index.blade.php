<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-red-700">
                    UniBridge
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Visa Procedure
                </h2>

                <p class="mt-1 text-sm font-medium text-slate-600">
                    Track and manage your study visa procedure.
                </p>
            </div>

            <a
                href="{{ route('student.applications.index') }}"
                class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-slate-800"
            >
                My Applications
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-bold">Please check the following:</p>

                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-950">
                            Your Visa Applications
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            Visa procedures become available after your application payment has been verified by admin.
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-bold text-slate-700">
                        {{ $visaApplications->total() }} Total
                    </div>
                </div>
            </div>

            @if ($visaApplications->count())
                <div class="grid gap-5 md:grid-cols-2">

                    @foreach ($visaApplications as $visaApplication)
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-red-700">
                                        {{ $visaApplication->country }}
                                    </p>

                                    <h3 class="mt-1 text-lg font-extrabold text-slate-950">
                                        {{ $visaApplication->visa_type }}
                                    </h3>
                                </div>

                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                    {{ ucwords(str_replace('_', ' ', $visaApplication->status)) }}
                                </span>
                            </div>

                            <div class="mt-5 space-y-3">

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        University
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-800">
                                        {{ $visaApplication->application?->university?->name ?? 'University not available' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Program
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-800">
                                        {{ $visaApplication->application?->program ?? 'Program not available' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                                        Started
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-800">
                                        {{ $visaApplication->created_at?->format('d M Y') ?? '—' }}
                                    </p>
                                </div>

                            </div>

                            <div class="mt-6">
                                <a
                                    href="{{ route('student.visa.show', $visaApplication) }}"
                                    class="inline-flex w-full items-center justify-center rounded-xl bg-red-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-red-800"
                                >
                                    Open Visa Procedure
                                </a>
                            </div>

                        </div>
                    @endforeach

                </div>

                @if ($visaApplications->hasPages())
                    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
                        {{ $visaApplications->links() }}
                    </div>
                @endif

            @else

                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">

                    <div class="mx-auto max-w-xl">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-red-700">
                            <span class="text-xl font-black">V</span>
                        </div>

                        <h3 class="mt-5 text-xl font-extrabold text-slate-950">
                            No Visa Procedure Started
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Once your university application is accepted and its payment is verified by admin,
                            you can start your visa procedure from the relevant application.
                        </p>

                        <div class="mt-6">
                            <a
                                href="{{ route('student.applications.index') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-800"
                            >
                                View My Applications
                            </a>
                        </div>
                    </div>

                </div>

            @endif

        </div>
    </div>

</x-app-layout>
