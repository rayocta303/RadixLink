@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="mb-6">
    <a href="{{ route('platform.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar User
    </a>
</div>

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <dl class="space-y-4">
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Telepon</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Role</dt>
                <dd class="text-sm font-medium">
                    @foreach($user->roles ?? [] as $role)
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                    </span>
                    @endforeach
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="text-sm font-medium">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Login Terakhir</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->last_login_at?->format('d M Y H:i') ?? '-' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-500 dark:text-gray-400">Terdaftar</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at?->format('d M Y H:i') ?? '-' }}</dd>
            </div>
        </dl>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('platform.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Edit User
            </a>
        </div>
    </div>
</div>
@endsection
