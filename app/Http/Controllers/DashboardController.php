<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\PlatformTicket;
use App\Models\PlatformInvoice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];
        $activities = collect();

        if ($user->isPlatformUser()) {
            $stats = [
                'tenants' => Tenant::count(),
                'active_subscriptions' => TenantSubscription::where('status', 'active')->count(),
                'monthly_revenue' => PlatformInvoice::where('status', 'paid')
                    ->whereMonth('paid_at', now()->month)
                    ->sum('total'),
                'open_tickets' => PlatformTicket::open()->count(),
            ];

            $activities = ActivityLog::with('user')
                ->whereNull('tenant_id')
                ->latest()
                ->limit(10)
                ->get();
        } else {
            $stats = [
                'active_customers' => 0,
                'online_users' => 0,
                'available_vouchers' => 0,
                'today_revenue' => 0,
            ];

            $activities = collect();
        }

        return view('dashboard', compact('stats', 'activities'));
    }
}
