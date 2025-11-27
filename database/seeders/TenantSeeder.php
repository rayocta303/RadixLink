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
                'subscription_plan' => 'premium',
            ],
            [
                'name' => 'Budi Santoso',
                'company_name' => 'Santoso Internet',
                'subdomain' => 'santoso',
                'email' => 'budi@santosoint.com',
                'phone' => '082345678901',
                'address' => 'Jl. Pahlawan No. 45, Surabaya',
                'subscription_plan' => 'standard',
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
        }

        $planLimits = $this->getPlanLimits($data['subscription_plan']);

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
            'trial_ends_at' => now()->addDays(14),
            'is_active' => true,
            'is_suspended' => false,
        ]);
        
        if ($dbCredentials) {
            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['data' => json_encode($dbCredentials)]);
        }

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

        $plan = SubscriptionPlan::where('slug', $data['subscription_plan'])->first();
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

            $this->command->info("  Seeded tenant data successfully");
        } catch (\Exception $e) {
            $this->command->error("  Seed error: " . $e->getMessage());
            Log::error("Tenant seed failed: " . $e->getMessage());
        } finally {
            DB::purge($connectionName);
        }
    }

    protected function getPlanLimits(string $plan): array
    {
        return match($plan) {
            'enterprise' => ['max_routers' => 999, 'max_users' => 999999, 'max_vouchers' => 999999, 'max_online_users' => 999],
            'premium' => ['max_routers' => 50, 'max_users' => 10000, 'max_vouchers' => 100000, 'max_online_users' => 500],
            'standard' => ['max_routers' => 10, 'max_users' => 2000, 'max_vouchers' => 20000, 'max_online_users' => 100],
            default => ['max_routers' => 3, 'max_users' => 500, 'max_vouchers' => 5000, 'max_online_users' => 25],
        };
    }
}
