<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->seedPlatformRolesAndPermissions();
        
        $this->command->info('Platform roles and permissions seeded successfully!');
    }

    public function seedPlatformRolesAndPermissions(): void
    {
        $platformPermissions = [
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'tenants.delete',
            'tenants.suspend',
            'subscriptions.view',
            'subscriptions.manage',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'tickets.view',
            'tickets.reply',
            'tickets.close',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'settings.view',
            'settings.update',
            'reports.view',
            'logs.view',
            'servers.monitor',
            'radius.monitor',
            'radius.debug',
        ];

        foreach ($platformPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $platformAdmin = Role::firstOrCreate(['name' => 'platform_admin', 'guard_name' => 'web']);
        $platformAdmin->syncPermissions([
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'tenants.suspend',
            'subscriptions.view',
            'subscriptions.manage',
            'invoices.view',
            'invoices.create',
            'tickets.view',
            'tickets.reply',
            'tickets.close',
            'users.view',
            'users.create',
            'users.edit',
            'settings.view',
            'settings.update',
            'reports.view',
        ]);

        $platformCashier = Role::firstOrCreate(['name' => 'platform_cashier', 'guard_name' => 'web']);
        $platformCashier->syncPermissions([
            'tenants.view',
            'subscriptions.view',
            'subscriptions.manage',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'reports.view',
        ]);

        $platformTechnician = Role::firstOrCreate(['name' => 'platform_technician', 'guard_name' => 'web']);
        $platformTechnician->syncPermissions([
            'tenants.view',
            'servers.monitor',
            'radius.monitor',
            'radius.debug',
            'logs.view',
            'reports.view',
        ]);

        $platformSupport = Role::firstOrCreate(['name' => 'platform_support', 'guard_name' => 'web']);
        $platformSupport->syncPermissions([
            'tenants.view',
            'tickets.view',
            'tickets.reply',
            'tickets.close',
            'users.view',
        ]);
    }

    public function seedTenantRolesAndPermissions(): void
    {
        $tenantPermissions = [
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.suspend',
            'customers.reset',
            'vouchers.view',
            'vouchers.create',
            'vouchers.generate',
            'vouchers.print',
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.pay',
            'nas.view',
            'nas.create',
            'nas.edit',
            'nas.delete',
            'nas.debug',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'reports.view',
            'reports.financial',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'settings.view',
            'settings.update',
            'resellers.view',
            'resellers.manage',
            'balance.topup',
            'balance.view',
            'tickets.view',
            'tickets.create',
            'tickets.reply',
        ];

        foreach ($tenantPermissions as $permission) {
            DB::connection('tenant')->table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'web'],
                ['name' => $permission, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $roles = [
            'owner' => $tenantPermissions,
            'admin' => [
                'customers.view',
                'customers.create',
                'customers.edit',
                'customers.delete',
                'customers.suspend',
                'customers.reset',
                'vouchers.view',
                'vouchers.create',
                'vouchers.generate',
                'vouchers.print',
                'invoices.view',
                'invoices.create',
                'invoices.edit',
                'invoices.pay',
                'nas.view',
                'nas.create',
                'nas.edit',
                'nas.delete',
                'services.view',
                'services.create',
                'services.edit',
                'services.delete',
                'reports.view',
                'reports.financial',
                'users.view',
                'users.create',
                'users.edit',
                'resellers.view',
                'resellers.manage',
                'tickets.view',
                'tickets.create',
                'tickets.reply',
            ],
            'technician' => [
                'customers.view',
                'customers.reset',
                'nas.view',
                'nas.create',
                'nas.edit',
                'nas.delete',
                'nas.debug',
                'services.view',
                'reports.view',
                'tickets.view',
                'tickets.create',
                'tickets.reply',
            ],
            'cashier' => [
                'customers.view',
                'vouchers.view',
                'vouchers.create',
                'vouchers.generate',
                'vouchers.print',
                'invoices.view',
                'invoices.create',
                'invoices.edit',
                'invoices.pay',
                'reports.view',
                'balance.topup',
                'balance.view',
            ],
            'support' => [
                'customers.view',
                'customers.reset',
                'vouchers.view',
                'invoices.view',
                'nas.view',
                'services.view',
                'tickets.view',
                'tickets.create',
                'tickets.reply',
            ],
            'reseller' => [
                'customers.view',
                'customers.create',
                'customers.edit',
                'vouchers.view',
                'vouchers.create',
                'vouchers.generate',
                'vouchers.print',
                'invoices.view',
                'invoices.create',
                'services.view',
                'reports.view',
                'balance.topup',
                'balance.view',
            ],
            'investor' => [
                'reports.view',
                'reports.financial',
                'invoices.view',
                'customers.view',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = DB::connection('tenant')->table('roles')->updateOrInsert(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['name' => $roleName, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]
            );
            
            $roleId = DB::connection('tenant')->table('roles')
                ->where('name', $roleName)
                ->where('guard_name', 'web')
                ->value('id');

            DB::connection('tenant')->table('role_has_permissions')
                ->where('role_id', $roleId)
                ->delete();

            foreach ($permissions as $permissionName) {
                $permissionId = DB::connection('tenant')->table('permissions')
                    ->where('name', $permissionName)
                    ->where('guard_name', 'web')
                    ->value('id');

                if ($permissionId && $roleId) {
                    DB::connection('tenant')->table('role_has_permissions')->insert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                    ]);
                }
            }
        }
    }
}
