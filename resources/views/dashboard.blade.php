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
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Active Customers</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ $stats['active_customers'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Online Users</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">{{ $stats['online_users'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Available Vouchers</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-purple-600">{{ $stats['available_vouchers'] ?? 0 }}</dd>
            </div>
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Today's Revenue</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">Rp {{ number_format($stats['today_revenue'] ?? 0) }}</dd>
            </div>
        @endif
    </div>

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
