@extends('layouts.app')

@section('title', 'Monitoring')
@section('page-title', 'Dashboard Monitoring')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .pulse-dot {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .refresh-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
@if(isset($dbError))
<div class="mb-6 rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Perhatian</h3>
            <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">{{ $dbError }}</p>
        </div>
    </div>
</div>
@endif

<div id="monitoring-dashboard">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Terakhir diperbarui: <span id="lastUpdated">{{ $stats['lastUpdated'] ?? '-' }}</span>
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Auto-refresh setiap 30 detik</p>
        </div>
        <button onclick="refreshStats()" id="refreshBtn" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
            <svg class="h-4 w-4" id="refreshIcon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span id="refreshText">Refresh</span>
        </button>
    </div>

    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistik Pengguna</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="stat-card bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Pelanggan</dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-white" id="totalCustomers">{{ $stats['userStats']['totalCustomers'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pelanggan Aktif</dt>
                                <dd class="text-2xl font-bold text-green-600 dark:text-green-400" id="activeCustomers">{{ $stats['userStats']['activeCustomers'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sedang Online</dt>
                                <dd class="flex items-center gap-2">
                                    <span class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="onlineUsers">{{ $stats['userStats']['onlineUsers'] ?? 0 }}</span>
                                    <span class="pulse-dot h-2 w-2 rounded-full bg-green-500"></span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sesi Hari Ini</dt>
                                <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="sessionsToday">{{ $stats['userStats']['sessionsToday'] ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Traffic Hari Ini</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                                    <span class="text-green-600 dark:text-green-400" title="Upload">↑ <span id="uploadToday">{{ $stats['userStats']['formattedUpload'] ?? '0 B' }}</span></span>
                                    <br>
                                    <span class="text-blue-600 dark:text-blue-400" title="Download">↓ <span id="downloadToday">{{ $stats['userStats']['formattedDownload'] ?? '0 B' }}</span></span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status NAS/Router</h3>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300" id="totalNas">{{ $stats['nasStats']['totalNas'] ?? 0 }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400">Total</p>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300" id="onlineNas">{{ $stats['nasStats']['onlineNas'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400">Online</p>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center">
                    <p class="text-2xl font-bold text-red-700 dark:text-red-300" id="offlineNas">{{ $stats['nasStats']['offlineNas'] ?? 0 }}</p>
                    <p class="text-xs text-red-600 dark:text-red-400">Offline</p>
                </div>
            </div>
            <div class="max-h-48 overflow-y-auto" id="nasListContainer">
                @forelse($stats['nasStats']['nasList'] ?? [] as $nas)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full {{ $nas['is_online'] ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas['name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $nas['nasname'] }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $nas['is_online'] ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ $nas['is_online'] ? 'Online' : 'Offline' }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada NAS terdaftar</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Keuangan</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        <p class="text-sm text-green-600 dark:text-green-400 font-medium">Pendapatan Bulan Ini</p>
                    </div>
                    <p class="text-xl font-bold text-green-700 dark:text-green-300" id="revenueThisMonth">{{ $stats['financialStats']['formattedRevenue'] ?? 'Rp 0' }}</p>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Invoice Pending</p>
                    </div>
                    <p class="text-xl font-bold text-yellow-700 dark:text-yellow-300" id="pendingInvoices">{{ $stats['financialStats']['pendingInvoices'] ?? 0 }}</p>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                        </svg>
                        <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Voucher Terjual Hari Ini</p>
                    </div>
                    <p class="text-xl font-bold text-purple-700 dark:text-purple-300" id="vouchersSoldToday">{{ $stats['financialStats']['vouchersSoldToday'] ?? 0 }}</p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Voucher Aktif</p>
                    </div>
                    <p class="text-xl font-bold text-blue-700 dark:text-blue-300" id="activeVouchers">{{ $stats['financialStats']['activeVouchers'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Traffic 7 Hari Terakhir</h3>
            <div id="trafficChart" style="height: 300px;"></div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">User Online Hari Ini (Per Jam)</h3>
            <div id="onlineUsersChart" style="height: 300px;"></div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-8">
        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pengguna Sedang Online</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="onlineUsersTable">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">NAS IP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Address</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mulai</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Durasi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Upload</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="onlineUsersBody">
                    @forelse($stats['recentActivity']['sessions'] ?? [] as $session)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $session['username'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $session['nas_ip'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $session['framed_ip'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $session['start_time'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $session['session_time'] }}</td>
                        <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400">{{ $session['upload'] }}</td>
                        <td class="px-4 py-3 text-sm text-blue-600 dark:text-blue-400">{{ $session['download'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada pengguna online</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pelanggan Baru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Username</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="recentCustomersBody">
                        @forelse($stats['recentActivity']['customers'] ?? [] as $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $customer['name'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $customer['username'] }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                        'suspended' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    ];
                                    $statusColor = $statusColors[$customer['status']] ?? $statusColors['active'];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst($customer['status']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $customer['created_at'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada pelanggan baru</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pembayaran Terakhir</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Pelanggan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Invoice</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="recentPaymentsBody">
                        @forelse($stats['recentActivity']['payments'] ?? [] as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $payment['customer'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $payment['invoice'] }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">{{ $payment['amount'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $payment['paid_at'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada pembayaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
let trafficChart = null;
let onlineUsersChart = null;
let autoRefreshInterval = null;

const chartColors = {
    upload: '#10B981',
    download: '#3B82F6',
    online: '#8B5CF6'
};

document.addEventListener('DOMContentLoaded', function() {
    initCharts();
    startAutoRefresh();
});

function initCharts() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#9CA3AF' : '#6B7280';
    const gridColor = isDark ? '#374151' : '#E5E7EB';

    const trafficData = @json($stats['trafficChart'] ?? ['labels' => [], 'uploads' => [], 'downloads' => []]);
    
    const trafficOptions = {
        series: [{
            name: 'Upload (GB)',
            data: trafficData.uploads
        }, {
            name: 'Download (GB)',
            data: trafficData.downloads
        }],
        chart: {
            type: 'area',
            height: 300,
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            background: 'transparent'
        },
        colors: [chartColors.upload, chartColors.download],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
            }
        },
        xaxis: {
            categories: trafficData.labels,
            labels: { style: { colors: textColor } },
            axisBorder: { color: gridColor },
            axisTicks: { color: gridColor }
        },
        yaxis: {
            labels: {
                style: { colors: textColor },
                formatter: (val) => val.toFixed(2) + ' GB'
            }
        },
        grid: { borderColor: gridColor },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            labels: { colors: textColor }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: (val) => val.toFixed(2) + ' GB' }
        }
    };

    trafficChart = new ApexCharts(document.querySelector('#trafficChart'), trafficOptions);
    trafficChart.render();

    const onlineData = @json($stats['onlineUsersChart'] ?? ['labels' => [], 'counts' => []]);

    const onlineOptions = {
        series: [{
            name: 'User Online',
            data: onlineData.counts
        }],
        chart: {
            type: 'bar',
            height: 300,
            fontFamily: 'Inter, sans-serif',
            toolbar: { show: false },
            background: 'transparent'
        },
        colors: [chartColors.online],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '60%'
            }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: onlineData.labels,
            labels: { 
                style: { colors: textColor },
                rotate: -45,
                rotateAlways: true
            },
            axisBorder: { color: gridColor },
            axisTicks: { color: gridColor }
        },
        yaxis: {
            labels: {
                style: { colors: textColor },
                formatter: (val) => Math.round(val)
            }
        },
        grid: { borderColor: gridColor },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: { formatter: (val) => Math.round(val) + ' user' }
        }
    };

    onlineUsersChart = new ApexCharts(document.querySelector('#onlineUsersChart'), onlineOptions);
    onlineUsersChart.render();
}

function startAutoRefresh() {
    autoRefreshInterval = setInterval(refreshStats, 30000);
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

async function refreshStats() {
    const refreshBtn = document.getElementById('refreshBtn');
    const refreshIcon = document.getElementById('refreshIcon');
    const refreshText = document.getElementById('refreshText');

    refreshIcon.classList.add('refresh-spin');
    refreshText.textContent = 'Memuat...';
    refreshBtn.disabled = true;

    try {
        const response = await fetch('{{ route("tenant.monitoring.stats") }}');
        const data = await response.json();

        if (data.success) {
            updateDashboard(data.stats);
        }
    } catch (error) {
        console.error('Error refreshing stats:', error);
    } finally {
        refreshIcon.classList.remove('refresh-spin');
        refreshText.textContent = 'Refresh';
        refreshBtn.disabled = false;
    }
}

function updateDashboard(stats) {
    document.getElementById('lastUpdated').textContent = stats.lastUpdated;
    
    document.getElementById('totalCustomers').textContent = stats.userStats.totalCustomers;
    document.getElementById('activeCustomers').textContent = stats.userStats.activeCustomers;
    document.getElementById('onlineUsers').textContent = stats.userStats.onlineUsers;
    document.getElementById('sessionsToday').textContent = stats.userStats.sessionsToday;
    document.getElementById('uploadToday').textContent = stats.userStats.formattedUpload;
    document.getElementById('downloadToday').textContent = stats.userStats.formattedDownload;

    document.getElementById('totalNas').textContent = stats.nasStats.totalNas;
    document.getElementById('onlineNas').textContent = stats.nasStats.onlineNas;
    document.getElementById('offlineNas').textContent = stats.nasStats.offlineNas;

    document.getElementById('revenueThisMonth').textContent = stats.financialStats.formattedRevenue;
    document.getElementById('pendingInvoices').textContent = stats.financialStats.pendingInvoices;
    document.getElementById('vouchersSoldToday').textContent = stats.financialStats.vouchersSoldToday;
    document.getElementById('activeVouchers').textContent = stats.financialStats.activeVouchers;

    if (trafficChart) {
        trafficChart.updateOptions({
            xaxis: { categories: stats.trafficChart.labels }
        });
        trafficChart.updateSeries([
            { name: 'Upload (GB)', data: stats.trafficChart.uploads },
            { name: 'Download (GB)', data: stats.trafficChart.downloads }
        ]);
    }

    if (onlineUsersChart) {
        onlineUsersChart.updateOptions({
            xaxis: { categories: stats.onlineUsersChart.labels }
        });
        onlineUsersChart.updateSeries([
            { name: 'User Online', data: stats.onlineUsersChart.counts }
        ]);
    }

    updateOnlineUsersTable(stats.recentActivity.sessions);
    updateNasList(stats.nasStats.nasList);
    updateRecentCustomers(stats.recentActivity.customers);
    updateRecentPayments(stats.recentActivity.payments);
}

function updateOnlineUsersTable(sessions) {
    const tbody = document.getElementById('onlineUsersBody');
    if (!sessions || sessions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada pengguna online</td></tr>';
        return;
    }

    tbody.innerHTML = sessions.map(session => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">${session.username}</td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">${session.nas_ip}</td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">${session.framed_ip || '-'}</td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${session.start_time}</td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${session.session_time}</td>
            <td class="px-4 py-3 text-sm text-green-600 dark:text-green-400">${session.upload}</td>
            <td class="px-4 py-3 text-sm text-blue-600 dark:text-blue-400">${session.download}</td>
        </tr>
    `).join('');
}

function updateNasList(nasList) {
    const container = document.getElementById('nasListContainer');
    if (!nasList || nasList.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada NAS terdaftar</p>';
        return;
    }

    container.innerHTML = nasList.map(nas => `
        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full ${nas.is_online ? 'bg-green-500' : 'bg-red-500'}"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">${nas.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${nas.nasname}</p>
                </div>
            </div>
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${nas.is_online ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'}">
                ${nas.is_online ? 'Online' : 'Offline'}
            </span>
        </div>
    `).join('');
}

function updateRecentCustomers(customers) {
    const tbody = document.getElementById('recentCustomersBody');
    if (!customers || customers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada pelanggan baru</td></tr>';
        return;
    }

    const statusColors = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        expired: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        suspended: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
    };

    tbody.innerHTML = customers.map(customer => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">${customer.name}</td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">${customer.username}</td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${statusColors[customer.status] || statusColors.active}">
                    ${customer.status.charAt(0).toUpperCase() + customer.status.slice(1)}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${customer.created_at}</td>
        </tr>
    `).join('');
}

function updateRecentPayments(payments) {
    const tbody = document.getElementById('recentPaymentsBody');
    if (!payments || payments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada pembayaran</td></tr>';
        return;
    }

    tbody.innerHTML = payments.map(payment => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">${payment.customer}</td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 font-mono">${payment.invoice}</td>
            <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">${payment.amount}</td>
            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">${payment.paid_at}</td>
        </tr>
    `).join('');
}

window.addEventListener('beforeunload', stopAutoRefresh);
</script>
@endpush
