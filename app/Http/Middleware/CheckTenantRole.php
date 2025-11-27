<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantDatabaseManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->isTenantUser()) {
            abort(403, 'Access denied. Tenant users only.');
        }

        $tenantId = $user->tenant_id;
        
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

        TenantDatabaseManager::setTenant($tenant);
        view()->share('currentTenant', $tenant);
        view()->share('tenantDbConnected', TenantDatabaseManager::isConnected());

        if (empty($roles)) {
            return $next($request);
        }

        $userRoles = $this->getTenantUserRoles($user->id);

        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }

        abort(403, 'Access denied. You do not have the required role.');
    }

    protected function getTenantUserRoles(int $userId): array
    {
        try {
            $roleIds = DB::connection('tenant')
                ->table('model_has_roles')
                ->where('model_type', 'App\\Models\\Tenant\\TenantUser')
                ->where('model_id', $userId)
                ->pluck('role_id');

            return DB::connection('tenant')
                ->table('roles')
                ->whereIn('id', $roleIds)
                ->pluck('name')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}
