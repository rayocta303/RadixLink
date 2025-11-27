@extends('layouts.app')

@section('title', 'Detail Paket Langganan')
@section('page-title', 'Detail Paket Langganan')

@section('content')
<div class="mb-6">
    <a href="{{ route('platform.subscriptions.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Paket
    </a>
</div>

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $subscription->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $subscription->slug }}</p>
        </div>
        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $subscription->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
            {{ $subscription->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Harga</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Bulanan</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($subscription->price_monthly ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tahunan</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($subscription->price_yearly ?? 0, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Limit</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Routers</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->max_routers ?? 0 }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Users</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->max_users ?? 0 }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Vouchers</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->max_vouchers ?? 0 }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Online Users</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->max_online_users ?? 0 }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($subscription->description)
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Deskripsi</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ $subscription->description }}</p>
        </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('platform.subscriptions.edit', $subscription) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Edit Paket
            </a>
        </div>
    </div>
</div>
@endsection
