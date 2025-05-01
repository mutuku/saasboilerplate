<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class SuperAdminUserManager extends Component
{
    use WithPagination;

    public $userId, $name, $email, $tenant_id, $isEditMode = false;
    public $searchTerm = '';
    public $confirmingUserDeletion = false;
    public $userToDelete = null;
    public $confirmingUserDeactivation = false;
    public $userToToggle = null;
    public $recentlyModifiedUserId = null;

    protected UserService $userService;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function mount()
    {
        $this->loadUsers();
    }

    public function getUsersProperty()
    {
        return User::query()
            ->when($this->searchTerm, function ($query) {
                $query->where('name', 'like', "%{$this->searchTerm}%")
                      ->orWhere('email', 'like', "%{$this->searchTerm}%");
            })
            ->paginate(10);
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ]);

        try {
            $user = $this->userService->createUser([
                'name' => $this->name,
                'email' => $this->email,
                'tenant_id' => $this->tenant_id,
            ]);
            $this->recentlyModifiedUserId = $user->id;
            $this->dispatch('notify', type: 'success', message: 'User created and instructions emailed.');
            $this->resetForm();
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        if ($user->is_super_admin && !Auth::user()->is_super_admin) {
            $this->dispatch('notify', type: 'error', message: 'You cannot edit another super admin.');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->tenant_id = $user->tenant_id;
        $this->isEditMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => "required|email|unique:users,email,{$this->userId}",
        ]);

        $user = User::findOrFail($this->userId);
        if ($user->is_super_admin && !Auth::user()->is_super_admin) {
            $this->dispatch('notify', type: 'error', message: 'You cannot edit another super admin.');
            return;
        }

        $this->userService->updateUser($user, [
            'name' => $this->name,
            'email' => $this->email,
            'tenant_id' => $this->tenant_id,
        ]);

        $this->recentlyModifiedUserId = $user->id;
        $this->dispatch('notify', type: 'success', message: 'User updated successfully.');
        $this->resetForm();
        $this->dispatch('switch-to-list-tab');
    }

    public function confirmDelete($id)
    {
        $this->userToDelete = User::find($id);
        if ($this->userToDelete?->is_super_admin && !Auth::user()->is_super_admin) {
            $this->dispatch('notify', type: 'error', message: 'You cannot delete a super admin.');
            return;
        }
        $this->confirmingUserDeletion = true;
    }

    public function delete()
    {
        if (!$this->userToDelete) return;

        try {
            if ($this->userToDelete->id === Auth::id()) {
                $this->dispatch('notify', type: 'error', message: 'You cannot delete your own account.');
                return;
            }
            if ($this->userToDelete->is_super_admin) {
                $this->dispatch('notify', type: 'error', message: 'You cannot delete a super admin.');
                return;
            }

            $this->userService->deleteUser($this->userToDelete);
            $this->userToDelete = null;
            $this->confirmingUserDeactivation = false;
            $this->dispatch('notify', type: 'success', message: 'User deleted successfully.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function confirmToggleActivation($id)
    {
        $this->userToToggle = User::find($id);
        if ($this->userToToggle?->is_super_admin && !Auth::user()->is_super_admin) {
            $this->dispatch('notify', type: 'error', message: 'You cannot deactivate a super admin.');
            return;
        }
        $this->confirmingUserDeactivation = true;
    }

    public function toggleActivation()
    {
        if (!$this->userToToggle) return;

        try {
            if ($this->userToToggle->id === Auth::id()) {
                $this->dispatch('notify', type: 'error', message: 'You cannot deactivate your own account.');
                return;
            }

            $this->userService->toggleActivation($this->userToToggle);
            $this->userToToggle = null;
            $this->confirmingUserDeactivation = false;
            $this->dispatch('notify', type: 'success', message: 'User status updated successfully.');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to toggle activation: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->name = $this->email = $this->tenant_id = '';
        $this->userId = null;
        $this->isEditMode = false;
    }

    public function loadUsers()
    {
        $this->users = $this->userService->getAllUsers();
    }

    public function render()
    {
        return view('livewire.admin.super-admin-user-manager', [
            'tenants' => \App\Models\Tenant::all(),
            'users' => $this->getUsersProperty(),
        ]);
    }
}
