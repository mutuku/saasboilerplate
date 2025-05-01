<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->check() && auth()->user()->is_super_admin) {
            return; // Skip tenant scoping for super admins
        }

        if (session('tenant_id')) {
            $builder->where('tenant_id', session('tenant_id'));
        }
    }
}
