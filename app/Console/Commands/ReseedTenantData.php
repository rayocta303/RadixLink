<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReseedTenantData extends Command
{
    protected $signature = 'tenant:reseed {tenant_id?} {--all : Reseed all tenants}';
    protected $description = 'Reseed roles, permissions and data for existing tenant databases';

    public function handle(): int
    {
        if ($this->option('all')) {
            $tenants = Tenant::all();
            foreach ($tenants as $tenant) {
                $this->reseedTenant($tenant);
            }
        } elseif ($tenantId = $this->argument('tenant_id')) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant not found: {$tenantId}");
                return 1;
            }
            $this->reseedTenant($tenant);
        } else {
            $this->error('Please provide tenant_id or use --all flag');
            return 1;
        }

        return 0;
    }

    protected function reseedTenant(Tenant $tenant): void
    {
        $this->info("Reseeding tenant: {$tenant->company_name} ({$tenant->subdomain})");

        $rawData = DB::table('tenants')->where('id', $tenant->id)->value('data');
        $dbCredentials = $rawData ? json_decode($rawData, true) : null;
        
        if (!$dbCredentials || !isset($dbCredentials['tenancy_db_name'])) {
            $this->warn("No database credentials for tenant: {$tenant->subdomain}");
            return;
        }

        $connectionName = 'tenant_reseed_' . Str::random(6);

        config([
            "database.connections.{$connectionName}" => [
                'driver' => 'mysql',
                'host' => $dbCredentials['tenancy_db_host'] ?? config('database.connections.mysql.host'),
                'port' => env('DB_PORT', '3306'),
                'database' => $dbCredentials['tenancy_db_name'],
                'username' => $dbCredentials['tenancy_db_username'] ?? config('database.connections.mysql.username'),
                'password' => $dbCredentials['tenancy_db_password'] ?? config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],
        ]);

        DB::purge($connectionName);

        try {
            $this->seedRolesAndPermissions($connectionName);
            $this->seedOwnerUser($connectionName, $tenant);
            $this->info("  - Successfully reseeded: {$tenant->subdomain}");
        } catch (\Exception $e) {
            $this->error("  - Failed: {$e->getMessage()}");
            Log::error("Reseed failed for {$tenant->subdomain}: " . $e->getMessage());
        } finally {
            DB::purge($connectionName);
        }
    }

    protected function seedRolesAndPermissions(string $connectionName): void
    {
        $now = now();

        $existingRoles = DB::connection($connectionName)->table('roles')->count();
        $existingPermissions = DB::connection($connectionName)->table('permissions')->count();
        
        if ($existingRoles > 0 && $existingPermissions > 0) {
            $this->info("  - Roles and permissions already exist, skipping seeding");
            return;
        }

        $this->addMissingRolesColumns($connectionName);

        if ($existingPermissions == 0) {
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

            DB::connection($connectionName)->table('permissions')->insert($permissions);
            $this->info("  - Seeded " . count($permissions) . " permissions");
        }

        if ($existingRoles == 0) {
            $roles = [
                ['name' => 'owner', 'guard_name' => 'tenant', 'display_name' => 'Pemilik', 'description' => 'Akses penuh ke seluruh fitur tenant', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'admin', 'guard_name' => 'tenant', 'display_name' => 'Administrator', 'description' => 'Mengelola operasional tenant', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'technician', 'guard_name' => 'tenant', 'display_name' => 'Teknisi', 'description' => 'Akses teknis jaringan', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'cashier', 'guard_name' => 'tenant', 'display_name' => 'Kasir', 'description' => 'Kelola transaksi dan voucher', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'support', 'guard_name' => 'tenant', 'display_name' => 'Dukungan', 'description' => 'Bantuan teknis ringan', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'reseller', 'guard_name' => 'tenant', 'display_name' => 'Reseller', 'description' => 'Kelola klien sendiri', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'investor', 'guard_name' => 'tenant', 'display_name' => 'Investor', 'description' => 'Akses laporan keuangan', 'created_at' => $now, 'updated_at' => $now],
            ];

            DB::connection($connectionName)->table('roles')->insert($roles);
            $this->info("  - Seeded " . count($roles) . " roles");

            $rolePermissions = [
                'owner' => ['customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.suspend', 'customers.reset', 'vouchers.view', 'vouchers.create', 'vouchers.generate', 'vouchers.print', 'vouchers.delete', 'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.pay', 'invoices.delete', 'nas.view', 'nas.create', 'nas.edit', 'nas.delete', 'nas.debug', 'services.view', 'services.create', 'services.edit', 'services.delete', 'reports.view', 'reports.financial', 'reports.export', 'users.view', 'users.create', 'users.edit', 'users.delete', 'settings.view', 'settings.update', 'resellers.view', 'resellers.manage', 'balance.view', 'balance.topup', 'tickets.view', 'tickets.create', 'tickets.reply', 'radius.monitor', 'radius.disconnect'],
                'admin' => ['customers.view', 'customers.create', 'customers.edit', 'customers.delete', 'customers.suspend', 'customers.reset', 'vouchers.view', 'vouchers.create', 'vouchers.generate', 'vouchers.print', 'vouchers.delete', 'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.pay', 'nas.view', 'nas.create', 'nas.edit', 'nas.delete', 'nas.debug', 'services.view', 'services.create', 'services.edit', 'services.delete', 'reports.view', 'reports.financial', 'users.view', 'users.create', 'users.edit', 'settings.view', 'settings.update', 'resellers.view', 'resellers.manage', 'tickets.view', 'tickets.create', 'tickets.reply', 'radius.monitor', 'radius.disconnect'],
                'technician' => ['customers.view', 'customers.reset', 'nas.view', 'nas.create', 'nas.edit', 'nas.debug', 'services.view', 'reports.view', 'radius.monitor', 'radius.disconnect'],
                'cashier' => ['customers.view', 'customers.create', 'customers.edit', 'vouchers.view', 'vouchers.create', 'vouchers.generate', 'vouchers.print', 'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.pay', 'reports.view', 'reports.financial', 'balance.view'],
                'support' => ['customers.view', 'customers.reset', 'vouchers.view', 'invoices.view', 'tickets.view', 'tickets.create', 'tickets.reply', 'radius.monitor'],
                'reseller' => ['customers.view', 'customers.create', 'customers.edit', 'vouchers.view', 'vouchers.generate', 'vouchers.print', 'invoices.view', 'invoices.create', 'reports.view', 'balance.view', 'balance.topup'],
                'investor' => ['reports.view', 'reports.financial'],
            ];

            $permissionIds = DB::connection($connectionName)->table('permissions')->pluck('id', 'name');
            $roleIds = DB::connection($connectionName)->table('roles')->pluck('id', 'name');

            $roleHasPermissions = [];
            foreach ($rolePermissions as $roleName => $perms) {
                $roleId = $roleIds[$roleName] ?? null;
                if (!$roleId) continue;
                foreach ($perms as $permName) {
                    if (isset($permissionIds[$permName])) {
                        $roleHasPermissions[] = [
                            'role_id' => $roleId,
                            'permission_id' => $permissionIds[$permName],
                        ];
                    }
                }
            }

            if (count($roleHasPermissions) > 0) {
                DB::connection($connectionName)->table('role_has_permissions')->insert($roleHasPermissions);
                $this->info("  - Seeded " . count($roleHasPermissions) . " role-permission assignments");
            }
        }
    }

    protected function seedOwnerUser(string $connectionName, Tenant $tenant): void
    {
        $existingUser = DB::connection($connectionName)->table('users')->count();
        if ($existingUser > 0) {
            $this->info("  - Owner user already exists, skipping");
            return;
        }

        $platformUser = User::where('tenant_id', $tenant->id)->first();
        if (!$platformUser) {
            $this->warn("  - No platform user found for tenant");
            return;
        }

        $tenantUserId = DB::connection($connectionName)->table('users')->insertGetId([
            'name' => $platformUser->name,
            'email' => $platformUser->email,
            'phone' => $platformUser->phone,
            'password' => $platformUser->password,
            'email_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ownerRoleId = DB::connection($connectionName)->table('roles')
            ->where('name', 'owner')
            ->value('id');

        if ($ownerRoleId) {
            DB::connection($connectionName)->table('model_has_roles')->insert([
                'role_id' => $ownerRoleId,
                'model_type' => 'App\\Models\\Tenant\\TenantUser',
                'model_id' => $tenantUserId,
            ]);
            $this->info("  - Created owner user with role assignment");
        }
    }

    protected function addMissingRolesColumns(string $connectionName): void
    {
        try {
            $columns = DB::connection($connectionName)->getSchemaBuilder()->getColumnListing('roles');
            
            if (!in_array('display_name', $columns)) {
                DB::connection($connectionName)->statement('ALTER TABLE roles ADD COLUMN display_name VARCHAR(255) NULL AFTER guard_name');
                $this->info("  - Added display_name column to roles table");
            }
            
            if (!in_array('description', $columns)) {
                DB::connection($connectionName)->statement('ALTER TABLE roles ADD COLUMN description VARCHAR(255) NULL AFTER display_name');
                $this->info("  - Added description column to roles table");
            }
        } catch (\Exception $e) {
            $this->warn("  - Could not add missing columns: " . $e->getMessage());
        }
    }
}
