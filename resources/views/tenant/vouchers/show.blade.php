@extends('layouts.app')

@section('title', 'Detail Voucher')
@section('page-title', 'Detail Voucher')

@section('content')
<div class="mb-6">
    <a href="{{ route('tenant.vouchers.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Voucher
    </a>
</div>

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white font-mono">{{ $voucher->code }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Batch: {{ $voucher->batch_id }}</p>
        </div>
        @php
            $statusColors = [
                'unused' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'used' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                'expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                'disabled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
            ];
            $statusLabels = [
                'unused' => 'Tersedia',
                'used' => 'Digunakan',
                'expired' => 'Expired',
                'disabled' => 'Nonaktif',
            ];
        @endphp
        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusColors[$voucher->status] ?? $statusColors['unused'] }}">
            {{ $statusLabels[$voucher->status] ?? ucfirst($voucher->status) }}
        </span>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Voucher</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Paket</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->servicePlan->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Durasi</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->servicePlan->validity_text ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Harga</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($voucher->price ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tipe</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->type === 'single' ? 'Single Use' : 'Multi Use' }}</dd>
                    </div>
                    @if($voucher->type === 'multi')
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Penggunaan</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->used_count ?? 0 }} / {{ $voucher->max_usage }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Kredensial</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Username</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $voucher->username }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Password</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $voucher->password }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Waktu</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Dibuat</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->generated_at?->format('d M Y H:i') ?? $voucher->created_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                @if($voucher->first_used_at)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Pertama Digunakan</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->first_used_at->format('d M Y H:i') }}</dd>
                </div>
                @endif
                @if($voucher->activated_at)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Diaktifkan</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->activated_at->format('d M Y H:i') }}</dd>
                </div>
                @endif
                @if($voucher->expires_at)
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Berakhir</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->expires_at->format('d M Y H:i') }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if($voucher->customer)
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pengguna</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nama</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $voucher->customer->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">MAC Address</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $voucher->used_mac ?? '-' }}</dd>
                </div>
            </dl>
        </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            @if($voucher->status === 'unused')
            <a href="{{ route('tenant.vouchers.edit', $voucher) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Edit Voucher
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
