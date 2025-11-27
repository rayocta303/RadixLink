<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\Tenant\Nas;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Voucher;
use Illuminate\Support\Facades\DB;

class TenantUsageService
{
    public function getUsage(Tenant $tenant): array
    {
        if (!TenantDatabaseManager::isConnected()) {
            return [
                'routers' => 0,
                'customers' => 0,
                'vouchers' => 0,
                'online_users' => 0,
            ];
        }

        return [
            'routers' => $this->getRouterCount($tenant),
            'customers' => $this->getCustomerCount($tenant),
            'vouchers' => $this->getVoucherCount($tenant),
            'online_users' => $this->getOnlineUserCount($tenant),
        ];
    }

    public function getUsageWithLimits(Tenant $tenant): array
    {
        $usage = $this->getUsage($tenant);
        $limits = $this->getLimits($tenant);

        return [
            'routers' => [
                'current' => $usage['routers'],
                'limit' => $limits['max_routers'],
                'remaining' => max(0, $limits['max_routers'] - $usage['routers']),
                'percentage' => $limits['max_routers'] > 0 ? round(($usage['routers'] / $limits['max_routers']) * 100, 1) : 0,
            ],
            'customers' => [
                'current' => $usage['customers'],
                'limit' => $limits['max_users'],
                'remaining' => max(0, $limits['max_users'] - $usage['customers']),
                'percentage' => $limits['max_users'] > 0 ? round(($usage['customers'] / $limits['max_users']) * 100, 1) : 0,
            ],
            'vouchers' => [
                'current' => $usage['vouchers'],
                'limit' => $limits['max_vouchers'],
                'remaining' => max(0, $limits['max_vouchers'] - $usage['vouchers']),
                'percentage' => $limits['max_vouchers'] > 0 ? round(($usage['vouchers'] / $limits['max_vouchers']) * 100, 1) : 0,
            ],
            'online_users' => [
                'current' => $usage['online_users'],
                'limit' => $limits['max_online_users'],
                'remaining' => max(0, $limits['max_online_users'] - $usage['online_users']),
                'percentage' => $limits['max_online_users'] > 0 ? round(($usage['online_users'] / $limits['max_online_users']) * 100, 1) : 0,
            ],
        ];
    }

    public function canAddRouter(Tenant $tenant): bool
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getRouterCount($tenant);
        
