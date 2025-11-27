<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Nas;
use App\Models\Tenant\Radacct;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\Payment;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.monitoring.index', [
                'dbError' => 'Database tenant belum dikonfigurasi.',
                'stats' => $this->getEmptyStats(),
            ]);
        }

        $stats = $this->getStats();
        return view('tenant.monitoring.index', compact('stats'));
    }

    public function stats()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Database tenant belum dikonfigurasi.',
                'stats' => $this->getEmptyStats(),
            ]);
        }

        return response()->json([
            'success' => true,
            'stats' => $this->getStats(),
        ]);
    }

    public function onlineUsers()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Database tenant belum dikonfigurasi.',
                'users' => [],
            ]);
        }

        $onlineUsers = Radacct::active()
            ->orderBy('acctstarttime', 'desc')
            ->take(100)
            ->get()
            ->map(function ($session) {
                return [
                    'username' => $session->username,
                    'nas_ip' => $session->nasipaddress,
                    'nas_port' => $session->nasportid,
                    'framed_ip' => $session->framedipaddress,
                    'mac_address' => $session->callingstationid,
                    'start_time' => $session->acctstarttime?->format('Y-m-d H:i:s'),
                    'session_time' => $session->session_duration,
                    'upload' => $session->formatted_upload,
                    'download' => $session->formatted_download,
                    'total' => $session->formatted_total,
                    'upload_bytes' => $session->upload_bytes,
                    'download_bytes' => $session->download_bytes,
                ];
            });

        return response()->json([
            'success' => true,
            'users' => $onlineUsers,
            'total' => $onlineUsers->count(),
        ]);
    }

    public function nasStatus()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Database tenant belum dikonfigurasi.',
                'devices' => [],
            ]);
        }

        $nasDevices = Nas::orderBy('name')->get()->map(function ($nas) {
            return [
                'id' => $nas->id,
                'name' => $nas->name,
                'shortname' => $nas->shortname,
                'nasname' => $nas->nasname,
                'type' => $nas->type,
                'description' => $nas->description,
                'is_active' => $nas->is_active,
                'is_online' => $nas->isOnline(),
                'last_seen' => $nas->last_seen?->format('Y-m-d H:i:s'),
                'last_seen_human' => $nas->last_seen?->diffForHumans(),
                'location' => $nas->location_name,
            ];
        });

        $onlineCount = $nasDevices->where('is_online', true)->count();
        $totalCount = $nasDevices->count();

        return response()->json([
            'success' => true,
            'devices' => $nasDevices,
            'summary' => [
                'total' => $totalCount,
                'online' => $onlineCount,
                'offline' => $totalCount - $onlineCount,
            ],
        ]);
    }

    private function getStats(): array
    {
        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $startOfMonth = $now->copy()->startOfMonth();

        $userStats = $this->getUserStats($today);
        $nasStats = $this->getNasStats();
        $financialStats = $this->getFinancialStats($today, $startOfMonth);
        $recentActivity = $this->getRecentActivity();
        $trafficChart = $this->getTrafficChartData();
        $onlineUsersChart = $this->getOnlineUsersChartData();

        return [
            'userStats' => $userStats,
            'nasStats' => $nasStats,
            'financialStats' => $financialStats,
            'recentActivity' => $recentActivity,
            'trafficChart' => $trafficChart,
            'onlineUsersChart' => $onlineUsersChart,
            'lastUpdated' => $now->format('d M Y H:i:s'),
        ];
    }

    private function getUserStats(Carbon $today): array
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();

        $onlineUsers = 0;
        $sessionsToday = 0;
        $trafficToday = ['upload' => 0, 'download' => 0];

        try {
            $onlineUsers = Radacct::active()->count();

            $sessionsToday = Radacct::whereDate('acctstarttime', $today)->count();

            $trafficData = Radacct::whereDate('acctstarttime', $today)
                ->selectRaw('COALESCE(SUM(acctinputoctets), 0) as upload, COALESCE(SUM(acctoutputoctets), 0) as download')
                ->first();

            $trafficToday = [
                'upload' => $trafficData->upload ?? 0,
                'download' => $trafficData->download ?? 0,
            ];
        } catch (\Exception $e) {
        }

        return [
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'onlineUsers' => $onlineUsers,
            'sessionsToday' => $sessionsToday,
            'trafficToday' => $trafficToday,
            'formattedUpload' => $this->formatBytes($trafficToday['upload']),
            'formattedDownload' => $this->formatBytes($trafficToday['download']),
        ];
    }

    private function getNasStats(): array
    {
        $nasDevices = Nas::all();
        $totalNas = $nasDevices->count();
        $onlineNas = $nasDevices->filter(fn($nas) => $nas->isOnline())->count();

        $nasList = $nasDevices->map(function ($nas) {
            return [
                'id' => $nas->id,
                'name' => $nas->name,
                'shortname' => $nas->shortname,
                'nasname' => $nas->nasname,
                'type' => $nas->type,
                'is_online' => $nas->isOnline(),
                'last_seen' => $nas->last_seen?->format('d M Y H:i'),
                'location' => $nas->location_name,
            ];
        });

        return [
            'totalNas' => $totalNas,
            'onlineNas' => $onlineNas,
            'offlineNas' => $totalNas - $onlineNas,
            'nasList' => $nasList,
        ];
    }

    private function getFinancialStats(Carbon $today, Carbon $startOfMonth): array
    {
        $revenueThisMonth = Invoice::where('status', 'paid')
            ->whereDate('paid_at', '>=', $startOfMonth)
            ->sum('total');

        $pendingInvoices = Invoice::where('status', 'pending')->count();

        $vouchersSoldToday = Voucher::whereIn('status', ['sold', 'used'])
            ->whereDate('updated_at', $today)
            ->count();

        $activeVouchers = Voucher::where('status', 'unused')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();

        return [
            'revenueThisMonth' => $revenueThisMonth,
            'formattedRevenue' => 'Rp ' . number_format($revenueThisMonth, 0, ',', '.'),
            'pendingInvoices' => $pendingInvoices,
            'vouchersSoldToday' => $vouchersSoldToday,
            'activeVouchers' => $activeVouchers,
        ];
    }

    private function getRecentActivity(): array
    {
        $recentSessions = [];
        try {
            $recentSessions = Radacct::active()
                ->orderBy('acctstarttime', 'desc')
                ->take(10)
                ->get()
                ->map(function ($session) {
                    return [
                        'username' => $session->username,
                        'nas_ip' => $session->nasipaddress,
                        'framed_ip' => $session->framedipaddress,
                        'start_time' => $session->acctstarttime?->format('d M H:i'),
                        'session_time' => $session->session_duration,
                        'upload' => $session->formatted_upload,
                        'download' => $session->formatted_download,
                    ];
                });
        } catch (\Exception $e) {
        }

        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'name', 'username', 'status', 'created_at'])
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'username' => $customer->username,
                    'status' => $customer->status,
                    'created_at' => $customer->created_at?->format('d M Y H:i'),
                ];
            });

        $recentPayments = Payment::with(['customer:id,name', 'invoice:id,invoice_number'])
            ->where('status', 'success')
            ->orderBy('paid_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'customer' => $payment->customer?->name ?? 'N/A',
                    'invoice' => $payment->invoice?->invoice_number ?? 'N/A',
                    'amount' => 'Rp ' . number_format($payment->amount, 0, ',', '.'),
                    'method' => $payment->payment_method,
                    'paid_at' => $payment->paid_at?->format('d M Y H:i'),
                ];
            });

        return [
            'sessions' => $recentSessions,
            'customers' => $recentCustomers,
            'payments' => $recentPayments,
        ];
    }

    private function getTrafficChartData(): array
    {
        $days = [];
        $uploads = [];
        $downloads = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d M');

            try {
                $traffic = Radacct::whereDate('acctstarttime', $date)
                    ->selectRaw('COALESCE(SUM(acctinputoctets), 0) as upload, COALESCE(SUM(acctoutputoctets), 0) as download')
                    ->first();

                $uploads[] = round(($traffic->upload ?? 0) / (1024 * 1024 * 1024), 2);
                $downloads[] = round(($traffic->download ?? 0) / (1024 * 1024 * 1024), 2);
            } catch (\Exception $e) {
                $uploads[] = 0;
                $downloads[] = 0;
            }
        }

        return [
            'labels' => $days,
            'uploads' => $uploads,
            'downloads' => $downloads,
        ];
    }

    private function getOnlineUsersChartData(): array
    {
        $hours = [];
        $counts = [];

        for ($i = 0; $i < 24; $i++) {
            $hours[] = sprintf('%02d:00', $i);

            try {
                $startTime = Carbon::today()->addHours($i);
                $endTime = Carbon::today()->addHours($i + 1);

                $count = Radacct::where(function ($query) use ($startTime, $endTime) {
                    $query->where('acctstarttime', '<=', $endTime)
                        ->where(function ($q) use ($startTime) {
                            $q->whereNull('acctstoptime')
                                ->orWhere('acctstoptime', '>=', $startTime);
                        });
                })->count();

                $counts[] = $count;
            } catch (\Exception $e) {
                $counts[] = 0;
            }
        }

        return [
            'labels' => $hours,
            'counts' => $counts,
        ];
    }

    private function getEmptyStats(): array
    {
        return [
            'userStats' => [
                'totalCustomers' => 0,
                'activeCustomers' => 0,
                'onlineUsers' => 0,
                'sessionsToday' => 0,
                'trafficToday' => ['upload' => 0, 'download' => 0],
                'formattedUpload' => '0 B',
                'formattedDownload' => '0 B',
            ],
            'nasStats' => [
                'totalNas' => 0,
                'onlineNas' => 0,
                'offlineNas' => 0,
                'nasList' => [],
            ],
            'financialStats' => [
                'revenueThisMonth' => 0,
                'formattedRevenue' => 'Rp 0',
                'pendingInvoices' => 0,
                'vouchersSoldToday' => 0,
                'activeVouchers' => 0,
            ],
            'recentActivity' => [
                'sessions' => [],
                'customers' => [],
                'payments' => [],
            ],
            'trafficChart' => [
                'labels' => [],
                'uploads' => [],
                'downloads' => [],
            ],
            'onlineUsersChart' => [
                'labels' => [],
                'counts' => [],
            ],
            'lastUpdated' => now()->format('d M Y H:i:s'),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
