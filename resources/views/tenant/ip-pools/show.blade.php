@extends('layouts.app')

@section('title', 'Detail IP Pool')
@section('page-title', 'Detail IP Pool')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6 flex flex-wrap gap-3 justify-end">
        <a href="{{ route('tenant.ip-pools.edit', $pool->id) }}" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
            </svg>
            Edit Pool
        </a>
    </div>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi IP Pool</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pool</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $pool->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pool (MikroTik)</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $pool->pool_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Range IP</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $pool->range_start }} - {{ $pool->range_end }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total IP</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $pool->total_ips ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipe</dt>
                        <dd class="mt-1">
                            @if($pool->type == 'hotspot')
                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                                    Hotspot
                                </span>
                            @elseif($pool->type == 'pppoe')
                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                    PPPoE
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    Keduanya
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1">
                            @if($pool->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                    Nonaktif
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">NAS / Router</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $pool->nas->shortname ?? 'Semua NAS' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Next Pool</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $pool->next_pool ?? '-' }}</dd>
                    </div>
                    @if($pool->description)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $pool->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        @if($pool->pppoeProfiles && $pool->pppoeProfiles->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profil PPPoE Terkait</h3>
            </div>
            <div class="p-6">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pool->pppoeProfiles as $profile)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $profile->profile_name }}</p>
                        </div>
                        <a href="{{ route('tenant.pppoe.edit-profile', $profile->id) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 text-sm">
                            Lihat
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if($pool->hotspotProfiles && $pool->hotspotProfiles->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profil Hotspot Terkait</h3>
            </div>
            <div class="p-6">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pool->hotspotProfiles as $profile)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $profile->profile_name }}</p>
                        </div>
                        <a href="{{ route('tenant.hotspot.edit-profile', $profile->id) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 text-sm">
                            Lihat
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>

    <div class="mt-6 flex justify-start">
        <a href="{{ route('tenant.ip-pools.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
            Kembali
        </a>
    </div>
</div>
@endsection
