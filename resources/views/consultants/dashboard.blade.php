<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Consultant Dashboard') }}
        </h2>
    </x-slot>

```
<div class="py-12 bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow-md sm:rounded-xl border border-gray-200">
            <div class="p-8">

                {{-- Welcome Section --}}
                <div class="mb-8">
                    <h3 class="text-3xl font-bold text-gray-900">
                        Welcome, {{ Auth::user()->name }}!
                    </h3>

                    <p class="mt-3 text-base text-gray-600">
                        Manage your consultant profile, students, bookings,
                        and availability from your dashboard.
                    </p>
                </div>

                {{-- Dashboard Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    {{-- My Profile --}}
                    <a href="{{ route('consultant.profile.edit') }}"
                       class="group block bg-purple-50 border border-purple-200 rounded-xl p-6 hover:bg-purple-100 hover:shadow-md transition">

                        <div class="text-3xl mb-4">
                            👤
                        </div>

                        <h4 class="text-lg font-bold text-purple-900">
                            My Profile
                        </h4>

                        <p class="mt-2 text-sm text-gray-700">
                            Update your professional information and expertise.
                        </p>
                    </a>

                    {{-- Students --}}
                    <a href="{{ route('consultant.students.index') }}"
                       class="group block bg-indigo-50 border border-indigo-200 rounded-xl p-6 hover:bg-indigo-100 hover:shadow-md transition">

                        <div class="text-3xl mb-4">
                            👨‍🎓
                        </div>

                        <h4 class="text-lg font-bold text-indigo-900">
                            View All Students
                        </h4>

                        <p class="mt-2 text-sm text-gray-700">
                            View students and their academic information.
                        </p>
                    </a>

                    {{-- Booking Requests --}}
                    <a href="{{ route('consultant.bookings.index') }}"
                       class="group block bg-green-50 border border-green-200 rounded-xl p-6 hover:bg-green-100 hover:shadow-md transition">

                        <div class="text-3xl mb-4">
                            📅
                        </div>

                        <h4 class="text-lg font-bold text-green-900">
                            Booking Requests
                        </h4>

                        <p class="mt-2 text-sm text-gray-700">
                            View and manage student consultation bookings.
                        </p>
                    </a>

                    {{-- Availability --}}
                    <a href="{{ route('consultant.availability.index') }}"
                       class="group block bg-blue-50 border border-blue-200 rounded-xl p-6 hover:bg-blue-100 hover:shadow-md transition">

                        <div class="text-3xl mb-4">
                            🕐
                        </div>

                        <h4 class="text-lg font-bold text-blue-900">
                            Manage Availability
                        </h4>

                        <p class="mt-2 text-sm text-gray-700">
                            Add or remove your available consultation times.
                        </p>
                    </a>

                </div>

            </div>
        </div>

    </div>
</div>
```

</x-app-layout>
