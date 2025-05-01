<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ApplyTenantScope;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'identify.tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'super.admin' => \App\Http\Middleware\SuperAdmin::class,
            'ensure.superadmin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
            'tenant.scope' => ApplyTenantScope::class,
        ]);
         // Apply ApplyTenantScope middleware globally
         $middleware->append(ApplyTenantScope::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
