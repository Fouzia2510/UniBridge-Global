<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'UniBridge') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">

<div x-data="{ profileOpen: false }" class="min-h-screen w-full min-w-0">
    <header class="border-b border-slate-200 bg-white">
        <div class="flex min-h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-4">

                {{-- PORTAL CONTENT MENU --}}
                <div class="relative shrink-0" x-data="{ contentOpen: false }">
                    <button
                        type="button"
                        @click="contentOpen = !contentOpen"
                        @keydown.escape.window="contentOpen = false"
                        :aria-expanded="contentOpen.toString()"
                        aria-label="Open portal content"
                        title="Portal Content"
                        class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-2xl font-bold text-slate-700 shadow-sm transition hover:bg-slate-100">
                        ⋮
                    </button>

                    <div
                        x-cloak
                        x-show="contentOpen"
                        @click.outside="contentOpen = false"
                        @keydown.escape.window="contentOpen = false"
                        x-transition
                        class="absolute left-0 top-14 z-[120] w-72 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                        <div class="border-b border-slate-200 bg-slate-950 px-5 py-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-300">
                                UniBridge Navigation
                            </p>
                            <h3 class="mt-1 text-lg font-black text-white">Portal Content</h3>
                            <p class="mt-1 text-sm text-slate-300">Quick access to your pages</p>
                        </div>

                        <div class="max-h-[65vh] space-y-1 overflow-y-auto p-3">

                            {{-- ADMIN MENU --}}
                            @if(auth()->user()->role === 'admin')

                                <a href="{{ route('admin.dashboard') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ⌂ Admin Dashboard
                                </a>

                                <a href="{{ route('notifications.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🔔 Notifications
                                </a>

                                <a href="{{ route('meeting-attendance.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🕒 Meeting Join Activity
                                </a>

                                <a href="{{ route('meeting-attendance.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🕒 Meeting Join Activity
                                </a>

                                {{-- WHATSAPP PAGES --}}
                                @if(request()->routeIs('admin.whatsapp.*'))

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        WhatsApp
                                    </p>

                                    <a href="{{ route('admin.whatsapp.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        💬 WhatsApp Contact Groups
                                    </a>

                                    <a href="{{ route('admin.whatsapp.chats') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📨 Chats
                                    </a>

                                    <a href="{{ route('admin.whatsapp.groups') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        👥 Groups
                                    </a>

                                    <a href="{{ route('admin.whatsapp.broadcasts') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📢 Broadcasts
                                    </a>

                                {{-- USERS AND ROLE MANAGEMENT --}}
                                @elseif(
                                    request()->routeIs('admin.users.*')
                                    || request()->routeIs('admin.students')
                                    || request()->routeIs('admin.consultants')
                                    || request()->routeIs('admin.administrators')
                                    || request()->routeIs('admin.role-management')
                                )

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        User Management
                                    </p>

                                    <a href="{{ route('admin.users.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        👥 All Users
                                    </a>

                                    <a href="{{ route('admin.students') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🎓 Students
                                    </a>

                                    <a href="{{ route('admin.consultants') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        ♙ Consultants
                                    </a>

                                    <a href="{{ route('admin.administrators') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🛡 Administrators
                                    </a>

                                    <a href="{{ route('admin.role-management') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        ⚙ Role Management
                                    </a>

                                {{-- APPLICATIONS --}}
                                @elseif(request()->routeIs('admin.applications*'))

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Applications
                                    </p>

                                    <a href="{{ route('admin.applications') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📄 All Applications
                                    </a>

                                    <a href="{{ route('admin.visa.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🛂 Visa Applications
                                    </a>

                                    <a href="{{ route('admin.documents.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📁 Document Verification
                                    </a>

                                {{-- UNIVERSITIES --}}
                                @elseif(request()->routeIs('admin.universities.*'))

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Universities
                                    </p>

                                    <a href="{{ route('admin.universities.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🏫 Manage Universities
                                    </a>

                                {{-- VISA APPLICATIONS --}}
                                @elseif(request()->routeIs('admin.visa.*'))

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Visa Management
                                    </p>

                                    <a href="{{ route('admin.visa.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🛂 Visa Applications
                                    </a>

                                    <a href="{{ route('admin.applications') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📄 Applications
                                    </a>

                                {{-- DOCUMENT VERIFICATION --}}
                                @elseif(request()->routeIs('admin.documents.*'))

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Documents
                                    </p>

                                    <a href="{{ route('admin.documents.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📁 Document Verification
                                    </a>

                                    <a href="{{ route('admin.applications') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📄 Applications
                                    </a>

                                {{-- CONSULTANT PROFILES --}}
                                @elseif(request()->routeIs('admin.consultant-profiles*'))

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Consultant Profiles
                                    </p>

                                    <a href="{{ route('admin.consultant-profiles') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📋 Consultant Profiles
                                    </a>

                                {{-- INTERNAL GROUPS --}}
                                @elseif(
                                    request()->routeIs('admin.chat-groups.*')
                                    || request()->routeIs('chat-groups.*')
                                )

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Internal Communication
                                    </p>

                                    <a href="{{ route('chat-groups.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        👥 Internal Group Chats
                                    </a>

                                    <a href="{{ route('admin.chat-groups.create') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        ➕ Create Internal Group
                                    </a>

                                {{-- DEFAULT ADMIN PAGES --}}
                                @else

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Administration
                                    </p>

                                    <a href="{{ route('admin.users.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        👥 All Users
                                    </a>

                                    <a href="{{ route('admin.students') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🎓 Students
                                    </a>

                                    <a href="{{ route('admin.consultants') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        ♙ Consultants
                                    </a>

                                    <a href="{{ route('admin.administrators') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🛡 Administrators
                                    </a>

                                    <a href="{{ route('admin.applications') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📄 Applications
                                    </a>

                                    <a href="{{ route('admin.universities.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🏫 Universities
                                    </a>

                                    <a href="{{ route('admin.visa.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🛂 Visa Applications
                                    </a>

                                    <a href="{{ route('admin.documents.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📁 Document Verification
                                    </a>

                                    <a href="{{ route('admin.consultant-profiles') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📋 Consultant Profiles
                                    </a>

                                    <a href="{{ route('admin.role-management') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        ⚙ Role Management
                                    </a>

                                    {{-- DEFAULT MENU: WHATSAPP LINKS --}}
                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        WhatsApp
                                    </p>

                                    <a href="{{ route('admin.whatsapp.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        💬 WhatsApp Contact Groups
                                    </a>

                                    <a href="{{ route('admin.whatsapp.chats') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        💬 WhatsApp Chats
                                    </a>

                                    <a href="{{ route('admin.whatsapp.groups') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        👥 WhatsApp Groups
                                    </a>

                                    <a href="{{ route('admin.whatsapp.broadcasts') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        📢 WhatsApp Broadcasts
                                    </a>

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Other Portals
                                    </p>

                                    <a href="{{ route('student.dashboard') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        🎓 Student Portal
                                    </a>

                                    <a href="{{ route('consultant.dashboard') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        ♙ Consultant Portal
                                    </a>

                                    <p class="px-3 pb-1 pt-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Internal Communication
                                    </p>

                                    <a href="{{ route('chat-groups.index') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        👥 Internal Group Chats
                                    </a>

                                    <a href="{{ route('admin.chat-groups.create') }}"
                                       class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        ➕ Create Internal Group
                                    </a>

                                @endif

                            {{-- STUDENT MENU --}}
                            @elseif(auth()->user()->role === 'student')

                                <p class="px-3 pb-1 pt-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Student Portal
                                </p>

                                <a href="{{ route('student.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ⌂ Dashboard
                                </a>

                                <a href="{{ route('student.profile') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    👤 My Profile
                                </a>

                                <a href="{{ route('student.universities.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🎓 Universities
                                </a>

                                <a href="{{ route('student.university-match') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🌍 University Matching
                                </a>

                                <a href="{{ route('student.consultants.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ♙ Find Consultants
                                </a>

                                <a href="{{ route('student.bookings.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    📅 My Bookings
                                </a>

                                <a href="{{ route('student.eligibility') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ✅ Eligibility Check
                                </a>

                                <a href="{{ route('student.applications.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    📄 Applications
                                </a>

                                <a href="{{ route('student.visa.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🛂 Visa Applications
                                </a>

                                <a href="{{ route('student.messages.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    💬 Messages
                                </a>

                                <a href="{{ route('student.my-meetings') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ◷ Meetings
                                </a>

                                <a href="{{ route('student.my-courses.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    📚 My Courses
                                </a>

                                <a href="{{ route('chat-groups.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    👥 Internal Group Chats
                                </a>

                                <a href="{{ route('notifications.index') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🔔 Notifications
                                </a>

                            {{-- CONSULTANT MENU --}}
                            @elseif(auth()->user()->role === 'consultant')

                                <p class="px-3 pb-1 pt-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Consultant Portal
                                </p>

                                <a href="{{ route('consultant.dashboard') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ⌂ Dashboard
                                </a>

                                <a href="{{ route('consultant.students.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ♙ My Students
                                </a>

                                <a href="{{ route('consultant.visitors') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    👥 Visitors
                                </a>

                                <a href="{{ route('consultant.bookings.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    📋 Bookings
                                </a>

                                <a href="{{ route('consultant.availability.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🕐 Availability
                                </a>

                                <a href="{{ route('consultant.messages.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    💬 Messages
                                </a>

                                <a href="{{ route('consultant.classroom.courses') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🏫 Google Classroom
                                </a>

                                <a href="{{ route('consultant.profile') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    👤 My Profile
                                </a>

                                <a href="{{ route('consultant.meetings') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    ◷ Meetings
                                </a>

                                <a href="{{ route('notifications.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    🔔 Notifications
                                </a>

                                <a href="{{ route('chat-groups.index') }}"
                                   class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    👥 Internal Group Chats
                                </a>

                            @endif
                        </div>

                        <div class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-xs text-slate-500">
                                Signed in as {{ auth()->user()->role }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NOTIFICATION BELL --}}
            @auth
                <a href="{{ route('notifications.index') }}"
                   aria-label="Notifications"
                   title="Notifications"
                   class="relative inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-xl text-slate-700 shadow-sm transition hover:bg-indigo-50 hover:text-indigo-700">
                    <span aria-hidden="true">🔔</span>
                   @php
    $unreadNotificationCount = 0;

    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            $unreadNotificationCount = auth()->user()
                ->unreadNotifications()
                ->count();
        }
    } catch (\Throwable $e) {
        $unreadNotificationCount = 0;
    }
