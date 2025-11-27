@extends('layouts.app')

@section('title', 'Buat Invoice')
@section('page-title', 'Buat Invoice Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('platform.invoices.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="tenant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tenant</label>
                        <select name="tenant_id" id="tenant_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="">Pilih Tenant</option>
                            @foreach($tenants ?? [] as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="subtotal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal (Rp)</label>
                        <input type="number" name="subtotal" id="subtotal" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="tax" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pajak (Rp)</label>
                            <input type="number" name="tax" id="tax" value="0" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                        <div>
                            <label for="discount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Diskon (Rp)</label>
                            <input type="number" name="discount" id="discount" value="0" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jatuh Tempo</label>
                        <input type="date" name="due_date" id="due_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2"></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('platform.invoices.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600">Batal</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Buat Invoice</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
