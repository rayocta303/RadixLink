<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantRolesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        
        $permissions = [
            ['name' => 'customers.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.suspend', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customers.reset', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'vouchers.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.generate', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.print', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'vouchers.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'invoices.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.pay', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'invoices.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'nas.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'nas.debug', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'services.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'services.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'services.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'services.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'reports.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'reports.financial', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'reports.export', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'users.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.edit', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.delete', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'settings.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'settings.update', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'resellers.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'resellers.manage', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'balance.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'balance.topup', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'tickets.view', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'tickets.create', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'tickets.reply', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            
            ['name' => 'radius.monitor', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'radius.disconnect', 'guard_name' => 'tenant', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('permissions')->insert($permissions);

        $roles = [
            [
                'name' => 'owner',
                'guard_name' => 'tenant',
                'display_name' => 'Pemilik',
                'description' => 'Akses penuh ke seluruh fitur tenant termasuk billing dan domain',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'admin',
                'guard_name' => 'tenant',
                'display_name' => 'Administrator',
                'description' => 'Mengelola operasional: pengguna, NAS, paket, voucher',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'technician',
                'guard_name' => 'tenant',
                'display_name' => 'Teknisi',
                'description' => 'Akses teknis jaringan: debug, monitoring, manajemen NAS',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'cashier',
                'guard_name' => 'tenant',
                'display_name' => 'Kasir',
                'description' => 'Transaksi: cetak voucher, kelola invoice, pembayaran',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'support',
                'guard_name' => 'tenant',
                'display_name' => 'Dukungan',
                'description' => 'Bantuan teknis ringan: reset akun pelanggan, cek status aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'reseller',
                'guard_name' => 'tenant',
                'display_name' => 'Reseller',
                'description' => 'Sub-tenant: kelola klien sendiri, topup saldo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'investor',
                'guard_name' => 'tenant',
                'display_name' => 'Investor',
                'description' => 'View-only: akses laporan keuangan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('roles')->insert($roles);

        $rolePermissions = [
            'owner' => [
                'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.suspend', 'customers.reset',
                'vouchers.view', 'vouchers.create', 'vouchers.generate', 'vouchers.print', 'vouchers.delete',
                'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.pay', 'invoices.delete',
                'nas.view', 'nas.create', 'nas.edit', 'nas.delete', 'nas.debug',
                'services.view', 'services.create', 'services.edit', 'services.delete',
                'reports.view', 'reports.financial', 'reports.export',
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'settings.view', 'settings.update',
                'resellers.view', 'resellers.manage',
                'balance.view', 'balance.topup',
                'tickets.view', 'tickets.create', 'tickets.reply',
                'radius.monitor', 'radius.disconnect',
            ],
            'admin' => [
                'customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.suspend', 'customers.reset',
                'vouchers.view', 'vouchers.create', 'vouchers.generate', 'vouchers.print', 'vouchers.delete',
                'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.pay',
                'nas.view', 'nas.create', 'nas.edit', 'nas.delete', 'nas.debug',
                'services.view', 'services.create', 'services.edit', 'services.delete',
                'reports.view', 'reports.financial',
                'users.view', 'users.create', 'users.edit',
                'settings.view', 'settings.update',
                'resellers.view', 'resellers.manage',
                'tickets.view', 'tickets.create', 'tickets.reply',
                'radius.monitor', 'radius.disconnect',
            ],
            'technician' => [
                'customers.view', 'customers.reset',
                'nas.view', 'nas.create', 'nas.edit', 'nas.debug',
                'services.view',
                'reports.view',
                'radius.monitor', 'radius.disconnect',
            ],
            'cashier' => [
                'customers.view', 'customers.create', 'customers.edit',
                'vouchers.view', 'vouchers.create', 'vouchers.generate', 'vouchers.print',
                'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.pay',
                'reports.view', 'reports.financial',
                'balance.view',
            ],
            'support' => [
                'customers.view', 'customers.reset',
                'vouchers.view',
                'invoices.view',
                'tickets.view', 'tickets.create', 'tickets.reply',
                'radius.monitor',
            ],
            'reseller' => [
                'customers.view', 'customers.create', 'customers.edit',
                'vouchers.view', 'vouchers.generate', 'vouchers.print',
                'invoices.view', 'invoices.create',
                'reports.view',
                'balance.view', 'balance.topup',
            ],
            'investor' => [
                'reports.view', 'reports.financial',
            ],
        ];

        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        $roleIds = DB::table('roles')->pluck('id', 'name');

        $roleHasPermissions = [];
        foreach ($rolePermissions as $roleName => $permissions) {
            $roleId = $roleIds[$roleName];
            foreach ($permissions as $permissionName) {
                if (isset($permissionIds[$permissionName])) {
                    $roleHasPermissions[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionIds[$permissionName],
                    ];
                }
            }
        }

        DB::table('role_has_permissions')->insert($roleHasPermissions);
    }
}
