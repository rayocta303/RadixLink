@extends('layouts.app')

@section('title', 'Edit Bandwidth')
@section('page-title', 'Edit Profil Bandwidth')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.bandwidth.update', $profile->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Dasar</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Profil <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $profile->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="profile_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Profil (MikroTik) <span class="text-red-500">*</span></label>
                        <input type="text" name="profile_name" id="profile_name" value="{{ old('profile_name', $profile->profile_name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('profile_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $profile->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Rate Limit</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: 10M, 100M, 1G (contoh: 10M = 10 Mbps)</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="rate_limit_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rate Upload <span class="text-red-500">*</span></label>
                        <input type="text" name="rate_limit_upload" id="rate_limit_upload" value="{{ old('rate_limit_upload', $profile->rate_limit_upload) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono">
                        @error('rate_limit_upload')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="rate_limit_download" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rate Download <span class="text-red-500">*</span></label>
                        <input type="text" name="rate_limit_download" id="rate_limit_download" value="{{ old('rate_limit_download', $profile->rate_limit_download) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono">
                        @error('rate_limit_download')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Burst Settings</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Opsional. Konfigurasi burst untuk kecepatan sementara yang lebih tinggi.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="burst_limit_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Burst Limit Upload</label>
                        <input type="text" name="burst_limit_upload" id="burst_limit_upload" value="{{ old('burst_limit_upload', $profile->burst_limit_upload) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono">
                        @error('burst_limit_upload')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="burst_limit_download" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Burst Limit Download</label>
                        <input type="text" name="burst_limit_download" id="burst_limit_download" value="{{ old('burst_limit_download', $profile->burst_limit_download) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono">
                        @error('burst_limit_download')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="burst_threshold_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Burst Threshold Upload</label>
                        <input type="text" name="burst_threshold_upload" id="burst_threshold_upload" value="{{ old('burst_threshold_upload', $profile->burst_threshold_upload) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono">
                        @error('burst_threshold_upload')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="burst_threshold_download" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Burst Threshold Download</label>
                        <input type="text" name="burst_threshold_download" id="burst_threshold_download" value="{{ old('burst_threshold_download', $profile->burst_threshold_download) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono">
                        @error('burst_threshold_download')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="burst_time_upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Burst Time Upload (detik)</label>
                        <input type="number" name="burst_time_upload" id="burst_time_upload" value="{{ old('burst_time_upload', $profile->burst_time_upload) }}" min="1" max="60"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('burst_time_upload')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="burst_time_download" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Burst Time Download (detik)</label>
                        <input type="number" name="burst_time_download" id="burst_time_download" value="{{ old('burst_time_download', $profile->burst_time_download) }}" min="1" max="60"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('burst_time_download')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority (1-8)</label>
                    <input type="number" name="priority" id="priority" value="{{ old('priority', $profile->priority) }}" min="1" max="8"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">1 = prioritas tertinggi, 8 = prioritas terendah</p>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $profile->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Profil Aktif
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.bandwidth.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
