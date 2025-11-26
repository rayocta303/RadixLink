@extends('layouts.app')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('platform.tenants.update', $tenant) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Owner Name</label>
                        <input type="text" name="name" id="name" value="{{ $tenant->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ $tenant->company_name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email" value="{{ $tenant->email }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ $tenant->phone }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                    </div>
                    <div>
                        <label for="subscription_plan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subscription Plan</label>
                        <select name="subscription_plan" id="subscription_plan" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="basic" {{ $tenant->subscription_plan === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="standard" {{ $tenant->subscription_plan === 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="premium" {{ $tenant->subscription_plan === 'premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="max_routers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Routers</label>
                            <input type="number" name="max_routers" id="max_routers" value="{{ $tenant->max_routers }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                        <div>
                            <label for="max_users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Users</label>
                            <input type="number" name="max_users" id="max_users" value="{{ $tenant->max_users }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                        <div>
                            <label for="max_vouchers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Vouchers</label>
                            <input type="number" name="max_vouchers" id="max_vouchers" value="{{ $tenant->max_vouchers }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('platform.tenants.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Update Tenant</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
