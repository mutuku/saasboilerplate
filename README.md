# Laravel + Livewire SaaS Boilerplate

A SaaS-ready boilerplate built on top of the official Laravel + [Livewire](https://livewire.laravel.com) starter kit.

## Features

- **Multitenancy** powered by [Spatie Multitenancy](https://spatie.be/docs/laravel-multitenancy), without tenant identifiers or custom subdomains.
- **Permissions** handled via [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission), roles are not used.
- Simple user-based access control:
  - `super_admin` and `tenant_admin` boolean flags are stored in the `users` table.
  - Other users have `null` for both fields.
- **Tenant onboarding** includes:
  - Selecting a **country**
  - Choosing a **currency**
  - Setting a **unit of measurement** (e.g. kg, lbs, liters)

## Installation

```bash
# 1. Clone the repository
git clone git@github.com:mutuku/saasboilerplate.git
cd saasboilerplate

# 2. Install dependencies
composer install

# 3. Copy and configure environment
cp .env.example .env
php artisan key:generate

# 4. Run database migrations and seeders
php artisan migrate --seed

# 5. Serve the application
php artisan serve
```

## Default Credentials

- **Superadmin**:  
  Email: `admin@example.com`  
  Password: `password`

- **Tenant**:  
  Email: `john@acme.com`  
  Password: `password`

## License

The Laravel + Livewire SaaS Boilerplate is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Developed By

[Agile.co.ke](https://agile.co.ke)
