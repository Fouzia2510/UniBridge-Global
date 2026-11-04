<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900">WhatsApp Central Contacts</h2>
            <p class="mt-1 text-sm text-slate-500">Manage saved numbers and review their latest consent status.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Find a Contact</h3>
                    <p class="mt-1 text-sm text-slate-500">Search by contact name or WhatsApp number.</p>
                </div>

                <form method="GET" action="{{ route('admin.whatsapp.contacts') }}"
                      class="flex flex-col gap-3 sm:flex-row">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search name or phone number..."
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                    >

                    <button type="submit"
                            class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white hover:bg-indigo-700">
                        Search
                    </button>

                    @if ($search !== '')
                        <a href="{{ route('admin.whatsapp.contacts') }}"
                           class="rounded-xl border border-slate-300 px-6 py-3 text-center text-sm font-bold text-slate-700 hover:bg-slate-50">
                            Clear
                        </a>
                    @endif
                </form>
            </section>
            {{-- Add New Contact --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-slate-900">Add New Contact</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Save a contact without automatically granting marketing consent.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.whatsapp.contacts.store') }}"
                      class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Contact Name</label>
                        <input type="text" name="contact_name"
                               value="{{ old('contact_name') }}"
                               placeholder="Enter contact name"
                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                        @error('contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">WhatsApp Number</label>
                        <input type="text" name="phone_number"
                               value="{{ old('phone_number') }}"
                               placeholder="923001234567"
                               required
                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white hover:bg-indigo-700">
                            Add Contact
                        </button>
                    </div>
                </form>
            </section>
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-2 border-b border-slate-200 p-5 sm:flex-row sm:items-center">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Saved Contacts</h3>
                        <p class="mt-1 text-sm text-slate-500">Incoming and manually saved contacts appear here.</p>
                    </div>

                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-bold text-indigo-700">
                        {{ $contacts->total() }} contacts
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Name</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">WhatsApp Number</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Contact Type</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Marketing Consent</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Added</th>
                                <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($contacts as $contact)
                                @php
                                    $consent = $contact->latestConsent;
                                @endphp

                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-900">
                                        {{ $contact->contact_name ?: 'Unnamed Contact' }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                        {{ $contact->phone_number }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-700">
                                        {{ ucfirst($contact->contact_type ?? 'manual') }}
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm">
                                        @if ($consent?->status === 'opted_in')
                                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                                Opted In
                                            </span>
                                        @elseif ($consent?->status === 'opted_out')
                                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700">
                                                Opted Out
                                            </span>
                                        @else
                                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                                No Consent
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-500">
                                        {{ $contact->created_at?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="min-w-64 px-5 py-4">
    <form method="POST"
          action="{{ route('admin.whatsapp.contacts.update', $contact) }}"
          class="space-y-2">
        @csrf
        @method('PATCH')

        <input
            type="text"
            name="contact_name"
            value="{{ $contact->contact_name }}"
            placeholder="Contact name"
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
        >

        <input
            type="text"
            name="phone_number"
            value="{{ $contact->phone_number }}"
            required
            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
        >

        <button type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-700">
            Save Changes
        </button>
    </form>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">
                                        No contacts found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($contacts->hasPages())
                    <div class="border-t border-slate-200 p-5">
                        {{ $contacts->links() }}
                    </div>
                @endif
            </section>

            <p class="text-xs text-slate-500">
                Important: Receiving a WhatsApp message does not automatically mean a contact has agreed to receive marketing messages.
            </p>

        </div>
    </div>
</x-app-layout>
