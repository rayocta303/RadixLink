<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantDatabaseManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isTenantUser()) {
            abort(403, 'Access denied. Tenant users only.');
        }

        $tenantId = auth()->user()->tenant_id;
        
        if (!$tenantId) {
            abort(403, 'No tenant associated with this user.');
        }

        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        if (!$tenant->isActive()) {
            abort(403, 'Tenant is not active or has expired.');
        }

        try {
            TenantDatabaseManager::setTenant($tenant);
            view()->share('currentTenant', $tenant);
        } catch (\Exception $e) {
            abort(500, 'Failed to connect to tenant database: ' . $e->getMessage());
        }

        return $next($request);
    }
}
