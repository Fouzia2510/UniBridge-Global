<x-app-layout>

    <x-slot name="header">

        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Consultant Profiles
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Manage public consultant profiles and connect them with registered consultant accounts.
            </p>
        </div>

    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))

                <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg">
                    {{ session('status') }}
                </div>

            @endif


            {{-- Validation Errors --}}
            @if ($errors->any())

                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">

                    <p class="font-semibold mb-2">
                        Please fix the following:
                    </p>

                    <ul class="list-disc list-inside text-sm">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Back Navigation --}}
            <div class="mb-6">

                <a
                    href="{{ route('admin.consultants') }}"
                    class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800"
                >
                    Back to Consultants
                </a>

            </div>


            {{-- Introduction --}}
            <div class="mb-8 bg-white shadow-sm sm:rounded-lg p-6">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div>

                        <h3 class="text-lg font-semibold text-gray-900">
                            Public Consultant Directory
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Link each public consultant profile with the correct registered consultant account.
                            This connection allows UniBridge to use the existing consultation booking system.
                        </p>

                    </div>


                    <div class="flex gap-3 text-sm">

                        <div class="px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-gray-500">
                                Profiles
                            </span>

                            <span class="font-semibold text-gray-900 ml-1">
                                {{ $consultants->count() }}
                            </span>
                        </div>


                        <div class="px-4 py-2 bg-purple-50 rounded-lg border border-purple-100">
                            <span class="text-purple-600">
                                Consultant Accounts
                            </span>

                            <span class="font-semibold text-purple-900 ml-1">
                                {{ $consultantUsers->count() }}
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            {{-- Consultant Profiles --}}
            <div class="space-y-6">

                @forelse ($consultants as $consultant)

                    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                        <div class="p-6">

                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

                                {{-- Profile Information --}}
                                <div class="flex-1">

                                    <div class="flex items-start gap-4">

                                        {{-- Logo --}}
                                        <div class="w-14 h-14 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">

                                            <span class="text-xl font-bold text-indigo-600">
                                                {{ strtoupper(substr($consultant->company, 0, 1)) }}
                                            </span>

                                        </div>


                                        <div class="min-w-0">

                                            <div class="flex flex-wrap items-center gap-2">

                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $consultant->company }}
                                                </h3>


                                                @if ($consultant->is_featured)

                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        Featured
                                                    </span>

                                                @endif


                                                @if ($consultant->is_active)

                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>

                                                @else

                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                        Inactive
                                                    </span>

                                                @endif

                                            </div>


                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $consultant->name }}
                                            </p>


                                            @if ($consultant->city)

                                                <p class="text-sm text-gray-500 mt-1">

                                                </p>

                                            @endif

                                        </div>

                                    </div>


                                    @if ($consultant->short_description)

                                        <p class="mt-4 text-sm text-gray-600 leading-6 max-w-3xl">
                                            {{ $consultant->short_description }}
                                        </p>

                                    @endif

                                </div>


                                {{-- Current Link Status --}}
                                <div class="lg:w-72">

                                    @if ($consultant->user)

                                        <div class="p-4 rounded-lg bg-green-50 border border-green-200">

                                            <p class="text-xs font-semibold uppercase tracking-wide text-green-700">
                                                Linked Account
                                            </p>

                                            <p class="mt-1 font-semibold text-green-900">
                                                {{ $consultant->user->name }}
                                            </p>

                                            <p class="text-xs text-green-700 mt-1 break-all">
                                                {{ $consultant->user->email }}
                                            </p>

                                        </div>

                                    @else

                                        <div class="p-4 rounded-lg bg-yellow-50 border border-yellow-200">

                                            <p class="text-xs font-semibold uppercase tracking-wide text-yellow-700">
                                                Not Linked
                                            </p>

                                            <p class="mt-1 text-sm text-yellow-800">
                                                No consultant account is currently connected to this public profile.
                                            </p>

                                        </div>

                                    @endif

                                </div>

                            </div>


                            {{-- Mapping Form --}}
                            <div class="mt-6 pt-6 border-t border-gray-200">

                                <form
                                    method="POST"
                                    action="{{ route('admin.consultant-profiles.update', $consultant) }}"
                                >

                                    @csrf
                                    @method('PATCH')


                                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">

                                        {{-- Consultant Account --}}
                                        <div class="lg:col-span-8">

                                            <label
                                                for="user_id_{{ $consultant->id }}"
                                                class="block text-sm font-medium text-gray-700 mb-2"
                                            >
                                                Registered Consultant Account
                                            </label>


                                            <select
                                                id="user_id_{{ $consultant->id }}"
                                                name="user_id"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >

                                                <option value="">
                                                    No account linked
                                                </option>


                                                @foreach ($consultantUsers as $user)

                                                    <option
                                                        value="{{ $user->id }}"
                                                        @selected($consultant->user_id == $user->id)
                                                    >
                                                        {{ $user->name }} - {{ $user->email }}
                                                    </option>

                                                @endforeach

                                            </select>


                                            <p class="text-xs text-gray-500 mt-2">
                                                Select the actual UniBridge consultant account that belongs to this public profile.
                                            </p>

                                        </div>


                                        {{-- Save --}}
                                        <div class="lg:col-span-4">

                                            <button
                                                type="submit"
                                                class="w-full inline-flex justify-center items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                                            >
                                                Save Account Link
                                            </button>

                                        </div>

                                    </div>

                                </form>

                            </div>


                            {{-- Public Profile --}}
                            <div class="mt-5 flex flex-wrap gap-4">

                                <a
                                    href="{{ route('consultants.show', $consultant) }}"
                                    target="_blank"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                >
                                    View Public Profile
                                </a>


                                @if ($consultant->website)

                                    <a
                                        href="{{ $consultant->website }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm font-medium text-gray-600 hover:text-gray-900"
                                    >
                                        Official Website
                                    </a>

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="bg-white shadow-sm sm:rounded-lg p-10 text-center">

                        <div class="text-4xl mb-4">

                        </div>

                        <h3 class="text-lg font-semibold text-gray-900">
                            No consultant profiles found
                        </h3>

                        <p class="text-sm text-gray-500 mt-2">
                            There are currently no public consultant profiles available.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>

</x-app-layout>





