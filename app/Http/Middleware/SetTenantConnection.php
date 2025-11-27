<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantDatabaseManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = session('tenant_id');
        
        if (!$tenantId) {
            return redirect()->route('tenant.select')
                ->with('error', 'Please select a tenant first.');
        }

        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            session()->forget('tenant_id');
            return redirect()->route('tenant.select')
                ->with('error', 'Tenant not found.');
        }

        if (!$tenant->isActive()) {
            session()->forget('tenant_id');
            return redirect()->route('tenant.select')
                ->with('error', 'Tenant is not active or has expired.');
        }

        try {
            TenantDatabaseManager::setTenant($tenant);
            view()->share('currentTenant', $tenant);
        } catch (\Exception $e) {
            return redirect()->route('tenant.select')
                ->with('error', 'Failed to connect to tenant database: ' . $e->getMessage());
        }

        return $next($request);
    }
}