@endphp
                    @if($unreadNotificationCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white">
                            {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                        </span>
                    @endif
                </a>
            @endauth

            {{-- PROFILE MENU --}}
            <div class="relative shrink-0">
                <button type="button"
                        @click="profileOpen = !profileOpen"
                        @keydown.escape.window="profileOpen = false"
                        aria-label="Open profile menu"
                        :aria-expanded="profileOpen.toString()"
                        class="flex items-center gap-3 rounded-xl px-2 py-2 transition hover:bg-slate-100">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()->role }}</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-600 font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </button>

                <div x-cloak
                     x-show="profileOpen"
                     @click.outside="profileOpen = false"
                     x-transition
                     class="absolute right-0 top-14 z-[110] w-64 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                    <div class="border-b border-slate-100 px-4 py-4">
                        <p class="break-words font-bold text-slate-900">{{ auth()->user()->name }}</p>
                        <p class="mt-1 break-all text-xs text-slate-500">{{ auth()->user()->email }}</p>
                    </div>

                    <div class="p-2">
                        @if(auth()->user()->role === 'consultant')
                            <a href="{{ route('consultant.profile') }}"
                               class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                My Profile
                            </a>
                        @elseif(auth()->user()->role === 'student')
                            <a href="{{ route('student.profile') }}"
                               class="block rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                My Profile
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-red-600 hover:bg-red-50">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- SESSION STATUS --}}
    @if(session('status'))
        <div class="px-4 pt-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('status') }}
            </div>
        </div>
    @endif

    {{-- OPTIONAL HEADER --}}
    @isset($header)
        <div class="px-4 pt-6 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    @endisset

    {{-- PAGE CONTENT --}}
    <main class="w-full min-w-0">
        {{ $slot }}
    </main>
