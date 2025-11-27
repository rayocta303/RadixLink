@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
<div class="max-w-4xl">
    <div class="mb-4">
        <a href="{{ route('tenant.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Daftar User
        </a>
    </div>
    
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <span class="text-xl font-semibold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('tenant.users.edit', $user) }}" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Edit User
                    </a>
                </div>
            </div>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Informasi Dasar</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Nama</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Telepon</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Role</dt>
                            <dd>
                                @php
                                    $role = $user->roles->first()?->name ?? 'user';
                                    $roleColors = [
                                        'owner' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                        'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                        'technician' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                        'cashier' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'reseller' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'support' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
                                        'investor' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400',
                                    ];
                                    $roleColor = $roleColors[$role] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleColor }}">
                                    {{ ucwords(str_replace('_', ' ', $role)) }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Aktivitas</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Dibuat Pada</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Diperbarui Pada</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->updated_at->format('d M Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Login Terakhir</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($user->last_login_at)
                                    {{ $user->last_login_at->format('d M Y H:i') }}
                                    <span class="text-gray-500 dark:text-gray-400">({{ $user->last_login_at->diffForHumans() }})</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">Belum pernah login</span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">IP Login Terakhir</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->last_login_ip ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <div class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Hak Akses</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @php
                        $permissions = [
                            'customers' => ['label' => 'Kelola Pelanggan', 'check' => $user->canManageCustomers()],
                            'vouchers' => ['label' => 'Kelola Voucher', 'check' => $user->canManageVouchers()],
                            'nas' => ['label' => 'Kelola NAS', 'check' => $user->canManageNas()],
                            'services' => ['label' => 'Kelola Layanan', 'check' => $user->canManageServices()],
                            'invoices' => ['label' => 'Kelola Invoice', 'check' => $user->canManageInvoices()],
                            'reports' => ['label' => 'Lihat Laporan', 'check' => $user->canViewReports()],
                            'financial' => ['label' => 'Laporan Keuangan', 'check' => $user->canViewFinancialReports()],
                            'users' => ['label' => 'Kelola User', 'check' => $user->canManageUsers()],
                            'settings' => ['label' => 'Pengaturan', 'check' => $user->canManageSettings()],
                            'reset_accounts' => ['label' => 'Reset Akun', 'check' => $user->canResetCustomerAccounts()],
                            'balance' => ['label' => 'Kelola Saldo', 'check' => $user->canManageBalance()],
                        ];
                    @endphp
                    
                    @foreach($permissions as $key => $permission)
                        <div class="flex items-center gap-2">
                            @if($permission['check'])
                                <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            @else
                                <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
