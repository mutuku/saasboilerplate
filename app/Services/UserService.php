<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class UserService
{
    /**
     * Get all users from the system
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers()
    {
        return User::with('tenant')->get();
    }

    /**
     * Get users belonging to a specific tenant
     *
     * @param int $tenantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTenantUsers($tenantId)
    {
        return User::where('tenant_id', $tenantId)->with('tenant')->get();
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function createUser(array $data): User
    {
        try {
            // Generate a random password
            $randomPassword = Str::random(12);
            $data['password'] = bcrypt($randomPassword);

            $user = User::create($data);

            // Send password reset instructions
            $this->sendPasswordResetInstructions($user);

            return $user;
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());
            throw new \Exception('Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing user
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function updateUser(User $user, array $data): User
    {
        try {
            $user->update($data);
            return $user;
        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            throw new \Exception('Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete a user
     *
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function deleteUser(User $user): bool
    {
        try {
            return $user->delete();
        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage());
            throw new \Exception('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user activation status
     *
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function toggleActivation(User $user): User
    {
        try {
            $user->deactivated = !$user->deactivated;
            $user->save();
            return $user;
        } catch (\Exception $e) {
            Log::error('User activation toggle failed: ' . $e->getMessage());
            throw new \Exception('Failed to toggle user activation: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset instructions to a user
     *
     * @param User $user
     * @return void
     * @throws \Exception
     */
    protected function sendPasswordResetInstructions(User $user): void
    {
        try {
            $response = Password::sendResetLink(['email' => $user->email]);

            if ($response !== Password::RESET_LINK_SENT) {
                Log::error('Failed to send password reset link to: ' . $user->email);
                throw new \Exception('Failed to send password reset link.');
            }
        } catch (\Exception $e) {
            Log::error('Password reset link sending failed: ' . $e->getMessage());
            throw new \Exception('Failed to send password reset instructions: ' . $e->getMessage());
        }
    }

    /**
     * Create a user with permissions
     *
     * @param array $data
     * @param array $permissions
     * @return User
     * @throws \Exception
     */
    public function createUserWithPermissions(array $data, array $permissions = []): User
    {
        DB::beginTransaction();

        try {
            $user = $this->createUser($data);

            // Sync permissions if current user is authorized
            if (Auth::check() && (Auth::user()->is_tenant_admin || Auth::user()->is_super_admin)) {
                $user->syncPermissions($permissions);
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create user with permissions failed: ' . $e->getMessage());
            throw new \Exception('Failed to create user with permissions: ' . $e->getMessage());
        }
    }

    /**
     * Update a user with permissions
     *
     * @param User $user
     * @param array $data
     * @param array $permissions
     * @return void
     * @throws \Exception
     */
    public function updateUserWithPermissions(User $user, array $data, array $permissions = []): User
    {
        DB::beginTransaction();

        try {
            $this->updateUser($user, $data);

            // Sync permissions if current user is authorized
            if (Auth::check() && (Auth::user()->is_tenant_admin || Auth::user()->is_super_admin)) {
                $user->syncPermissions($permissions);
            }

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update user with permissions failed: ' . $e->getMessage());
            throw new \Exception('Failed to update user with permissions: ' . $e->getMessage());
        }
    }

    /**
     * Get permissions grouped by category for tenant admin
     *
     * @return array
     */
    public function getGroupedPermissionsForTenantAdmin(): array
    {
        if (!Auth::check() || !Auth::user()->is_tenant_admin && !Auth::user()->is_super_admin) {
            return [];
        }

        $groupedPermissions = [
            'Users' => ['view_users', 'edit_users', 'create_users', 'delete_users'],
            'Billing' => ['view_billing', 'manage_subscription', 'view_invoices'],
            'Reports' => ['view_reports', 'export_reports', 'create_reports'],
            'Settings' => ['view_settings', 'edit_settings'],
            // Add more groups as needed
        ];

        $allPermissions = [];

        foreach ($groupedPermissions as $group => $perms) {
            $allPermissions[$group] = Permission::whereIn('name', $perms)->get();
        }

        return $allPermissions;
    }

    /**
     * Resend password reset instructions to a user
     *
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function resendPasswordResetInstructions(User $user): void
    {
        $this->sendPasswordResetInstructions($user);
    }
}