</div>

{{-- UniBridge AI Chatbot --}}
<div
    x-data="{
        open: false,
        prompt: '',
        loading: false,
        messages: [{
            role: 'assistant',
            text: 'Welcome to UniBridge! Ask me about universities, courses, admissions or studying abroad.'
        }],
        async sendMessage() {
            const question = this.prompt.trim();
            if (!question || this.loading) return;

            this.messages.push({ role: 'user', text: question });
            this.prompt = '';
            this.loading = true;

            this.$nextTick(() => {
                this.$refs.chatMessages.scrollTop = this.$refs.chatMessages.scrollHeight;
            });

            try {
                const response = await fetch(@js(route('api.gemini.generate')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ prompt: question })
                });

                const rawResponse = await response.text();
                const cleanResponse = rawResponse.replace(/^\uFEFF/, '');
                let data;

                try {
                    data = JSON.parse(cleanResponse);
                } catch (error) {
                    console.error('UniBridge AI invalid JSON:', rawResponse);
                    throw new Error('AI server returned an invalid response.');
                }

                if (!response.ok) {
                    throw new Error(data.message || 'AI service unavailable.');
                }

                if (typeof data.text !== 'string' || !data.text.trim()) {
                    throw new Error('AI returned an empty response.');
                }

                this.messages.push({ role: 'assistant', text: data.text });
            } catch (error) {
                this.messages.push({
                    role: 'assistant',
                    text: 'Sorry, the AI service is temporarily unavailable. Please try again later.'
                });
                console.error('UniBridge AI:', error);
            } finally {
                this.loading = false;
                this.$nextTick(() => {
                    this.$refs.chatMessages.scrollTop = this.$refs.chatMessages.scrollHeight;
                });
            }
        }
    }"
    x-cloak
    style="position:fixed;right:22px;bottom:22px;z-index:9999;font-family:Arial,sans-serif;"
