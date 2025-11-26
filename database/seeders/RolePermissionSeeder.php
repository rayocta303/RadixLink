<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $platformPermissions = [
            'platform.tenants.view',
            'platform.tenants.create',
            'platform.tenants.update',
            'platform.tenants.delete',
            'platform.tenants.suspend',
            'platform.subscriptions.view',
            'platform.subscriptions.create',
            'platform.subscriptions.update',
            'platform.subscriptions.delete',
            'platform.invoices.view',
            'platform.invoices.create',
            'platform.invoices.update',
            'platform.invoices.delete',
            'platform.tickets.view',
            'platform.tickets.create',
            'platform.tickets.update',
            'platform.tickets.close',
            'platform.users.view',
            'platform.users.create',
            'platform.users.update',
            'platform.users.delete',
            'platform.settings.view',
            'platform.settings.update',
            'platform.reports.view',
            'platform.reports.export',
        ];

        foreach ($platformPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $platformAdmin = Role::firstOrCreate(['name' => 'platform_admin', 'guard_name' => 'web']);
        $platformAdmin->givePermissionTo([
            'platform.tenants.view',
            'platform.tenants.create',
            'platform.tenants.update',
            'platform.tenants.suspend',
            'platform.subscriptions.view',
            'platform.subscriptions.create',
            'platform.subscriptions.update',
            'platform.invoices.view',
            'platform.invoices.create',
            'platform.invoices.update',
            'platform.tickets.view',
            'platform.tickets.update',
            'platform.tickets.close',
            'platform.users.view',
            'platform.reports.view',
        ]);

        $platformSupport = Role::firstOrCreate(['name' => 'platform_support', 'guard_name' => 'web']);
        $platformSupport->givePermissionTo([
            'platform.tenants.view',
            'platform.tickets.view',
            'platform.tickets.update',
            'platform.tickets.close',
        ]);

        $tenantOwner = Role::firstOrCreate(['name' => 'tenant_owner', 'guard_name' => 'web']);
        $tenantAdmin = Role::firstOrCreate(['name' => 'tenant_admin', 'guard_name' => 'web']);
        $tenantTechnician = Role::firstOrCreate(['name' => 'tenant_technician', 'guard_name' => 'web']);
        $tenantCashier = Role::firstOrCreate(['name' => 'tenant_cashier', 'guard_name' => 'web']);
        $tenantReseller = Role::firstOrCreate(['name' => 'tenant_reseller', 'guard_name' => 'web']);
        $tenantInvestor = Role::firstOrCreate(['name' => 'tenant_investor', 'guard_name' => 'web']);
    }
}
