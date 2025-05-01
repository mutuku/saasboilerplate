<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class ApplyTenantScope
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_super_admin && session('tenant_id')) {
            Model::addGlobalScope(new TenantScope);
        }

        return $next($request);
    }
}
