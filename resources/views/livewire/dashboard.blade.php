<div>
    <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>

        <p class="mt-2 text-gray-600">
            You're logged in to tenant: {{ $tenant->name ?? 'N/A' }} (ID: {{ $tenantId ?? 'N/A' }})
        </p>

        @impersonating($guard = null)
            <p class="mt-1 text-sm text-yellow-600 font-semibold">
                ⚠️ You are currently impersonating this user.
            </p>
            <a href="{{ route('impersonate.leave') }}">Leave impersonation</a>
        @endImpersonating
    </div>
</div>
