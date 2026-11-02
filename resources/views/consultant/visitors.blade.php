<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-600">
                    Consultant Portal
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    Visitors
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Track students and visitors who interact with your consultant profile.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Success message --}}
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                <ul class="list-disc space-y-1 pl-5 text-sm font-semibold text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- =========================
             STATISTICS
        ========================== --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">
                    Total Visits
                </p>

                <p class="mt-2 text-3xl font-extrabold text-slate-950">
                    {{ $totalVisitors }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    All individual profile visits
                </p>
            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">
                    Today's Visits
                </p>

                <p class="mt-2 text-3xl font-extrabold text-indigo-600">
                    {{ $todayVisitors }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Visits recorded today
                </p>
            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">
                    Online Visits
                </p>

                <p class="mt-2 text-3xl font-extrabold text-emerald-600">
                    {{ $onlineVisitors }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Public profile visits
                </p>
            </div>


            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-violet-600">
                    Unique Visitors
                </p>

                <p class="mt-2 text-3xl font-extrabold text-violet-600">
                    {{ $registeredVisitors }}
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    Registered students
                </p>
            </div>

        </div>


        {{-- =========================
             SEARCH & FILTERS
        ========================== --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-950">
                    Search & Filters
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Find visitors by name, email, lead status, visit type or country.
                </p>
            </div>


            <form
                method="GET"
                action="{{ route('consultant.visitors') }}"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5"
            >

                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label
                        for="search"
                        class="mb-1 block text-sm font-semibold text-slate-700"
                    >
                        Search
                    </label>

                    <input
                        id="search"
                        name="search"
                        type="text"
                        value="{{ request('search') }}"
                        placeholder="Name or email..."
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>


                {{-- Lead Status --}}
                <div>
                    <label
                        for="lead_status"
                        class="mb-1 block text-sm font-semibold text-slate-700"
                    >
                        Lead Status
                    </label>

                    <select
                        id="lead_status"
                        name="lead_status"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            All Leads
                        </option>

                        <option
                            value="new"
                            @selected(request('lead_status') === 'new')
                        >
                            New
                        </option>

                        <option
                            value="contacted"
                            @selected(request('lead_status') === 'contacted')
                        >
                            Contacted
                        </option>

                        <option
                            value="interested"
                            @selected(request('lead_status') === 'interested')
                        >
                            Interested
                        </option>

                        <option
                            value="converted"
                            @selected(request('lead_status') === 'converted')
                        >
                            Converted
                        </option>

                        <option
                            value="not_interested"
                            @selected(request('lead_status') === 'not_interested')
                        >
                            Not Interested
                        </option>
                    </select>
                </div>


                {{-- Visit Type --}}
                <div>
                    <label
                        for="visit_type"
                        class="mb-1 block text-sm font-semibold text-slate-700"
                    >
                        Visit Type
                    </label>

                    <select
                        id="visit_type"
                        name="visit_type"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            All Types
                        </option>

                        <option
                            value="online"
                            @selected(request('visit_type') === 'online')
                        >
                            Online
                        </option>

                        <option
                            value="walking"
                            @selected(request('visit_type') === 'walking')
                        >
                            Walking
                        </option>

                        <option
                            value="office"
                            @selected(request('visit_type') === 'office')
                        >
                            Office
                        </option>

                        <option
                            value="meeting"
                            @selected(request('visit_type') === 'meeting')
                        >
                            Meeting
                        </option>
                    </select>
                </div>


                {{-- Source --}}
                <div>
                    <label
                        for="source"
                        class="mb-1 block text-sm font-semibold text-slate-700"
                    >
                        Source
                    </label>

                    <select
                        id="source"
                        name="source"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">
                            All Sources
                        </option>

                        <option
                            value="public_profile"
                            @selected(request('source') === 'public_profile')
                        >
                            Public Profile
                        </option>

                        <option
                            value="manual"
                            @selected(request('source') === 'manual')
                        >
                            Manual
                        </option>
                    </select>
                </div>


                {{-- Country --}}
                <div>
                    <label
                        for="country_interest"
                        class="mb-1 block text-sm font-semibold text-slate-700"
                    >
                        Country Interest
                    </label>

                    <input
                        id="country_interest"
                        name="country_interest"
                        type="text"
                        value="{{ request('country_interest') }}"
                        placeholder="e.g. UK, Canada"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </div>


                {{-- Buttons --}}
                <div class="flex items-end gap-2 lg:col-span-5">

                    <button
                        type="submit"
                        class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        Search
                    </button>

                    <a
                        href="{{ route('consultant.visitors') }}"
                        class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                    >
                        Reset
                    </a>

                </div>

            </form>

        </div>


        {{-- =========================
             VISITOR TABLE
        ========================== --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="text-lg font-extrabold text-slate-950">
                        Visitor Intelligence
                    </h3>

                    <p class="text-sm text-slate-500">
                        Showing {{ $visitors->count() }}
                        of {{ $visitors->total() }}
                        unique visitor groups
                    </p>
                </div>


                <div class="flex flex-wrap gap-2">

                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
                        {{ $visitors->total() }} Unique
                    </span>

                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                        {{ $totalVisitors }} Visits
                    </span>

                </div>

            </div>


            @if ($visitors->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">

                            <tr>

                                <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Visitor
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Profile
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Country
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Visits
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Application
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Lead Status
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Last Activity
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-extrabold uppercase tracking-wider text-slate-500">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @foreach ($visitors as $item)

                                @php

                                    $visitor = $item->visitor;

                                    $visitorName =
                                        $item->visitor_name
                                        ?? 'Guest Visitor';

                                    $visitorEmail =
                                        $item->visitor_email
                                        ?? 'No email';

                                    $visitType =
                                        ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $item->visit_type
                                            )
                                        );

                                    $source =
                                        ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $item->source
                                            )
                                        );

                                    $country =
                                        $item->country
                                        ?: 'Not specified';

                                    $profileCompletion =
                                        (int) $item->profile_completion;

                                    $readiness =
                                        $item->readiness_score;

                                    $applicationStatus =
                                        $item->application_status;

                                    $applicationLabel =
                                        $applicationStatus
                                        ? ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $applicationStatus
                                            )
                                        )
                                        : 'No application';

                                    $applicationClass =
                                        match ($applicationStatus) {

                                            'accepted' =>
                                                'bg-emerald-100 text-emerald-700',

                                            'rejected' =>
                                                'bg-red-100 text-red-700',

                                            'under_review' =>
                                                'bg-blue-100 text-blue-700',

                                            'submitted' =>
                                                'bg-indigo-100 text-indigo-700',

                                            'withdrawn' =>
                                                'bg-slate-200 text-slate-700',

                                            default =>
                                                'bg-slate-100 text-slate-600',
                                        };


                                    $leadStatus =
                                        $item->lead_status
                                        ?: 'new';

                                    $leadLabel =
                                        match ($leadStatus) {

                                            'new' =>
                                                'New',

                                            'contacted' =>
                                                'Contacted',

                                            'interested' =>
                                                'Interested',

                                            'converted' =>
                                                'Converted',

                                            'not_interested' =>
                                                'Not Interested',

                                            default =>
                                                'New',
                                        };


                                    $leadClass =
                                        match ($leadStatus) {

                                            'new' =>
                                                'bg-slate-100 text-slate-700',

                                            'contacted' =>
                                                'bg-blue-100 text-blue-700',

                                            'interested' =>
                                                'bg-amber-100 text-amber-700',

                                            'converted' =>
                                                'bg-emerald-100 text-emerald-700',

                                            'not_interested' =>
                                                'bg-red-100 text-red-700',

                                            default =>
                                                'bg-slate-100 text-slate-700',
                                        };

                                @endphp


                                <tr class="transition hover:bg-slate-50">


                                    {{-- Visitor --}}
                                    <td class="px-5 py-4">

                                        <div class="flex items-start gap-3">

                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-extrabold text-indigo-700">
                                                {{ strtoupper(substr($visitorName, 0, 1)) }}
                                            </div>


                                            <div class="min-w-0">

                                                <div class="flex items-center gap-2">

                                                    <p class="truncate text-sm font-bold text-slate-900">
                                                        {{ $visitorName }}
                                                    </p>


                                                    @if ($item->is_registered)

                                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                                            Registered
                                                        </span>

                                                    @else

                                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                                                            Guest
                                                        </span>

                                                    @endif

                                                </div>


                                                <p class="mt-1 truncate text-xs text-slate-500">
                                                    {{ $visitorEmail }}
                                                </p>


                                                <p class="mt-1 text-xs text-slate-400">
                                                    {{ $visitType }}
                                                    ·
                                                    {{ $source }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Profile --}}
                                    <td class="px-5 py-4">

                                        @if ($item->is_registered)

                                            <div class="w-32">

                                                <div class="flex items-center justify-between">

                                                    <span class="text-xs font-bold text-slate-600">
                                                        {{ $profileCompletion }}%
                                                    </span>


                                                    @if ($readiness !== null)

                                                        <span class="text-[10px] font-semibold text-violet-600">
                                                            Ready {{ $readiness }}%
                                                        </span>

                                                    @endif

                                                </div>


                                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">

                                                    <div
                                                        class="h-full rounded-full bg-indigo-600"
                                                        style="width: {{ min(100, max(0, $profileCompletion)) }}%;"
                                                    ></div>

                                                </div>

                                            </div>

                                        @else

                                            <span class="text-xs font-semibold text-slate-400">
                                                No profile
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Country --}}
                                    <td class="px-5 py-4">

                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ $country }}
                                        </p>


                                        @if ($item->program)

                                            <p class="mt-1 max-w-32 truncate text-xs text-slate-400">
                                                {{ $item->program }}
                                            </p>

                                        @endif

                                    </td>


                                    {{-- Visits --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex min-w-10 items-center justify-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-extrabold text-indigo-700">
                                            {{ $item->visit_count }}
                                        </span>

                                        <p class="mt-1 text-xs text-slate-400">
                                            visit{{ $item->visit_count == 1 ? '' : 's' }}
                                        </p>

                                    </td>


                                    {{-- Application --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $applicationClass }}">
                                            {{ $applicationLabel }}
                                        </span>


                                        @if ($item->applications_count > 0)

                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $item->applications_count }}
                                                application{{ $item->applications_count == 1 ? '' : 's' }}
                                            </p>

                                        @endif

                                    </td>


                                    {{-- Lead Status --}}
                                    <td class="px-5 py-4">

                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-extrabold {{ $leadClass }}">
                                            {{ $leadLabel }}
                                        </span>


                                        @if ($item->lead_status === 'converted')

                                            <p class="mt-1 text-[11px] font-semibold text-emerald-600">
                                                Lead converted
                                            </p>

                                        @elseif ($item->lead_status === 'interested')

                                            <p class="mt-1 text-[11px] font-semibold text-amber-600">
                                                Follow-up needed
                                            </p>

                                        @elseif ($item->lead_status === 'contacted')

                                            <p class="mt-1 text-[11px] font-semibold text-blue-600">
                                                Contact made
                                            </p>

                                        @endif

                                    </td>


                                    {{-- Last Activity --}}
                                    <td class="px-5 py-4">

                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $item->last_visit?->format('d M Y') ?? 'Not available' }}
                                        </p>


                                        @if ($item->last_visit)

                                            <p class="text-xs text-slate-500">
                                                {{ $item->last_visit->format('h:i A') }}
                                            </p>

                                        @endif


                                        @if ($item->first_visit && $item->visit_count > 1)

                                            <p class="mt-1 text-[11px] text-slate-400">
                                                First:
                                                {{ $item->first_visit->format('d M Y') }}
                                            </p>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td class="px-5 py-4 text-right">

                                        <a
                                            href="{{ route('consultant.visitors.show', $visitor) }}"
                                            class="inline-flex items-center rounded-xl bg-indigo-50 px-4 py-2 text-xs font-bold text-indigo-700 transition hover:bg-indigo-100"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                @if ($visitors->hasPages())

                    <div class="border-t border-slate-200 px-5 py-4">
                        {{ $visitors->links() }}
                    </div>

                @endif


            @else

                <div class="px-6 py-16 text-center">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-3xl">
                        👥
                    </div>

                    <h3 class="mt-5 text-lg font-extrabold text-slate-900">
                        No visitors found
                    </h3>

                    <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                        Visitors will automatically appear here when someone visits
                        your public consultant profile.
                    </p>

                    <a
                        href="{{ route('consultants.index') }}"
                        class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700"
                    >
                        View Consultant Directory
                    </a>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>