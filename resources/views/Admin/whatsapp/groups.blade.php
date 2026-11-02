<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900">WhatsApp Contact Groups</h2>
            <p class="mt-1 text-sm text-slate-500">Create groups and manage their saved contacts in one place.</p>
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

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-bold">Please correct these errors:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-5">
                    <h3 class="text-lg font-bold text-slate-900">Create a Contact Group</h3>
                    <p class="mt-1 text-sm text-slate-500">Organize contacts into students, consultants or general groups.</p>
                </div>

                <form method="POST" action="{{ route('admin.whatsapp.groups.create') }}"
                      class="grid grid-cols-1 gap-4 p-5 md:grid-cols-2">
                    @csrf

                    <div>
                        <label for="group_name" class="mb-2 block text-sm font-semibold text-slate-700">Group Name</label>
                        <input id="group_name" name="name" type="text" required maxlength="255"
                               value="{{ old('name') }}" placeholder="e.g. Canada Students"
                               class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label for="group_category" class="mb-2 block text-sm font-semibold text-slate-700">Category</label>
                        <select id="group_category" name="category" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">
                            <option value="students" {{ old('category') === 'students' ? 'selected' : '' }}>Students</option>
                            <option value="consultants" {{ old('category') === 'consultants' ? 'selected' : '' }}>Consultants</option>
                            <option value="general" {{ old('category', 'general') === 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="group_description" class="mb-2 block text-sm font-semibold text-slate-700">Description (optional)</label>
                        <textarea id="group_description" name="description" rows="2" maxlength="2000"
                                  placeholder="What is this group for?"
                                  class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm">{{ old('description') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit"
                                class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white hover:bg-indigo-700">
                            + Create Group
                        </button>
                    </div>
                </form>
            </section>

            <section>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">Saved Groups</h3>
                        <p class="mt-1 text-sm text-slate-500">Add contacts, review consent and manage membership.</p>
                    </div>
                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-bold text-indigo-700">
                        {{ $groups->total() }} groups
                    </span>
                </div>

                <div class="space-y-5">
                    @forelse ($groups as $group)
                        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="flex flex-col justify-between gap-3 border-b border-slate-100 p-5 sm:flex-row sm:items-start">
                                <div>
                                    <h4 class="text-lg font-bold text-slate-900">{{ $group->name }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ ucfirst($group->category ?? 'general') }} · {{ $group->members_count }} contacts
                                    </p>
                                    @if ($group->description)
                                        <p class="mt-2 text-sm text-slate-600">{{ $group->description }}</p>
                                    @endif
                                </div>

                                <a href="{{ route('admin.whatsapp.group-inbox', ['group' => $group->id]) }}"
   class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white hover:bg-indigo-700">
    Open Group Inbox
</a>
<form method="POST"
                                      action="{{ route('admin.whatsapp.groups.delete', ['group' => $group->id]) }}"
                                      onsubmit="return confirm('Delete this group and its saved memberships?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                                        Delete Group
                                    </button>
                                </form>
                            </div>

                            <div class="border-b border-slate-100 bg-slate-50 p-5">
                                <h5 class="mb-3 font-bold text-slate-800">Add a Contact</h5>

                                <form method="POST"
                                      action="{{ route('admin.whatsapp.groups.members.add', ['group' => $group->id]) }}"
                                      class="grid grid-cols-1 gap-3 md:grid-cols-3">
                                    @csrf

                                    <div>
                                        <label for="member_name_{{ $group->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Contact Name</label>
                                        <input id="member_name_{{ $group->id }}" name="contact_name" type="text"
                                               required maxlength="255" placeholder="Full name"
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label for="member_phone_{{ $group->id }}" class="mb-1 block text-xs font-semibold text-slate-700">WhatsApp Number</label>
                                        <input id="member_phone_{{ $group->id }}" name="phone_number" type="tel"
                                               required maxlength="30" placeholder="+923001234567"
                                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label for="member_type_{{ $group->id }}" class="mb-1 block text-xs font-semibold text-slate-700">Contact Type</label>
                                        <select id="member_type_{{ $group->id }}" name="contact_type" required
                                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            <option value="student">Student</option>
                                            <option value="consultant">Consultant</option>
                                            <option value="manual">Other Contact</option>
                                        </select>
                                    </div>

                                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 md:col-span-3">
                                        <label class="flex items-start gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="whatsapp_opt_in" value="1"
                                                   class="mt-1 rounded border-slate-300">
                                            <span>
                                                <strong>Permission to receive WhatsApp messages confirmed</strong>
                                                <span class="mt-1 block text-xs text-slate-600">
                                                    Check only when the contact has explicitly agreed. Adding a contact does not automatically grant messaging permission.
                                                </span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="md:col-span-3">
                                        <button type="submit"
                                                class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-700">
                                            + Add Contact to {{ $group->name }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="p-5">
                                <h5 class="mb-3 font-bold text-slate-800">Group Members</h5>

                                <div class="divide-y divide-slate-100">
                                    @forelse ($group->members as $member)
                                        <div class="flex flex-col justify-between gap-3 py-3 sm:flex-row sm:items-center">
                                            <div>
                                                <p class="font-semibold text-slate-800">{{ $member->contact_name ?: 'Unnamed contact' }}</p>
                                                <p class="mt-1 text-sm text-slate-500">
                                                    {{ $member->phone_number }} · {{ ucfirst($member->contact_type ?? 'manual') }}
                                                </p>

                                                @if ($member->whatsapp_opt_in && $member->opted_in_at)
                                                    <p class="mt-1 text-xs font-semibold text-emerald-700">
                                                        Consent recorded · {{ $member->opted_in_at->format('d M Y, h:i A') }}
                                                    </p>
                                                @else
                                                    <p class="mt-1 text-xs font-semibold text-amber-700">
                                                        WhatsApp consent not recorded
                                                    </p>
                                                @endif
                                            </div>

                                            <div class="flex flex-wrap items-center gap-2">
                                                <form method="POST"
                                                  action="{{ route('admin.whatsapp.groups.members.remove', ['group' => $group->id, 'member' => $member->id]) }}"
                                                  onsubmit="return confirm('Remove this contact from the group?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50">
                                                    Remove Contact
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">This group has no contacts yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
                            <h4 class="font-bold text-slate-800">No groups created yet</h4>
                            <p class="mt-2 text-sm text-slate-500">Use the form above to create your first contact group.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $groups->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>

