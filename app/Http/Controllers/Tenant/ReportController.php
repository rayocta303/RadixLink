<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Payment;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.reports.index', [
                'stats' => $this->getEmptyStats(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $stats = $this->getDashboardStats();
        return view('tenant.reports.index', compact('stats'));
    }

    public function sales(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.reports.sales', [
                'salesData' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $salesData = DB::connection('tenant')
            ->table('vouchers')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_vouchers'),
                DB::raw('SUM(price) as total_revenue'),
                DB::raw("SUM(CASE WHEN status = 'used' THEN 1 ELSE 0 END) as used_vouchers")
            )
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('tenant.reports.sales', compact('salesData', 'startDate', 'endDate'));
    }

    public function customers(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.reports.customers', [
                'customerStats' => [],
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $customerStats = [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'suspended' => Customer::where('status', 'suspended')->count(),
            'expired' => Customer::where('status', 'expired')->count(),
            'by_service_type' => Customer::select('service_type', DB::raw('count(*) as total'))
                ->groupBy('service_type')
                ->pluck('total', 'service_type'),
            'recent' => Customer::with('servicePlan')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('tenant.reports.customers', compact('customerStats'));
    }

    public function revenue(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.reports.revenue', [
                'revenueData' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $revenueData = DB::connection('tenant')
            ->table('invoices')
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as invoice_count')
            )
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $totalRevenue = $revenueData->sum('revenue');

        return view('tenant.reports.revenue', compact('revenueData', 'totalRevenue', 'startDate', 'endDate'));
    }

    protected function getDashboardStats(): array
    {
        return [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->count(),
            'total_vouchers' => Voucher::count(),
            'unused_vouchers' => Voucher::where('status', 'unused')->count(),
            'used_vouchers' => Voucher::where('status', 'used')->count(),
            'today_revenue' => Invoice::where('status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('total'),
            'month_revenue' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total'),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
        ];
    }

    protected function getEmptyStats(): array
    {
        return [
            'total_customers' => 0,
            'active_customers' => 0,
            'total_vouchers' => 0,
            'unused_vouchers' => 0,
            'used_vouchers' => 0,
            'today_revenue' => 0,
            'month_revenue' => 0,
            'pending_invoices' => 0,
        ];
    }
}
