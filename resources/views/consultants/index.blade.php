<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consultant Network | UniBridge</title>

    <meta
        name="description"
        content="Explore education consultants and study abroad advisors through the UniBridge Consultant Network."
    >

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f8fafc;
        }

        .hero-grid {
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15, 23, 42, 0.04) 1px, transparent 1px);
            background-size: 36px 36px;
        }

        .consultant-card {
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .consultant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.10);
            border-color: #bfdbfe;
        }

        .logo-box {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
        }
    </style>
</head>

<body class="text-slate-900">

    <!-- =========================================================
         NAVBAR
    ========================================================== -->

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="h-20 flex items-center justify-between">

                <!-- Logo -->

                <a href="{{ url('/') }}" class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-sm">

                        <svg
                            class="w-6 h-6 text-white"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5z"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 12v5c3 2 5 3 7 3s4-1 7-3v-5"
                            />
                        </svg>

                    </div>

                    <div>
                        <div class="text-xl font-extrabold tracking-tight">
                            Uni<span class="text-blue-600">Bridge</span>
                        </div>

                        <div class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-semibold">
                            Education Platform
                        </div>
                    </div>

                </a>


                <!-- Desktop Navigation -->

                <div class="hidden md:flex items-center gap-8">

                    <a
                        href="{{ url('/') }}"
                        class="text-sm font-medium text-slate-600 hover:text-blue-600 transition"
                    >
                        Home
                    </a>

                    <a
                        href="{{ url('/#destinations') }}"
                        class="text-sm font-medium text-slate-600 hover:text-blue-600 transition"
                    >
                        Destinations
                    </a>

                    <a
                        href="{{ url('/#universities') }}"
                        class="text-sm font-medium text-slate-600 hover:text-blue-600 transition"
                    >
                        Universities
                    </a>

                    <a
                        href="{{ route('consultants.index') }}"
                        class="text-sm font-semibold text-blue-600"
                    >
                        Consultants
                    </a>

                    <a
                        href="{{ url('/#how-it-works') }}"
                        class="text-sm font-medium text-slate-600 hover:text-blue-600 transition"
                    >
                        How It Works
                    </a>

                </div>


                <!-- Auth -->

                <div class="flex items-center gap-3">

                    @auth

                        <a
                            href="{{ route('dashboard') }}"
                            class="hidden sm:inline-flex items-center px-4 py-2.5 rounded-lg bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition"
                        >
                            Dashboard
                        </a>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="hidden sm:inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-100 transition"
                        >
                            Sign In
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition shadow-sm"
                        >
                            Get Started
                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </nav>


    <!-- =========================================================
         HERO
    ========================================================== -->

    <section class="hero-grid bg-white border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">

            <div class="max-w-3xl">

                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider mb-6"
                >

                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>

                    UniBridge Consultant Network

                </div>


                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-950 leading-tight">

                    Find the right
                    <span class="text-blue-600">study abroad consultant</span>
                    for your journey.

                </h1>


                <p class="mt-6 text-lg text-slate-600 leading-8 max-w-2xl">

                    Explore education consultants, compare their services and
                    destinations, and find professional guidance for your
                    international education plans.

                </p>


                <!-- Search -->

                <form
    method="GET"
    action="{{ route('consultants.index') }}"
    class="mt-8"
>
    <div class="bg-white border border-slate-200 rounded-2xl p-2 shadow-lg shadow-slate-200/50">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-[1fr_190px_190px_auto] gap-2">

            <!-- Search -->
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search consultant or company..."
                class="w-full px-4 py-3.5 rounded-xl border-0 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm"
            >

            <!-- Country -->
            <select
                name="country"
                class="w-full px-4 py-3.5 rounded-xl border-0 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm text-slate-700"
            >
                <option value="">All Countries</option>

                @foreach($countries as $country)
                    <option
                        value="{{ $country }}"
                        @selected(request('country') === $country)
                    >
                        {{ $country }}
                    </option>
                @endforeach
            </select>

            <!-- City -->
            <select
                name="city"
                class="w-full px-4 py-3.5 rounded-xl border-0 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none text-sm text-slate-700"
            >
                <option value="">All Cities</option>

                @foreach($cities as $city)
                    <option
                        value="{{ $city }}"
                        @selected(request('city') === $city)
                    >
                        {{ $city }}
                    </option>
                @endforeach
            </select>

            <!-- Search Button -->
            <button
                type="submit"
                class="px-6 py-3.5 rounded-xl bg-blue-600 text-white font-semibold text-sm hover:bg-blue-700 transition"
            >
                Search Consultants
            </button>

        </div>
    </div>
