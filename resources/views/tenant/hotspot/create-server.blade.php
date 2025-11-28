@extends('layouts.app')

@section('title', 'Tambah Server Hotspot')
@section('page-title', 'Tambah Server Hotspot Baru')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.hotspot.store-server') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Server</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Server <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Hotspot Lantai 1">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="dns_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNS Name</label>
                        <input type="text" name="dns_name" id="dns_name" value="{{ old('dns_name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="hotspot.local">
                        @error('dns_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="interface" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interface <span class="text-red-500">*</span></label>
                        <input type="text" name="interface" id="interface" value="{{ old('interface') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono"
                            placeholder="wlan1 atau bridge-local">
                        @error('interface')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Address</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono"
                            placeholder="192.168.88.1">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="nas_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NAS / Router</label>
                        <select name="nas_id" id="nas_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">-- Pilih NAS --</option>
                            @foreach($nasList ?? [] as $nas)
                                <option value="{{ $nas->id }}" {{ old('nas_id') == $nas->id ? 'selected' : '' }}>{{ $nas->shortname }} ({{ $nas->nasname }})</option>
                            @endforeach
                        </select>
                        @error('nas_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
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
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="default_profile_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Default Profile</label>
                        <select name="default_profile_id" id="default_profile_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">-- Pilih Profil --</option>
                            @foreach($profiles ?? [] as $profile)
                                <option value="{{ $profile->id }}" {{ old('default_profile_id') == $profile->id ? 'selected' : '' }}>{{ $profile->name }}</option>
                            @endforeach
                        </select>
                        @error('default_profile_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="html_directory" class="block text-sm font-medium text-gray-700 dark:text-gray-300">HTML Directory</label>
                        <input type="text" name="html_directory" id="html_directory" value="{{ old('html_directory', 'hotspot') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="hotspot">
                        @error('html_directory')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="login_timeout" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Login Timeout</label>
                        <input type="text" name="login_timeout" id="login_timeout" value="{{ old('login_timeout', '2m') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="2m">
                        @error('login_timeout')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="http_cookie_lifetime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">HTTP Cookie Lifetime</label>
                        <input type="text" name="http_cookie_lifetime" id="http_cookie_lifetime" value="{{ old('http_cookie_lifetime', '3d') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="3d">
                        @error('http_cookie_lifetime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Catatan tentang server Hotspot ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="split_user_domain" id="split_user_domain" value="1" {{ old('split_user_domain') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="split_user_domain" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Split User Domain
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Server Aktif
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.hotspot.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan Server
            </button>
        </div>
    </form>
</div>
@endsection
