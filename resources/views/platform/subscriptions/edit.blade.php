@extends('layouts.app')

@section('title', 'Edit Paket Langganan')
@section('page-title', 'Edit Paket Langganan')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('platform.subscriptions.update', $subscription) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Paket</label>
                        <input type="text" name="name" id="name" value="{{ $subscription->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price_monthly" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Bulanan (Rp)</label>
                            <input type="number" name="price_monthly" id="price_monthly" value="{{ $subscription->price_monthly }}" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                        <div>
                            <label for="price_yearly" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Harga Tahunan (Rp)</label>
                            <input type="number" name="price_yearly" id="price_yearly" value="{{ $subscription->price_yearly }}" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="max_routers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Routers</label>
                            <input type="number" name="max_routers" id="max_routers" value="{{ $subscription->max_routers }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                        <div>
                            <label for="max_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Users</label>
                            <input type="number" name="max_users" id="max_users" value="{{ $subscription->max_users }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="max_vouchers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Vouchers</label>
                            <input type="number" name="max_vouchers" id="max_vouchers" value="{{ $subscription->max_vouchers }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                        <div>
                            <label for="max_online_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Online Users</label>
                            <input type="number" name="max_online_users" id="max_online_users" value="{{ $subscription->max_online_users }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $subscription->is_active ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('platform.subscriptions.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600">Batal</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Update Paket</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
