<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $consultant->company }} | UniBridge Consultant Network</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system,
                BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .hero-gradient {
            background:
                radial-gradient(
                    circle at top left,
                    rgba(99, 102, 241, 0.16),
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    rgba(14, 165, 233, 0.12),
                    transparent 35%
                ),
                #f8fafc;
        }

        .consultant-card {
            transition: all 0.25s ease;
        }

        .consultant-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    {{-- Navigation --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between">

                <a
                    href="{{ route('consultants.index') }}"
                    class="flex items-center gap-3"
                >
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-lg">
                        U
                    </div>

                    <div>
                        <div class="font-bold text-xl text-slate-900">
                            UniBridge
                        </div>

                        <div class="text-xs text-slate-500">
                            Study Abroad Platform
                        </div>
                    </div>
                </a>

                <div class="hidden md:flex items-center gap-6 text-sm font-medium">

                    <a
                        href="{{ route('consultants.index') }}"
                        class="text-slate-600 hover:text-indigo-600 transition"
                    >
                        Consultants
                    </a>

                    @auth

                        @if(auth()->user()->role === 'student')

                            <a
                                href="{{ route('student.universities.index') }}"
                                class="text-slate-600 hover:text-indigo-600 transition"
                            >
                                Universities
                            </a>

                            <a
                                href="{{ route('student.bookings.index') }}"
                                class="text-slate-600 hover:text-indigo-600 transition"
                            >
                                My Bookings
                            </a>

                            <a
                                href="{{ route('student.dashboard') }}"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition"
                            >
                                Dashboard
                            </a>

                        @elseif(auth()->user()->role === 'consultant')

                            <a
                                href="{{ route('consultant.dashboard') }}"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition"
                            >
                                Dashboard
                            </a>

                        @elseif(auth()->user()->role === 'admin')

                            <a
                                href="{{ route('admin.dashboard') }}"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition"
                            >
                                Dashboard
                            </a>

                        @endif

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="text-slate-600 hover:text-indigo-600 transition"
                        >
                            Login
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition"
                        >
                            Get Started
                        </a>

                    @endauth

                </div>

            </div>
        </div>
    </nav>


    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

            <div class="flex items-center gap-2 text-sm">

                <a
                    href="{{ route('consultants.index') }}"
                    class="text-indigo-600 hover:text-indigo-800"
                >
                    Consultant Network
                </a>

                <span class="text-slate-400">
                    /
                </span>

                <span class="text-slate-500">
                    {{ $consultant->company }}
                </span>

            </div>

        </div>
    </div>


    {{-- Hero --}}
    <section class="hero-gradient">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Main Profile --}}
                <div class="lg:col-span-2">

                    <div class="flex flex-col sm:flex-row gap-6 items-start">

                        {{-- Logo --}}
                        <div class="flex-shrink-0">

                            @if($consultant->logo)

                                <img
                                    src="{{ asset('storage/' . $consultant->logo) }}"
                                    alt="{{ $consultant->company }}"
                                    class="w-28 h-28 rounded-2xl object-cover border border-slate-200 bg-white shadow-sm"
                                >

                            @else

                                <div class="w-28 h-28 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-4xl font-bold shadow-sm">
                                    {{ strtoupper(substr($consultant->company, 0, 1)) }}
                                </div>

                            @endif

                        </div>


                        <div class="flex-1">

                            <div class="flex flex-wrap items-center gap-3 mb-3">

                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                    Consultant Network
                                </span>

                                @if($consultant->is_featured)

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        Featured
                                    </span>

                                @endif

                                @if($consultant->is_verified)

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        Verified
                                    </span>

                                @endif

                            </div>


                            <h1 class="text-3xl md:text-4xl font-bold text-slate-900">
                                {{ $consultant->company }}
                            </h1>


                            @if($consultant->name && $consultant->name !== $consultant->company)

                                <p class="mt-2 text-lg text-slate-600">
                                    {{ $consultant->name }}
                                </p>

                            @endif


                            @if($consultant->city)

                                <div class="mt-4 flex items-center gap-2 text-slate-600">

                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                    </svg>

                                    <span>
                                        {{ $consultant->city }}
                                    </span>

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- Description --}}
                    @if($consultant->short_description)

                        <div class="mt-8">

                            <h2 class="text-xl font-bold text-slate-900 mb-3">
                                About {{ $consultant->company }}
                            </h2>

                            <p class="text-slate-600 leading-7">
                                {{ $consultant->short_description }}
                            </p>

                        </div>

                    @endif


                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">

                        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

                            <div class="text-sm text-slate-500">
                                Location
                            </div>

                            <div class="mt-1 font-semibold text-slate-900">
                                {{ $consultant->city ?: 'Pakistan' }}
                            </div>

                        </div>


                        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

                            <div class="text-sm text-slate-500">
                                Services
                            </div>

                            <div class="mt-1 font-semibold text-slate-900">
                                {{ is_array($consultant->services) ? count($consultant->services) : 0 }}
                            </div>

                        </div>


                        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">

                            <div class="text-sm text-slate-500">
                                Destinations
                            </div>

                            <div class="mt-1 font-semibold text-slate-900">
                                {{ is_array($consultant->destinations) ? count($consultant->destinations) : 0 }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Sidebar --}}
                <div>

                    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-xl">

                        <h2 class="text-xl font-bold">
                            Consultant Profile
                        </h2>

                        <p class="mt-2 text-slate-300 text-sm leading-6">
                            Explore the consultant's services, destinations and public reference information.
                        </p>


                        {{-- Booking CTA --}}
                        @auth

                            @if(auth()->user()->role === 'student' && $consultant->user_id)

                                <a
                                    href="{{ route('student.bookings.create', ['consultant_id' => $consultant->user_id]) }}"
                                    class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-indigo-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-indigo-400 transition shadow-lg"
                                >

                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5v12a2 2 0 002 2z"
                                        />
                                    </svg>

                                    Book Consultation

                                </a>


                            @elseif(auth()->user()->role === 'student' && !$consultant->user_id)

                                <div class="mt-6 bg-slate-800 border border-slate-700 rounded-xl p-4">

                                    <div class="font-semibold text-white">
                                        Booking Currently Unavailable
                                    </div>

                                    <p class="mt-1 text-sm text-slate-400">
                                        This consultant is not currently connected to the UniBridge booking system.
                                    </p>

                                </div>

                            @endif

                        @else

                            <a
                                href="{{ route('login') }}"
                                class="mt-6 w-full inline-flex items-center justify-center gap-2 bg-indigo-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-indigo-400 transition shadow-lg"
                            >

                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12H3m0 0l4-4m-4 4l4 4m8-9V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2v-2"
                                    />
                                </svg>

                                Login to Book Consultation

                            </a>

                        @endauth


                        {{-- UniBridge Official WhatsApp Helpline --}}
