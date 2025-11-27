<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantProvisioningService
{
    protected CpanelService $cpanel;

    public function __construct()
    {
        if (config('tenancy.mode') === 'cpanel') {
            $this->cpanel = new CpanelService();
        }
    }

    public function provision(array $tenantData): ?Tenant
    {
        try {
            $tenantId = Str::uuid()->toString();
            $mode = config('tenancy.mode');
            $dbCredentials = null;

            if ($mode === 'cpanel') {
                $dbCredentials = $this->setupTenantDatabase($tenantData['subdomain']);
            }

            $planLimits = $this->getPlanLimits($tenantData['subscription_plan'] ?? 'basic');

            $tenant = Tenant::create([
                'id' => $tenantId,
                'name' => $tenantData['name'],
                'company_name' => $tenantData['company_name'],
                'subdomain' => $tenantData['subdomain'],
                'email' => $tenantData['email'],
                'phone' => $tenantData['phone'] ?? null,
                'address' => $tenantData['address'] ?? null,
                'subscription_plan' => $tenantData['subscription_plan'] ?? 'basic',
                'subscription_expires_at' => now()->addMonths(1),
                'max_routers' => $planLimits['max_routers'],
                'max_users' => $planLimits['max_users'],
                'max_vouchers' => $planLimits['max_vouchers'],
                'max_online_users' => $planLimits['max_online_users'],
                'is_active' => true,
                'is_suspended' => false,
                'tenancy_db_name' => $dbCredentials['tenancy_db_name'] ?? null,
                'tenancy_db_username' => $dbCredentials['tenancy_db_username'] ?? null,
                'tenancy_db_password' => $dbCredentials['tenancy_db_password'] ?? null,
                'tenancy_db_host' => $dbCredentials['tenancy_db_host'] ?? null,
                'data' => $dbCredentials,
            ]);

            $ownerUser = User::create([
                'tenant_id' => $tenant->id,
                'name' => $tenantData['name'],
                'email' => $tenantData['email'],
                'phone' => $tenantData['phone'] ?? null,
                'password' => Hash::make($tenantData['password'] ?? 'password123'),
                'email_verified_at' => now(),
                'user_type' => 'tenant',
                'is_active' => true,
            ]);

            $ownerUser->assignRole('tenant_owner');

            $plan = SubscriptionPlan::where('slug', $tenantData['subscription_plan'] ?? 'basic')->first();
            if ($plan) {
                TenantSubscription::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'billing_cycle' => 'monthly',
                    'amount' => $plan->price_monthly,
                    'starts_at' => now(),
                    'ends_at' => now()->addMonths(1),
                ]);
            }

            Log::info("Tenant provisioned successfully: {$tenant->company_name} ({$tenant->subdomain})");

            return $tenant;
        } catch (\Exception $e) {
            Log::error("Failed to provision tenant: " . $e->getMessage());
            throw $e;
        }
    }

    protected function setupTenantDatabase(string $subdomain): ?array
    {
        try {
            $dbName = 't_' . substr(preg_replace('/[^a-z0-9]/', '', strtolower($subdomain)), 0, 10);
            $cpanelUsername = config('tenancy.cpanel.username');
            $fullDbName = $cpanelUsername . '_' . $dbName;

            $dbCheck = $this->cpanel->listDatabases();
            $dbExists = false;

            if ($dbCheck['success'] && isset($dbCheck['data'])) {
                foreach ($dbCheck['data'] as $db) {
                    if ($db['database'] === $fullDbName) {
                        $dbExists = true;
                        break;
                    }
                }
            }

            if (!$dbExists) {
                $result = $this->cpanel->createDatabase($dbName);
                if (!$result['success']) {
                    throw new \Exception("Failed to create database: " . ($result['error'] ?? 'Unknown error'));
                }
                Log::info("Database {$fullDbName} created successfully");

                $mainUser = config('database.connections.mysql.username');
                $this->cpanel->setDatabaseUserPrivileges($mainUser, $fullDbName);
            }

            $credentials = [
                'tenancy_db_name' => $fullDbName,
                'tenancy_db_username' => config('database.connections.mysql.username'),
                'tenancy_db_password' => config('database.connections.mysql.password'),
                'tenancy_db_host' => config('database.connections.mysql.host'),
            ];

            $this->runTenantMigrations($credentials);
            $this->seedTenantData($credentials, $subdomain);

            return $credentials;
        } catch (\Exception $e) {
            Log::error("Error setting up tenant database: " . $e->getMessage());
            throw $e;
        }
    }

    protected function runTenantMigrations(array $dbCredentials): void
    {
        $connectionName = 'tenant_migration_' . Str::random(6);

        config([
            "database.connections.{$connectionName}" => [
                'driver' => 'mysql',
                'host' => $dbCredentials['tenancy_db_host'],
                'port' => env('DB_PORT', '3306'),
                'database' => $dbCredentials['tenancy_db_name'],
                'username' => $dbCredentials['tenancy_db_username'],
                'password' => $dbCredentials['tenancy_db_password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],
        ]);

        DB::purge($connectionName);

        try {
            $migrationPath = database_path('migrations/tenant');
            $files = glob($migrationPath . '/*.php');
            sort($files);

            foreach ($files as $file) {
                $migration = require $file;

                $currentConnection = DB::getDefaultConnection();
                DB::setDefaultConnection($connectionName);

                try {
                    $migration->up();
                    Log::info("Migrated: " . basename($file));
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        throw $e;
                    }
                    Log::info("Skipped (exists): " . basename($file));
                } finally {
                    DB::setDefaultConnection($currentConnection);
                }
            }
        } finally {
            DB::purge($connectionName);
        }
    }

    protected function seedTenantData(array $dbCredentials, string $subdomain): void
    {
        $connectionName = 'tenant_seed_' . Str::random(6);

        config([
            "database.connections.{$connectionName}" => [
                'driver' => 'mysql',
                'host' => $dbCredentials['tenancy_db_host'],
                'port' => env('DB_PORT', '3306'),
                'database' => $dbCredentials['tenancy_db_name'],
                'username' => $dbCredentials['tenancy_db_username'],
                'password' => $dbCredentials['tenancy_db_password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ],
        ]);

        DB::purge($connectionName);

        try {
            $existingPlans = DB::connection($connectionName)->table('service_plans')->count();
            if ($existingPlans > 0) {
                return;
            }

            DB::connection($connectionName)->table('service_plans')->insert([
                ['name' => 'Paket Harian 1GB', 'code' => 'DAILY-1GB', 'description' => 'Paket internet harian 1GB', 'type' => 'hotspot', 'price' => 5000, 'validity' => 1, 'validity_unit' => 'days', 'bandwidth_up' => '5M', 'bandwidth_down' => '10M', 'quota_bytes' => 1073741824, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Paket Mingguan 5GB', 'code' => 'WEEKLY-5GB', 'description' => 'Paket internet mingguan 5GB', 'type' => 'hotspot', 'price' => 25000, 'validity' => 7, 'validity_unit' => 'days', 'bandwidth_up' => '10M', 'bandwidth_down' => '20M', 'quota_bytes' => 5368709120, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Paket Bulanan 20Mbps', 'code' => 'MONTHLY-20M', 'description' => 'Unlimited 20Mbps', 'type' => 'pppoe', 'price' => 150000, 'validity' => 30, 'validity_unit' => 'days', 'bandwidth_up' => '10M', 'bandwidth_down' => '20M', 'quota_bytes' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Paket Bulanan 50Mbps', 'code' => 'MONTHLY-50M', 'description' => 'Unlimited 50Mbps', 'type' => 'pppoe', 'price' => 300000, 'validity' => 30, 'validity_unit' => 'days', 'bandwidth_up' => '25M', 'bandwidth_down' => '50M', 'quota_bytes' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);

            DB::connection($connectionName)->table('nas')->insert([
                ['name' => 'Router Utama', 'shortname' => 'router-main', 'nasname' => '192.168.1.1', 'ports' => 1812, 'secret' => 'secret123', 'type' => 'mikrotik', 'api_username' => 'admin', 'api_password' => 'admin', 'api_port' => 8728, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);

            $this->seedTenantRolesAndPermissions($connectionName);

            DB::connection($connectionName)->table('tenant_settings')->insert([
                ['key' => 'company_name', 'value' => $subdomain, 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'currency', 'value' => 'IDR', 'group' => 'billing', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'tax_rate', 'value' => '11', 'group' => 'billing', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'invoice_prefix', 'value' => 'INV', 'group' => 'billing', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'voucher_prefix', 'value' => '', 'group' => 'voucher', 'created_at' => now(), 'updated_at' => now()],
                ['key' => 'voucher_length', 'value' => '8', 'group' => 'voucher', 'created_at' => now(), 'updated_at' => now()],
            ]);

            Log::info("Seeded tenant data for: {$subdomain}");
        } catch (\Exception $e) {
            Log::error("Seed error: " . $e->getMessage());
        } finally {
            DB::purge($connectionName);
        }
    }

    protected function seedTenantRolesAndPermissions(string $connectionName): void
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

        DB::connection($connectionName)->table('permissions')->insert($permissions);

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
            $roleId = $roleIds[$roleName];
            foreach ($perms as $permName) {
                if (isset($permissionIds[$permName])) {
                    $roleHasPermissions[] = [
                        'role_id' => $roleId,
                        'permission_id' => $permissionIds[$permName],
                    ];
                }
            }
        }

        DB::connection($connectionName)->table('role_has_permissions')->insert($roleHasPermissions);
    }

    protected function getPlanLimits(string $plan): array
    {
        return match ($plan) {
            'platinum' => ['max_routers' => 999, 'max_users' => 999999, 'max_vouchers' => 999999, 'max_online_users' => 999],
            'enterprise' => ['max_routers' => 100, 'max_users' => 50000, 'max_vouchers' => 500000, 'max_online_users' => 500],
            'business' => ['max_routers' => 50, 'max_users' => 10000, 'max_vouchers' => 100000, 'max_online_users' => 250],
            'professional' => ['max_routers' => 25, 'max_users' => 5000, 'max_vouchers' => 50000, 'max_online_users' => 150],
            'basic' => ['max_routers' => 10, 'max_users' => 2000, 'max_vouchers' => 20000, 'max_online_users' => 100],
            'starter' => ['max_routers' => 5, 'max_users' => 1000, 'max_vouchers' => 10000, 'max_online_users' => 50],
            'free' => ['max_routers' => 1, 'max_users' => 50, 'max_vouchers' => 100, 'max_online_users' => 10],
            default => ['max_routers' => 1, 'max_users' => 50, 'max_vouchers' => 100, 'max_online_users' => 10],
        };
    }

    public function deprovision(Tenant $tenant): bool
    {
        try {
            $mode = config('tenancy.mode');

            if ($mode === 'cpanel' && $tenant->data) {
                $dbName = $tenant->data['tenancy_db_name'] ?? null;
                if ($dbName) {
                    $this->cpanel->deleteDatabase($dbName);
                    Log::info("Database {$dbName} deleted for tenant: {$tenant->subdomain}");
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to deprovision tenant: " . $e->getMessage());
            return false;
        }
    }
}
