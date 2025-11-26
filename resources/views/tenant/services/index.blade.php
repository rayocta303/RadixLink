@extends('layouts.app')

@section('title', 'Service Plans')
@section('page-title', 'Service Plans')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Manage internet service plans for your customers.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('tenant.services.create') }}" class="block rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Add Plan</a>
    </div>
</div>
<div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <p class="text-gray-500 dark:text-gray-400 text-center py-8">No service plans created yet. Create your first plan.</p>
</div>
@endsection
