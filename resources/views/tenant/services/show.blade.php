@extends('layouts.app')

@section('title', 'Detail Paket Layanan')
@section('page-title', 'Detail Paket Layanan')

@section('content')
<div class="mb-6">
    <a href="{{ route('tenant.services.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Paket
    </a>
</div>

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $service->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kode: {{ $service->code }}</p>
        </div>
        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $service->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
            {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Dasar</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tipe</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($service->type) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Harga</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($service->price ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Durasi</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->validity_text }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Bandwidth</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->bandwidth_text }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Kuota</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->quota_text }}</dd>
                    </div>
                </dl>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pengaturan Lanjutan</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">FUP</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->has_fup ? 'Ya' : 'Tidak' }}</dd>
                    </div>
                    @if($service->has_fup)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Bandwidth FUP</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->fup_bandwidth_down }}/{{ $service->fup_bandwidth_up }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Sharing</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->can_share ? 'Ya' : 'Tidak' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Max Devices</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->max_devices ?? 1 }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Simultaneous Use</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->simultaneous_use ?? 1 }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Jumlah Pelanggan</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->customers_count ?? 0 }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($service->description)
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Deskripsi</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ $service->description }}</p>
        </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('tenant.services.edit', $service) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Edit Paket
            </a>
        </div>
    </div>
</div>
@endsection
