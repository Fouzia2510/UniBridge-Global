<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-bold text-indigo-700">
                    UniBridge
                </p>

                <h2 class="text-2xl font-extrabold text-slate-950">
                    My Documents
                </h2>

                <p class="mt-1 text-sm text-slate-600">
                    Upload and manage your important study-abroad documents.
                </p>
            </div>

            <a
                href="{{ route('profile.edit') }}"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                Back to Profile
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-extrabold text-emerald-700">
                            ✓
                        </div>

                        <p class="pt-1 text-sm font-semibold text-emerald-800">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            @endif


            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm">
                    <p class="font-bold text-red-800">
                        Please fix the following:
                    </p>

                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Google Drive --}}
            @php
                $googleDriveConnected =
                    is_array(auth()->user()->google_drive_oauth_token ?? null)
                    && !empty(auth()->user()->google_drive_oauth_token['access_token']);
            @endphp

            <div class="overflow-hidden rounded-2xl border border-blue-200 bg-gradient-to-r from-blue-50 via-indigo-50 to-violet-50 shadow-sm">

                <div class="p-6">

                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                        <div class="flex items-start gap-4">

                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-xl font-extrabold text-blue-700 shadow-sm">
                                GD
                            </div>

                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-lg font-extrabold text-slate-950">
                                        Google Drive
                                    </h3>

                                    @if ($googleDriveConnected)
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-extrabold text-emerald-700">
                                            Connected
                                        </span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-extrabold text-amber-700">
                                            Not Connected
                                        </span>
                                    @endif
                                </div>

                                @if ($googleDriveConnected)
                                    <p class="mt-1 text-sm text-slate-600">
                                        Your UniBridge documents can be backed up to your connected Google Drive.
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-slate-600">
                                        Connect Google Drive to securely manage your UniBridge documents.
                                    </p>
                                @endif
                            </div>

                        </div>


                        <div class="flex flex-wrap gap-2">

                            <a
                                href="{{ route('google.drive.connect') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700"
                            >
                                {{ $googleDriveConnected ? 'Reconnect Google Drive' : 'Connect Google Drive' }}
                            </a>

                        </div>

                    </div>


                    <div class="mt-5 rounded-xl border border-blue-100 bg-white/80 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-extrabold text-blue-700">
                                i
                            </div>

                            <p class="text-xs leading-5 text-slate-600">
                                Your documents remain private. UniBridge only requests the
                                Google Drive permission needed for files created or managed
                                by this application.
                            </p>
                        </div>
                    </div>

                </div>

            </div>


            {{-- Upload new document --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="mb-5">
                    <h3 class="text-lg font-extrabold text-slate-950">
                        Upload New Document
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Accepted: PDF, JPG, JPEG, PNG and WEBP. Maximum size: 5 MB.
                    </p>
                </div>


                <form
                    method="POST"
                    action="{{ route('documents.store') }}"
                    enctype="multipart/form-data"
                    class="grid gap-5 md:grid-cols-2"
                >
                    @csrf


                    <div>
                        <label
                            for="document_type"
                            class="block text-sm font-bold text-slate-700"
                        >
                            Document Type
                        </label>

                        <select
                            id="document_type"
                            name="document_type"
                            required
                            class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">
                                Select document type
                            </option>

                            <option value="cnic" @selected(old('document_type') === 'cnic')>
                                CNIC
                            </option>

                            <option value="passport" @selected(old('document_type') === 'passport')>
                                Passport
                            </option>

                            <option value="degree" @selected(old('document_type') === 'degree')>
                                Degree
                            </option>

                            <option value="transcript" @selected(old('document_type') === 'transcript')>
                                Transcript
                            </option>

                            <option value="english_certificate" @selected(old('document_type') === 'english_certificate')>
                                English Certificate
                            </option>

                            <option value="other" @selected(old('document_type') === 'other')>
                                Other
                            </option>
                        </select>
                    </div>


                    <div>
                        <label
                            for="title"
                            class="block text-sm font-bold text-slate-700"
                        >
                            Document Title
                        </label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            required
                            maxlength="150"
                            placeholder="e.g. Bachelor's Degree"
                            class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>


                    <div class="md:col-span-2">
                        <label
                            for="document"
                            class="block text-sm font-bold text-slate-700"
                        >
                            Select File
                        </label>

                        <input
                            id="document"
                            type="file"
                            name="document"
                            required
                            accept=".pdf,.jpg,.jpeg,.png,.webp"
                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm"
                        />
                    </div>


                    <div class="md:col-span-2">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            Upload Document
                        </button>
                    </div>

                </form>

            </div>


            {{-- Uploaded documents --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="mb-5">
                    <h3 class="text-lg font-extrabold text-slate-950">
                        Uploaded Documents
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Your uploaded documents and their verification status.
                    </p>
                </div>


                @if ($documents->count())

                    <div class="space-y-4">

                        @foreach ($documents as $document)

                            <div class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-indigo-200 hover:shadow-sm">

                                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">

                                    <div class="flex min-w-0 items-start gap-4">

                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-sm font-extrabold text-slate-700">
                                            DOC
                                        </div>


                                        <div class="min-w-0">

                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="font-extrabold text-slate-950">
                                                    {{ $document->title }}
                                                </h4>

                                                @if ($document->verification_status === 'approved')

                                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                                                        Approved
                                                    </span>

                                                @elseif ($document->verification_status === 'rejected')

                                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                                        Rejected
                                                    </span>

                                                @else

                                                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">
                                                        Pending
                                                    </span>

                                                @endif
                                            </div>


                                            <p class="mt-1 truncate text-sm text-slate-500">
                                                {{ $document->original_name }}
                                            </p>


                                            <div class="mt-3 flex flex-wrap gap-2">

                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700">
                                                    {{ ucwords(str_replace('_', ' ', $document->document_type)) }}
                                                </span>


                                                @if ($document->google_drive_file_id)

                                                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">
                                                        Google Drive Linked
                                                    </span>

                                                @endif


                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                                    {{ $document->created_at?->format('d M Y') }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>


                                    <div class="flex flex-wrap gap-2">

                                        {{-- View in Google Drive --}}
                                        @if ($document->google_drive_url)

                                            <a
                                                href="{{ $document->google_drive_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 transition hover:bg-blue-100"
                                            >
                                                View in Drive
                                            </a>

                                        @endif


                                        {{-- Download --}}
                                        <a
                                            href="{{ route('documents.download', $document) }}"
                                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                                        >
                                            Download
                                        </a>


                                        {{-- Replace --}}
                                        <button
                                            type="button"
                                            onclick="document.getElementById('replace-document-{{ $document->id }}').classList.toggle('hidden')"
                                            class="inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100"
                                        >
                                            Replace
                                        </button>


                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route('documents.destroy', $document) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this document?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-700"
                                            >
                                                Delete
                                            </button>
                                        </form>

                                    </div>

                                </div>


                                {{-- Replace document form --}}
                                <div
                                    id="replace-document-{{ $document->id }}"
                                    class="mt-5 hidden rounded-2xl border border-indigo-100 bg-indigo-50/60 p-5"
                                >

                                    <div class="mb-4">
                                        <h5 class="font-extrabold text-slate-950">
                                            Replace Document
                                        </h5>

                                        <p class="mt-1 text-sm text-slate-600">
                                            Upload a new file to replace the current document.
                                            The verification status will return to Pending.
                                        </p>
                                    </div>


                                    <form
                                        method="POST"
                                        action="{{ route('documents.update', $document) }}"
                                        enctype="multipart/form-data"
                                        class="grid gap-4 md:grid-cols-2"
                                    >
                                        @csrf
                                        @method('PUT')


                                        <div>
                                            <label
                                                for="replace_type_{{ $document->id }}"
                                                class="block text-sm font-bold text-slate-700"
                                            >
                                                Document Type
                                            </label>

                                            <select
                                                id="replace_type_{{ $document->id }}"
                                                name="document_type"
                                                required
                                                class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="cnic" @selected($document->document_type === 'cnic')>
                                                    CNIC
                                                </option>

                                                <option value="passport" @selected($document->document_type === 'passport')>
                                                    Passport
                                                </option>

                                                <option value="degree" @selected($document->document_type === 'degree')>
                                                    Degree
                                                </option>

                                                <option value="transcript" @selected($document->document_type === 'transcript')>
                                                    Transcript
                                                </option>

                                                <option value="english_certificate" @selected($document->document_type === 'english_certificate')>
                                                    English Certificate
                                                </option>

                                                <option value="other" @selected($document->document_type === 'other')>
                                                    Other
                                                </option>
                                            </select>
                                        </div>


                                        <div>
                                            <label
                                                for="replace_title_{{ $document->id }}"
                                                class="block text-sm font-bold text-slate-700"
                                            >
                                                Document Title
                                            </label>

                                            <input
                                                id="replace_title_{{ $document->id }}"
                                                type="text"
                                                name="title"
                                                value="{{ $document->title }}"
                                                required
                                                maxlength="150"
                                                class="mt-2 block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                        </div>


                                        <div class="md:col-span-2">
                                            <label
                                                for="replace_file_{{ $document->id }}"
                                                class="block text-sm font-bold text-slate-700"
                                            >
                                                New File
                                            </label>

                                            <input
                                                id="replace_file_{{ $document->id }}"
                                                type="file"
                                                name="document"
                                                required
                                                accept=".pdf,.jpg,.jpeg,.png,.webp"
                                                class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm"
                                            >
                                        </div>


                                        <div class="flex flex-wrap gap-2 md:col-span-2">

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-extrabold text-white shadow-sm transition hover:bg-indigo-700"
                                            >
                                                Replace Document
                                            </button>

                                            <button
                                                type="button"
                                                onclick="document.getElementById('replace-document-{{ $document->id }}').classList.add('hidden')"
                                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Cancel
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-sm font-extrabold text-slate-600">
                            DOC
                        </div>

                        <h4 class="mt-3 font-extrabold text-slate-900">
                            No documents uploaded yet
                        </h4>

                        <p class="mt-1 text-sm text-slate-500">
                            Upload your CNIC, passport, degree, transcript or other required documents above.
                        </p>

                    </div>

                @endif

            </div>


            {{-- Privacy --}}
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-sm font-extrabold text-emerald-700">
                        SEC
                    </div>

                    <div>
                        <h4 class="font-extrabold text-emerald-900">
                            Document Privacy
                        </h4>

                        <p class="mt-1 text-sm leading-6 text-emerald-800">
                            Your uploaded documents are stored privately and are not exposed
                            through the public storage URL. Only authorized access can download
                            your documents.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>