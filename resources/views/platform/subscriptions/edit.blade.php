@extends('layouts.app')

@section('title', 'Edit Paket Langganan')
@section('page-title', 'Edit Paket Langganan: ' . $subscription->name)

@section('content')
<div class="max-w-4xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('platform.subscriptions.update', $subscription) }}" method="POST" id="subscriptionForm">
                @csrf
                @method('PUT')
                <div class="space-y-8">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Informasi Dasar</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi umum tentang paket langganan.</p>
                        
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Paket <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $subscription->name) }}" required placeholder="Contoh: CLOUD BASIC" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug <span class="text-red-500">*</span></label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug', $subscription->slug) }}" required placeholder="Contoh: cloud-basic" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('slug') border-red-500 @enderror">
                                @error('slug')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                                <textarea name="description" id="description" rows="3" placeholder="Deskripsi singkat tentang paket ini..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description', $subscription->description) }}</textarea>
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
                                    <input type="number" name="price_monthly" id="price_monthly" value="{{ old('price_monthly', $subscription->price_monthly) }}" required min="0" step="1000" class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('price_monthly') border-red-500 @enderror">
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
                                    <input type="number" name="price_yearly" id="price_yearly" value="{{ old('price_yearly', $subscription->price_yearly) }}" required min="0" step="1000" class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm @error('price_yearly') border-red-500 @enderror">
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
                                <input type="number" name="max_routers" id="max_routers" value="{{ old('max_routers', $subscription->max_routers) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_routers') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah maksimal router yang bisa terhubung</p>
                                @error('max_routers')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="max_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Langganan (Pelanggan) <span class="text-red-500">*</span></label>
                                <input type="number" name="max_users" id="max_users" value="{{ old('max_users', $subscription->max_users) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_users') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah maksimal pelanggan PPPoE, DHCP, Hotspot</p>
                                @error('max_users')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="max_vouchers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Voucher <span class="text-red-500">*</span></label>
                                <input type="number" name="max_vouchers" id="max_vouchers" value="{{ old('max_vouchers', $subscription->max_vouchers) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_vouchers') border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jumlah maksimal voucher yang bisa dibuat</p>
                                @error('max_vouchers')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="sm:col-span-3">
                                <label for="max_online_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max User Online <span class="text-red-500">*</span></label>
                                <input type="number" name="max_online_users" id="max_online_users" value="{{ old('max_online_users', $subscription->max_online_users) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 @error('max_online_users') border-red-500 @enderror">
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
                                <div class="relative flex items-start p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ $subscription->custom_domain ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700' : '' }}">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="custom_domain" id="custom_domain" value="1" {{ old('custom_domain', $subscription->custom_domain) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="custom_domain" class="font-medium text-gray-700 dark:text-gray-300">Custom Domain</label>
                                        <p class="text-gray-500 dark:text-gray-400">Pelanggan dapat menggunakan domain sendiri</p>
                                    </div>
                                </div>

                                <div class="relative flex items-start p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ $subscription->api_access ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700' : '' }}">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="api_access" id="api_access" value="1" {{ old('api_access', $subscription->api_access) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="api_access" class="font-medium text-gray-700 dark:text-gray-300">Akses API</label>
                                        <p class="text-gray-500 dark:text-gray-400">Akses ke REST API untuk integrasi</p>
                                    </div>
                                </div>

                                <div class="relative flex items-start p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ $subscription->priority_support ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700' : '' }}">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="priority_support" id="priority_support" value="1" {{ old('priority_support', $subscription->priority_support) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
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
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Fitur Tambahan</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tambahkan fitur-fitur kustom yang akan ditampilkan di halaman pricing.</p>
                            </div>
                            <button type="button" onclick="addFeature()" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900 dark:text-primary-300 dark:hover:bg-primary-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Tambah Fitur
                            </button>
                        </div>
                        
                        <div id="features-container" class="mt-4 space-y-3">
                            @php
                                $features = old('features', $subscription->features ?? []);
                                if (empty($features)) {
                                    $features = [''];
                                }
                            @endphp
                            @foreach($features as $feature)
                            <div class="feature-item flex items-center gap-2">
                                <input type="text" name="features[]" value="{{ $feature }}" placeholder="Contoh: Free VPN Radius" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                                <button type="button" onclick="removeFeature(this)" class="p-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pb-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Pengaturan</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pengaturan tambahan untuk paket ini.</p>
                        
                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-2">
                                <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Urutan Tampil</label>
                                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $subscription->sort_order) }}" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Urutan kecil tampil lebih dulu</p>
                            </div>

                            <div class="sm:col-span-4 flex items-end">
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">Aktifkan Paket</label>
                                        <p class="text-gray-500 dark:text-gray-400">Paket akan ditampilkan dan dapat dipilih pelanggan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <div class="text-gray-500 dark:text-gray-400">
                                <span>Dibuat: {{ $subscription->created_at->format('d M Y H:i') }}</span>
                                <span class="mx-2">|</span>
                                <span>Terakhir diupdate: {{ $subscription->updated_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div>
                            <button type="button" onclick="confirmDelete()" class="rounded-md bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Hapus Paket
                            </button>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('platform.subscriptions.index') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600">Batal</a>
                            <button type="submit" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Paket
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
            <form id="deleteForm" action="{{ route('platform.subscriptions.destroy', $subscription) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDeleteModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Hapus Paket Langganan</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Apakah Anda yakin ingin menghapus paket <strong>{{ $subscription->name }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="document.getElementById('deleteForm').submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
    document.getElementById('price_monthly').addEventListener('input', updateDiscount);
    document.getElementById('price_yearly').addEventListener('input', updateDiscount);
});

function addFeature() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'feature-item flex items-center gap-2';
    div.innerHTML = `
        <input type="text" name="features[]" placeholder="Contoh: WhatsApp Notifikasi" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
        <button type="button" onclick="removeFeature(this)" class="p-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeFeature(button) {
    const container = document.getElementById('features-container');
    if (container.children.length > 1) {
        button.closest('.feature-item').remove();
    } else {
        button.closest('.feature-item').querySelector('input').value = '';
    }
}

function confirmDelete() {
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endpush
@endsection
