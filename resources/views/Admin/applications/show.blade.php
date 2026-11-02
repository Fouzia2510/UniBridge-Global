<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $consultant->company }} | UniBridge Consultant Network</title>

    <meta
        name="description"
        content="{{ $consultant->short_description ?: 'Explore consultant services and study abroad guidance through the UniBridge Consultant Network.' }}"
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

        .profile-card {
            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                border-color 0.25s ease;
        }

        .profile-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
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
         BREADCRUMB
    ========================================================== -->

    <section class="bg-white border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">

            <div class="flex items-center gap-2 text-sm">

                <a
                    href="{{ route('consultants.index') }}"
                    class="text-slate-500 hover:text-blue-600 transition"
                >
                    Consultants
                </a>

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
                        d="m9 18 6-6-6-6"
                    />
                </svg>

                <span class="text-slate-900 font-medium">
                    {{ $consultant->company }}
                </span>

            </div>

        </div>

    </section>


    <!-- =========================================================
         PROFILE HERO
    ========================================================== -->

    <section class="hero-grid bg-white border-b border-slate-200">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">

            <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-10 items-start">

                <!-- Profile Information -->

                <div>

                    <div class="flex flex-col sm:flex-row items-start gap-6">

                        <!-- Logo -->

                        <div
                            class="logo-box w-24 h-24 rounded-3xl flex items-center justify-center flex-shrink-0 border border-blue-100"
                        >

                            @if($consultant->logo)

                                <img
                                    src="{{ asset('storage/' . $consultant->logo) }}"
                                    alt="{{ $consultant->company }}"
                                    class="w-full h-full object-contain rounded-3xl"
                                >

                            @else

                                <span class="text-4xl font-extrabold text-blue-700">
                                    {{ strtoupper(substr($consultant->company, 0, 1)) }}
                                </span>

                            @endif

                        </div>


                        <!-- Name -->

                        <div>

                            <div class="flex flex-wrap items-center gap-3">

                                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-950">
                                    {{ $consultant->company }}
                                </h1>

                                @if($consultant->is_featured)

                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-100 text-amber-700 text-xs font-bold">

                                        <svg
                                            class="w-4 h-4"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.922-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.176 0l-2.802 2.036c-.784.57-1.838-.196-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.98 8.72c-.783-.57-.38-1.81.588-1.81H8.03a1 1 0 00.95-.69l1.07-3.292z"/>
                                        </svg>

                                        Featured

                                    </span>

                                @endif

                            </div>


                            @if($consultant->name && $consultant->name !== $consultant->company)

                                <p class="mt-2 text-lg text-slate-500">
                                    {{ $consultant->name }}
                                </p>

                            @endif


                            @if($consultant->city)

                                <div class="mt-4 flex items-center gap-2 text-sm text-slate-500">

                                    <svg
                                        class="w-5 h-5 text-blue-600"
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

                                    <span>
                                        {{ $consultant->city }}
                                    </span>

                                </div>

                            @endif

                        </div>

                    </div>


                    <!-- Description -->

                    @if($consultant->short_description)

                        <div class="mt-8 max-w-3xl">

                            <p class="text-lg text-slate-600 leading-8">
                                {{ $consultant->short_description }}
                            </p>

                        </div>

                    @endif


                    <!-- Quick Stats -->

                    <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 gap-4 max-w-3xl">

                        <div class="bg-white border border-slate-200 rounded-2xl p-5">

                            <div class="text-2xl font-extrabold text-slate-950">
                                {{ count($consultant->services ?? []) }}
                            </div>

                            <div class="mt-1 text-sm text-slate-500">
                                Services
                            </div>

                        </div>


                        <div class="bg-white border border-slate-200 rounded-2xl p-5">

                            <div class="text-2xl font-extrabold text-slate-950">
                                {{ count($consultant->destinations ?? []) }}
                            </div>

                            <div class="mt-1 text-sm text-slate-500">
                                Destinations
                            </div>

                        </div>


                        <div class="bg-white border border-slate-200 rounded-2xl p-5 col-span-2 sm:col-span-1">

                            <div class="text-2xl font-extrabold text-blue-600">
                                {{ $consultant->city ?: '—' }}
                            </div>

                            <div class="mt-1 text-sm text-slate-500">
                                Location
                            </div>

                        </div>

                    </div>

                </div>


                <!-- Contact Card -->

                <div class="bg-slate-950 rounded-3xl p-7 text-white shadow-xl">

                    <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">
                        Consultant Profile
                    </p>

                    <h2 class="mt-3 text-2xl font-extrabold">
                        Need guidance for your study abroad plan?
                    </h2>

                    <p class="mt-4 text-sm text-slate-400 leading-6">
                        Explore this consultant's services and destinations,
                        then contact them through their listed website.
                    </p>


                    @if($consultant->website)

                        <a
                            href="{{ $consultant->website }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-7 w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition"
                        >

                            Visit Website

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
                                    d="M14 5h5v5M19 5l-8 8"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 14v4a1 1 0 01-1 1H6a1 1 0 01-1-1V6a1 1 0 011-1h4"
                                />
                            </svg>

                        </a>

                    @endif


                    <a
                        href="{{ route('consultants.index') }}"
                        class="mt-3 w-full inline-flex items-center justify-center px-5 py-3.5 rounded-xl bg-white/10 text-white text-sm font-semibold hover:bg-white/15 transition border border-white/10"
                    >
                        Browse Other Consultants
                    </a>

                </div>

            </div>

        </div>

    </section>


    <!-- =========================================================
         MAIN PROFILE CONTENT
    ========================================================== -->

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-8">


            <!-- LEFT -->

            <div class="space-y-8">


                <!-- Services -->

                <section class="profile-card bg-white border border-slate-200 rounded-3xl p-7">

                    <div>

                        <p class="text-sm font-bold text-blue-600 uppercase tracking-wider">
                            What they offer
                        </p>

                        <h2 class="mt-2 text-2xl font-extrabold text-slate-950">
                            Consultant Services
                        </h2>

                    </div>


                    @if(!empty($consultant->services))

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                            @foreach($consultant->services as $service)

                                <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">

                                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">

                                        <svg
                                            class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>

                                    </div>

                                    <div class="pt-1">

                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $service }}
                                        </p>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <p class="mt-6 text-slate-500">
                            Service information is currently unavailable.
                        </p>

                    @endif

                </section>


                <!-- Destinations -->

                <section class="profile-card bg-white border border-slate-200 rounded-3xl p-7">

                    <p class="text-sm font-bold text-blue-600 uppercase tracking-wider">
                        Study destinations
                    </p>

                    <h2 class="mt-2 text-2xl font-extrabold text-slate-950">
                        Destinations
                    </h2>

                    <p class="mt-2 text-slate-500">
                        Countries listed for this consultant's study abroad services.
                    </p>


                    @if(!empty($consultant->destinations))

                        <div class="mt-6 flex flex-wrap gap-3">

                            @foreach($consultant->destinations as $destination)

                                <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 text-sm font-semibold">

                                    <svg
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="9"
                                        />

                                        <path
                                            stroke-linecap="round"
                                            d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"
                                        />
                                    </svg>

                                    {{ $destination }}

                                </span>

                            @endforeach

                        </div>

                    @else

                        <p class="mt-6 text-slate-500">
                            Destination information is currently unavailable.
                        </p>

                    @endif

                </section>


                <!-- Location -->

                <section class="profile-card bg-white border border-slate-200 rounded-3xl p-7">

                    <p class="text-sm font-bold text-blue-600 uppercase tracking-wider">
                        Location
                    </p>

                    <h2 class="mt-2 text-2xl font-extrabold text-slate-950">
                        Consultant Office
                    </h2>


                    <div class="mt-6 flex items-start gap-4">

                        <div class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0">

                            <svg
                                class="w-5 h-5 text-slate-600"
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

                        </div>


                        <div>

                            @if($consultant->address)

                                <p class="font-semibold text-slate-900">
                                    {{ $consultant->address }}
                                </p>

                            @elseif($consultant->city)

                                <p class="font-semibold text-slate-900">
                                    {{ $consultant->city }}, Pakistan
                                </p>

                            @else

                                <p class="font-semibold text-slate-900">
                                    Location information unavailable
                                </p>

                            @endif


                            @if($consultant->city)

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $consultant->city }}
                                </p>

                            @endif

                        </div>

                    </div>

                </section>


                <!-- Reference -->

                @if($consultant->reference_url)

                    <section class="profile-card bg-white border border-slate-200 rounded-3xl p-7">

                        <p class="text-sm font-bold text-blue-600 uppercase tracking-wider">
                            Public reference
                        </p>

                        <h2 class="mt-2 text-2xl font-extrabold text-slate-950">
                            Source & Reference
                        </h2>

                        <p class="mt-3 text-slate-500 leading-7">
                            This profile includes publicly available information
                            from the reference listed below.
                        </p>


                        <a
                            href="{{ $consultant->reference_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-blue-50 hover:text-blue-700 transition"
                        >

                            {{ $consultant->reference_label ?: 'View Reference' }}

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
                                    d="M14 5h5v5M19 5l-8 8"
                                />
                            </svg>

                        </a>

                    </section>

                @endif

            </div>


            <!-- RIGHT SIDEBAR -->

            <aside class="space-y-6">


                <!-- Contact -->

                <div class="bg-white border border-slate-200 rounded-3xl p-6">

                    <p class="text-sm font-bold text-slate-900">
                        Interested in this consultant?
                    </p>

                    <p class="mt-2 text-sm text-slate-500 leading-6">
                        Visit the consultant's listed website for current
                        contact details and consultation options.
                    </p>


                    @if($consultant->website)

                        <a
                            href="{{ $consultant->website }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition"
                        >
                            Contact Consultant
                        </a>

                    @endif

                </div>


                <!-- Profile Summary -->

                <div class="bg-white border border-slate-200 rounded-3xl p-6">

                    <p class="text-sm font-bold text-slate-900">
                        Profile Summary
                    </p>


                    <div class="mt-5 space-y-4">

                        <div class="flex items-center justify-between gap-4">

                            <span class="text-sm text-slate-500">
                                Location
                            </span>

                            <span class="text-sm font-semibold text-slate-900 text-right">
                                {{ $consultant->city ?: 'Not listed' }}
                            </span>

                        </div>


                        <div class="h-px bg-slate-100"></div>


                        <div class="flex items-center justify-between gap-4">

                            <span class="text-sm text-slate-500">
                                Services
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                {{ count($consultant->services ?? []) }}
                            </span>

                        </div>


                        <div class="h-px bg-slate-100"></div>


                        <div class="flex items-center justify-between gap-4">

                            <span class="text-sm text-slate-500">
                                Destinations
                            </span>

                            <span class="text-sm font-semibold text-slate-900">
                                {{ count($consultant->destinations ?? []) }}
                            </span>

                        </div>

                    </div>

                </div>


                <!-- Back -->

                <a
                    href="{{ route('consultants.index') }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-blue-600 transition"
                >

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
                            d="M19 12H5M11 18l-6-6 6-6"
                        />
                    </svg>

                    Back to Consultant Directory

                </a>

            </aside>

        </div>

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
                    Plan your international education journey with confidence.
                </h2>

                <p class="mt-4 text-slate-400 leading-7">
                    Explore universities, compare destinations and find
                    consultant guidance for your study abroad plans.
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
                    © {{ date('Y') }} UniBridge. All rights reserved.
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