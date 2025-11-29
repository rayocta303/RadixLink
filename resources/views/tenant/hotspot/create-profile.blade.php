@extends('layouts.app')

@section('title', 'Tambah Profil Hotspot')
@section('page-title', 'Tambah Profil Hotspot Baru')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.hotspot.profiles.store') }}" method="POST" class="space-y-6">
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
                            placeholder="Hotspot 1 Jam">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="profile_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Profil (MikroTik) <span class="text-red-500">*</span></label>
                        <input type="text" name="profile_name" id="profile_name" value="{{ old('profile_name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="hs-1jam">
                        @error('profile_name')
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
                        <label for="parent_queue" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parent Queue</label>
                        <input type="text" name="parent_queue" id="parent_queue" value="{{ old('parent_queue') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="none">
                        @error('parent_queue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="shared_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shared Users</label>
                        <input type="number" name="shared_users" id="shared_users" value="{{ old('shared_users', 1) }}" min="1" max="100"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah perangkat yang bisa login bersamaan</p>
                        @error('shared_users')
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
                            placeholder="1h atau 1d">
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
                        <input type="text" name="keepalive_timeout" id="keepalive_timeout" value="{{ old('keepalive_timeout', '2m') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="2m">
                        @error('keepalive_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="status_autorefresh" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Autorefresh (detik)</label>
                        <input type="number" name="status_autorefresh" id="status_autorefresh" value="{{ old('status_autorefresh', 60) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('status_autorefresh')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="mac_cookie_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">MAC Cookie Timeout</label>
                        <input type="text" name="mac_cookie_timeout" id="mac_cookie_timeout" value="{{ old('mac_cookie_timeout', '3d') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="3d">
                        @error('mac_cookie_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="login_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Login By</label>
                    <input type="text" name="login_by" id="login_by" value="{{ old('login_by', 'cookie,http-chap,http-pap') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="cookie,http-chap,http-pap">
                    @error('login_by')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Catatan tentang profil Hotspot ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="transparent_proxy" id="transparent_proxy" value="1" {{ old('transparent_proxy') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="transparent_proxy" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Transparent Proxy
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="open_status_page" id="open_status_page" value="1" {{ old('open_status_page', true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="open_status_page" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Open Status Page
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
            <a href="{{ route('tenant.hotspot.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan Profil
            </button>
        </div>
    </form>
</div>
@endsection