        return $currentCount < $limits['max_routers'];
    }

    public function canAddCustomer(Tenant $tenant): bool
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getCustomerCount($tenant);
        
        return $currentCount < $limits['max_users'];
    }

    public function canAddVoucher(Tenant $tenant, int $count = 1): bool
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getVoucherCount($tenant);
        
        return ($currentCount + $count) <= $limits['max_vouchers'];
    }

    public function canAddOnlineUser(Tenant $tenant): bool
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getOnlineUserCount($tenant);
        
        return $currentCount < $limits['max_online_users'];
    }

    public function getRemainingRouters(Tenant $tenant): int
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getRouterCount($tenant);
        
        return max(0, $limits['max_routers'] - $currentCount);
    }

    public function getRemainingCustomers(Tenant $tenant): int
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getCustomerCount($tenant);
        
        return max(0, $limits['max_users'] - $currentCount);
    }

    public function getRemainingVouchers(Tenant $tenant): int
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getVoucherCount($tenant);
        
        return max(0, $limits['max_vouchers'] - $currentCount);
    }

    public function getRemainingOnlineUsers(Tenant $tenant): int
    {
        $limits = $this->getLimits($tenant);
        $currentCount = $this->getOnlineUserCount($tenant);
        
        return max(0, $limits['max_online_users'] - $currentCount);
    }

    public function getUsagePercentage(Tenant $tenant): array
    {
        $usage = $this->getUsage($tenant);
        $limits = $this->getLimits($tenant);

        return [
            'routers' => $limits['max_routers'] > 0 ? round(($usage['routers'] / $limits['max_routers']) * 100, 1) : 0,
            'customers' => $limits['max_users'] > 0 ? round(($usage['customers'] / $limits['max_users']) * 100, 1) : 0,
            'vouchers' => $limits['max_vouchers'] > 0 ? round(($usage['vouchers'] / $limits['max_vouchers']) * 100, 1) : 0,
            'online_users' => $limits['max_online_users'] > 0 ? round(($usage['online_users'] / $limits['max_online_users']) * 100, 1) : 0,
        ];
    }

    public function getLimits(Tenant $tenant): array
    {
        $plan = $this->getSubscriptionPlan($tenant);

        if ($plan) {
            return [
                'max_routers' => $plan->max_routers,
                'max_users' => $plan->max_users,
                'max_vouchers' => $plan->max_vouchers,
                'max_online_users' => $plan->max_online_users,
                'custom_domain' => $plan->custom_domain,
                'api_access' => $plan->api_access,
                'priority_support' => $plan->priority_support,
                'features' => $plan->features ?? [],
            ];
        }

        return [
            'max_routers' => $tenant->max_routers ?? 1,
            'max_users' => $tenant->max_users ?? 25,
            'max_vouchers' => $tenant->max_vouchers ?? 50,
            'max_online_users' => $tenant->max_online_users ?? 5,
            'custom_domain' => false,
            'api_access' => false,
            'priority_support' => false,
            'features' => [],
        ];
    }

    public function isApproachingLimit(Tenant $tenant, string $resource): bool
    {
        $percentages = $this->getUsagePercentage($tenant);
        
        $resourceKey = match($resource) {
            'router', 'routers' => 'routers',
            'customer', 'customers', 'user', 'users' => 'customers',
            'voucher', 'vouchers' => 'vouchers',
            'online_user', 'online_users' => 'online_users',
            default => $resource,
        };

        return isset($percentages[$resourceKey]) && $percentages[$resourceKey] >= 80;
    }

    public function getLimitWarnings(Tenant $tenant): array
    {
        $warnings = [];
        $percentages = $this->getUsagePercentage($tenant);

        if ($percentages['routers'] >= 80) {
            $warnings['routers'] = $percentages['routers'] >= 100 
                ? 'Anda telah mencapai batas maksimum router. Upgrade paket untuk menambah kapasitas.'
                : "Penggunaan router mencapai {$percentages['routers']}%. Pertimbangkan upgrade paket.";
        }

        if ($percentages['customers'] >= 80) {
            $warnings['customers'] = $percentages['customers'] >= 100
                ? 'Anda telah mencapai batas maksimum pelanggan. Upgrade paket untuk menambah kapasitas.'
                : "Penggunaan pelanggan mencapai {$percentages['customers']}%. Pertimbangkan upgrade paket.";
        }

        if ($percentages['vouchers'] >= 80) {
            $warnings['vouchers'] = $percentages['vouchers'] >= 100
                ? 'Anda telah mencapai batas maksimum voucher. Upgrade paket untuk menambah kapasitas.'
                : "Penggunaan voucher mencapai {$percentages['vouchers']}%. Pertimbangkan upgrade paket.";
        }

        if ($percentages['online_users'] >= 80) {
            $warnings['online_users'] = $percentages['online_users'] >= 100
                ? 'Anda telah mencapai batas maksimum pengguna online. Upgrade paket untuk menambah kapasitas.'
                : "Penggunaan pengguna online mencapai {$percentages['online_users']}%. Pertimbangkan upgrade paket.";
        }

        return $warnings;
    }

    protected function getSubscriptionPlan(Tenant $tenant): ?SubscriptionPlan
    {
        $activeSubscription = $tenant->subscription()
            ->where('status', 'active')
            ->whereNull('cancelled_at')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->first();

        if ($activeSubscription && $activeSubscription->plan) {
            return $activeSubscription->plan;
        }

        if ($tenant->subscription_plan) {
            return SubscriptionPlan::where('slug', $tenant->subscription_plan)->first();
        }

        return SubscriptionPlan::where('slug', 'free')->first();
    }

    protected function getRouterCount(Tenant $tenant): int
    {
        if (!TenantDatabaseManager::isConnected()) {
            return 0;
        }

        try {
            return Nas::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getCustomerCount(Tenant $tenant): int
    {
        if (!TenantDatabaseManager::isConnected()) {
            return 0;
        }

        try {
            return Customer::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getVoucherCount(Tenant $tenant): int
    {
        if (!TenantDatabaseManager::isConnected()) {
            return 0;
        }

        try {
            return Voucher::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getOnlineUserCount(Tenant $tenant): int
    {
        if (!TenantDatabaseManager::isConnected()) {
            return 0;
        }

        try {
            return DB::connection('tenant')
                ->table('radacct')
                ->whereNull('acctstoptime')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function hasFeature(Tenant $tenant, string $feature): bool
    {
        $limits = $this->getLimits($tenant);
        $features = $limits['features'] ?? [];

        return isset($features[$feature]) && $features[$feature] === true;
    }

    public function canUseCustomDomain(Tenant $tenant): bool
    {
        $limits = $this->getLimits($tenant);
        return $limits['custom_domain'] ?? false;
    }

    public function canUseApi(Tenant $tenant): bool
    {
        $limits = $this->getLimits($tenant);
        return $limits['api_access'] ?? false;
    }

    public function hasPrioritySupport(Tenant $tenant): bool
    {
        $limits = $this->getLimits($tenant);
        return $limits['priority_support'] ?? false;
    }
}
