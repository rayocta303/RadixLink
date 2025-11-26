@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customer Management')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Manage your PPPoE and Hotspot customers.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('tenant.customers.create') }}" class="block rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Add Customer</a>
    </div>
</div>
<div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <p class="text-gray-500 dark:text-gray-400 text-center py-8">No customers registered yet.</p>
</div>
@endsection
