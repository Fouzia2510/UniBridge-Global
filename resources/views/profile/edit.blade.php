<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ========================================================= --}}
            {{-- PROFILE PHOTO --}}
            {{-- ========================================================= --}}

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">

                    <h3 class="text-lg font-semibold text-gray-900">
                        Profile Picture
                    </h3>

                    <p class="mt-1 text-sm text-gray-600">
                        Choose a profile picture, preview it, then save it.
                        JPG, JPEG, PNG or WEBP files are allowed.
                    </p>

                    @if (session('status') === 'profile-photo-updated')
                        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            Profile picture updated successfully.
                        </div>
                    @endif

                    @if (session('status') === 'profile-photo-deleted')
                        <div class="mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                            Profile picture removed successfully.
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                            <ul class="list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- CURRENT PHOTO --}}

                    <div class="mt-6">
                        <div class="flex justify-center sm:justify-start">

                            @if ($user->profile_photo_path)

                                <button
                                    type="button"
                                    id="open-profile-photo"
                                    class="group block rounded-full focus:outline-none focus:ring-4 focus:ring-indigo-300"
                                    title="Click to view profile picture"
                                >
                                    <img
                                        id="profile-photo-preview"
                                        src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                        alt="{{ $user->name }}"
                                        class="h-20 w-20 rounded-full object-cover border-4 border-indigo-100 shadow-lg cursor-pointer transition duration-200 group-hover:scale-105 group-hover:opacity-90"
                                    >
                                </button>

                            @else

                                <div
                                    id="profile-photo-placeholder"
                                    class="h-20 w-20 rounded-full bg-indigo-600 text-white flex items-center justify-center text-2xl font-bold border-4 border-indigo-100 shadow-lg"
                                >
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>

                                <img
                                    id="profile-photo-preview"
                                    src=""
                                    alt="Profile preview"
                                    class="hidden h-20 w-20 rounded-full object-cover border-4 border-indigo-100 shadow-lg cursor-pointer"
                                >

                            @endif

                        </div>

                        @if ($user->profile_photo_path)
                            <p class="mt-2 text-xs text-gray-500">
                                Click the picture to view it.
                            </p>
                        @endif
                    </div>

                    {{-- UPLOAD FORM --}}

                    <form
                        id="profile-photo-form"
                        method="POST"
                        action="{{ route('profile.photo.update') }}"
                        enctype="multipart/form-data"
                        class="mt-6"
                    >

                        @csrf

                        <div>

                            <label
                                for="profile_photo"
                                class="block text-sm font-semibold text-gray-700"
                            >
                                Choose New Picture
                            </label>

                            <input
                                id="profile_photo"
                                name="profile_photo"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="mt-2 block w-full text-sm text-gray-700
                                       file:mr-4
                                       file:rounded-lg
                                       file:border-0
                                       file:bg-indigo-50
                                       file:px-4
                                       file:py-2
                                       file:text-sm
                                       file:font-semibold
                                       file:text-indigo-700
                                       hover:file:bg-indigo-100
                                       cursor-pointer"
                                required
                            >

                            <p class="mt-2 text-xs text-gray-500">
                                Maximum size: 2 MB
                            </p>

                        </div>

                        <div
                            id="selected-file-box"
                            class="hidden mt-4 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3"
                        >
                            <p class="text-sm font-semibold text-indigo-900">
                                Selected Picture
                            </p>

                            <p
                                id="selected-file-name"
                                class="mt-1 text-sm text-indigo-700 break-all"
                            ></p>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center gap-3">

                            <button
                                id="profile-photo-save"
                                type="submit"
                                disabled
                                class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-40"
                            >
                                Save Picture
                            </button>

                            <button
                                id="profile-photo-change"
                                type="button"
                                class="inline-flex items-center rounded-lg border border-indigo-200 bg-white px-6 py-2.5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Change Picture
                            </button>

                        </div>

                    </form>

                    {{-- REMOVE PHOTO --}}

                    @if ($user->profile_photo_path)

                        <form
                            method="POST"
                            action="{{ route('profile.photo.delete') }}"
                            class="mt-3"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-lg border border-red-200 bg-white px-6 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                onclick="return confirm('Are you sure you want to remove your profile picture?')"
                            >
                                Remove Picture
                            </button>

                        </form>

                    @endif

                </div>
            </div>


            {{-- ========================================================= --}}
            {{-- PASSKEY / BIOMETRIC SECURITY --}}
            {{-- ========================================================= --}}

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="max-w-xl">

                    <h3 class="text-lg font-semibold text-gray-900">
                        Biometric & Passkey Security
                    </h3>

                    <p class="mt-1 text-sm text-gray-600">
                        Add a passkey to securely sign in using Windows Hello,
                        fingerprint, Face ID, or another device-supported
                        biometric method.
                    </p>

                    <div class="mt-5 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-4">

                        <div class="flex items-start gap-3">

                            <div class="mt-0.5 text-indigo-600 text-lg">
                                🔐
                            </div>

                            <div>

                                <p class="text-sm font-semibold text-indigo-900">
                                    Your biometric data stays on your device
                                </p>

                                <p class="mt-1 text-xs leading-5 text-indigo-700">
                                    UniBridge does not receive or store your
                                    fingerprint or face image. Your device handles
                                    biometric verification and sends a secure
                                    passkey credential.
                                </p>

                            </div>

                        </div>

                    </div>

                    {{-- STATUS --}}

                    <div
                        id="passkey-status"
                        class="hidden mt-4 rounded-lg px-4 py-3 text-sm font-medium"
                    ></div>

                    {{-- BUTTON --}}

                    <div class="mt-5 flex flex-wrap items-center gap-3">

                        <button
                            type="button"
                            id="register-passkey-button"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Add Passkey
                        </button>

                        <span
                            id="passkey-support"
                            class="text-xs text-gray-500"
                        >
                            Checking browser support...
                        </span>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- PROFILE INFORMATION --}}
            {{-- ========================================================= --}}

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="max-w-xl">

                    @include(
                        'profile.partials.update-profile-information-form'
                    )

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- PASSWORD --}}
            {{-- ========================================================= --}}

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="max-w-xl">

                    @include(
                        'profile.partials.update-password-form'
                    )

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- DELETE ACCOUNT --}}
            {{-- ========================================================= --}}

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="max-w-xl">

                    @include(
                        'profile.partials.delete-user-form'
                    )

                </div>

            </div>

        </div>
    </div>


    {{-- ================================================================ --}}
    {{-- PROFILE PHOTO MODAL --}}
    {{-- ================================================================ --}}

    @if ($user->profile_photo_path)

        <div
            id="profile-photo-modal"
            class="hidden fixed inset-0 z-[9999] items-center justify-center bg-black/80 p-4"
        >

            <div
                id="profile-photo-modal-background"
                class="absolute inset-0"
            ></div>

            <div
                class="relative z-10 flex max-h-[90vh] max-w-[90vw] flex-col items-center"
            >

                <button
                    type="button"
                    id="close-profile-photo"
                    class="absolute -right-3 -top-3 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white text-2xl font-bold text-gray-700 shadow-lg hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-white/50"
                    title="Close"
                >
                    &times;
                </button>

                <img
                    id="profile-photo-large"
                    src="{{ asset('storage/' . $user->profile_photo_path) }}"
                    alt="{{ $user->name }}"
                    class="max-h-[80vh] max-w-[85vw] rounded-xl object-contain shadow-2xl"
                >

                <p class="mt-3 rounded-lg bg-black/60 px-4 py-2 text-sm font-medium text-white">
                    {{ $user->name }} — Profile Picture
                </p>

            </div>

        </div>

    @endif


    {{-- ================================================================ --}}
    {{-- JAVASCRIPT --}}
    {{-- ================================================================ --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            /* =============================================================
               PROFILE PHOTO
            ============================================================= */

            const input =
                document.getElementById('profile_photo');

            const preview =
                document.getElementById('profile-photo-preview');

            const placeholder =
                document.getElementById('profile-photo-placeholder');

            const form =
                document.getElementById('profile-photo-form');

            const saveButton =
                document.getElementById('profile-photo-save');

            const changeButton =
                document.getElementById('profile-photo-change');

            const selectedFileBox =
                document.getElementById('selected-file-box');

            const selectedFileName =
                document.getElementById('selected-file-name');


            if (input) {

                input.addEventListener('change', function (event) {

                    const file =
                        event.target.files &&
                        event.target.files[0];

                    if (!file) {

                        if (saveButton) {
                            saveButton.disabled = true;
                        }

                        if (selectedFileBox) {
                            selectedFileBox.classList.add('hidden');
                        }

                        return;
                    }


                    const allowedTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ];


                    if (!allowedTypes.includes(file.type)) {

                        alert(
                            'Please select a JPG, JPEG, PNG or WEBP image.'
                        );

                        input.value = '';

                        if (saveButton) {
                            saveButton.disabled = true;
                        }

                        if (selectedFileBox) {
                            selectedFileBox.classList.add('hidden');
                        }

                        return;
                    }


                    const maxSize =
                        2 * 1024 * 1024;


                    if (file.size > maxSize) {

                        alert(
                            'The image must be 2 MB or smaller.'
                        );

                        input.value = '';

                        if (saveButton) {
                            saveButton.disabled = true;
                        }

                        if (selectedFileBox) {
                            selectedFileBox.classList.add('hidden');
                        }

                        return;
                    }


                    if (selectedFileName) {
                        selectedFileName.textContent =
                            file.name;
                    }

                    if (selectedFileBox) {
                        selectedFileBox.classList.remove('hidden');
                    }


                    if (preview) {

                        const reader =
                            new FileReader();

                        reader.onload =
                            function (event) {

                                preview.src =
                                    event.target.result;

                                preview.classList.remove(
                                    'hidden'
                                );

                                if (placeholder) {
                                    placeholder.classList.add(
                                        'hidden'
                                    );
                                }

                            };

                        reader.readAsDataURL(file);
                    }


                    if (saveButton) {
                        saveButton.disabled = false;
                    }

                });

            }


            if (changeButton && input) {

                changeButton.addEventListener(
                    'click',
                    function () {
                        input.click();
                    }
                );

            }


            if (form) {

                form.addEventListener(
                    'submit',
                    function () {

                        if (
                            !input ||
                            !input.files ||
                            !input.files.length
                        ) {

                            alert(
                                'Please choose a picture first.'
                            );

                            return;
                        }


                        if (saveButton) {

                            saveButton.disabled = true;

                            saveButton.textContent =
                                'Saving...';

                            saveButton.classList.add(
                                'opacity-75',
                                'cursor-not-allowed'
                            );

                        }

                    }
                );

            }


            /* =============================================================
               PROFILE PHOTO MODAL
            ============================================================= */

            const openButton =
                document.getElementById(
                    'open-profile-photo'
                );

            const modal =
                document.getElementById(
                    'profile-photo-modal'
                );

            const closeButton =
                document.getElementById(
                    'close-profile-photo'
                );

            const modalBackground =
                document.getElementById(
                    'profile-photo-modal-background'
                );


            if (openButton && modal) {

                openButton.addEventListener(
                    'click',
                    function () {

                        modal.classList.remove(
                            'hidden'
                        );

                        modal.classList.add(
                            'flex'
                        );

                        document.body.classList.add(
                            'overflow-hidden'
                        );

                    }
                );

            }


            function closeProfilePhoto() {

                if (!modal) {
                    return;
                }

                modal.classList.add(
                    'hidden'
                );

                modal.classList.remove(
                    'flex'
                );

                document.body.classList.remove(
                    'overflow-hidden'
                );

            }


            if (closeButton) {

                closeButton.addEventListener(
                    'click',
                    closeProfilePhoto
                );

            }


            if (modalBackground) {

                modalBackground.addEventListener(
                    'click',
                    closeProfilePhoto
                );

            }


            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key === 'Escape' &&
                        modal &&
                        !modal.classList.contains('hidden')
                    ) {

                        closeProfilePhoto();

                    }

                }
            );


            /* =============================================================
               PASSKEY / BIOMETRIC SECURITY
            ============================================================= */

            const passkeyButton =
                document.getElementById(
                    'register-passkey-button'
                );

            const passkeySupport =
                document.getElementById(
                    'passkey-support'
                );

            const passkeyStatus =
                document.getElementById(
                    'passkey-status'
                );


            function showPasskeyStatus(
                message,
                type = 'success'
            ) {

                if (!passkeyStatus) {
                    return;
                }


                passkeyStatus.textContent =
                    message;


                passkeyStatus.classList.remove(
                    'hidden',
                    'border',
                    'border-green-200',
                    'bg-green-50',
                    'text-green-700',
                    'border-red-200',
                    'bg-red-50',
                    'text-red-700',
                    'border-yellow-200',
                    'bg-yellow-50',
                    'text-yellow-700'
                );


                if (type === 'success') {

                    passkeyStatus.classList.add(
                        'border',
                        'border-green-200',
                        'bg-green-50',
                        'text-green-700'
                    );

                } else if (type === 'error') {

                    passkeyStatus.classList.add(
                        'border',
                        'border-red-200',
                        'bg-red-50',
                        'text-red-700'
                    );

                } else {

                    passkeyStatus.classList.add(
                        'border',
                        'border-yellow-200',
                        'bg-yellow-50',
                        'text-yellow-700'
                    );

                }


                passkeyStatus.classList.remove(
                    'hidden'
                );

            }


            async function setupPasskey() {

                if (!passkeyButton) {
                    return;
                }


                try {

                    if (
                        !window.Passkeys ||
                        !window.Passkeys.isSupported()
                    ) {

                        if (passkeySupport) {

                            passkeySupport.textContent =
                                'Passkeys are not supported by this browser or device.';

                            passkeySupport.classList.add(
                                'text-red-600'
                            );

                        }


                        passkeyButton.disabled =
                            true;

                        passkeyButton.classList.add(
                            'cursor-not-allowed',
                            'opacity-40'
                        );

                        return;
                    }


                    if (passkeySupport) {

                        passkeySupport.textContent =
                            'Passkey support is available on this device.';

                        passkeySupport.classList.add(
                            'text-green-600'
                        );

                    }


                    passkeyButton.addEventListener(
                        'click',
                        async function () {

                            const passkeyName =
                                prompt(
                                    'Enter a name for this passkey:',
                                    'My Windows Device'
                                );


                            if (
                                !passkeyName ||
                                !passkeyName.trim()
                            ) {
                                return;
                            }


                            passkeyButton.disabled =
                                true;

                            passkeyButton.textContent =
                                'Waiting for verification...';


                            showPasskeyStatus(
                                'Follow the security prompt from your device.',
                                'info'
                            );


                            try {

                                await window.Passkeys.register({
                                    name:
                                        passkeyName.trim()
                                });


                                showPasskeyStatus(
                                    'Passkey added successfully. Your device can now use it for secure authentication.',
                                    'success'
                                );


                                passkeyButton.textContent =
                                    'Passkey Added';


                                setTimeout(
                                    function () {
                                        window.location.reload();
                                    },
                                    1500
                                );


                            } catch (error) {

                                console.error(
                                    'Passkey registration error:',
                                    error
                                );


                                let message =
                                    'Passkey registration could not be completed.';


                                if (
                                    error &&
                                    error.name ===
                                    'NotSupportedError'
                                ) {

                                    message =
                                        'This browser or device does not support passkeys.';

                                } else if (
                                    error &&
                                    error.name ===
                                    'UserCancelledError'
                                ) {

                                    message =
                                        'Passkey registration was cancelled.';

                                } else if (
                                    error &&
                                    error.name ===
                                    'PasskeyExistsError'
                                ) {

                                    message =
                                        'A passkey for this account/device is already registered.';

                                } else if (
                                    error &&
                                    error.name ===
                                    'InvalidDomainError'
                                ) {

                                    message =
                                        'This domain is not configured correctly for passkeys.';

                                } else if (
                                    error &&
                                    error.message
                                ) {

                                    message =
                                        error.message;

                                }


                                showPasskeyStatus(
                                    message,
                                    'error'
                                );


                                passkeyButton.disabled =
                                    false;

                                passkeyButton.textContent =
                                    'Add Passkey';

                            }

                        }
                    );


                } catch (error) {

                    console.error(
                        'Passkey support check failed:',
                        error
                    );


                    if (passkeySupport) {

                        passkeySupport.textContent =
                            'Unable to check passkey support.';

                        passkeySupport.classList.add(
                            'text-red-600'
                        );

                    }

                }

            }


            setupPasskey();

        });

    </script>

</x-app-layout>