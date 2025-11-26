@extends('layouts.app')

@section('title', 'Subscriptions')
@section('page-title', 'Paket Langganan')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Kelola paket langganan dan subscription aktif.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('platform.subscriptions.create') }}" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah Paket
        </a>
    </div>
</div>

<div class="mt-8">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Paket Langganan</h3>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        @forelse($plans as $plan)
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden {{ $plan->slug === 'premium' ? 'ring-2 ring-primary-500' : '' }}">
            @if($plan->slug === 'premium')
            <div class="absolute top-0 right-0 bg-primary-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                POPULER
            </div>
            @endif
            <div class="p-6">
                <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h4>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $plan->description }}</p>
                <div class="mt-4">
                    <span class="text-3xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">/bulan</span>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    atau Rp {{ number_format($plan->price_yearly, 0, ',', '.') }}/tahun
                </p>
                
                <ul class="mt-6 space-y-3">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Maks. <strong>{{ $plan->max_routers >= 999 ? 'Unlimited' : $plan->max_routers }}</strong> Router</span>
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Maks. <strong>{{ $plan->max_users >= 999999 ? 'Unlimited' : number_format($plan->max_users) }}</strong> User</span>
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span>Maks. <strong>{{ $plan->max_vouchers >= 999999 ? 'Unlimited' : number_format($plan->max_vouchers) }}</strong> Voucher</span>
                    </li>
                    @if($plan->features)
                        @php $features = is_array($plan->features) ? $plan->features : json_decode($plan->features, true); @endphp
                        @if($features)
                            @foreach($features as $feature => $enabled)
                                @if($enabled)
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    <span>{{ ucwords(str_replace('_', ' ', $feature)) }}</span>
                                </li>
                                @endif
                            @endforeach
                        @endif
                    @endif
                </ul>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-700/50">
                <a href="{{ route('platform.subscriptions.edit', $plan) }}" class="block w-full rounded-lg {{ $plan->slug === 'premium' ? 'bg-primary-600 hover:bg-primary-500 text-white' : 'bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-900 dark:text-white' }} px-4 py-2.5 text-center text-sm font-semibold transition-colors">
                    Edit Paket
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Belum ada paket</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat paket langganan pertama.</p>
            <div class="mt-6">
                <a href="{{ route('platform.subscriptions.create') }}" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Paket
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<div class="mt-12">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Tenant Berlangganan</h3>
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <table id="subscriptionsTable" class="w-full stripe hover">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Paket</th>
                        <th>Status</th>
                        <th>Mulai</th>
                        <th>Berakhir</th>
                        <th class="no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants ?? [] as $tenant)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">{{ strtoupper(substr($tenant->company_name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $tenant->company_name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $tenant->subdomain }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                                {{ ucfirst($tenant->subscription_plan ?? 'Free') }}
                            </span>
                        </td>
                        <td>
                            @if($tenant->is_active && !$tenant->is_suspended)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td>{{ $tenant->created_at?->format('d M Y') ?? '-' }}</td>
                        <td>{{ $tenant->trial_ends_at?->format('d M Y') ?? '-' }}</td>
                        <td>
                            <a href="{{ route('platform.tenants.edit', $tenant) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300" title="Kelola">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#subscriptionsTable').DataTable({
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ],
        order: [[3, 'desc']]
    });
});
</script>
@endpush
