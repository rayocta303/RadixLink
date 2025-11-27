@extends('layouts.app')

@section('title', 'Tambah Paket Layanan')
@section('page-title', 'Tambah Paket Layanan')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.services.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Paket</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Paket <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Contoh: Paket Hemat 10 Mbps">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Paket</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Kosongkan untuk auto-generate">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Layanan <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="hotspot" {{ old('type') == 'hotspot' ? 'selected' : '' }}>Hotspot</option>
                            <option value="pppoe" {{ old('type') == 'pppoe' ? 'selected' : '' }}>PPPoE</option>
                            <option value="dhcp" {{ old('type') == 'dhcp' ? 'selected' : '' }}>DHCP</option>
                            <option value="hybrid" {{ old('type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" id="price" value="{{ old('price', 0) }}" min="0" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Deskripsi singkat paket layanan">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Durasi Layanan</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="validity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durasi <span class="text-red-500">*</span></label>
                        <input type="number" name="validity" id="validity" value="{{ old('validity', 30) }}" min="1" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('validity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="validity_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan Waktu <span class="text-red-500">*</span></label>
                        <select name="validity_unit" id="validity_unit" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="minutes" {{ old('validity_unit') == 'minutes' ? 'selected' : '' }}>Menit</option>
                            <option value="hours" {{ old('validity_unit') == 'hours' ? 'selected' : '' }}>Jam</option>
                            <option value="days" {{ old('validity_unit', 'days') == 'days' ? 'selected' : '' }}>Hari</option>
                            <option value="months" {{ old('validity_unit') == 'months' ? 'selected' : '' }}>Bulan</option>
                        </select>
                        @error('validity_unit')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Bandwidth</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: angka diikuti satuan (contoh: 10M, 512K, 1G)</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="bandwidth_down" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Download Speed <span class="text-red-500">*</span></label>
                        <input type="text" name="bandwidth_down" id="bandwidth_down" value="{{ old('bandwidth_down', '10M') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Contoh: 10M, 512K">
                        @error('bandwidth_down')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="bandwidth_up" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Speed <span class="text-red-500">*</span></label>
                        <input type="text" name="bandwidth_up" id="bandwidth_up" value="{{ old('bandwidth_up', '5M') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Contoh: 5M, 256K">
                        @error('bandwidth_up')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="quota_gb" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kuota (GB)</label>
                        <input type="number" name="quota_gb" id="quota_gb" value="{{ old('quota_gb', 0) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="0 = Unlimited">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Masukkan 0 untuk unlimited</p>
                    </div>
                    <div>
                        <label for="simultaneous_use" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Penggunaan Simultan</label>
                        <input type="number" name="simultaneous_use" id="simultaneous_use" value="{{ old('simultaneous_use', 1) }}" min="1" max="100"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah koneksi bersamaan yang diizinkan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">FUP (Fair Usage Policy)</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="has_fup" id="has_fup" value="1" {{ old('has_fup') ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                        onchange="toggleFupFields()">
                    <label for="has_fup" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Aktifkan FUP (bandwidth akan diturunkan setelah kuota tertentu)
                    </label>
                </div>
                <div id="fupFields" class="space-y-4 {{ old('has_fup') ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="fup_threshold_gb" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Threshold FUP (GB)</label>
                            <input type="number" name="fup_threshold_gb" id="fup_threshold_gb" value="{{ old('fup_threshold_gb', 0) }}" min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="fup_bandwidth_down" class="block text-sm font-medium text-gray-700 dark:text-gray-300">FUP Download</label>
                            <input type="text" name="fup_bandwidth_down" id="fup_bandwidth_down" value="{{ old('fup_bandwidth_down') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                placeholder="Contoh: 1M">
                        </div>
                        <div>
                            <label for="fup_bandwidth_up" class="block text-sm font-medium text-gray-700 dark:text-gray-300">FUP Upload</label>
                            <input type="text" name="fup_bandwidth_up" id="fup_bandwidth_up" value="{{ old('fup_bandwidth_up') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                placeholder="Contoh: 512K">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pengaturan Lainnya</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="max_devices" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maksimum Device</label>
                        <input type="number" name="max_devices" id="max_devices" value="{{ old('max_devices', 1) }}" min="1" max="100"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div class="flex items-end pb-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="can_share" id="can_share" value="1" {{ old('can_share') ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="can_share" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Dapat dibagi (sharing)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Paket Aktif (tersedia untuk dijual)
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.services.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan Paket
            </button>
        </div>
    </form>
</div>

<script>
function toggleFupFields() {
    const hasFup = document.getElementById('has_fup').checked;
    const fupFields = document.getElementById('fupFields');
    fupFields.classList.toggle('hidden', !hasFup);
}
</script>
@endsection
