<?php

namespace App\Http\Middleware;

use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route('tenant_identifier')) {
            $tenant = $this->tenantService->getTenantByIdentifier($request->route('tenant_identifier'));

            if ($tenant) {
                session(['tenant_id' => $tenant->id]);
            } else {
                abort(404, 'Tenant not found');
            }
        }

        return $next($request);
    }
}
