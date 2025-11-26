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
            $this->command->info("Tenant {$data['company_name']} already exists, skipping creation...");
            return;
        }
        
        $tenantId = Str::uuid()->toString();
        $dbCredentials = null;
        
        if ($mode === 'cpanel') {
            $dbCredentials = $this->provisionCpanelDatabase($data['subdomain']);
            
            if (!$dbCredentials) {
                $this->command->warn("Failed to create cPanel database for {$data['company_name']}");
                $this->command->info("Creating tenant tables in central database instead...");
                $dbCredentials = $this->createCentralDatabaseTables($data['subdomain']);
            }
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
            $this->command->info("  Database info stored: " . json_encode(array_keys($dbCredentials)));
        }
    }

    protected function provisionCpanelDatabase(string $subdomain): ?array
    {
        try {
            $cpanel = new CpanelService();
            $result = $cpanel->provisionTenantDatabase($subdomain);
            
            if ($result['success']) {
                Log::info("cPanel database provisioned for tenant: {$subdomain}", $result);
                
                return [
                    'tenancy_db_name' => $result['database'],
                    'tenancy_db_username' => $result['username'],
                    'tenancy_db_password' => $result['password'],
                    'tenancy_db_host' => $result['host'],
                ];
            }
            
            Log::error("Failed to provision cPanel database for tenant: {$subdomain}", $result);
            return null;
        } catch (\Exception $e) {
            Log::error("Exception while provisioning cPanel database: " . $e->getMessage());
            return null;
        }
    }

    protected function createCentralDatabaseTables(string $subdomain): array
    {
        $prefix = 't_' . substr(preg_replace('/[^a-z0-9]/', '', strtolower($subdomain)), 0, 10) . '_';
        
        return [
            'tenancy_db_name' => config('database.connections.mysql.database'),
            'tenancy_db_username' => config('database.connections.mysql.username'),
            'tenancy_db_password' => config('database.connections.mysql.password'),
            'tenancy_db_host' => config('database.connections.mysql.host'),
            'table_prefix' => $prefix,
            'mode' => 'central_prefixed',
        ];
    }

    protected function getPlanLimits(string $plan): array
    {
        return match($plan) {
            'enterprise' => [
                'max_routers' => 999,
                'max_users' => 999999,
                'max_vouchers' => 999999,
                'max_online_users' => 999,
            ],
            'premium' => [
                'max_routers' => 50,
                'max_users' => 10000,
                'max_vouchers' => 100000,
                'max_online_users' => 500,
            ],
            'standard' => [
                'max_routers' => 10,
                'max_users' => 2000,
                'max_vouchers' => 20000,
                'max_online_users' => 100,
            ],
            default => [
                'max_routers' => 3,
                'max_users' => 500,
                'max_vouchers' => 5000,
                'max_online_users' => 25,
            ],
        };
    }
}
