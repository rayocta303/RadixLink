@extends('layouts.app')

@section('title', 'Buat Paket Langganan')
@section('page-title', 'Buat Paket Langganan Baru')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('platform.subscriptions.store') }}" method="POST" id="subscriptionForm">
                @csrf
                <div class="space-y-8">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Informasi Dasar</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi umum tentang paket langganan.</p>
                        
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Paket <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: CLOUD BASIC" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug <span class="text-red-500">*</span></label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required placeholder="Contoh: cloud-basic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('slug') border-red-500 @enderror">
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                                <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat tentang paket ini..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Harga</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tentukan harga bulanan dan tahunan untuk paket ini.</p>
                        
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="price_monthly" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Bulanan <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="price_monthly" id="price_monthly" value="{{ old('price_monthly', 0) }}" required min="0" step="1000" class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('price_monthly') border-red-500 @enderror">
                                </div>
                                @error('price_monthly')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="price_yearly" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Tahunan <span class="text-red-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="price_yearly" id="price_yearly" value="{{ old('price_yearly', 0) }}" required min="0" step="1000" class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('price_yearly') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Diskon: <span id="yearly_discount">0%</span> dari harga bulanan x 12</p>
                                @error('price_yearly')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Batasan Resource</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tentukan batasan maksimal resource yang bisa digunakan pelanggan.</p>
                        
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="max_routers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Router MikroTik <span class="text-red-500">*</span></label>
                                <input type="number" name="max_routers" id="max_routers" value="{{ old('max_routers', 2) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_routers') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah maksimal router yang bisa terhubung</p>
                                @error('max_routers')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="max_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Langganan (Pelanggan) <span class="text-red-500">*</span></label>
                                <input type="number" name="max_users" id="max_users" value="{{ old('max_users', 200) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_users') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah maksimal pelanggan PPPoE, DHCP, Hotspot</p>
                                @error('max_users')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="max_vouchers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Voucher <span class="text-red-500">*</span></label>
                                <input type="number" name="max_vouchers" id="max_vouchers" value="{{ old('max_vouchers', 5000) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_vouchers') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah maksimal voucher yang bisa dibuat</p>
                                @error('max_vouchers')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="max_online_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max User Online <span class="text-red-500">*</span></label>
                                <input type="number" name="max_online_users" id="max_online_users" value="{{ old('max_online_users', 250) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_online_users') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Batas maksimal user yang bisa online bersamaan</p>
                                @error('max_online_users')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Fitur Paket</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih fitur-fitur yang tersedia dalam paket ini.</p>
                        
                        <div class="mt-6 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div class="relative flex items-start p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="custom_domain" id="custom_domain" value="1" {{ old('custom_domain') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="custom_domain" class="font-medium text-gray-700 dark:text-gray-300">Custom Domain</label>
                                        <p class="text-gray-500 dark:text-gray-400">Pelanggan dapat menggunakan domain sendiri</p>
                                    </div>
                                </div>

                                <div class="relative flex items-start p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="api_access" id="api_access" value="1" {{ old('api_access') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="api_access" class="font-medium text-gray-700 dark:text-gray-300">Akses API</label>
                                        <p class="text-gray-500 dark:text-gray-400">Akses ke REST API untuk integrasi</p>
                                    </div>
                                </div>

                                <div class="relative flex items-start p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="priority_support" id="priority_support" value="1" {{ old('priority_support') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="priority_support" class="font-medium text-gray-700 dark:text-gray-300">Priority Support</label>
                                        <p class="text-gray-500 dark:text-gray-400">Dukungan teknis prioritas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Fitur Paket Layanan</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih fitur-fitur yang akan ditampilkan di halaman pricing.</p>
                        
                        @php
                            $availableFeatures = [
                                'Free VPN Radius' => 'Koneksi VPN untuk RADIUS server',
                                'Free VPN Remote' => 'Akses remote ke router via VPN',
                                'WhatsApp Notifikasi' => 'Notifikasi tagihan & pembayaran via WhatsApp',
                                'Payment Gateway' => 'Pembayaran otomatis QRIS, E-Wallet, VA',
                                'Aplikasi Client Area' => 'Portal pelanggan untuk cek tagihan & profil',
                                'Custom Domain' => 'Gunakan domain sendiri untuk aplikasi',
                                'RADIUS PPPoE' => 'Autentikasi PPPoE via RADIUS',
                                'RADIUS Hotspot' => 'Autentikasi Hotspot via RADIUS',
                                'Laporan Lengkap' => 'Laporan transaksi & analitik lengkap',
                                'Integrasi OLT' => 'Manajemen OLT GPON/EPON terintegrasi',
                                'Multi Lokasi' => 'Kelola banyak lokasi dalam satu dashboard',
                                'Sistem Reseller' => 'Fitur kemitraan & reseller',
                                'Template Voucher' => 'Desain voucher kustom',
                                'Tiket Support' => 'Sistem tiket untuk pelanggan',
                                'Backup Otomatis' => 'Backup data otomatis terjadwal',
                                'Export Excel' => 'Export data ke format Excel',
                            ];
                            $oldFeatures = old('features', []);
                        @endphp
                        
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($availableFeatures as $featureName => $featureDesc)
                            <label class="feature-checkbox relative flex items-start p-3 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="features[]" value="{{ $featureName }}" {{ in_array($featureName, $oldFeatures) ? 'checked' : '' }} class="feature-check h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                </div>
                                <div class="ml-3">
                                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $featureName }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $featureDesc }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Fitur Kustom</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tambahkan fitur lain yang tidak ada di daftar</p>
                                </div>
                                <button type="button" onclick="addCustomFeature()" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-primary-700 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900 dark:text-primary-300">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Tambah
                                </button>
                            </div>
                            <div id="custom-features-container" class="space-y-2"></div>
                        </div>
                    </div>

                    <div class="pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Pengaturan</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pengaturan tambahan untuk paket ini.</p>
                        
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Urutan Tampil</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Urutan kecil tampil lebih dulu</p>
                            </div>

                            <div class="sm:col-span-4 flex items-end">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">Aktifkan Paket</label>
                                        <p class="text-gray-500 dark:text-gray-400">Paket akan ditampilkan dan dapat dipilih pelanggan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('platform.subscriptions.index') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600">Batal</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Buat Paket
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slug').value = slug;
});

