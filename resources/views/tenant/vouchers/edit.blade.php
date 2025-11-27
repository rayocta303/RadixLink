@extends('layouts.app')

@section('title', 'Edit Voucher')
@section('page-title', 'Edit Voucher')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Kode Voucher:</p>
                <p class="text-lg font-bold font-mono text-gray-900 dark:text-white">{{ $voucher->code }}</p>
            </div>

            <form action="{{ route('tenant.vouchers.update', $voucher) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="service_plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Paket Layanan</label>
                        <select name="service_plan_id" id="service_plan_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            @foreach($servicePlans ?? [] as $plan)
                            <option value="{{ $plan->id }}" {{ $voucher->service_plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }} - Rp {{ number_format($plan->price, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="unused" {{ $voucher->status === 'unused' ? 'selected' : '' }}>Tersedia</option>
                            <option value="used" {{ $voucher->status === 'used' ? 'selected' : '' }}>Digunakan</option>
                            <option value="expired" {{ $voucher->status === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="disabled" {{ $voucher->status === 'disabled' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('tenant.vouchers.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600">Batal</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Update Voucher</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
