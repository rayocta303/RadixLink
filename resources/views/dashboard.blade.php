@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @if(auth()->user()->isPlatformUser())
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Tenants</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $stats['tenants'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Active Subscriptions</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $stats['active_subscriptions'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Monthly Revenue</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Rp {{ number_format($stats['monthly_revenue'] ?? 0) }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Open Tickets</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $stats['open_tickets'] ?? 0 }}</dd>
            </div>
        @else
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan Aktif</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $stats['active_customers'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">User Online</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ $stats['online_users'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Voucher Tersedia</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-purple-600">{{ $stats['available_vouchers'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Pendapatan Hari Ini</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Rp {{ number_format($stats['today_revenue'] ?? 0) }}</dd>
            </div>
        @endif
    </div>

    @if(!auth()->user()->isPlatformUser() && $usageData && $subscriptionInfo)
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Penggunaan Resource</h3>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $subscriptionInfo['plan_slug'] === 'platinum' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }}">
                        {{ $subscriptionInfo['plan_name'] }}
                    </span>
                </div>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Router</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $usageData['routers']['current'] ?? 0 }} / {{ ($usageData['routers']['limit'] ?? 0) >= 9999 ? 'Unlimited' : ($usageData['routers']['limit'] ?? 0) }}
                            </span>
                        </div>
                        @php $routerPct = min(100, $usageData['routers']['percentage'] ?? 0); @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $routerPct > 80 ? 'bg-red-500' : ($routerPct > 60 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $routerPct }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Pelanggan</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $usageData['customers']['current'] ?? 0 }} / {{ ($usageData['customers']['limit'] ?? 0) >= 99999 ? 'Unlimited' : number_format($usageData['customers']['limit'] ?? 0) }}
                            </span>
                        </div>
                        @php $customerPct = min(100, $usageData['customers']['percentage'] ?? 0); @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $customerPct > 80 ? 'bg-red-500' : ($customerPct > 60 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $customerPct }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Voucher</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($usageData['vouchers']['current'] ?? 0) }} / {{ ($usageData['vouchers']['limit'] ?? 0) >= 999999 ? 'Unlimited' : number_format($usageData['vouchers']['limit'] ?? 0) }}
                            </span>
                        </div>
                        @php $voucherPct = min(100, $usageData['vouchers']['percentage'] ?? 0); @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $voucherPct > 80 ? 'bg-red-500' : ($voucherPct > 60 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $voucherPct }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">User Online</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $usageData['online_users']['current'] ?? 0 }} / {{ ($usageData['online_users']['limit'] ?? 0) >= 9999 ? 'Unlimited' : number_format($usageData['online_users']['limit'] ?? 0) }}
                            </span>
                        </div>
                        @php $onlinePct = min(100, $usageData['online_users']['percentage'] ?? 0); @endphp
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $onlinePct > 80 ? 'bg-red-500' : ($onlinePct > 60 ? 'bg-yellow-500' : 'bg-green-500') }}" style="width: {{ $onlinePct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
            <div class="p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white mb-4">Info Langganan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Paket</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscriptionInfo['plan_name'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                        @if($subscriptionInfo['is_expired'])
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Kadaluarsa</span>
                        @else
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Aktif</span>
                        @endif
                    </div>
                    @if($subscriptionInfo['expires_at'])
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Berakhir</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscriptionInfo['expires_at']->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Sisa Hari</span>
                        <span class="text-sm font-medium {{ $subscriptionInfo['days_remaining'] < 7 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                            {{ (int) max(0, $subscriptionInfo['days_remaining']) }} hari
                        </span>
                    </div>
                    @endif
                    <div class="pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Fitur:</p>
                        <div class="flex flex-wrap gap-2">
                            @if($subscriptionInfo['custom_domain'])
                                <span class="inline-flex items-center rounded px-2 py-1 text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Custom Domain</span>
                            @endif
                            @if($subscriptionInfo['api_access'])
                                <span class="inline-flex items-center rounded px-2 py-1 text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">API Access</span>
                            @endif
                            @if($subscriptionInfo['priority_support'])
                                <span class="inline-flex items-center rounded px-2 py-1 text-xs bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Priority Support</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
            <div class="p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Recent Activity</h3>
                <div class="mt-6 flow-root">
                    <ul role="list" class="-mb-8">
                        @forelse($activities ?? [] as $activity)
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activity->description }}</p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                <time>{{ $activity->created_at->diffForHumans() }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-gray-500 dark:text-gray-400">No recent activity</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
            <div class="p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Quick Actions</h3>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    @if(auth()->user()->isPlatformUser())
                        <a href="{{ route('platform.tenants.create') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-5 shadow-sm hover:border-gray-400 dark:hover:border-gray-500">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Add Tenant</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Create new ISP</p>
                            </div>
                        </a>
                        <a href="{{ route('platform.tickets.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-5 shadow-sm hover:border-gray-400 dark:hover:border-gray-500">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Support</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">View tickets</p>
                            </div>
                        </a>
                    @else
                        <a href="{{ route('tenant.vouchers.create') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-5 shadow-sm hover:border-gray-400 dark:hover:border-gray-500">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Generate Voucher</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Create new vouchers</p>
                            </div>
                        </a>
                        <a href="{{ route('tenant.customers.create') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-5 shadow-sm hover:border-gray-400 dark:hover:border-gray-500">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Add Customer</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Register new customer</p>
                            </div>
                        </a>
                        <a href="{{ route('tenant.nas.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-5 shadow-sm hover:border-gray-400 dark:hover:border-gray-500">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Manage NAS</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Router settings</p>
                            </div>
                        </a>
                        <a href="{{ route('tenant.reports.index') }}" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-6 py-5 shadow-sm hover:border-gray-400 dark:hover:border-gray-500">
                            <div class="flex-shrink-0">
                                <svg class="h-10 w-10 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Reports</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">View analytics</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
