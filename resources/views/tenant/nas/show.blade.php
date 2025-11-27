@extends('layouts.app')

@section('title', 'Detail NAS/Router')
@section('page-title', 'Detail NAS/Router')

@section('content')
<div class="mb-6">
    <a href="{{ route('tenant.nas.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar NAS
    </a>
</div>

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                <svg class="h-8 w-8 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $nas->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $nas->nasname }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($nas->isOnline())
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                    <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                    Online
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                    <span class="h-2 w-2 rounded-full bg-gray-500"></span>
                    Offline
                </span>
            @endif
            <form action="{{ route('tenant.nas.test', $nas) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                    Test Koneksi
                </button>
            </form>
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Dasar</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Shortname</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->shortname }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">IP Address</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white font-mono">{{ $nas->nasname }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tipe</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($nas->type) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="text-sm font-medium">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $nas->status === 'enabled' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ ucfirst($nas->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Terakhir Online</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->last_seen?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pengaturan API</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">API Port</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->api_port ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Winbox Port</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->winbox_port ?? 8291 }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">SSL</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->use_ssl ? 'Ya' : 'Tidak' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">API Username</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->api_username ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if($nas->hasLocation())
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Lokasi</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Nama Lokasi</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->location_name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Koordinat</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->coordinates }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Coverage</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->coverage ?? 0 }} m</dd>
                </div>
            </dl>
        </div>
        @endif

        @if($nas->vpn_enabled)
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">VPN</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Tipe VPN</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ strtoupper($nas->vpn_type ?? '-') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Server</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->vpn_server ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Port</dt>
                    <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $nas->vpn_port ?? '-' }}</dd>
                </div>
            </dl>
        </div>
        @endif

        @if($nas->description)
        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Deskripsi</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ $nas->description }}</p>
        </div>
        @endif

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('tenant.nas.edit', $nas) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Edit NAS
            </a>
        </div>
    </div>
</div>
@endsection
