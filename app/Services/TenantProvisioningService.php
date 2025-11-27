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
                'trial_ends_at' => now()->addDays(14),
                'is_active' => true,
                'is_suspended' => false,
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

            DB::connection($connectionName)->table('roles')->insert([
                ['name' => 'owner', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'technician', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'cashier', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'reseller', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ]);

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

    protected function getPlanLimits(string $plan): array
    {
        return match ($plan) {
            'enterprise' => ['max_routers' => 999, 'max_users' => 999999, 'max_vouchers' => 999999, 'max_online_users' => 999],
            'premium' => ['max_routers' => 50, 'max_users' => 10000, 'max_vouchers' => 100000, 'max_online_users' => 500],
            'standard' => ['max_routers' => 10, 'max_users' => 2000, 'max_vouchers' => 20000, 'max_online_users' => 100],
            default => ['max_routers' => 3, 'max_users' => 500, 'max_vouchers' => 5000, 'max_online_users' => 25],
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
