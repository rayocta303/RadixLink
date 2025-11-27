@extends('layouts.app')

@section('title', 'Generate Vouchers')
@section('page-title', 'Generate Voucher Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('tenant.vouchers.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paket Layanan</label>
                        <select name="service_plan_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="">Pilih Paket</option>
                            @foreach($servicePlans ?? [] as $plan)
                            <option value="{{ $plan->id }}">{{ $plan->name }} - Rp {{ number_format($plan->price ?? 0, 0, ',', '.') }} ({{ $plan->validity_text ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
                        <input type="number" name="quantity" value="10" min="1" max="500" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 500 voucher per batch</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prefix Kode</label>
                            <input type="text" name="prefix" placeholder="VC-" maxlength="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Panjang Kode</label>
                            <select name="code_length" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                                <option value="6">6 karakter</option>
                                <option value="8" selected>8 karakter</option>
                                <option value="10">10 karakter</option>
                                <option value="12">12 karakter</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Kode</label>
                        <select name="code_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="alphanumeric">Alphanumeric (A-Z, 0-9)</option>
                            <option value="numeric">Numeric only (0-9)</option>
                            <option value="alpha">Alphabetic only (A-Z)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Voucher</label>
                        <select name="type" id="voucher_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="single">Single Use (1x pakai)</option>
                            <option value="multi">Multi Use (banyak pakai)</option>
                        </select>
                    </div>
                    <div id="max_usage_container" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Penggunaan</label>
                        <input type="number" name="max_usage" id="max_usage" value="5" min="2" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Berapa kali voucher bisa digunakan</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('tenant.vouchers.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600">Batal</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Generate Vouchers</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('voucher_type').addEventListener('change', function() {
    var container = document.getElementById('max_usage_container');
    container.style.display = this.value === 'multi' ? 'block' : 'none';
});
</script>
@endpush
