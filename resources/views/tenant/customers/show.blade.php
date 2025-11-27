
@extends('layouts.app')

@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')
<div class="max-w-5xl space-y-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Pelanggan</h3>
            @php
                $statusColors = [
                    'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                    'expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                    'suspended' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                ];
                $statusColor = $statusColors[$customer->status] ?? $statusColors['active'];
            @endphp
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium {{ $statusColor }}">
                <span class="h-1.5 w-1.5 rounded-full {{ $customer->status === 'active' ? 'bg-green-500' : ($customer->status === 'expired' ? 'bg-red-500' : 'bg-yellow-500') }}"></span>
                {{ ucfirst($customer->status) }}
            </span>
        </div>
        <div class="p-6">
            <div class="flex items-start gap-6">
                <div class="h-20 w-20 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-semibold text-primary-600 dark:text-primary-400">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                </div>
                <div class="flex-1">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Lengkap</dt>
                            <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $customer->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</dt>
                            <dd class="mt-1 text-base font-mono text-gray-900 dark:text-white">{{ $customer->username }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->email ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No. Telepon</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->phone ?? '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->address ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Layanan</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipe Layanan</dt>
                    <dd class="mt-1">
                        @if($customer->service_type === 'pppoe')
                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">PPPoE</span>
                        @elseif($customer->service_type === 'hotspot')
                            <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">Hotspot</span>
                        @elseif($customer->service_type === 'dhcp')
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">DHCP / Static</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-400">{{ ucfirst($customer->service_type) }}</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Paket Layanan</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->servicePlan->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Registrasi</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $customer->registered_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jatuh Tempo</dt>
                    <dd class="mt-1 text-sm {{ $customer->expires_at?->isPast() ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-900 dark:text-white' }}">
                        {{ $customer->expires_at?->format('d M Y') ?? '-' }}
                    </dd>
                </div>
                @if($customer->suspended_at)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Disuspend Pada</dt>
                    <dd class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">{{ $customer->suspended_at->format('d M Y H:i') }}</dd>
                    @if($customer->suspend_reason)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alasan: {{ $customer->suspend_reason }}</p>
                    @endif
                </div>
                @endif
            </dl>
        </div>
    </div>

    @if($customer->invoices && $customer->invoices->count() > 0)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Invoice</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Invoice</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($customer->invoices->take(5) as $invoice)
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                <a href="{{ route('tenant.invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $invoice->created_at->format('d M Y') }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @php
                                    $invoiceStatusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                        'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $invoiceStatusColors[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($customer->payments && $customer->payments->count() > 0)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Pembayaran</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($customer->payments->take(5) as $payment)
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->payment_id }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->paid_at?->format('d M Y H:i') }} - {{ ucfirst($payment->payment_method) }}</div>
                    </div>
                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="flex justify-between gap-3">
        <a href="{{ route('tenant.customers.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
            Kembali
        </a>
        <div class="flex gap-3">
            @if($customer->status === 'active')
                <form action="{{ route('tenant.customers.suspend', $customer) }}" method="POST" onsubmit="return confirm('Yakin ingin suspend pelanggan ini?')">
                    @csrf
                    <button type="submit" class="rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500">
                        Suspend
                    </button>
                </form>
            @elseif($customer->status === 'suspended')
                <form action="{{ route('tenant.customers.activate', $customer) }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        Aktifkan
                    </button>
                </form>
            @endif
            <a href="{{ route('tenant.customers.edit', $customer) }}" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                Edit Pelanggan
            </a>
        </div>
    </div>
</div>
@endsection
