<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                        {{ session('status') }}
                    </div>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-left text-sm text-gray-500">
                            <th class="py-2 pr-4">Student</th>
                            <th class="py-2 pr-4">Date</th>
                            <th class="py-2 pr-4">Time</th>
                            <th class="py-2 pr-4">Notes</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($bookings as $booking)
                            <tr class="text-sm">
                                <td class="py-3 pr-4 font-medium">{{ $booking->student->name }}</td>
                                <td class="py-3 pr-4">{{ $booking->preferred_date->format('d M Y') }}</td>
                                <td class="py-3 pr-4">{{ $booking->preferred_time }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $booking->notes ?? '—' }}</td>
                                <td class="py-3 pr-4">
                                    <span class="text-xs px-2 py-1 rounded-full
                                        @class([
                                            'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                            'bg-green-100 text-green-800' => $booking->status === 'confirmed',
                                            'bg-blue-100 text-blue-800' => $booking->status === 'completed',
                                            'bg-red-100 text-red-800' => $booking->status === 'cancelled',
                                        ])">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4">
                                    <form method="POST" action="{{ route('consultant.bookings.update', $booking) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-md">
                                            <option value="pending" @selected($booking->status === 'pending')>Pending</option>
                                            <option value="confirmed" @selected($booking->status === 'confirmed')>Confirmed</option>
                                            <option value="completed" @selected($booking->status === 'completed')>Completed</option>
                                            <option value="cancelled" @selected($booking->status === 'cancelled')>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">No booking requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>