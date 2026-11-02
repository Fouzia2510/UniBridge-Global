<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-950">Meeting Activity</h2>
                <p class="mt-1 text-sm text-slate-600">Saved join-link activity for consultation meetings.</p>
            </div>
            @if (auth()->user()->role === \App\Models\User::ROLE_CONSULTANT)
                <a href="{{ route('consultant.bookings.index') }}" class="font-semibold text-blue-700 hover:text-blue-900">← Back to bookings</a>
            @elseif (auth()->user()->role === \App\Models\User::ROLE_STUDENT)
                <a href="{{ route('student.bookings.index') }}" class="font-semibold text-blue-700 hover:text-blue-900">← Back to my meetings</a>
            @else
                <a href="{{ route('admin.dashboard') }}" class="font-semibold text-blue-700 hover:text-blue-900">← Admin dashboard</a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm leading-6 text-blue-900">
                Activity is recorded when someone opens UniBridge’s guarded Join link. Google Meet does not provide this app with verified attendance or leave times, so these entries are join-link opens—not proof that the person entered or stayed in the meeting.
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-4">Join-link opened</th>
                                <th class="px-5 py-4">Student</th>
                                <th class="px-5 py-4">Consultant</th>
                                <th class="px-5 py-4">Scheduled meeting</th>
                                <th class="px-5 py-4">Opened by</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse ($events as $event)
                                @if ($event->booking)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">
                                            {{ $event->joined_at->timezone(config('app.timezone'))->format('d M Y, h:i:s A') }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-800">
                                            {{ $event->booking->student->name ?? 'Deleted student' }}
                                            <div class="text-xs text-slate-500">{{ $event->booking->student->email ?? '' }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-slate-800">
                                            {{ $event->booking->consultant->name ?? 'Deleted consultant' }}
                                            <div class="text-xs text-slate-500">{{ $event->booking->consultant->email ?? '' }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-700">
                                            {{ $event->booking->meetingStartsAt()->format('d M Y, h:i A') }}
                                            <div class="text-xs text-slate-500">{{ ucfirst($event->booking->status) }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-slate-700">
                                            {{ $event->user->name ?? 'Unknown user' }}
                                            <div class="text-xs text-slate-500">{{ ucfirst($event->user->role ?? 'unknown') }}</div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-500">No meeting join activity has been recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-5 py-4">{{ $events->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
