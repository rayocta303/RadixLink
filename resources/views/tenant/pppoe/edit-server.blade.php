@extends('layouts.app')

@section('title', 'Edit Server PPPoE')
@section('page-title', 'Edit Server PPPoE')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.pppoe.update-server', $server->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Server</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Server <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $server->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="service_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service Name (MikroTik) <span class="text-red-500">*</span></label>
                        <input type="text" name="service_name" id="service_name" value="{{ old('service_name', $server->service_name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('service_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="interface" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interface <span class="text-red-500">*</span></label>
                        <input type="text" name="interface" id="interface" value="{{ old('interface', $server->interface) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono">
                        @error('interface')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nas_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NAS / Router</label>
                        <select name="nas_id" id="nas_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">-- Pilih NAS --</option>
                            @foreach($nasList ?? [] as $nas)
                                <option value="{{ $nas->id }}" {{ old('nas_id', $server->nas_id) == $nas->id ? 'selected' : '' }}>{{ $nas->shortname }} ({{ $nas->nasname }})</option>
                            @endforeach
                        </select>
                        @error('nas_id')
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
                                <option value="{{ $profile->id }}" {{ old('default_profile_id', $server->default_profile_id) == $profile->id ? 'selected' : '' }}>{{ $profile->name }}</option>
                            @endforeach
                        </select>
                        @error('default_profile_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="max_mtu" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max MTU</label>
                        <input type="number" name="max_mtu" id="max_mtu" value="{{ old('max_mtu', $server->max_mtu) }}" min="576" max="65535"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('max_mtu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="max_mru" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max MRU</label>
                        <input type="number" name="max_mru" id="max_mru" value="{{ old('max_mru', $server->max_mru) }}" min="576" max="65535"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('max_mru')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="max_sessions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Sessions (0 = unlimited)</label>
                        <input type="number" name="max_sessions" id="max_sessions" value="{{ old('max_sessions', $server->max_sessions) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('max_sessions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="authentication" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Authentication</label>
                    <input type="text" name="authentication" id="authentication" value="{{ old('authentication', $server->authentication) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    @error('authentication')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $server->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="one_session_per_host" id="one_session_per_host" value="1" {{ old('one_session_per_host', $server->one_session_per_host) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="one_session_per_host" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        One Session Per Host
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $server->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Server Aktif
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.pppoe.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
