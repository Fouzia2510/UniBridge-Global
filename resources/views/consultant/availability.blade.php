<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                Availability
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Manage your one-hour consultation slots. Times use {{ config('app.timezone') }}.
            </p>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">
                    <p class="font-semibold text-red-800 mb-2">
                        Please fix the following:
                    </p>

                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Add Availability --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

                        <div class="mb-6">
                            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-indigo-600"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <h3 class="text-xl font-bold text-gray-900">
                                Add Availability
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Create a time slot for students to book.
                            </p>
                        </div>

                        <form method="POST"
                              action="{{ route('consultant.availability.store') }}"
                              class="space-y-5">

                            @csrf

                            {{-- Date --}}
                            <div>
                                <label for="date"
                                       class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date
                                </label>

                                <input
                                    type="date"
                                    id="date"
                                    name="date"
                                    value="{{ old('date') }}"
                                    min="{{ now()->toDateString() }}"
                                    required
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            {{-- Time --}}
                            <div>
                                <label for="time"
                                       class="block text-sm font-semibold text-gray-700 mb-2">
                                    Time
                                </label>

                                <input
                                    type="time"
                                    id="time"
                                    name="time"
                                    value="{{ old('time') }}"
                                    required
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>

                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <svg class="w-5 h-5 mr-2"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
                                </svg>

                                Add Time Slot
                            </button>

                        </form>
                    </div>
                </div>

                {{-- Existing Slots --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        My Availability
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Your available consultation time slots.
                                    </p>
                                </div>

                                <div class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">
                                    {{ $slots->count() }} {{ Str::plural('slot', $slots->count()) }}
                                </div>

                            </div>
                        </div>

                        @if ($slots->count())
                            <div class="divide-y divide-gray-100">

                                @foreach ($slots as $slot)
                                    <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                                        <div class="flex items-center gap-4">

                                            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-600"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                          stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>

                                            <div>
                                                <div class="font-bold text-gray-900">
                                                    {{ \Carbon\Carbon::parse($slot->date)->format('l, d M Y') }}
                                                </div>

                                                <div class="text-sm text-gray-500 mt-1">
                                                    {{ \Carbon\Carbon::parse($slot->time)->format('h:i A') }}
                                                </div>
                                            </div>

                                        </div>

                                        <div class="flex items-center gap-3">

                                            @if ($slot->hasEnded())
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                    Time passed
                                                </span>
                                            @elseif ($slot->is_booked)
                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                                    Booked
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                    Available
                                                </span>

                                                <form method="POST"
                                                      action="{{ route('consultant.availability.destroy', $slot) }}"
                                                      onsubmit="return confirm('Are you sure you want to remove this availability slot?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                                                        Remove
                                                    </button>
                                                </form>
                                            @endif

                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="p-10 text-center">

                                <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                                    <svg class="w-7 h-7 text-gray-400"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>

                                <h4 class="text-lg font-bold text-gray-900">
                                    No Availability Added
                                </h4>

                                <p class="text-sm text-gray-500 mt-2">
                                    Add your first future date and time so students can book a consultation with you.
                                </p>

                            </div>
                        @endif

                    </div>
                </div>

            </div>

            {{-- Back to Dashboard --}}
            <div class="mt-8">
                <a href="{{ route('consultant.dashboard') }}"
                   class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    ← Back to Consultant Dashboard
                </a>
            </div>

        </div>
    </div>

</x-app-layout>