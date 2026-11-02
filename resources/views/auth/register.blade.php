<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

<title>
    @if ($portalRole === 'student')
        Student Registration — UniBridge
    @elseif ($portalRole === 'consultant')
        Consultant Registration — UniBridge
    @else
        Create Account — UniBridge
    @endif
</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|instrument-sans:400,500,600,700" rel="stylesheet">

<style>
    html, body {
        min-height: 100%;
    }

    body {
        font-family: 'Instrument Sans', sans-serif;
    }

    .font-display {
        font-family: 'Manrope', sans-serif;
    }

    .auth-bg {
        background:
            radial-gradient(circle at 15% 20%, rgba(37,99,235,.10), transparent 32%),
            radial-gradient(circle at 85% 80%, rgba(37,99,235,.07), transparent 30%),
            #f7faff;
        color: #14213d;
    }

    .grid-bg {
        background-image:
            linear-gradient(rgba(11,23,54,.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(11,23,54,.025) 1px, transparent 1px);
        background-size: 45px 45px;
    }

    .landing-auth [class*="text-white/"] { color: #64748b !important; }
    .landing-auth [class~="text-white"] { color: #14213d !important; }
    .landing-auth [class*="bg-white"] { background-color: #fff !important; }
    .landing-auth [class*="bg-black"] { background-color: rgba(255,255,255,.96) !important; }
    .landing-auth [class*="text-red-"],
    .landing-auth [class*="text-yellow-"] { color: #2563eb !important; }
    .landing-auth [class*="border-red-"],
    .landing-auth [class*="border-yellow-"] { border-color: #bfdbfe !important; }
    .landing-auth [class*="bg-red-"],
    .landing-auth [class*="bg-yellow-"] { background-color: #eff6ff !important; }
    .landing-auth button[class*="bg-red-"] { background-color: #2563eb !important; color: #fff !important; }
    .landing-auth [class*="shadow-red-"] { --tw-shadow-color: rgba(37,99,235,.18) !important; }
    .landing-auth [class*="bg-zinc-"] { background-color: #fbfcfe !important; color: #14213d !important; }
    .landing-auth header { border-color: #e5eaf2 !important; }
    .landing-auth header a:last-child { color: #64748b !important; }
    .landing-auth .auth-brand-mark {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: linear-gradient(135deg, #2563eb, #0b1736);
        color: #fff;
        box-shadow: 0 8px 20px rgba(37,99,235,.22);
    }
    .landing-auth .auth-brand-name { color: #0b1736; }
    .landing-auth .auth-brand-name span { color: #2563eb; }
</style>
</head>

<body class="auth-bg grid-bg landing-auth antialiased">

<div class="min-h-screen flex flex-col">

<!-- HEADER -->
<header class="border-b border-white/10 bg-black/70 backdrop-blur-xl">

    <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

        <div class="h-20 flex items-center justify-between">

            <a href="{{ url('/') }}"
               class="flex items-center gap-3">

                <div class="auth-brand-mark w-10 h-10 rounded-xl
                            flex items-center justify-center
                            font-display font-extrabold text-lg
                        shadow-lg">
                    🎓
                </div>

                <div>

                    <div class="auth-brand-name font-display font-extrabold text-xl">
                        Uni<span>Bridge</span>
                    </div>

                    <div class="text-[10px] uppercase
                                tracking-[.22em] text-white/35">
                        Global Education
                    </div>

                </div>

            </a>

            <a href="{{ url('/') }}"
               class="text-sm text-white/50
                      hover:text-white transition">
                ← Back to Home
            </a>

        </div>

    </div>

</header>


<!-- MAIN -->
<main class="flex-1 flex items-center justify-center px-5 py-12">

    <div class="w-full max-w-lg">


        @if (!$portalRole)

            <!-- REGISTRATION PORTAL SELECTOR -->

            <div class="text-center mb-8">

                <p class="text-yellow-400 text-xs font-bold
                          uppercase tracking-[.18em] mb-3">
                    Join UniBridge
                </p>

                <h1 class="font-display text-3xl sm:text-4xl
                           font-extrabold tracking-tight">
                    Create your account
                </h1>

                <p class="text-sm text-white/40 mt-3">
                    Choose the type of UniBridge account you need.
                </p>

            </div>


            <div class="space-y-4">

                <!-- STUDENT -->
                <a href="{{ route('register', ['role' => 'student']) }}"
                   class="block rounded-2xl border border-white/10
                          bg-white/[.04] p-5
                          hover:border-red-500/40
                          hover:bg-red-500/[.06]
                          transition">

                    <div class="flex items-center gap-4">

                        <div class="w-12 h-12 rounded-xl
                                    bg-red-600/10
                                    border border-red-500/20
                                    flex items-center justify-center
                                    text-red-400 text-xl">
                            🎓
                        </div>

                        <div class="flex-1">

                            <h2 class="font-display font-bold text-lg">
                                Student Registration
                            </h2>

                            <p class="text-sm text-white/40 mt-1">
                                Create an account to explore universities,
                                applications and study-abroad services.
                            </p>

                        </div>

                        <span class="text-white/30 text-xl">
                            →
                        </span>

                    </div>

                </a>


                <!-- CONSULTANT -->
                <a href="{{ route('register', ['role' => 'consultant']) }}"
                   class="block rounded-2xl border border-white/10
                          bg-white/[.04] p-5
                          hover:border-yellow-500/40
                          hover:bg-yellow-500/[.05]
                          transition">

                    <div class="flex items-center gap-4">

                        <div class="w-12 h-12 rounded-xl
                                    bg-yellow-500/10
                                    border border-yellow-500/20
                                    flex items-center justify-center
                                    text-yellow-400 text-xl">
                            👨‍💼
                        </div>

                        <div class="flex-1">

                            <h2 class="font-display font-bold text-lg">
                                Consultant Registration
                            </h2>

                            <p class="text-sm text-white/40 mt-1">
                                Create a consultant account to work with
                                students on UniBridge.
                            </p>

                        </div>

                        <span class="text-white/30 text-xl">
                            →
                        </span>

                    </div>

                </a>


                <!-- ADMIN -->
                <div class="rounded-2xl border border-white/10
                            bg-white/[.02] p-5 opacity-60">

                    <div class="flex items-center gap-4">

                        <div class="w-12 h-12 rounded-xl
                                    bg-blue-500/10
                                    border border-blue-500/20
                                    flex items-center justify-center
                                    text-blue-400 text-xl">
                            🛡️
                        </div>

                        <div class="flex-1">

                            <h2 class="font-display font-bold text-lg">
                                Admin
                            </h2>

                            <p class="text-sm text-white/40 mt-1">
                                Public admin registration is not available.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <div class="mt-7 text-center">

                <p class="text-sm text-white/40">
                    Already have an account?
                </p>

                <a href="{{ route('login') }}"
                   class="inline-block mt-2
                          font-bold text-yellow-400
                          hover:text-yellow-300 transition">
                    Log in
                </a>

            </div>


        @else

            <!-- REGISTRATION FORM -->

            <div class="text-center mb-8">

                <div class="mx-auto mb-5 w-16 h-16 rounded-2xl
                            bg-red-600/10
                            border border-red-500/20
                            flex items-center justify-center
                            text-3xl">

                    @if ($portalRole === 'student')
                        🎓
                    @else
                        👨‍💼
                    @endif

                </div>

                <p class="text-yellow-400 text-xs font-bold
                          uppercase tracking-[.18em] mb-3">
                    {{ ucfirst($portalRole) }} Registration
                </p>

                <h1 class="font-display text-3xl sm:text-4xl
                           font-extrabold tracking-tight">
                    Create your account
                </h1>

                <p class="text-sm text-white/40 mt-3">

                    @if ($portalRole === 'student')
                        Start your international education journey.
                    @else
                        Join UniBridge as an education consultant.
                    @endif

                </p>

            </div>


            <!-- ERRORS -->
            @if ($errors->any())

                <div class="mb-5 rounded-xl
                            border border-red-500/20
                            bg-red-500/10 px-4 py-3">

                    <ul class="text-sm text-red-300 space-y-1">

                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach

                    </ul>

                </div>

            @endif


            <!-- FORM -->
            <form method="POST"
                  action="{{ route('register') }}"
                  class="space-y-5">

                @csrf

                <input type="hidden"
                       name="role"
                       value="{{ $portalRole }}">


                <!-- NAME -->
                <div>

                    <label for="name"
                           class="block text-sm font-semibold
                                  text-white/75 mb-2">
                        Full Name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter your full name"
                        class="w-full rounded-xl
                               border border-white/10
                               bg-white/[.04]
                               px-4 py-3.5
                               text-white
                               placeholder-white/25
                               outline-none
                               focus:border-red-500/60
                               focus:ring-2
                               focus:ring-red-500/10
                               transition">

                </div>


                @if ($portalRole === 'student')

                    <!-- ROLL NUMBER -->
                    <div>

                        <label for="roll_number"
                               class="block text-sm font-semibold
                                      text-white/75 mb-2">
                            Roll Number
                        </label>

                        <input
                            id="roll_number"
                            type="text"
                            name="roll_number"
                            value="{{ old('roll_number') }}"
                            required
                            placeholder="e.g. S23-1128"
                            class="w-full rounded-xl
                                   border border-white/10
                                   bg-white/[.04]
                                   px-4 py-3.5
                                   text-white
                                   placeholder-white/25
                                   outline-none
                                   focus:border-red-500/60
                                   focus:ring-2
                                   focus:ring-red-500/10
                                   transition">

                    </div>


                    <!-- DEPARTMENT -->
                    <div>

                        <label for="department"
                               class="block text-sm font-semibold
                                      text-white/75 mb-2">
                            Department
                        </label>

                        <select
                            id="department"
                            name="department"
                            required
                            class="w-full rounded-xl
                                   border border-white/10
                                   bg-zinc-900
                                   px-4 py-3.5
                                   text-white
                                   outline-none
                                   focus:border-red-500/60
                                   focus:ring-2
                                   focus:ring-red-500/10
                                   transition">

                            <option value=""
                                    class="bg-zinc-900">
                                Select your department
                            </option>

                            <option value="Computer Science"
                                @selected(old('department') === 'Computer Science')
                                class="bg-zinc-900">
                                Computer Science
                            </option>

                            <option value="Software Engineering"
                                @selected(old('department') === 'Software Engineering')
                                class="bg-zinc-900">
                                Software Engineering
                            </option>

                            <option value="Information Technology"
                                @selected(old('department') === 'Information Technology')
                                class="bg-zinc-900">
                                Information Technology
                            </option>

                            <option value="Business Administration"
                                @selected(old('department') === 'Business Administration')
                                class="bg-zinc-900">
                                Business Administration
                            </option>

                        </select>

                    </div>

                @else

                    <!-- CONSULTANT NOTICE -->

                    <div class="rounded-xl
                                border border-yellow-500/20
                                bg-yellow-500/5
                                px-4 py-4">

                        <p class="text-sm font-semibold text-yellow-300">
                            Consultant approval
                        </p>

                        <p class="text-xs text-white/45 mt-1 leading-5">
                            After email verification, your consultant
                            account will remain pending until an
                            administrator approves it.
                        </p>

                    </div>

                @endif


                <!-- EMAIL -->
                <div>

                    <label for="email"
                           class="block text-sm font-semibold
                                  text-white/75 mb-2">
                        Email Address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        placeholder="Enter your email"
                        class="w-full rounded-xl
                               border border-white/10
                               bg-white/[.04]
                               px-4 py-3.5
                               text-white
                               placeholder-white/25
                               outline-none
                               focus:border-red-500/60
                               focus:ring-2
                               focus:ring-red-500/10
                               transition">

                </div>


                <!-- PASSWORD -->
                <div>

                    <label for="password"
                           class="block text-sm font-semibold
                                  text-white/75 mb-2">
                        Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Create a strong password"
                        class="w-full rounded-xl
                               border border-white/10
                               bg-white/[.04]
                               px-4 py-3.5
                               text-white
                               placeholder-white/25
                               outline-none
                               focus:border-red-500/60
                               focus:ring-2
                               focus:ring-red-500/10
                               transition">

                </div>


                <!-- CONFIRM PASSWORD -->
                <div>

                    <label for="password_confirmation"
                           class="block text-sm font-semibold
                                  text-white/75 mb-2">
                        Confirm Password
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                        class="w-full rounded-xl
                               border border-white/10
                               bg-white/[.04]
                               px-4 py-3.5
                               text-white
                               placeholder-white/25
                               outline-none
                               focus:border-red-500/60
                               focus:ring-2
                               focus:ring-red-500/10
                               transition">

                </div>


                <!-- REGISTER BUTTON -->
                <button
                    type="submit"
                    class="w-full inline-flex
                           items-center justify-center
                           gap-2 rounded-xl
                           bg-red-600
                           px-6 py-3.5
                           font-display font-extrabold
                           text-white
                           shadow-lg shadow-red-600/20
                           hover:bg-red-500
                           active:scale-[.99]
                           transition">

                    Create
                    {{ ucfirst($portalRole) }}
                    Account

                    <span>→</span>

                </button>

            </form>


            <!-- LOGIN -->
            <div class="mt-7 pt-6
                        border-t border-white/10
                        text-center">

                <p class="text-sm text-white/40">
                    Already have an account?
                </p>

                <a href="{{ route('login', ['role' => $portalRole]) }}"
                   class="inline-block mt-2
                          font-bold text-yellow-400
                          hover:text-yellow-300 transition">

                    Log in as {{ ucfirst($portalRole) }}

                </a>

            </div>


            <!-- CHANGE PORTAL -->
            <div class="mt-5 text-center">

                <a href="{{ route('register') }}"
                   class="text-xs font-semibold
                          text-white/35
                          hover:text-white/70 transition">

                    ← Choose a different account type

                </a>

            </div>

        @endif


        <!-- FOOTER -->
        <p class="text-center text-xs text-white/25 mt-7">
            © {{ date('Y') }} UniBridge · Global Education Platform
        </p>

    </div>

</main>

</div>

</body>
</html>