<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User — {{ $user->name }}
            </h2>

            <p class="text-sm text-gray-500">
                Manage this user's account, documents and access.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Back --}}
            <div>
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                    ← Back to Users
                </a>
            </div>

            {{-- Success --}}
            @if (session('status'))
                <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Errors --}}
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3">
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

            {{-- User summary --}}
            <div class="bg-white shadow-sm sm:rounded-xl p-6">

                <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                    {{-- Profile picture --}}
                    <div class="shrink-0">

                        @if ($user->profile_photo_path)

                            <img
                                src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                alt="{{ $user->name }}"
                                class="w-24 h-24 rounded-full object-cover border-2 border-indigo-100"
                            >

                        @else

                            <div class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center text-3xl font-bold text-indigo-700">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>

                        @endif

                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $user->name }}
                        </h3>

                        <p class="text-sm text-gray-500">
                            {{ $user->email }}
                        </p>

                        <div class="mt-3 flex flex-wrap gap-2">

                            <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                {{ ucfirst($user->role) }}
                            </span>

                            @if ($user->is_approved || $user->role === 'admin')

                                <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                    Approved
                                </span>

                            @else

                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Pending Approval
                                </span>

                            @endif

                        </div>
                    </div>

                </div>
            </div>

            {{-- Account information --}}
            <div class="bg-white shadow-sm sm:rounded-xl p-6">

                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900">
                        Account Information
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Update the user's name, email address and password.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.users.update', $user) }}"
                    class="space-y-6"
                >

                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-semibold text-gray-700"
                        >
                            Full Name
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label
                            for="email"
                            class="block text-sm font-semibold text-gray-700"
                        >
                            Email Address
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        @error('email')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label
                            for="password"
                            class="block text-sm font-semibold text-gray-700"
                        >
                            New Password
                        </label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        <p class="mt-1 text-xs text-gray-500">
                            Leave blank to keep the current password.
                        </p>

                        @error('password')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm password --}}
                    <div>
                        <label
                            for="password_confirmation"
                            class="block text-sm font-semibold text-gray-700"
                        >
                            Confirm New Password
                        </label>

                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>


                    {{-- Student academic profile --}}
                    @if ($user->role === 'student')
                        @php
                            $studentProfile = $user->studentProfile;
                        @endphp

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-bold text-gray-900">
                                Academic & Study Preferences
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Manage this student's academic information and study-abroad preferences.
                            </p>

                            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">

                                <div>
                                    <label for="degree" class="block text-sm font-semibold text-gray-700">Current Degree</label>
                                    <input id="degree" name="degree" type="text" value="{{ old('degree', optional($studentProfile)->degree) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('degree') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="field_of_study" class="block text-sm font-semibold text-gray-700">Field of Study</label>
                                    <input id="field_of_study" name="field_of_study" type="text" value="{{ old('field_of_study', optional($studentProfile)->field_of_study) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('field_of_study') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="cgpa" class="block text-sm font-semibold text-gray-700">CGPA (out of 4.00)</label>
                                    <input id="cgpa" name="cgpa" type="number" step="0.01" min="0" max="4" value="{{ old('cgpa', optional($studentProfile)->cgpa) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('cgpa') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="graduation_year" class="block text-sm font-semibold text-gray-700">Graduation Year</label>
                                    <input id="graduation_year" name="graduation_year" type="number" min="1900" max="2100" value="{{ old('graduation_year', optional($studentProfile)->graduation_year) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('graduation_year') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="english_test" class="block text-sm font-semibold text-gray-700">English Test</label>
                                    <select id="english_test" name="english_test" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Select test</option>
                                        @foreach (['IELTS', 'PTE', 'Other', 'None'] as $test)
                                            <option value="{{ $test }}" @selected(old('english_test', optional($studentProfile)->english_test) === $test)>{{ $test }}</option>
                                        @endforeach
                                    </select>
                                    @error('english_test') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="english_score" class="block text-sm font-semibold text-gray-700">English Test Score (0–9)</label>
                                    <input id="english_score" name="english_score" type="number" step="0.01" min="0" max="9" value="{{ old('english_score', optional($studentProfile)->english_score) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('english_score') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="preferred_country" class="block text-sm font-semibold text-gray-700">Preferred Country</label>
                                    <input id="preferred_country" name="preferred_country" type="text" value="{{ old('preferred_country', optional($studentProfile)->preferred_country) }}" placeholder="e.g. Canada" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('preferred_country') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="preferred_degree_level" class="block text-sm font-semibold text-gray-700">Preferred Degree Level</label>
                                    <input id="preferred_degree_level" name="preferred_degree_level" type="text" value="{{ old('preferred_degree_level', optional($studentProfile)->preferred_degree_level) }}" placeholder="e.g. Master's" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('preferred_degree_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="preferred_subject" class="block text-sm font-semibold text-gray-700">Preferred Subject</label>
                                    <input id="preferred_subject" name="preferred_subject" type="text" value="{{ old('preferred_subject', optional($studentProfile)->preferred_subject) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('preferred_subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="budget" class="block text-sm font-semibold text-gray-700">Study Budget</label>
                                    <input id="budget" name="budget" type="number" step="0.01" min="0" value="{{ old('budget', optional($studentProfile)->budget) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('budget') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="preferred_intake" class="block text-sm font-semibold text-gray-700">Preferred Intake</label>
                                    <input id="preferred_intake" name="preferred_intake" type="text" value="{{ old('preferred_intake', optional($studentProfile)->preferred_intake) }}" placeholder="e.g. Fall 2027" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('preferred_intake') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="work_experience_years" class="block text-sm font-semibold text-gray-700">Work Experience (Years)</label>
                                    <input id="work_experience_years" name="work_experience_years" type="number" step="0.5" min="0" value="{{ old('work_experience_years', optional($studentProfile)->work_experience_years) }}" class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('work_experience_years') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                            </div>
                        </div>
                    @endif
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                        >
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>

            {{-- Documents --}}
            <div class="bg-white shadow-sm sm:rounded-xl p-6">

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-6">

                    <div>
                        <h3 class="text-lg font-bold text-gray-900">
                            Documents & CNIC
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Review documents uploaded by this user.
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.documents.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Document Verification
                    </a>

                </div>

                {{-- Privacy warning --}}
                <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                    <p class="text-sm font-semibold text-amber-800">
                        Sensitive documents
                    </p>

                    <p class="mt-1 text-xs text-amber-700">
                        CNIC and identity documents contain sensitive personal
                        information. They are available only through authorized
                        admin document controls.
                    </p>
                </div>

                @if ($documents->count())

                    <div class="space-y-4">

                        @foreach ($documents as $document)

                            <div class="rounded-xl border border-gray-200 p-4">

                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                                    <div class="min-w-0">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <h4 class="font-semibold text-gray-900">
                                                {{ $document->title }}
                                            </h4>

                                            @if ($document->verification_status === 'approved')

                                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                    Approved
                                                </span>

                                            @elseif ($document->verification_status === 'rejected')

                                                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                    Rejected
                                                </span>

                                            @else

                                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                    Pending
                                                </span>

                                            @endif

                                        </div>

                                        <div class="mt-2 text-sm text-gray-600 space-y-1">

                                            <p>
                                                <span class="font-medium">
                                                    Type:
                                                </span>

                                                {{ ucwords(str_replace('_', ' ', $document->document_type)) }}
                                            </p>

                                            <p>
                                                <span class="font-medium">
                                                    File:
                                                </span>

                                                {{ $document->original_name }}
                                            </p>

                                            <p>
                                                <span class="font-medium">
                                                    Uploaded:
                                                </span>

                                                {{ $document->created_at?->format('d M Y, h:i A') }}
                                            </p>

                                            @if ($document->google_drive_file_id)

                                                <p class="text-green-700">
                                                    ✓ Google Drive linked
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                    <div class="flex flex-wrap gap-2">

                                        <a
                                            href="{{ route('admin.documents.show', $document) }}"
                                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                                        >
                                            Review
                                        </a>

                                        <a
                                            href="{{ route('admin.documents.download', $document) }}"
                                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                                        >
                                            Download
                                        </a>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-xl border border-dashed border-gray-300 px-6 py-10 text-center">

                        <div class="text-3xl mb-3">
                            📄
                        </div>

                        <h4 class="font-semibold text-gray-900">
                            No documents uploaded
                        </h4>

                        <p class="mt-1 text-sm text-gray-500">
                            This user has not uploaded any CNIC or other documents yet.
                        </p>

                    </div>

                @endif

            </div>

            {{-- Approval --}}
            @if ($user->role !== 'admin' && ! $user->is_approved)

                <div class="bg-white shadow-sm sm:rounded-xl p-6 border border-amber-200">

                    <h3 class="text-lg font-bold text-gray-900">
                        Account Approval
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        This user is currently waiting for administrator approval.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('admin.users.approve', $user) }}"
                        class="mt-4"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700"
                        >
                            Approve User
                        </button>

                    </form>

                </div>

            @endif

            {{-- Role management --}}
            @if ($user->id !== auth()->id())

                <div class="bg-white shadow-sm sm:rounded-xl p-6">

                    <div class="mb-5">

                        <h3 class="text-lg font-bold text-gray-900">
                            Role Management
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Change the access role assigned to this account.
                        </p>

                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.users.role', $user) }}"
                        class="flex flex-col sm:flex-row gap-3"
                    >

                        @csrf
                        @method('PATCH')

                        <select
                            name="role"
                            class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                            class="inline-flex items-center justify-center rounded-lg bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900"
                        >
                            Update Role
                        </button>

                    </form>

                </div>

            @endif

            {{-- Danger zone --}}
            @if ($user->id !== auth()->id())

                <div class="bg-white shadow-sm sm:rounded-xl p-6 border border-red-200">

                    <h3 class="text-lg font-bold text-red-700">
                        Danger Zone
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Permanently delete this user account and its associated data.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('admin.users.destroy', $user) }}"
                        class="mt-4"
                        onsubmit="return confirm('Are you sure you want to permanently delete this user?');"
                    >

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
                        >
                            Delete User
                        </button>

                    </form>

                </div>

            @endif

        </div>
    </div>
</x-app-layout>