<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\PlatformTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        $stats = $this->getStats();
        return view('platform.monitoring.index', $stats);
    }

    public function stats()
    {
        return response()->json($this->getStats());
    }

    private function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)
            ->where('is_suspended', false)
            ->count();
        $suspendedTenants = Tenant::where('is_suspended', true)->count();
        $newThisMonth = Tenant::whereBetween('created_at', [$startOfMonth, $now])
            ->count();
        $expiringThisMonth = Tenant::whereBetween('subscription_expires_at', [$now, $endOfMonth])
            ->count();

        $revenueThisMonth = TenantSubscription::where('status', 'active')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('amount');

        $activeSubscriptions = TenantSubscription::where('status', 'active')
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', $now);
            })
            ->count();

        $subscriptionDistribution = TenantSubscription::select('plan_id', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('plan_id')
            ->with('plan:id,name')
            ->get()
            ->map(function ($item) {
                return [
                    'plan' => $item->plan?->name ?? 'Unknown',
                    'count' => $item->count,
                ];
            });

        $dbStatus = 'Terhubung';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Terputus';
        }

        $totalPlatformUsers = User::where('user_type', 'platform')->count();
        $pendingTickets = PlatformTicket::whereIn('status', ['open', 'in_progress', 'waiting'])->count();

        $recentTenants = Tenant::latest()
            ->take(5)
            ->get(['id', 'company_name', 'email', 'subscription_plan', 'created_at']);

        $recentTickets = PlatformTicket::with(['tenant:id,company_name', 'user:id,name'])
            ->latest()
            ->take(5)
            ->get(['id', 'tenant_id', 'user_id', 'ticket_number', 'subject', 'priority', 'status', 'created_at']);

        return [
            'tenantStats' => [
                'total' => $totalTenants,
                'active' => $activeTenants,
                'suspended' => $suspendedTenants,
                'newThisMonth' => $newThisMonth,
                'expiringThisMonth' => $expiringThisMonth,
            ],
            'subscriptionStats' => [
                'revenueThisMonth' => $revenueThisMonth,
                'activeSubscriptions' => $activeSubscriptions,
                'distribution' => $subscriptionDistribution,
            ],
            'systemHealth' => [
                'phpVersion' => phpversion(),
                'laravelVersion' => app()->version(),
                'dbStatus' => $dbStatus,
                'totalPlatformUsers' => $totalPlatformUsers,
                'pendingTickets' => $pendingTickets,
            ],
            'recentActivity' => [
                'tenants' => $recentTenants,
                'tickets' => $recentTickets,
            ],
            'lastUpdated' => $now->format('d M Y H:i:s'),
        ];
    }
}
