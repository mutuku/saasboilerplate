<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class UserManager extends Component
{
    use WithPagination;

    // User properties
    public $userId, $name, $email, $password, $tenant_id;
    public $is_tenant_admin = false;
    public $is_super_admin = false;
    public $permissions = [];
    public $searchTerm = '';

    // UI state properties
    public $isEditMode = false;
    public $confirmingUserDeletion = false;
    public $userToDelete = null;
    public $confirmingUserDeactivation = false;
    public $userToToggle = null;
    public $recentlyModifiedUserId = null;

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
            'tenant_id' => Auth::user()->is_super_admin ? 'required|exists:tenants,id' : '',
        ];
    }

    protected UserService $userService;

    public function boot(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function mount()
    {
        // Set default tenant_id for tenant admins
        if (Auth::user()->is_tenant_admin) {
            $this->tenant_id = Auth::user()->tenant_id;
        }
    }

    public function getUsersProperty()
    {
        $user = Auth::user();
        $query = User::query();

        // Filter users based on current user's role
        if ($user->is_super_admin) {
            // Super admin can see all users
        } elseif ($user->is_tenant_admin) {
            // Tenant admin can only see users from their tenant
            $query->where('tenant_id', $user->tenant_id);
        } else {
            // Regular users can only see themselves
            $query->where('id', $user->id);
        }

        // Apply search filter if provided
        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            });
        }

        return $query->with('tenant')->paginate(10);
    }

    public function create()
    {
        $this->validate();

        try {
            // Set tenant_id for tenant admins
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'tenant_id' => Auth::user()->is_tenant_admin
                    ? Auth::user()->tenant_id
                    : $this->tenant_id,
            ];

            if (Auth::user()->is_super_admin) {
                $userData['is_super_admin'] = $this->is_super_admin;
                $userData['is_tenant_admin'] = $this->is_tenant_admin;
            }

            $user = $this->userService->createUserWithPermissions(
                $userData,
                $this->permissions
            );

            $this->recentlyModifiedUserId = $user->id;

            $this->dispatch('notify', type: 'success', message: 'User created successfully. Password reset instructions sent to their email.');
            $this->resetForm();
            $this->recentlyModifiedUserId = $user->id;
            $this->dispatch('switch-to-list-tab');

        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Check if current user has permission to edit this user
        if (!$this->canManageUser($user)) {
            $this->dispatch('notify', type: 'error', message: 'You do not have permission to edit this user.');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->tenant_id = $user->tenant_id;
        $this->is_super_admin = $user->is_super_admin;
        $this->is_tenant_admin = $user->is_tenant_admin;
        $this->permissions = $user->getPermissionNames()->toArray();
        $this->isEditMode = true;
    }

    public function update()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->userId);

            if (!$this->canManageUser($user)) {
                $this->dispatch('notify', type: 'error', message: 'You do not have permission to update this user.');
                return;
            }

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'tenant_id' => $this->tenant_id,
            ];

            // Only super admin can modify these properties
            if (Auth::user()->is_super_admin) {
                $userData['is_super_admin'] = $this->is_super_admin;
                $userData['is_tenant_admin'] = $this->is_tenant_admin;
            }

            $user = $this->userService->updateUserWithPermissions(
                $user,
                $userData,
                $this->permissions
            );

            $this->resetForm();
            $this->dispatch('notify', type: 'success', message: 'User updated successfully.');
            $this->recentlyModifiedUserId = $user->id;
            $this->dispatch('switch-to-list-tab');
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function confirmDelete($id)
    {
        $this->userToDelete = User::findOrFail($id);
        $this->confirmingUserDeletion = true;
    }

    public function delete()
    {
        if (!$this->userToDelete) {
            return;
        }

        try {
            if ($this->userToDelete->id === Auth::id()) {
                $this->dispatch('notify', type: 'error', message: 'You cannot delete your own account.');
                return;
            }

            if ($this->userToDelete->is_super_admin && !Auth::user()->is_super_admin) {
                $this->dispatch('notify', type: 'error', message: 'You cannot delete a super admin.');
                return;
            }

            $this->userService->deleteUser($this->userToDelete);
            $this->confirmingUserDeletion = false;
            $this->userToDelete = null;

            $this->dispatch('notify', type: 'success', message: 'User deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function confirmToggleActivation($id)
    {
        $this->userToToggle = User::findOrFail($id);
        $this->confirmingUserDeactivation = true;
    }

    public function toggleActivation()
    {
        if (!$this->userToToggle) {
            return;
        }

        try {
            if ($this->userToToggle->id === Auth::id()) {
                $this->dispatch('notify', type: 'error', message: 'You cannot deactivate your own account.');
                return;
            }

            if ($this->userToToggle->is_super_admin && !Auth::user()->is_super_admin) {
                $this->dispatch('notify', type: 'error', message: 'You cannot deactivate a super admin.');
                return;
            }

            $this->userService->toggleActivation($this->userToToggle);
            $this->confirmingUserDeactivation = false;
            $this->userToToggle = null;

            $this->dispatch('notify', type: 'success', message: 'User activation status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to toggle user activation: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to update user status: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'userId', 'is_super_admin', 'is_tenant_admin', 'permissions', 'isEditMode']);
        $this->recentlyModifiedUserId = null;

        // For tenant admins, always set the tenant_id to their own tenant
        if (Auth::user()->is_tenant_admin) {
            $this->tenant_id = Auth::user()->tenant_id;
        } else {
            $this->reset(['tenant_id']);
        }
    }

    protected function canManageUser(User $user): bool
    {
        $currentUser = Auth::user();

        // Super admin can manage all users
        if ($currentUser->is_super_admin) {
            return true;
        }

        // Tenant admin can only manage users in their tenant who are not super admins
        if ($currentUser->is_tenant_admin) {
            return $user->tenant_id === $currentUser->tenant_id && !$user->is_super_admin;
        }

        // Regular users can't manage anyone
        return false;
    }

    public function render()
    {
        return view('livewire.user-manager', [
            'users' => $this->users,
            'allPermissions' => $this->userService->getGroupedPermissionsForTenantAdmin(),
            'tenants' => Auth::user()->is_super_admin ? \App\Models\Tenant::all() : collect([]),
        ]);
    }
}
