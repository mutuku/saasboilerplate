<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function attemptLogin(string $email, string $password, int $tenantId): bool
    {
        $user = User::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            // If user has a tenant_id, set it in the session
            if ($user->tenant_id) {
                session(['tenant_id' => $user->tenant_id]);
            } elseif ($user->is_super_admin) {
                // For super admins, don't set tenant_id
                session()->forget('tenant_id');
            } else {
                // User has no tenant and is not admin
                return false;
            }

            Auth::login($user);
            return true;
        }

        return false;
    }

    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tenant_id' => $data['tenant_id'] ?? null,
            //'is_super_admin' => $data['is_super_admin'] ?? false,
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
