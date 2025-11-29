<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use App\Services\CpanelService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenantMode = config('tenancy.mode');
        
        $dummyTenants = [
            [
                'name' => 'Ahmad Wijaya',
                'company_name' => 'Wijaya Net',
                'subdomain' => 'wijayanet',
                'email' => 'ahmad@wijayanet.id',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 123, Jakarta Selatan',
                'subscription_plan' => 'business',
            ],
            [
                'name' => 'Budi Santoso',
                'company_name' => 'Santoso Internet',
                'subdomain' => 'santoso',
                'email' => 'budi@santosoint.com',
                'phone' => '082345678901',
                'address' => 'Jl. Pahlawan No. 45, Surabaya',
                'subscription_plan' => 'professional',
            ],
            [
                'name' => 'Citra Dewi',
                'company_name' => 'Dewi Hotspot',
                'subdomain' => 'dewispot',
                'email' => 'citra@dewihotspot.net',
                'phone' => '083456789012',
                'address' => 'Jl. Sudirman No. 78, Bandung',
                'subscription_plan' => 'basic',
            ],
        ];

        foreach ($dummyTenants as $data) {
            $this->createTenant($data, $tenantMode);
        }
    }

    protected function createTenant(array $data, string $mode): void
    {
        $existingTenant = Tenant::where('subdomain', $data['subdomain'])->first();
        if ($existingTenant) {
            $this->command->info("Tenant {$data['company_name']} already exists, skipping...");
            return;
        }
        
        $tenantId = Str::uuid()->toString();
        $dbCredentials = null;
        
        if ($mode === 'cpanel') {
            $dbCredentials = $this->setupTenantDatabase($data['subdomain']);
        } else {
            $dbCredentials = $this->getLocalDatabaseCredentials($data['subdomain']);
        }

        $plan = SubscriptionPlan::where('slug', $data['subscription_plan'])->first();
        $planLimits = $plan ? [
            'max_routers' => $plan->max_routers,
            'max_users' => $plan->max_users,
            'max_vouchers' => $plan->max_vouchers,
            'max_online_users' => $plan->max_online_users,
        ] : $this->getDefaultPlanLimits($data['subscription_plan']);

        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => $data['name'],
            'company_name' => $data['company_name'],
            'subdomain' => $data['subdomain'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'subscription_plan' => $data['subscription_plan'],
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
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'user_type' => 'tenant',
            'is_active' => true,
        ]);

        $ownerUser->assignRole('tenant_owner');

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

        $this->command->info("Created tenant: {$data['company_name']} ({$data['subdomain']})");
        
        if ($dbCredentials) {
            $this->command->info("  Database: {$dbCredentials['tenancy_db_name']}");
        }
    }

    protected function setupTenantDatabase(string $subdomain): ?array
    {
        try {
            $cpanel = new CpanelService();
            $dbName = 't_' . substr(preg_replace('/[^a-z0-9]/', '', strtolower($subdomain)), 0, 10);
            $cpanelUsername = config('tenancy.cpanel.username');
            $fullDbName = $cpanelUsername . '_' . $dbName;
            
            $dbCheck = $cpanel->listDatabases();
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
                $result = $cpanel->createDatabase($dbName);
                if (!$result['success']) {
                    $this->command->warn("Failed to create database: " . ($result['error'] ?? 'Unknown error'));
                    return null;
                }
                $this->command->info("Database {$fullDbName} created successfully");
                
                $mainUser = config('database.connections.mysql.username');
                $cpanel->setDatabaseUserPrivileges($mainUser, $fullDbName);
            } else {
                $this->command->info("Database {$fullDbName} already exists");
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
            $this->command->error("Error setting up tenant database: " . $e->getMessage());
            Log::error("Tenant database setup failed: " . $e->getMessage());
            return null;
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
                    $this->command->info("  Migrated: " . basename($file));
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        throw $e;
                    }
                    $this->command->info("  Skipped (exists): " . basename($file));
                } finally {
                    DB::setDefaultConnection($currentConnection);
                }
            }
        } catch (\Exception $e) {
            $this->command->error("  Migration error: " . $e->getMessage());
            Log::error("Tenant migration failed: " . $e->getMessage());
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
                $this->command->info("  Data already exists, skipping seed");
                return;
            }

            DB::connection($connectionName)->table('service_plans')->insert([
                ['name' => 'Paket Harian 1GB', 'code' => 'DAILY-1GB', 'description' => 'Paket internet harian 1GB', 'type' => 'hotspot', 'price' => 5000, 'validity' => 1, 'validity_unit' => 'days', 'bandwidth_up' => '5M', 'bandwidth_down' => '10M', 'quota_bytes' => 1073741824, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Paket Mingguan 5GB', 'code' => 'WEEKLY-5GB', 'description' => 'Paket internet mingguan 5GB', 'type' => 'hotspot', 'price' => 25000, 'validity' => 7, 'validity_unit' => 'days', 'bandwidth_up' => '10M', 'bandwidth_down' => '20M', 'quota_bytes' => 5368709120, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Paket Bulanan 20Mbps', 'code' => 'MONTHLY-20M', 'description' => 'Unlimited 20Mbps', 'type' => 'pppoe', 'price' => 150000, 'validity' => 30, 'validity_unit' => 'days', 'bandwidth_up' => '10M', 'bandwidth_down' => '20M', 'quota_bytes' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Paket Bulanan 50Mbps', 'code' => 'MONTHLY-50M', 'description' => 'Unlimited 50Mbps', 'type' => 'pppoe', 'price' => 300000, 'validity' => 30, 'validity_unit' => 'days', 'bandwidth_up' => '25M', 'bandwidth_down' => '50M', 'quota_bytes' => null, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);

            DB::connection($connectionName)->table('nas')->insert([
                ['name' => 'Router Utama', 'shortname' => 'router-main', 'nasname' => '192.168.1.1', 'ports' => 1812, 'secret' => 'secret123', 'type' => 'mikrotik', 'location_name' => 'Kantor Pusat', 'longitude' => 106.8456, 'latitude' => -6.2088, 'coverage' => 500, 'api_username' => 'admin', 'api_password' => 'admin', 'api_port' => 8728, 'winbox_port' => 8291, 'is_active' => true, 'status' => 'enabled', 'vpn_enabled' => false, 'vpn_port' => 1701, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Hotspot Area A', 'shortname' => 'hotspot-a', 'nasname' => '192.168.2.1', 'ports' => 1812, 'secret' => 'secret456', 'type' => 'mikrotik', 'location_name' => 'Hotspot Area A', 'longitude' => 107.6191, 'latitude' => -6.9175, 'coverage' => 200, 'api_username' => 'admin', 'api_password' => 'admin', 'api_port' => 8728, 'winbox_port' => 8291, 'is_active' => true, 'status' => 'enabled', 'vpn_enabled' => false, 'vpn_port' => 1701, 'created_at' => now(), 'updated_at' => now()],
            ]);

            $planIds = DB::connection($connectionName)->table('service_plans')->pluck('id')->toArray();
            
            $customers = [];
            for ($i = 1; $i <= 5; $i++) {
                $customers[] = [
                    'username' => $subdomain . '_user' . $i,
                    'password' => Hash::make('password123'),
                    'name' => 'Pelanggan ' . $i,
                    'email' => 'pelanggan' . $i . '@' . $subdomain . '.id',
                    'phone' => '08' . rand(1000000000, 9999999999),
                    'service_plan_id' => $planIds[array_rand($planIds)],
                    'service_type' => $i <= 3 ? 'pppoe' : 'hotspot',
                    'status' => 'active',
                    'registered_at' => now(),
                    'expires_at' => now()->addDays(30),
                    'balance' => rand(0, 50000),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::connection($connectionName)->table('customers')->insert($customers);

            $vouchers = [];
            for ($i = 1; $i <= 10; $i++) {
                $vouchers[] = [
                    'code' => strtoupper(Str::random(8)),
                    'username' => 'v' . Str::random(6),
                    'password' => Str::random(8),
                    'service_plan_id' => $planIds[array_rand($planIds)],
                    'status' => 'unused',
                    'type' => 'single',
                    'max_usage' => 1,
                    'price' => rand(1, 5) * 5000,
                    'batch_id' => 'BATCH-001',
                    'generated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::connection($connectionName)->table('vouchers')->insert($vouchers);

            $this->seedTenantRoles($connectionName);
            $this->seedTenantOwnerUser($connectionName, $subdomain);

            $this->command->info("  Seeded tenant data successfully");
        } catch (\Exception $e) {
            $this->command->error("  Seed error: " . $e->getMessage());
            Log::error("Tenant seed failed: " . $e->getMessage());
        } finally {
            DB::purge($connectionName);
        }
    }

    protected function seedTenantRoles(string $connectionName): void
    {
        $now = now();

        $existingRoles = DB::connection($connectionName)->table('roles')->count();
        if ($existingRoles > 0) {
            return;
        }

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
            ['name' => 'admin', 'guard_name' => 'tenant', 'display_name' => 'Administrator', 'description' => 'Mengelola operasional: pengguna, NAS, paket, voucher', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'technician', 'guard_name' => 'tenant', 'display_name' => 'Teknisi', 'description' => 'Akses teknis jaringan: debug, monitoring, manajemen NAS', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cashier', 'guard_name' => 'tenant', 'display_name' => 'Kasir', 'description' => 'Transaksi: cetak voucher, kelola invoice, pembayaran', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'support', 'guard_name' => 'tenant', 'display_name' => 'Dukungan', 'description' => 'Bantuan teknis ringan: reset akun pelanggan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'reseller', 'guard_name' => 'tenant', 'display_name' => 'Reseller', 'description' => 'Sub-tenant: kelola klien sendiri, topup saldo', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'investor', 'guard_name' => 'tenant', 'display_name' => 'Investor', 'description' => 'View-only: akses laporan keuangan', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::connection($connectionName)->table('roles')->insert($roles);

        $permissionIds = DB::connection($connectionName)->table('permissions')->pluck('id', 'name');
        $roleIds = DB::connection($connectionName)->table('roles')->pluck('id', 'name');

        $ownerPermissions = $permissionIds->keys()->toArray();
        $roleHasPermissions = [];
        foreach ($ownerPermissions as $permName) {
            $roleHasPermissions[] = [
                'role_id' => $roleIds['owner'],
                'permission_id' => $permissionIds[$permName],
            ];
        }

        DB::connection($connectionName)->table('role_has_permissions')->insert($roleHasPermissions);
        $this->command->info("  Tenant roles and permissions seeded");
    }

    protected function seedTenantOwnerUser(string $connectionName, string $subdomain): void
    {
        $existingUsers = DB::connection($connectionName)->table('users')->count();
        if ($existingUsers > 0) {
            return;
        }

        $userId = DB::connection($connectionName)->table('users')->insertGetId([
            'name' => 'Owner ' . ucfirst($subdomain),
            'email' => 'owner@' . $subdomain . '.id',
            'phone' => '08' . rand(1000000000, 9999999999),
            'password' => Hash::make('owner123'),
            'email_verified_at' => now(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ownerRoleId = DB::connection($connectionName)->table('roles')->where('name', 'owner')->value('id');
        
        DB::connection($connectionName)->table('model_has_roles')->insert([
            'role_id' => $ownerRoleId,
            'model_type' => 'App\\Models\\Tenant\\TenantUser',
            'model_id' => $userId,
        ]);

        $this->command->info("  Tenant owner user created (owner@{$subdomain}.id / owner123)");
    }

    protected function getDefaultPlanLimits(string $plan): array
    {
        return match($plan) {
            'platinum', 'enterprise' => ['max_routers' => 999, 'max_users' => 99999, 'max_vouchers' => 999999, 'max_online_users' => 9999],
            'business' => ['max_routers' => 50, 'max_users' => 5000, 'max_vouchers' => 50000, 'max_online_users' => 1000],
            'professional' => ['max_routers' => 15, 'max_users' => 1000, 'max_vouchers' => 10000, 'max_online_users' => 200],
            'basic' => ['max_routers' => 5, 'max_users' => 250, 'max_vouchers' => 2500, 'max_online_users' => 50],
            'starter' => ['max_routers' => 2, 'max_users' => 100, 'max_vouchers' => 500, 'max_online_users' => 25],
            default => ['max_routers' => 1, 'max_users' => 25, 'max_vouchers' => 50, 'max_online_users' => 5],
        };
    }

    protected function getLocalDatabaseCredentials(string $subdomain): array
    {
        $cpanelUsername = config('tenancy.cpanel.username');
        $dbName = 't_' . substr(preg_replace('/[^a-z0-9]/', '', strtolower($subdomain)), 0, 10);
        $fullDbName = $cpanelUsername . '_' . $dbName;
        
        $credentials = [
            'tenancy_db_name' => $fullDbName,
            'tenancy_db_username' => config('database.connections.mysql.username'),
            'tenancy_db_password' => config('database.connections.mysql.password'),
            'tenancy_db_host' => config('database.connections.mysql.host'),
        ];

        $this->runTenantMigrations($credentials);
        $this->seedTenantData($credentials, $subdomain);

        return $credentials;
    }
}
