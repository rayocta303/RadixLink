@extends('layouts.app')

@section('title', 'Monitoring Dashboard')
@section('page-title', 'Dashboard Monitoring Platform')

@section('content')
<div id="monitoring-dashboard">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Terakhir diperbarui: <span id="lastUpdated">{{ $lastUpdated }}</span>
        </p>
        <button onclick="refreshStats()" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="h-4 w-4" id="refreshIcon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Refresh
        </button>
    </div>

    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistik Tenant</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Tenant</dt>
                                <dd class="text-2xl font-bold text-gray-900 dark:text-white" id="totalTenants">{{ $tenantStats['total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
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
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Tenant Aktif</dt>
                                <dd class="text-2xl font-bold text-green-600 dark:text-green-400" id="activeTenants">{{ $tenantStats['active'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Suspended</dt>
                                <dd class="text-2xl font-bold text-red-600 dark:text-red-400" id="suspendedTenants">{{ $tenantStats['suspended'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Masa Trial</dt>
                                <dd class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="trialTenants">{{ $tenantStats['trial'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Kadaluarsa Bulan Ini</dt>
                                <dd class="text-2xl font-bold text-orange-600 dark:text-orange-400" id="expiringTenants">{{ $tenantStats['expiringThisMonth'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistik Langganan</h3>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <p class="text-sm text-green-600 dark:text-green-400 font-medium">Pendapatan Bulan Ini</p>
                    <p class="text-2xl font-bold text-green-700 dark:text-green-300" id="revenueThisMonth">Rp {{ number_format($subscriptionStats['revenueThisMonth'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Langganan Aktif</p>
                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300" id="activeSubscriptions">{{ $subscriptionStats['activeSubscriptions'] }}</p>
                </div>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Distribusi Paket Langganan</h4>
                <div id="subscriptionChart" style="height: 250px;"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Kesehatan Sistem</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Versi PHP</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white" id="phpVersion">{{ $systemHealth['phpVersion'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Versi Laravel</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white" id="laravelVersion">{{ $systemHealth['laravelVersion'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status Database</span>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $systemHealth['dbStatus'] === 'Terhubung' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}" id="dbStatus">
                        <span class="h-1.5 w-1.5 rounded-full {{ $systemHealth['dbStatus'] === 'Terhubung' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $systemHealth['dbStatus'] }}
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total User Platform</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white" id="totalPlatformUsers">{{ $systemHealth['totalPlatformUsers'] }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Tiket Pending</span>
                    </div>
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $systemHealth['pendingTickets'] > 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}" id="pendingTickets">{{ $systemHealth['pendingTickets'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tenant Terbaru</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700" id="recentTenantsList">
                @forelse($recentActivity['tenants'] as $tenant)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">{{ strtoupper(substr($tenant->company_name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->company_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $tenant->subscription_plan === 'premium' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' : ($tenant->subscription_plan === 'standard' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400') }}">
                            {{ ucfirst($tenant->subscription_plan ?? 'Free') }}
                        </span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $tenant->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    Belum ada tenant terdaftar
                </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tiket Terbaru</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700" id="recentTicketsList">
                @forelse($recentActivity['tickets'] as $ticket)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">{{ $ticket->ticket_number }}</span>
                        @php
                            $priorityColors = [
                                'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                                'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            ];
                            $statusColors = [
                                'open' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'waiting' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                            ];
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $priorityColors[$ticket->priority] ?? $priorityColors['low'] }}">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColors[$ticket->status] ?? $statusColors['open'] }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ Str::limit($ticket->subject, 50) }}</p>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $ticket->tenant?->company_name ?? 'N/A' }}</span>
                        <span>{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                    Belum ada tiket support
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .apexcharts-legend-text {
        color: #6b7280 !important;
    }
    .dark .apexcharts-legend-text {
        color: #9ca3af !important;
    }
    .apexcharts-tooltip {
        background: #fff !important;
        border: 1px solid #e5e7eb !important;
    }
    .dark .apexcharts-tooltip {
        background: #1f2937 !important;
        border: 1px solid #374151 !important;
        color: #fff !important;
    }
    .dark .apexcharts-tooltip-title {
        background: #374151 !important;
        border-bottom: 1px solid #4b5563 !important;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin {
        animation: spin 1s linear infinite;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let subscriptionChart = null;
    let autoRefreshInterval = null;
    const isDarkMode = () => document.documentElement.classList.contains('dark');

    function initChart(distribution) {
        const chartOptions = {
            series: distribution.map(item => item.count),
            labels: distribution.map(item => item.plan),
            chart: {
                type: 'donut',
                height: 250,
                background: 'transparent',
            },
            colors: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444'],
            legend: {
                position: 'bottom',
                labels: {
                    colors: isDarkMode() ? '#9ca3af' : '#6b7280'
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '60%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                color: isDarkMode() ? '#fff' : '#374151',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            stroke: {
                show: false
            },
            tooltip: {
                theme: isDarkMode() ? 'dark' : 'light'
            },
            noData: {
                text: 'Tidak ada data',
                style: {
                    color: isDarkMode() ? '#9ca3af' : '#6b7280'
                }
            }
        };

        if (subscriptionChart) {
            subscriptionChart.destroy();
        }

        subscriptionChart = new ApexCharts(document.querySelector("#subscriptionChart"), chartOptions);
        subscriptionChart.render();
    }

    function updateStats(data) {
        document.getElementById('totalTenants').textContent = data.tenantStats.total;
        document.getElementById('activeTenants').textContent = data.tenantStats.active;
        document.getElementById('suspendedTenants').textContent = data.tenantStats.suspended;
        document.getElementById('trialTenants').textContent = data.tenantStats.trial;
        document.getElementById('expiringTenants').textContent = data.tenantStats.expiringThisMonth;

        document.getElementById('revenueThisMonth').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.subscriptionStats.revenueThisMonth);
        document.getElementById('activeSubscriptions').textContent = data.subscriptionStats.activeSubscriptions;

        document.getElementById('phpVersion').textContent = data.systemHealth.phpVersion;
        document.getElementById('laravelVersion').textContent = data.systemHealth.laravelVersion;
        document.getElementById('totalPlatformUsers').textContent = data.systemHealth.totalPlatformUsers;
        document.getElementById('pendingTickets').textContent = data.systemHealth.pendingTickets;

        document.getElementById('lastUpdated').textContent = data.lastUpdated;

        if (data.subscriptionStats.distribution.length > 0) {
            initChart(data.subscriptionStats.distribution);
        }
    }

    async function refreshStats() {
        const refreshIcon = document.getElementById('refreshIcon');
        refreshIcon.classList.add('animate-spin');

        try {
            const response = await fetch('{{ route("platform.monitoring.stats") }}');
            const data = await response.json();
            updateStats(data);
        } catch (error) {
            console.error('Failed to refresh stats:', error);
        } finally {
            refreshIcon.classList.remove('animate-spin');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialDistribution = @json($subscriptionStats['distribution']);
        if (initialDistribution.length > 0) {
            initChart(initialDistribution);
        } else {
            document.querySelector("#subscriptionChart").innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">Tidak ada data langganan</div>';
        }

        autoRefreshInterval = setInterval(refreshStats, 30000);
    });

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(autoRefreshInterval);
        } else {
            refreshStats();
            autoRefreshInterval = setInterval(refreshStats, 30000);
        }
    });

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                const distribution = @json($subscriptionStats['distribution']);
                if (distribution.length > 0) {
                    initChart(distribution);
                }
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });
</script>
@endpush
