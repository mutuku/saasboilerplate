<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\TenantService;
use Livewire\Component;

class Dashboard extends Component
{
    public function render(TenantService $tenantService)
    {
         // Get tenant ID directly from the authenticated user
        $tenantId = auth()->user()?->tenant_id;

        $tenant = $tenantService->getAllTenants()->find($tenantId);

        // Get tenant users if the user is authenticated
        $users = [];
        if (auth()->check()) {
            $users = User::where('tenant_id', $tenantId)->get();
        }

        return view('livewire.dashboard', [
            'tenant' => $tenant,
            'tenantId' => $tenantId,
            'users' => $users,
        ]);


    }
}
