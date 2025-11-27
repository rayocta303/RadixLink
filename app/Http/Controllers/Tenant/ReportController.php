<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Payment;
use App\Models\Tenant\Transaction;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.reports.index', $this->getEmptyIndexData());
        }

        try {
            $monthlyRevenue = Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total') ?? 0;

            $totalCustomers = Customer::count() ?? 0;

            $vouchersSold = Voucher::where('status', 'used')->count() ?? 0;

            $onlineUsers = Customer::where('status', 'active')
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->count() ?? 0;

            $todayRevenue = Invoice::where('status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('total') ?? 0;

            $pendingInvoices = Invoice::where('status', 'pending')->count() ?? 0;

            $recentTransactions = $this->getRecentTransactions();

            $revenueChartData = $this->getMonthlyRevenueData();
            $revenueData = $revenueChartData['data'];
            $revenueLabels = $revenueChartData['labels'];

            $customerDistribution = $this->getCustomerDistribution();

            return view('tenant.reports.index', compact(
                'monthlyRevenue',
                'totalCustomers',
                'vouchersSold',
                'onlineUsers',
                'todayRevenue',
                'pendingInvoices',
                'recentTransactions',
                'revenueData',
                'revenueLabels',
                'customerDistribution'
            ));
        } catch (\Exception $e) {
            return view('tenant.reports.index', array_merge(
                $this->getEmptyIndexData(),
                ['dbError' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()]
            ));
        }
    }

    public function sales(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.reports.sales', [
                'salesData' => collect(),
                'salesHistory' => collect(),
                'totalSales' => 0,
                'voucherSold' => 0,
                'newCustomers' => 0,
                'totalTransactions' => 0,
                'startDate' => now()->startOfMonth()->format('Y-m-d'),
                'endDate' => now()->format('Y-m-d'),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        try {
            $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', now()->format('Y-m-d'));

            $totalSales = Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->sum('total') ?? 0;

            $voucherSold = Voucher::where('status', 'used')
                ->whereBetween('activated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count() ?? 0;

            $newCustomers = Customer::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count() ?? 0;

            $totalTransactions = Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->count() ?? 0;

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

            $salesHistory = $this->getSalesHistory($startDate, $endDate);

            return view('tenant.reports.sales', compact(
                'salesData',
                'salesHistory',
                'totalSales',
                'voucherSold',
                'newCustomers',
                'totalTransactions',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            return view('tenant.reports.sales', [
                'salesData' => collect(),
                'salesHistory' => collect(),
                'totalSales' => 0,
                'voucherSold' => 0,
                'newCustomers' => 0,
                'totalTransactions' => 0,
                'startDate' => now()->startOfMonth()->format('Y-m-d'),
                'endDate' => now()->format('Y-m-d'),
                'dbError' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
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

    protected function getRecentTransactions()
    {
        try {
            $transactions = Transaction::with('customer')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            if ($transactions->isEmpty()) {
                $transactions = Invoice::with('customer')
                    ->where('status', 'paid')
                    ->orderBy('paid_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($invoice) {
                        return (object) [
                            'transaction_id' => $invoice->invoice_number,
                            'customer' => $invoice->customer,
                            'type' => 'income',
                            'amount' => $invoice->total,
                            'created_at' => $invoice->paid_at ?? $invoice->created_at,
                        ];
                    });
            }

            return $transactions;
        } catch (\Exception $e) {
            return collect();
        }
    }

    protected function getMonthlyRevenueData(): array
    {
        $labels = [];
        $data = [];

        try {
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->translatedFormat('M');

                $revenue = Invoice::where('status', 'paid')
                    ->whereMonth('paid_at', $date->month)
                    ->whereYear('paid_at', $date->year)
                    ->sum('total');

                $data[] = (float) ($revenue ?? 0);
            }
        } catch (\Exception $e) {
            $labels = ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $data = [0, 0, 0, 0, 0, 0];
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    protected function getCustomerDistribution(): array
    {
        try {
            $pppoeCount = Customer::where('service_type', 'pppoe')->count() ?? 0;
            $hotspotCount = Customer::where('service_type', 'hotspot')->count() ?? 0;

            if ($pppoeCount == 0 && $hotspotCount == 0) {
                return [0, 0];
            }

            return [$pppoeCount, $hotspotCount];
        } catch (\Exception $e) {
            return [0, 0];
        }
    }

    protected function getSalesHistory(string $startDate, string $endDate)
    {
        try {
            $invoiceSales = Invoice::with(['customer', 'servicePlan'])
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('paid_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($invoice) {
                    return (object) [
                        'date' => $invoice->paid_at ? $invoice->paid_at->format('Y-m-d H:i') : $invoice->created_at->format('Y-m-d H:i'),
                        'id' => $invoice->invoice_number,
                        'customer' => $invoice->customer->name ?? '-',
                        'type' => ucfirst($invoice->type ?? 'Langganan'),
                        'item' => $invoice->servicePlan->name ?? 'Langganan',
                        'amount' => $invoice->total,
                        'status' => 'success',
                    ];
                });

            $voucherSales = Voucher::with(['customer', 'servicePlan'])
                ->where('status', 'used')
                ->whereBetween('activated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('activated_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($voucher) {
                    return (object) [
                        'date' => $voucher->activated_at ? $voucher->activated_at->format('Y-m-d H:i') : $voucher->created_at->format('Y-m-d H:i'),
                        'id' => 'VCH-' . $voucher->code,
                        'customer' => $voucher->customer->name ?? '-',
                        'type' => 'Voucher',
                        'item' => $voucher->servicePlan->name ?? 'Voucher',
                        'amount' => $voucher->price,
                        'status' => 'success',
                    ];
                });

            $combined = $invoiceSales->concat($voucherSales)
                ->sortByDesc('date')
                ->take(50)
                ->values();

            return $combined;
        } catch (\Exception $e) {
            return collect();
        }
    }

    protected function getEmptyIndexData(): array
    {
        return [
            'monthlyRevenue' => 0,
            'totalCustomers' => 0,
            'vouchersSold' => 0,
            'onlineUsers' => 0,
            'todayRevenue' => 0,
            'pendingInvoices' => 0,
            'recentTransactions' => collect(),
            'revenueData' => [0, 0, 0, 0, 0, 0],
            'revenueLabels' => ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            'customerDistribution' => [0, 0],
            'dbError' => 'Database tenant belum dikonfigurasi.',
        ];
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
