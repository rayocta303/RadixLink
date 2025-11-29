@extends('layouts.app')

@section('title', 'Tambah Profil PPPoE')
@section('page-title', 'Tambah Profil PPPoE Baru')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.pppoe.profiles.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Profil</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Profil <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="PPPoE 10 Mbps">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="profile_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Profil (MikroTik) <span class="text-red-500">*</span></label>
                        <input type="text" name="profile_name" id="profile_name" value="{{ old('profile_name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="pppoe-10m">
                        @error('profile_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="local_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Local Address</label>
                        <input type="text" name="local_address" id="local_address" value="{{ old('local_address') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono"
                            placeholder="10.0.0.1">
                        @error('local_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="remote_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remote Address</label>
                        <input type="text" name="remote_address" id="remote_address" value="{{ old('remote_address') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono"
                            placeholder="Atau gunakan IP Pool">
                        @error('remote_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="ip_pool_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Pool</label>
                        <select name="ip_pool_id" id="ip_pool_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">-- Pilih IP Pool --</option>
                            @foreach($ipPools ?? [] as $pool)
                                <option value="{{ $pool->id }}" {{ old('ip_pool_id') == $pool->id ? 'selected' : '' }}>{{ $pool->name }} ({{ $pool->pool_name }})</option>
                            @endforeach
                        </select>
                        @error('ip_pool_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="bandwidth_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Profil Bandwidth</label>
                        <select name="bandwidth_id" id="bandwidth_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">-- Pilih Bandwidth --</option>
                            @foreach($bandwidthProfiles ?? [] as $bw)
                                <option value="{{ $bw->id }}" {{ old('bandwidth_id') == $bw->id ? 'selected' : '' }}>{{ $bw->name }} ({{ $bw->rate_limit_download }})</option>
                            @endforeach
                        </select>
                        @error('bandwidth_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="dns_server" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNS Server</label>
                        <input type="text" name="dns_server" id="dns_server" value="{{ old('dns_server', '8.8.8.8,8.8.4.4') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono"
                            placeholder="8.8.8.8,8.8.4.4">
                        @error('dns_server')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="parent_queue" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parent Queue</label>
                        <input type="text" name="parent_queue" id="parent_queue" value="{{ old('parent_queue') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="none">
                        @error('parent_queue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pengaturan Sesi</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="session_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Session Timeout</label>
                        <input type="text" name="session_timeout" id="session_timeout" value="{{ old('session_timeout') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="1d atau 1h">
                        @error('session_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="idle_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Idle Timeout</label>
                        <input type="text" name="idle_timeout" id="idle_timeout" value="{{ old('idle_timeout') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="5m">
                        @error('idle_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="keepalive_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Keepalive Timeout</label>
                        <input type="text" name="keepalive_timeout" id="keepalive_timeout" value="{{ old('keepalive_timeout', '10') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="10">
                        @error('keepalive_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Catatan tentang profil PPPoE ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="only_one" id="only_one" value="1" {{ old('only_one') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="only_one" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Hanya Satu Sesi (Only One)
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Profil Aktif
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.pppoe.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan Profil
            </button>
        </div>
    </form>
</div>
@endsection
