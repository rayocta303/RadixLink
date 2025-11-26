@extends('layouts.app')

@section('title', 'Subscriptions')
@section('page-title', 'Subscription Plans')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Manage subscription plans and active subscriptions.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('platform.subscriptions.create') }}" class="block rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Add Plan</a>
    </div>
</div>
<div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Subscription Plans</h3>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        @forelse($plans as $plan)
        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</h4>
            <p class="mt-2 text-2xl font-bold text-primary-600">Rp {{ number_format($plan->price_monthly) }}<span class="text-sm font-normal text-gray-500">/bulan</span></p>
            <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <li>Max {{ $plan->max_routers }} router</li>
                <li>Max {{ $plan->max_users }} user</li>
                <li>Max {{ $plan->max_vouchers }} voucher</li>
            </ul>
        </div>
        @empty
        <p class="text-gray-500 dark:text-gray-400">No subscription plans found.</p>
        @endforelse
    </div>
</div>
@endsection
