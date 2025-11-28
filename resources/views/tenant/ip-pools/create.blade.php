@extends('layouts.app')

@section('title', 'Tambah IP Pool')
@section('page-title', 'Tambah IP Pool Baru')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.ip-pools.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi IP Pool</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pool <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Pool Hotspot Lantai 1">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pool_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pool (MikroTik) <span class="text-red-500">*</span></label>
                        <input type="text" name="pool_name" id="pool_name" value="{{ old('pool_name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="hotspot-pool-1">
                        @error('pool_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="range_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Awal <span class="text-red-500">*</span></label>
                        <input type="text" name="range_start" id="range_start" value="{{ old('range_start') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono"
                            placeholder="192.168.10.2">
                        @error('range_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="range_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Akhir <span class="text-red-500">*</span></label>
                        <input type="text" name="range_end" id="range_end" value="{{ old('range_end') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm font-mono"
                            placeholder="192.168.10.254">
                        @error('range_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Pool <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="hotspot" {{ old('type') == 'hotspot' ? 'selected' : '' }}>Hotspot</option>
                            <option value="pppoe" {{ old('type') == 'pppoe' ? 'selected' : '' }}>PPPoE</option>
                            <option value="both" {{ old('type', 'both') == 'both' ? 'selected' : '' }}>Keduanya</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nas_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">NAS / Router</label>
                        <select name="nas_id" id="nas_id"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">-- Semua NAS --</option>
                            @foreach($nasList ?? [] as $nas)
                                <option value="{{ $nas->id }}" {{ old('nas_id') == $nas->id ? 'selected' : '' }}>{{ $nas->shortname }} ({{ $nas->nasname }})</option>
                            @endforeach
                        </select>
                        @error('nas_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="next_pool" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Next Pool</label>
                    <input type="text" name="next_pool" id="next_pool" value="{{ old('next_pool') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Nama pool lain jika pool ini penuh">
                    @error('next_pool')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Catatan tambahan tentang IP Pool ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Pool Aktif
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.ip-pools.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan IP Pool
            </button>
        </div>
    </form>
</div>
@endsection
