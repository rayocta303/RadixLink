<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\PlatformTicket;
use App\Models\PlatformInvoice;
use App\Models\ActivityLog;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\Invoice;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];
        $activities = collect();
        $chartData = [];
        $tenantDbConnected = false;

        if ($user->isPlatformUser()) {
            $stats = $this->getPlatformStats();
            $activities = ActivityLog::with('user')
                ->whereNull('tenant_id')
                ->latest()
                ->limit(10)
                ->get();
            $chartData = $this->getPlatformChartData();
        } else {
            $tenant = Tenant::find($user->tenant_id);
            
            if ($tenant) {
                TenantDatabaseManager::setTenant($tenant);
                $tenantDbConnected = TenantDatabaseManager::isConnected();
                
                if ($tenantDbConnected) {
                    $stats = $this->getTenantStats();
                    $chartData = $this->getTenantChartData();
                } else {
                    $stats = $this->getEmptyTenantStats();
                }
            } else {
                $stats = $this->getEmptyTenantStats();
            }
        }

        return view('dashboard', compact('stats', 'activities', 'chartData', 'tenantDbConnected'));
    }

    protected function getPlatformStats(): array
    {
        return [
            'tenants' => Tenant::count(),
            'active_subscriptions' => TenantSubscription::where('status', 'active')->count(),
            'monthly_revenue' => PlatformInvoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->sum('total'),
            'open_tickets' => PlatformTicket::where('status', 'open')->count(),
        ];
    }

    protected function getPlatformChartData(): array
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7Days->push([
                'date' => $date->format('d M'),
                'tenants' => Tenant::whereDate('created_at', $date)->count(),
                'revenue' => PlatformInvoice::where('status', 'paid')
                    ->whereDate('paid_at', $date)
                    ->sum('total'),
            ]);
        }

        return [
            'labels' => $last7Days->pluck('date')->toArray(),
            'tenants' => $last7Days->pluck('tenants')->toArray(),
            'revenue' => $last7Days->pluck('revenue')->toArray(),
        ];
    }

    protected function getTenantStats(): array
    {
        try {
            $activeCustomers = Customer::where('status', 'active')->count();
            
            $onlineUsers = 0;
            try {
                $onlineUsers = DB::connection('tenant')
                    ->table('radacct')
                    ->whereNull('acctstoptime')
                    ->count();
            } catch (\Exception $e) {
            }
            
            $availableVouchers = Voucher::where('status', 'unused')->count();
            
            $todayRevenue = 0;
            try {
                $todayRevenue = Invoice::where('status', 'paid')
                    ->whereDate('paid_at', today())
                    ->sum('total');
            } catch (\Exception $e) {
            }

            return [
                'active_customers' => $activeCustomers,
                'online_users' => $onlineUsers,
                'available_vouchers' => $availableVouchers,
                'today_revenue' => $todayRevenue,
            ];
        } catch (\Exception $e) {
            return $this->getEmptyTenantStats();
        }
    }

    protected function getTenantChartData(): array
    {
        try {
            $last7Days = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                
                $revenue = 0;
                try {
                    $revenue = Invoice::where('status', 'paid')
                        ->whereDate('paid_at', $date)
                        ->sum('total');
                } catch (\Exception $e) {
                }
                
                $newCustomers = 0;
                try {
                    $newCustomers = Customer::whereDate('created_at', $date)->count();
                } catch (\Exception $e) {
                }
                
                $last7Days->push([
                    'date' => $date->format('d M'),
                    'revenue' => $revenue,
                    'customers' => $newCustomers,
                ]);
            }

            return [
                'labels' => $last7Days->pluck('date')->toArray(),
                'revenue' => $last7Days->pluck('revenue')->toArray(),
                'customers' => $last7Days->pluck('customers')->toArray(),
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'revenue' => [],
                'customers' => [],
            ];
        }
    }

    protected function getEmptyTenantStats(): array
    {
        return [
            'active_customers' => 0,
            'online_users' => 0,
            'available_vouchers' => 0,
            'today_revenue' => 0,
        ];
    }
}
