<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create a super admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_super_admin' => true,
        ]);

        // Create tenants and their users
        $acme = Tenant::create([
            'name' => 'Acme Corp',
            'identifier' => 'acme.example.com',
        ]);

        $acme->users()->createMany([
            [
                'name' => 'John Doe',
                'email' => 'john@acme.com',
                'password' => Hash::make('password'),
                'is_tenant_admin' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@acme.com',
                'password' => Hash::make('password'),
            ]
        ]);

        $wayne = Tenant::create([
            'name' => 'Wayne Enterprises',
            'identifier' => 'wayne.example.com',
        ]);

        $wayne->users()->createMany([
            [
                'name' => 'Bruce Wayne',
                'email' => 'bruce@wayne.com',
                'password' => Hash::make('password'),
                'is_tenant_admin' => true,
            ],
            [
                'name' => 'Lucius Fox',
                'email' => 'lucius@wayne.com',
                'password' => Hash::make('password'),
            ]
        ]);
    }
}