<a
    href="https://wa.me/923034253277?text=Hello%2C%20I%20need%20help%20with%20UniBridge."
    target="_blank"
    rel="noopener noreferrer"
    class="mt-3 w-full inline-flex items-center justify-center gap-2 bg-emerald-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-emerald-700 transition"
>
    <span>Contact UniBridge on WhatsApp</span>
</a>

                        {{-- Website --}}
                        @if($consultant->website)

                            <a
                                href="{{ $consultant->website }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 bg-white text-slate-900 px-5 py-3 rounded-xl font-semibold hover:bg-slate-100 transition"
                            >

                                Visit Website

                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10v-4M14 4h6m0 0v6m0-6L10 14"
                                    />
                                </svg>

                            </a>

                        @endif


                        <a
                            href="{{ route('consultants.index') }}"
                            class="mt-3 w-full inline-flex items-center justify-center px-5 py-3 rounded-xl font-semibold border border-slate-700 text-white hover:bg-slate-800 transition"
                        >
                            Browse Other Consultants
                        </a>

                    </div>


                    {{-- Contact / Address --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 mt-6 shadow-sm">

                        <h3 class="font-bold text-slate-900">
                            Location & Contact
                        </h3>


                        @if($consultant->city)

                            <div class="mt-4">

                                <div class="text-xs uppercase tracking-wide text-slate-400">
                                    City
                                </div>

                                <div class="mt-1 text-slate-700">
                                    {{ $consultant->city }}
                                </div>

                            </div>

                        @endif


                        @if($consultant->address)

                            <div class="mt-4">

                                <div class="text-xs uppercase tracking-wide text-slate-400">
                                    Address
                                </div>

                                <div class="mt-1 text-slate-700 leading-6">
                                    {{ $consultant->address }}
                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </section>


    {{-- Services --}}
    <section class="py-12 bg-white">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="max-w-3xl">

                <h2 class="text-2xl font-bold text-slate-900">
                    Services
                </h2>

                <p class="mt-2 text-slate-500">
                    Services listed for this consultant profile.
                </p>

            </div>


            @if(is_array($consultant->services) && count($consultant->services))

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-7">

                    @foreach($consultant->services as $service)

                        <div class="consultant-card bg-slate-50 border border-slate-200 rounded-xl p-5">

                            <div class="flex items-start gap-3">

                                <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 font-bold">
                                    âœ“
                                </div>

                                <div class="font-medium text-slate-800">
                                    {{ $service }}
                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="mt-6 bg-slate-50 border border-slate-200 rounded-xl p-5 text-slate-500">
                    No services have been listed yet.
                </div>

            @endif

        </div>

    </section>


    {{-- Destinations --}}
    <section class="py-12 bg-slate-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <h2 class="text-2xl font-bold text-slate-900">
                Study Destinations
            </h2>

            <p class="mt-2 text-slate-500">
                Destinations associated with this consultant profile.
            </p>


            @if(is_array($consultant->destinations) && count($consultant->destinations))

                <div class="flex flex-wrap gap-3 mt-7">

                    @foreach($consultant->destinations as $destination)

                        <span class="px-4 py-2 rounded-full bg-white border border-slate-200 text-slate-700 font-medium shadow-sm">
                            {{ $destination }}
                        </span>

                    @endforeach

                </div>

            @else

                <div class="mt-6 bg-white border border-slate-200 rounded-xl p-5 text-slate-500">
                    No study destinations have been listed yet.
                </div>

            @endif

        </div>

    </section>


    {{-- Public Reference --}}
    @if($consultant->reference_url)

        <section class="py-12 bg-white">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 md:p-8">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                        <div>

                            <div class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">
                                Public Reference
                            </div>

                            <h2 class="mt-2 text-xl font-bold text-slate-900">
                                Reference information
                            </h2>

                            <p class="mt-2 text-slate-600">
                                This profile includes a public reference source for additional information.
                            </p>

                        </div>


                        <a
                            href="{{ $consultant->reference_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center justify-center px-5 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition whitespace-nowrap"
                        >

                            {{ $consultant->reference_label ?: 'View Reference' }}

                            <svg
                                class="w-4 h-4 ml-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10v-4M14 4h6m0 0v6m0-6L10 14"
                                />
                            </svg>

                        </a>

                    </div>

                </div>

            </div>

        </section>

    @endif


    {{-- Bottom CTA --}}
    <section class="py-14 bg-slate-900">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            <h2 class="text-3xl font-bold text-white">
                Explore More Study Abroad Options
            </h2>

            <p class="mt-3 text-slate-300">
                Browse universities and consultant profiles through UniBridge.
            </p>


            <div class="mt-7 flex flex-col sm:flex-row gap-3 justify-center">

                <a
                    href="{{ route('consultants.index') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-slate-900 rounded-xl font-semibold hover:bg-slate-100 transition"
                >
                    Consultant Network
                </a>


                @auth

                    @if(auth()->user()->role === 'student')

                        @if($consultant->user_id)

                            <a
                                href="{{ route('student.bookings.create', ['consultant_id' => $consultant->user_id]) }}"
                                class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition"
                            >
                                Book Consultation
                            </a>

                        @else

                            <a
                                href="{{ route('student.universities.index') }}"
                                class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition"
                            >
                                Explore Universities
                            </a>

                        @endif

                    @elseif(auth()->user()->role === 'consultant')

                        <a
                            href="{{ route('consultant.dashboard') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition"
                        >
                            Consultant Dashboard
                        </a>

                    @elseif(auth()->user()->role === 'admin')

                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition"
                        >
                            Admin Dashboard
                        </a>

                    @endif

                @else

                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition"
                    >
                        Login to Continue
                    </a>

                @endauth

            </div>

        </div>

    </section>


    {{-- Footer --}}
    <footer class="bg-white border-t border-slate-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <div class="font-bold text-slate-900">
                        UniBridge
                    </div>

                    <div class="text-sm text-slate-500 mt-1">
                        Study abroad guidance and university discovery platform.
                    </div>

                </div>

                <div class="text-sm text-slate-500">
                    Consultant Network
                </div>

            </div>

        </div>

    </footer>

</body>
</html>
