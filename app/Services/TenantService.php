<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

class TenantService
{
    public function getAllTenants(): Collection
    {
        return Tenant::all();
    }
    
    public function getTenantByIdentifier(string $identifier): ?Tenant
    {
        return Tenant::where('identifier', $identifier)->first();
    }
    
    public function createTenant(array $data): Tenant
    {
        return Tenant::create($data);
    }
    
    public function updateTenant(Tenant $tenant, array $data): bool
    {
        return $tenant->update($data);
    }
    
    public function deleteTenant(Tenant $tenant): bool
    {
        return $tenant->delete();
    }
}