function updateDiscount() {
    const monthly = parseFloat(document.getElementById('price_monthly').value) || 0;
    const yearly = parseFloat(document.getElementById('price_yearly').value) || 0;
    const yearlyFromMonthly = monthly * 12;
    
    if (yearlyFromMonthly > 0 && yearly >= 0) {
        const discount = ((yearlyFromMonthly - yearly) / yearlyFromMonthly * 100);
        const displayDiscount = Math.max(0, discount).toFixed(1);
        document.getElementById('yearly_discount').textContent = displayDiscount + '%';
    } else {
        document.getElementById('yearly_discount').textContent = '0%';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateDiscount();
});

document.getElementById('price_monthly').addEventListener('input', updateDiscount);
document.getElementById('price_yearly').addEventListener('input', updateDiscount);

function addCustomFeature() {
    const container = document.getElementById('custom-features-container');
    const div = document.createElement('div');
    div.className = 'custom-feature-item flex items-center gap-2';
    div.innerHTML = `
        <input type="text" name="features[]" placeholder="Nama fitur kustom..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
        <button type="button" onclick="removeCustomFeature(this)" class="p-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
    div.querySelector('input').focus();
}

function removeCustomFeature(button) {
    button.closest('.custom-feature-item').remove();
}

document.querySelectorAll('.feature-checkbox').forEach(label => {
    const checkbox = label.querySelector('.feature-check');
    
    function updateStyle() {
        if (checkbox.checked) {
            label.classList.add('bg-primary-50', 'dark:bg-primary-900/20', 'border-primary-300', 'dark:border-primary-700');
        } else {
            label.classList.remove('bg-primary-50', 'dark:bg-primary-900/20', 'border-primary-300', 'dark:border-primary-700');
        }
    }
    
    checkbox.addEventListener('change', updateStyle);
    updateStyle();
});
</script>
@endpush
@endsection