>
    {{-- Floating button --}}
    <button type="button"
            @click="open = !open"
            style="border:0;border-radius:30px;background:#4338ca;color:white;padding:15px 23px;font-size:16px;font-weight:bold;box-shadow:0 5px 20px #0003;cursor:pointer;">
        ✦ UniBridge AI
    </button>

    {{-- Chat window --}}
    <section x-show="open"
             x-transition
             style="position:absolute;right:0;bottom:72px;width:min(430px,calc(100vw - 32px));height:min(620px,calc(100vh - 120px));min-height:360px;background:white;border:1px solid #dbe1ea;border-radius:18px;box-shadow:0 12px 40px #0003;display:flex;flex-direction:column;overflow:hidden;">
        {{-- Header --}}
        <div style="flex-shrink:0;background:#1e1b4b;color:white;padding:18px;display:flex;justify-content:space-between;align-items:center;">
            <div>
                <div style="font-size:18px;font-weight:bold;">UniBridge AI Assistant</div>
                <div style="font-size:13px;color:#c7d2fe;margin-top:5px;">Study-abroad support</div>
            </div>
            <button type="button"
                    @click="open = false"
                    aria-label="Close chat"
                    style="border:0;background:transparent;color:white;font-size:28px;cursor:pointer;padding:0 5px;">
                &times;
            </button>
        </div>

        {{-- Conversation area --}}
        <div x-ref="chatMessages"
             style="flex:1;min-height:0;overflow-y:auto;padding:18px;background:#f8fafc;display:flex;flex-direction:column;gap:14px;">
            <template x-for="(message, index) in messages" :key="index">
                <div
                    style="max-width:88%;padding:12px 15px;border-radius:14px;font-size:15px;line-height:1.6;white-space:pre-wrap;overflow-wrap:anywhere;"
                    :style="message.role === 'user'
                        ? 'align-self:flex-end;background:#4338ca;color:white;border-bottom-right-radius:4px;'
                        : 'align-self:flex-start;background:white;color:#1e293b;border:1px solid #e2e8f0;border-bottom-left-radius:4px;'"
                    x-text="message.text">
                </div>
            </template>

            <div x-show="loading"
                 style="align-self:flex-start;background:white;border:1px solid #e2e8f0;border-radius:12px;padding:12px;color:#64748b;font-size:14px;">
                AI is responding...
            </div>
        </div>

        {{-- Message input --}}
        <form @submit.prevent="sendMessage()"
              style="flex-shrink:0;display:flex;align-items:flex-end;gap:9px;padding:13px;background:white;border-top:1px solid #e2e8f0;">
            <textarea x-model="prompt"
                      @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                      maxlength="10000"
                      rows="2"
                      required
                      placeholder="Type your message here..."
                      aria-label="Type your message"
                      style="display:block;box-sizing:border-box;flex:1;width:0;min-width:0;min-height:52px;max-height:100px;resize:vertical;border:1px solid #cbd5e1;border-radius:11px;padding:12px;font-size:15px;line-height:1.5;color:#0f172a;background:white;outline-color:#4f46e5;">
            </textarea>

            <button type="submit"
                    :disabled="loading || !prompt.trim()"
                    style="flex-shrink:0;min-height:48px;border:0;border-radius:11px;background:#4338ca;color:white;padding:0 17px;font-size:15px;font-weight:bold;cursor:pointer;"
                    :style="loading || !prompt.trim() ? 'opacity:0.55;cursor:not-allowed;' : ''">
                <span x-show="!loading">Send</span>
                <span x-show="loading">...</span>
            </button>
        </form>

        <div style="flex-shrink:0;padding:0 10px 10px;text-align:center;font-size:11px;color:#64748b;background:white;">
            Please verify important admission information.
        </div>
    </section>
</div>
{{-- End UniBridge AI Chatbot --}}

</body>
</html>
