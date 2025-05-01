<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSuperAdmin
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (! $user || ! $user->is_super_admin) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
