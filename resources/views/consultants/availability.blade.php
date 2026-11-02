<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Availability') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">
                    Consultant Availability
                </h1>

                <p class="text-sm text-gray-600 mt-1">
                    Add the dates and times when students can book a consultation with you.
                </p>
            </div>


            {{-- Success Message --}}
            @if (session('status'))

                <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>

            @endif


            {{-- Validation Errors --}}
            @if ($errors->any())

                <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-md">

                    <p class="font-semibold mb-2">
                        Please fix the following errors:
                    </p>

                    <ul class="list-disc list-inside text-sm">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Add Availability --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">

                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Add Available Slot
                </h2>

                <form
                    method="POST"
                    action="{{ route('consultant.availability.store') }}"
                >

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

                        {{-- Date --}}
                        <div>

                            <x-input-label
                                for="date"
                                value="Date"
                            />

                            <x-text-input
                                id="date"
                                name="date"
                                type="date"
                                class="block mt-1 w-full"
                                min="{{ now()->toDateString() }}"
                                value="{{ old('date') }}"
                                required
                            />

                        </div>


                        {{-- Time --}}
                        <div>

                            <x-input-label
                                for="time"
                                value="Time"
                            />

                            <x-text-input
                                id="time"
                                name="time"
                                type="time"
                                class="block mt-1 w-full"
                                value="{{ old('time') }}"
                                required
                            />

                        </div>


                        {{-- Button --}}
                        <div>

                            <x-primary-button class="w-full justify-center">
                                + Add Availability
                            </x-primary-button>

                        </div>

                    </div>

                </form>

            </div>


            {{-- Availability List --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-4">

                    <div>

                        <h2 class="text-lg font-semibold text-gray-900">
                            My Available Slots
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Students can book slots marked as Available.
                        </p>

                    </div>

                    <div class="text-sm text-gray-500">

                        Total:
                        <span class="font-semibold text-gray-900">
                            {{ $slots->count() }}
                        </span>

                    </div>

                </div>


                {{-- Slots Table --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead>

                            <tr class="text-left text-sm text-gray-500">

                                <th class="py-3 pr-6">
                                    Date
                                </th>

                                <th class="py-3 pr-6">
                                    Time
                                </th>

                                <th class="py-3 pr-6">
                                    Status
                                </th>

                                <th class="py-3">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @forelse ($slots as $slot)

                                <tr class="text-sm">

                                    {{-- Date --}}
                                    <td class="py-4 pr-6">

                                        <span class="font-medium text-gray-900">

                                            {{ \Carbon\Carbon::parse($slot->date)->format('d M Y') }}

                                        </span>

                                    </td>


                                    {{-- Time --}}
                                    <td class="py-4 pr-6">

                                        <span class="text-gray-700">

                                            {{ \Carbon\Carbon::parse($slot->time)->format('h:i A') }}

                                        </span>

                                    </td>


                                    {{-- Status --}}
                                    <td class="py-4 pr-6">

                                        @if ($slot->hasEnded())

                                            <span class="inline-flex items-center text-xs font-medium bg-gray-100 text-gray-700 px-3 py-1 rounded-full">
                                                Time passed
                                            </span>

                                        @elseif ($slot->is_booked)

                                            <span class="inline-flex items-center text-xs font-medium bg-red-100 text-red-800 px-3 py-1 rounded-full">

                                                🔴 Booked

                                            </span>

                                        @else

                                            <span class="inline-flex items-center text-xs font-medium bg-green-100 text-green-800 px-3 py-1 rounded-full">

                                                🟢 Available

                                            </span>

                                        @endif

                                    </td>


                                    {{-- Action --}}
                                    <td class="py-4">

                                        @if (!$slot->is_booked && !$slot->hasEnded())

                                            <form
                                                method="POST"
                                                action="{{ route('consultant.availability.destroy', $slot) }}"
                                                onsubmit="return confirm('Are you sure you want to remove this availability slot?')"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                                >
                                                    Remove
                                                </button>

                                            </form>

                                        @else

                                            <span class="text-xs text-gray-400">
                                                Cannot remove
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="py-10 text-center"
                                    >

                                        <div class="text-gray-400 text-4xl mb-2">
                                            📅
                                        </div>

                                        <p class="text-gray-600 font-medium">
                                            No availability slots added yet.
                                        </p>

                                        <p class="text-sm text-gray-400 mt-1">
                                            Add a date and time above so students can book you.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Information Box --}}
            <div class="mt-6 bg-indigo-50 border border-indigo-200 rounded-lg p-5">

                <h3 class="font-semibold text-indigo-900">
                    💡 How availability works
                </h3>

                <ul class="mt-2 text-sm text-indigo-700 space-y-1">

                    <li>• Add a date and time when you are available.</li>

                    <li>• Students will see your available slots on the Book a Consultant page.</li>

                    <li>• Once a student books a slot, its status becomes <strong>Booked</strong>.</li>

                    <li>• Booked slots cannot be removed.</li>

                </ul>

            </div>

        </div>

    </div>

</x-app-layout>