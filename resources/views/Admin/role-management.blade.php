<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Role Management') }}
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Change platform permissions between Student, Consultant and Administrator.
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

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b border-gray-200">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Account Roles
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Select a new role for each platform account.
                            </p>
                        </div>

                        <a
                            href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700"
                        >
                            â† Manage Users
                        </a>

                    </div>

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
                                    Current Role
                                </th>

                                <th class="px-6 py-3">
                                    Change Role
                                </th>

                            </tr>

                        </thead>


                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse ($users as $user)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4">

                                        <div class="font-medium text-gray-900">
                                            {{ $user->name }}
                                        </div>

                                    </td>


                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $user->email }}
                                    </td>


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


                                    <td class="px-6 py-4">

                                        @if ($user->id === auth()->id())

                                            <span class="text-sm text-gray-400">
                                                Your account
                                            </span>

                                        @else

                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.update', $user) }}"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <div class="flex items-center gap-2">

                                                    <select
                                                        name="role"
                                                        class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    >

                                                        <option
                                                            value="student"
                                                            @selected($user->role === 'student')
                                                        >
                                                            Student
                                                        </option>

                                                        <option
                                                            value="consultant"
                                                            @selected($user->role === 'consultant')
                                                        >
                                                            Consultant
                                                        </option>

                                                        <option
                                                            value="admin"
                                                            @selected($user->role === 'admin')
                                                        >
                                                            Administrator
                                                        </option>

                                                    </select>


                                                    <button
                                                        type="submit"
                                                        class="px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700"
                                                    >
                                                        Update
                                                    </button>

                                                </div>

                                            </form>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="px-6 py-12 text-center text-gray-500"
                                    >
                                        No users found.
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