</form>

            </div>

        </div>

    </section>


    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        <!-- Heading -->

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-8">

            <div>

                <p class="text-sm font-bold text-blue-600 uppercase tracking-wider">
                    Consultant Directory
                </p>

                <h2 class="mt-2 text-3xl font-extrabold text-slate-950">
                    Education Consultants
                </h2>

                <p class="mt-2 text-slate-500">
                    Browse consultants available through the UniBridge network.
                </p>

            </div>


            <div class="text-sm text-slate-500">

                Showing

                <span class="font-bold text-slate-900">
                    {{ $consultants->total() }}
                </span>

                consultant{{ $consultants->total() === 1 ? '' : 's' }}

            </div>

        </div>


        @if(request('search') || request('country') || request('city'))

            <!-- Active Filters -->
            <div class="mb-8 flex flex-wrap items-center gap-2">
                <span class="text-sm text-slate-500">Active filters:</span>

                @if(request('search'))
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-medium border border-blue-100">
                        Search: {{ request('search') }}
                    </span>
                @endif

                @if(request('country'))
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-medium border border-blue-100">
                        Country: {{ request('country') }}
                    </span>
                @endif

                @if(request('city'))
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-medium border border-blue-100">
                        City: {{ request('city') }}
                    </span>
                @endif

                <a href="{{ route('consultants.index') }}"
                   class="text-sm font-semibold text-slate-600 hover:text-blue-600 ml-1">
                    Clear filters
                </a>
            </div>

        @endif



        <!-- =====================================================
             CONSULTANT GRID
        ====================================================== -->

        @if($consultants->count())

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach($consultants as $consultant)

                    <article
                        class="consultant-card bg-white border border-slate-200 rounded-2xl overflow-hidden"
                    >

                        <!-- Top -->

                        <div class="p-6">

                            <div class="flex items-start justify-between gap-4">

                                <!-- Logo -->

                                <div
                                    class="logo-box w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0"
                                >

                                    @if($consultant->logo)

                                        <img
                                            src="{{ asset('storage/' . $consultant->logo) }}"
                                            alt="{{ $consultant->company }}"
                                            class="w-full h-full object-contain rounded-2xl"
                                        >

                                    @else

                                        <span class="text-2xl font-extrabold text-blue-700">
                                            {{ strtoupper(substr($consultant->company, 0, 1)) }}
                                        </span>

                                    @endif

                                </div>


                                <!-- Featured -->

                                @if($consultant->is_featured)

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-100 text-amber-700 text-xs font-bold">

                                        <svg
                                            class="w-3.5 h-3.5"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.922-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.176 0l-2.802 2.036c-.784.57-1.838-.196-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.98 8.72c-.783-.57-.38-1.81.588-1.81H8.03a1 1 0 00.95-.69l1.07-3.292z"/>
                                        </svg>

                                        Featured

                                    </span>

                                @endif

                            </div>


                            <!-- Name -->

                            <div class="mt-5">

                                <h3 class="text-xl font-bold text-slate-950">
                                    {{ $consultant->company }}
                                </h3>

                                @if($consultant->name !== $consultant->company)

                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $consultant->name }}
                                    </p>

                                @endif

                            </div>


                            <!-- Location -->

                            @if($consultant->city)

                                <div class="mt-4 flex items-center gap-2 text-sm text-slate-500">

                                    <svg
                                        class="w-4 h-4 text-slate-400"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 21s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z"
                                        />

                                        <circle
                                            cx="12"
                                            cy="11"
                                            r="2.5"
                                        />
                                    </svg>

                                    {{ $consultant->city }}

                                </div>

                            @endif


                            <!-- Description -->

                            <p class="mt-4 text-sm text-slate-600 leading-6">

                                {{ \Illuminate\Support\Str::limit($consultant->short_description, 145) }}

                            </p>


                            <!-- Services -->

                            @if(!empty($consultant->services))

                                <div class="mt-5 flex flex-wrap gap-2">

                                    @foreach(array_slice($consultant->services, 0, 3) as $service)

                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium">
                                            {{ $service }}
                                        </span>

                                    @endforeach

                                    @if(count($consultant->services) > 3)

                                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold">
                                            +{{ count($consultant->services) - 3 }}
                                        </span>

                                    @endif

                                </div>

                            @endif

                        </div>


                        <!-- Bottom -->

                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">

                            <div class="flex items-center justify-between gap-4">

                                <div class="text-xs text-slate-500">

                                    <span class="font-semibold text-slate-700">
                                        Destinations
                                    </span>

                                    <div class="mt-1">
                                        {{ count($consultant->destinations ?? []) }} available
                                    </div>

                                </div>


                                <a
                                    href="{{ route('consultants.show', $consultant) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-blue-600 transition"
                                >

                                    View Profile

                                    <svg
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 12h14M13 6l6 6-6 6"
                                        />
                                    </svg>

                                </a>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>


            <!-- =================================================
                 PAGINATION
            ================================================== -->

            @if($consultants->hasPages())

                <div class="mt-10">

                    {{ $consultants->links() }}

                </div>

            @endif

        @else

            <!-- Empty State -->

            <div class="bg-white border border-slate-200 rounded-2xl p-12 text-center">

                <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center">

                    <svg
                        class="w-8 h-8 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                        />

                        <path
                            stroke-linecap="round"
                            d="m20 20-4-4"
                        />
                    </svg>

                </div>


                <h3 class="mt-5 text-xl font-bold text-slate-900">
                    No consultants found
                </h3>


                <p class="mt-2 text-slate-500 max-w-md mx-auto">
                    We couldn't find any consultants matching your search.
                    Try another company name or city.
                </p>


                <a
                    href="{{ route('consultants.index') }}"
                    class="inline-flex mt-6 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition"
                >
                    View All Consultants
                </a>

            </div>

        @endif

    </main>


    <!-- =========================================================
         CTA
    ========================================================== -->

    <section class="bg-slate-950">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

            <div class="max-w-3xl">

                <p class="text-blue-400 text-sm font-bold uppercase tracking-wider">
                    Need guidance?
                </p>

                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-white">
                    Make your international education plan with confidence.
                </h2>

                <p class="mt-4 text-slate-400 leading-7">
                    Explore universities, check your eligibility and connect
                    with the right guidance for your study abroad journey.
                </p>


                <div class="mt-7 flex flex-wrap gap-3">

                    @auth

                        <a
                            href="{{ route('student.universities.index') }}"
                            class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition"
                        >
                            Explore Universities
                        </a>

                    @else

                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center px-5 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition"
                        >
                            Create Free Account
                        </a>

                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center px-5 py-3 rounded-xl bg-white/10 text-white text-sm font-semibold hover:bg-white/15 transition border border-white/10"
                        >
                            Sign In
                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         FOOTER
    ========================================================== -->

    <footer class="bg-slate-950 border-t border-white/10">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <div class="flex flex-col md:flex-row items-center justify-between gap-4">

                <div class="text-sm text-slate-500">
                    Â© {{ date('Y') }} UniBridge. All rights reserved.
                </div>

                <div class="flex items-center gap-6 text-sm">

                    <a
                        href="{{ url('/') }}"
                        class="text-slate-400 hover:text-white transition"
                    >
                        Home
                    </a>

                    <a
                        href="{{ route('consultants.index') }}"
                        class="text-slate-400 hover:text-white transition"
                    >
                        Consultants
                    </a>

                    <a
                        href="{{ route('login') }}"
                        class="text-slate-400 hover:text-white transition"
                    >
                        Sign In
                    </a>

                </div>

            </div>

        </div>

    </footer>

</body>
</html>

