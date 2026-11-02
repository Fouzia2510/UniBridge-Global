<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('chat-groups.index') }}"
               class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                ← Back
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Create Internal Group</h2>
                <p class="mt-1 text-sm text-slate-500">Create a shared UniBridge conversation.</p>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <p class="font-semibold">Please correct the following:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.chat-groups.store') }}"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Group details</h3>
                    <p class="mt-1 text-sm text-slate-500">Give the group a name members will recognize.</p>

                    <div class="mt-5 space-y-5">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Group name *</label>
                            <input id="name" name="name" type="text" required maxlength="255"
                                   value="{{ old('name') }}"
                                   class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g. Canada Applicants">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Description (optional)</label>
                            <textarea id="description" name="description" rows="3" maxlength="5000"
                                      class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="What is this group for?">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="avatar" class="mb-2 block text-sm font-semibold text-slate-700">Group picture (optional)</label>
                            <input id="avatar" name="avatar" type="file" accept="image/*"
                                   class="block w-full rounded-xl border border-slate-300 p-2 text-sm text-slate-600">
                            <p class="mt-1 text-xs text-slate-500">Image only, maximum 2 MB.</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">UniBridge members</h3>
                    <p class="mt-1 text-sm text-slate-500">Select the approved accounts to include. You will be added automatically.</p>

                    <div class="mt-4 max-h-80 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-3">
                        @forelse ($users as $user)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg p-3 hover:bg-slate-50">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                       @checked(in_array($user->id, old('user_ids', [])))
                                       class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-800">{{ $user->name }}</span>
                                    <span class="block break-all text-xs text-slate-500">{{ $user->email }}</span>
                                    <span class="mt-1 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </span>
                            </label>
                        @empty
                            <p class="p-3 text-sm text-slate-500">No other approved accounts are available.</p>
                        @endforelse
                    </div>
                    @error('user_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('user_ids.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900">Phone-only contacts</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Optional. These contacts are stored only in this internal group; adding a phone number does not create an account or send a WhatsApp message.
                    </p>

                    @for ($i = 0; $i < 3; $i++)
                        <div class="mt-5 rounded-xl border border-slate-200 p-4">
                            <p class="mb-3 text-sm font-semibold text-slate-700">Contact {{ $i + 1 }}</p>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="contact_name_{{ $i }}" class="mb-1 block text-xs font-semibold text-slate-600">Name</label>
                                    <input id="contact_name_{{ $i }}"
                                           name="contacts[{{ $i }}][name]"
                                           type="text" maxlength="255"
                                           value="{{ old('contacts.'.$i.'.name') }}"
                                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Contact name">
                                </div>
                                <div>
                                    <label for="contact_phone_{{ $i }}" class="mb-1 block text-xs font-semibold text-slate-600">Phone number</label>
                                    <input id="contact_phone_{{ $i }}"
                                           name="contacts[{{ $i }}][phone]"
                                           type="text" maxlength="30"
                                           value="{{ old('contacts.'.$i.'.phone') }}"
                                           class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="+92...">
                                </div>
                            </div>
                        </div>
                    @endfor

                    @error('contacts')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('chat-groups.index') }}"
                       class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>