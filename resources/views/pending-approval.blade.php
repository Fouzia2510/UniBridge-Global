<x-guest-layout>
    <div class="text-center py-8">
        <div class="text-5xl mb-4">⏳</div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Your account is pending approval</h2>
        <p class="text-gray-600 mb-6">
            Thanks for signing up! An administrator needs to review and approve your account before you can access the platform. This usually doesn't take long — please check back soon.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                Log out
            </button>
        </form>
    </div>
</x-guest-layout>