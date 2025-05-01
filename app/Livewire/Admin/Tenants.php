<?php

namespace App\Livewire\Admin;

use App\Services\TenantService;
use Livewire\Component;
use Livewire\WithPagination;

class Tenants extends Component
{
    use WithPagination;

    public string $name = '';
    public string $identifier = '';
    public ?int $editingTenantId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'identifier' => 'required|string|max:255|alpha_dash|unique:tenants,identifier',
    ];

    public function createTenant(TenantService $tenantService)
    {
        $this->validate();

        $tenantService->createTenant([
            'name' => $this->name,
            'identifier' => $this->identifier,
        ]);

        $this->reset(['name', 'identifier']);
        session()->flash('message', 'Tenant created successfully.');
    }

    public function editTenant(int $tenantId, TenantService $tenantService)
    {
        $tenant = $tenantService->getAllTenants()->find($tenantId);
        $this->editingTenantId = $tenant->id;
        $this->name = $tenant->name;
        $this->identifier = $tenant->identifier;
    }

    public function updateTenant(TenantService $tenantService)
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'identifier' => 'required|string|max:255|alpha_dash|unique:tenants,identifier,' . $this->editingTenantId,
        ]);

        $tenant = $tenantService->getAllTenants()->find($this->editingTenantId);
        $tenantService->updateTenant($tenant, [
            'name' => $this->name,
            'identifier' => $this->identifier,
        ]);

        $this->reset(['name', 'identifier', 'editingTenantId']);
        session()->flash('message', 'Tenant updated successfully.');
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'identifier', 'editingTenantId']);
    }

    public function deleteTenant(int $tenantId, TenantService $tenantService)
    {
        $tenant = $tenantService->getAllTenants()->find($tenantId);
        $tenantService->deleteTenant($tenant);

        session()->flash('message', 'Tenant deleted successfully.');
    }

    public function render(TenantService $tenantService)
    {
        return view('livewire.admin.tenants', [
            'tenants' => $tenantService->getAllTenants(),
        ]);
    }
}
