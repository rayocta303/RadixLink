@extends('layouts.app')

@section('title', 'Detail Tenant')
@section('page-title', 'Detail Tenant')

@section('content')
<div class="mb-6">
    <a href="{{ route('platform.tenants.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Tenant
    </a>
</div>

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ strtoupper(substr($tenant->company_name, 0, 2)) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $tenant->company_name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $tenant->subdomain }}.example.com</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('platform.tenants.edit', $tenant) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                    Edit
                </a>
                @if($tenant->is_suspended)
                <form action="{{ route('platform.tenants.activate', $tenant) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Aktifkan
                    </button>
                </form>
                @else
                <form action="{{ route('platform.tenants.suspend', $tenant) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                        Suspend
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Tenant</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Nama Pemilik</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Telepon</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->phone ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Alamat</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->address ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Terdaftar</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Langganan & Limit</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Paket</dt>
                        <dd class="text-sm font-medium">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                                {{ ucfirst($tenant->subscription_plan ?? 'Basic') }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="text-sm font-medium">
                            @if($tenant->is_active && !$tenant->is_suspended)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Aktif</span>
                            @elseif($tenant->is_suspended)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Suspended</span>
                            @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">Nonaktif</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Routers</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->max_routers ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Users</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->max_users ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Vouchers</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->max_vouchers ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Berakhir</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant->subscription_expires_at?->format('d M Y') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
