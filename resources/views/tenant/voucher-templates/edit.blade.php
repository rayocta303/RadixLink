@extends('layouts.app')

@section('title', 'Edit Template Voucher')
@section('page-title', 'Edit Template: ' . $template->name)

@section('content')
<div class="max-w-6xl">
    <form action="{{ route('tenant.voucher-templates.update', $template) }}" method="POST" id="templateForm">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Informasi Template</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Template *</label>
                                <input type="text" name="name" value="{{ old('name', $template->name) }}" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                                <textarea name="description" rows="2" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">{{ old('description', $template->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pengaturan Cetak</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ukuran Kertas</label>
                                    <select name="paper_size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                                        @foreach($paperSizes as $value => $label)
                                        <option value="{{ $value }}" {{ old('paper_size', $template->paper_size) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Orientasi</label>
                                    <select name="orientation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                                        <option value="portrait" {{ old('orientation', $template->orientation) == 'portrait' ? 'selected' : '' }}>Portrait</option>
                                        <option value="landscape" {{ old('orientation', $template->orientation) == 'landscape' ? 'selected' : '' }}>Landscape</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Voucher per Halaman</label>
                                <input type="number" name="vouchers_per_page" value="{{ old('vouchers_per_page', $template->vouchers_per_page) }}" min="1" max="20" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            </div>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_qr_code" value="1" {{ old('show_qr_code', $template->show_qr_code) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Tampilkan QR Code</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_logo" value="1" {{ old('show_logo', $template->show_logo) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Tampilkan Logo</span>
                                </label>
                            </div>
                            <div class="flex items-center space-x-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $template->is_default) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Jadikan Default</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Template HTML</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Variabel: {company_name}, {plan_name}, {voucher_code}, {username}, {password}, {validity}, {bandwidth}, {price}, {qr_code}, {logo_url}, {footer_text}</p>
                        <textarea name="html_template" id="htmlTemplate" rows="15" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 font-mono text-xs">{{ old('html_template', $template->html_template) }}</textarea>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">CSS Styles</h3>
                        <textarea name="css_styles" id="cssStyles" rows="12" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2 font-mono text-xs">{{ old('css_styles', $template->css_styles) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="lg:sticky lg:top-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Preview</h3>
                            <button type="button" onclick="updatePreview()" class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                Refresh Preview
                            </button>
                        </div>
                        <div id="previewContainer" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900 min-h-[400px] overflow-auto">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('tenant.voucher-templates.index') }}" class="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                        Batal
                    </a>
                    <button type="submit" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const sampleData = {
    company_name: 'WiFi Hotspot',
    plan_name: 'Paket Harian 24 Jam',
    voucher_code: 'WIFI-ABC12345',
    username: 'v123456',
    password: 'pass1234',
    validity: '24 Jam',
    bandwidth: '10 Mbps / 5 Mbps',
    price: '10.000',
    logo_url: 'https://via.placeholder.com/100x40?text=LOGO',
    qr_code: 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=WIFI-ABC12345',
    footer_text: 'Terima kasih telah menggunakan layanan kami'
};

function updatePreview() {
    let html = document.getElementById('htmlTemplate').value;
    let css = document.getElementById('cssStyles').value;
    
    for (const [key, value] of Object.entries(sampleData)) {
        html = html.replace(new RegExp(`\\{${key}\\}`, 'g'), value);
    }
    
    html = html.replace(/\{if_logo\}([\s\S]*?)\{\/if_logo\}/g, '$1');
    html = html.replace(/\{if_qrcode\}([\s\S]*?)\{\/if_qrcode\}/g, '$1');
    
    const preview = `<style>${css}</style>${html}`;
    document.getElementById('previewContainer').innerHTML = preview;
}

document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    
    document.getElementById('htmlTemplate').addEventListener('input', debounce(updatePreview, 500));
    document.getElementById('cssStyles').addEventListener('input', debounce(updatePreview, 500));
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush
