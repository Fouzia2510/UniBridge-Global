<x-app-layout>

    @php
        $pageTitle = $pageTitle ?? 'Manage Users';
        $pageDescription = $pageDescription ?? 'View and manage registered UniBridge platform accounts.';
        $userType = $userType ?? 'all';
    @endphp

    <x-slot name="header">

        <div>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $pageTitle }}
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                {{ $pageDescription }}
            </p>

        </div>

    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))

                <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                    {{ session('status') }}
                </div>

            @endif


            {{-- Navigation --}}

            <div class="mb-6 bg-white shadow-sm sm:rounded-lg p-4">

                <div class="flex flex-wrap gap-2">

                    <a
                        href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md
                        {{ $userType === 'all'
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        All Users
                    </a>


                    <a
                        href="{{ route('admin.students') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md
                        {{ $userType === 'student'
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        Students
                    </a>


                    <a
                        href="{{ route('admin.consultants') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md
                        {{ $userType === 'consultant'
                            ? 'bg-purple-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        Consultants
                    </a>


                    <a
                        href="{{ route('admin.administrators') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md
                        {{ $userType === 'admin'
                            ? 'bg-red-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                    >
                        Administrators
                    </a>


                    <a
                        href="{{ route('admin.role-management') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md bg-indigo-100 text-indigo-700 hover:bg-indigo-200"
                    >
                        Role Management
                    </a>

                </div>

            </div>


            {{-- Users Table --}}

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b border-gray-200">

                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $pageTitle }}
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        {{ $users->count() }}
                        {{ $users->count() === 1 ? 'account' : 'accounts' }}
                        found.
                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">

                                <th class="px-6 py-3">
                                    Name
                                </th>

                                <th class="px-6 py-3">
                                    Email
                                </th>

                                <th class="px-6 py-3">
                                    Role
                                </th>

                                <th class="px-6 py-3">
                                    Status
                                </th>

                                <th class="px-6 py-3">
                                    Joined
                                </th>

                                <th class="px-6 py-3">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse ($users as $user)

                                <tr class="hover:bg-gray-50">

                                    {{-- Name --}}

                                    <td class="px-6 py-4">

                                        <div class="font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>

                                    </td>


                                    {{-- Email --}}

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $user->email }}
                                    </td>


                                    {{-- Role --}}

                                    <td class="px-6 py-4">

                                        @if ($user->role === 'student')

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Student
                                            </span>

                                        @elseif ($user->role === 'consultant')

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Consultant
                                            </span>

                                        @elseif ($user->role === 'admin')

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Administrator
                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Unknown
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Status --}}

                                    <td class="px-6 py-4">

                                        @if ($user->role === 'admin' || $user->is_approved)

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Approved
                                            </span>

                                        @else

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Joined --}}

                                    <td class="px-6 py-4 text-sm text-gray-500">

                                        {{ $user->created_at->format('d M Y') }}

                                    </td>


                                    {{-- Actions --}}

                                    <td class="px-6 py-4">

                                        <div class="flex gap-3 items-center flex-wrap">

                                            @if (! $user->is_approved && $user->role !== 'admin')

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.users.approve', $user) }}"
                                                >

                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="text-green-600 hover:text-green-800 text-xs font-medium"
                                                    >
                                                        Approve
                                                    </button>

                                                </form>

                                            @endif


                                            <a
                                                href="{{ route('admin.users.edit', $user) }}"
                                                class="text-indigo-600 hover:text-indigo-800 text-xs font-medium"
                                            >
                                                Edit
                                            </a>


                                            @if ($user->id !== auth()->id())

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.users.destroy', $user) }}"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?')"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-red-600 hover:text-red-800 text-xs font-medium"
                                                    >
                                                        Delete
                                                    </button>

                                                </form>

                                            @else

                                                <span class="text-gray-400 text-xs">
                                                    (You)
                                                </span>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="px-6 py-12 text-center text-gray-500"
                                    >
                                        No
                                        {{ $userType === 'student'
                                            ? 'students'
                                            : ($userType === 'consultant'
                                                ? 'consultants'
                                                : ($userType === 'admin'
                                                    ? 'administrators'
                                                    : 'users')) }}
                                        found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
