<x-app-layout>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Main Hero --}}
            <div class="overflow-hidden rounded-3xl border border-indigo-100 bg-white shadow-sm">
                <div class="relative p-6 sm:p-8 lg:p-10">

                    <div class="absolute right-0 top-0 h-40 w-40 rounded-full bg-indigo-50 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 h-32 w-32 rounded-full bg-cyan-50 blur-3xl"></div>

                    <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                        <div class="max-w-3xl">

                            <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50 px-4 py-2 text-xs font-bold uppercase tracking-wider text-indigo-700">
                                <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                                Administration Panel
                            </div>

                            <h1 class="text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                                Welcome to UniBridge Admin
                            </h1>

                            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                                Manage users, students, consultants, universities, applications,
                                roles, and Google integrations from one central control center.
                            </p>

                        </div>

                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-5 lg:min-w-[230px]">

                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                Signed in as
                            </p>

                            <p class="mt-2 text-lg font-bold text-slate-900">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="text-sm font-semibold capitalize text-indigo-600">
                                {{ auth()->user()->role }}
                            </p>

                        </div>

                    </div>
                </div>
            </div>


            {{-- Statistics --}}
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Total Users --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-semibold text-slate-500">
                                Total Users
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ \App\Models\User::count() }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Registered platform accounts
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <span class="text-xl font-black">U</span>
                        </div>

                    </div>
                </div>


                {{-- Students --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-semibold text-slate-500">
                                Students
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ \App\Models\User::where('role', 'student')->count() }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Student accounts
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">
                            <span class="text-xl font-black">S</span>
                        </div>

                    </div>
                </div>


                {{-- Consultants --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-semibold text-slate-500">
                                Consultants
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ \App\Models\User::where('role', 'consultant')->count() }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Consultant accounts
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <span class="text-xl font-black">C</span>
                        </div>

                    </div>
                </div>


                {{-- Administrators --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-semibold text-slate-500">
                                Administrators
                            </p>

                            <p class="mt-2 text-3xl font-black text-slate-900">
                                {{ \App\Models\User::where('role', 'admin')->count() }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Administrator accounts
                            </p>
                        </div>

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <span class="text-xl font-black">A</span>
                        </div>

                    </div>
                </div>

            </div>


            {{-- User Management --}}
            <div class="mt-10">

                <div class="mb-5">
                    <h3 class="text-xl font-extrabold text-slate-900">
                        User Management
                    </h3>

                    <p class="mt-1 text-sm text-slate-600">
                        Manage students, consultants, administrators, and all registered users.
                    </p>
                </div>


                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

                    {{-- All Users --}}
                    <a href="{{ route('admin.users.index') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-indigo-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <span class="text-lg font-black">U</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            All Users
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            View and manage all registered platform users.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-indigo-600 group-hover:text-indigo-700">
                            Manage Users →
                        </span>

                    </a>


                    {{-- Students --}}
                    <a href="{{ route('admin.students') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-cyan-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">
                            <span class="text-lg font-black">S</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Students
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Review and manage registered students.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-cyan-600 group-hover:text-cyan-700">
                            View Students →
                        </span>

                    </a>


                    {{-- Consultants --}}
                    <a href="{{ route('admin.consultants') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-violet-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                            <span class="text-lg font-black">C</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Consultants
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Manage approved study-abroad consultants.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-violet-600 group-hover:text-violet-700">
                            View Consultants →
                        </span>

                    </a>


                    {{-- Administrators --}}
                    <a href="{{ route('admin.administrators') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-amber-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                            <span class="text-lg font-black">A</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Administrators
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Access administrator account management.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-amber-600 group-hover:text-amber-700">
                            Manage Admins →
                        </span>

                    </a>

                </div>
            </div>


            {{-- Platform Management --}}
            <div class="mt-10">

                <div class="mb-5">
                    <h3 class="text-xl font-extrabold text-slate-900">
                        Platform Management
                    </h3>

                    <p class="mt-1 text-sm text-slate-600">
                        Manage universities, applications, consultant profiles, and roles.
                    </p>
                </div>


                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

                    {{-- Universities --}}
                    <a href="{{ route('admin.universities.index') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <span class="text-lg font-black">U</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Universities
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Browse and manage university information.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-blue-600 group-hover:text-blue-700">
                            Manage Universities →
                        </span>

                    </a>


                    {{-- Applications --}}
                    <a href="{{ route('admin.applications') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <span class="text-lg font-black">A</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Applications
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Review and manage student university applications.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-emerald-600 group-hover:text-emerald-700">
                            Review Applications →
                        </span>

                    </a>


                    {{-- Consultant Profiles --}}
                    <a href="{{ route('admin.consultant-profiles') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-pink-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-50 text-pink-600">
                            <span class="text-lg font-black">P</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Consultant Profiles
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            View and manage consultant accounts and profiles.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-pink-600 group-hover:text-pink-700">
                            Manage Profiles →
                        </span>

                    </a>


                    {{-- Role Management --}}
                    <a href="{{ route('admin.role-management') }}"
                       class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:border-orange-300 hover:shadow-lg">

                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <span class="text-lg font-black">R</span>
                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Role Management
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Manage platform roles and access permissions.
                        </p>

                        <span class="mt-4 inline-flex text-sm font-bold text-orange-600 group-hover:text-orange-700">
                            Manage Roles →
                        </span>

                    </a>

                </div>
            </div>


            {{-- Google Integrations --}}
            <div class="mt-10">

                <div class="mb-5">
                    <h3 class="text-xl font-extrabold text-slate-900">
                        Google Integrations
                    </h3>

                    <p class="mt-1 text-sm text-slate-600">
                        Manage Google services used by UniBridge.
                    </p>
                </div>


                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                    {{-- Google Meet --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600">
                                <span class="text-lg font-black">M</span>
                            </div>

                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">
                                Consultant account required
                            </span>

                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Google Meet
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Each consultant must connect their own Google account before a confirmed booking can receive a Meet link. A connected account indicator alone does not guarantee Google API access is still valid.
                        </p>

                    </div>


                    {{-- Google Classroom --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                        <div class="flex items-start justify-between gap-4">

                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <span class="text-lg font-black">G</span>
                            </div>

                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                Connected
                            </span>

                        </div>

                        <h4 class="mt-5 text-lg font-bold text-slate-900">
                            Google Classroom
                        </h4>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Connect Google Classroom to access classroom course information.
                        </p>

                        <a href="{{ route('google.classroom.connect') }}"
                           class="mt-5 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700">
                            Reconnect Google Classroom
                        </a>

                    </div>

                </div>
            </div>


            {{-- Admin Notice --}}
            <div class="mt-10 rounded-2xl border border-amber-200 bg-amber-50 p-6">

                <div class="flex gap-4">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <span class="font-black">!</span>
                    </div>

                    <div>

                        <h3 class="font-bold text-amber-900">
                            Administrator Access
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-amber-800">
                            You are signed in with administrator privileges.
                            Changes made from this area can affect the entire UniBridge platform.
                        </p>

                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>