@extends('layouts.app')

@section('title', 'Detail Invoice')
@section('page-title', 'Detail Invoice')

@section('content')
<div class="mb-6">
    <a href="{{ route('platform.invoices.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Invoice
    </a>
</div>

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Invoice #{{ $invoice->id }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dibuat: {{ $invoice->issue_date?->format('d M Y') }}</p>
        </div>
        @php
            $statusColors = [
                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
            ];
        @endphp
        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusColors[$invoice->status] ?? $statusColors['draft'] }}">
            {{ ucfirst($invoice->status) }}
        </span>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tenant</h3>
                <p class="text-gray-900 dark:text-white font-medium">{{ $invoice->tenant->company_name ?? '-' }}</p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $invoice->tenant->email ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Detail Pembayaran</h3>
                <dl class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Jatuh Tempo</dt>
                        <dd class="text-gray-900 dark:text-white">{{ $invoice->due_date?->format('d M Y') }}</dd>
                    </div>
                    @if($invoice->paid_at)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Dibayar</dt>
                        <dd class="text-gray-900 dark:text-white">{{ $invoice->paid_at?->format('d M Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 dark:text-gray-400">
                        <th class="pb-3">Deskripsi</th>
                        <th class="pb-3 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="border-t border-gray-200 dark:border-gray-700">
                    <tr>
                        <td class="py-3 text-gray-900 dark:text-white">Subtotal</td>
                        <td class="py-3 text-right text-gray-900 dark:text-white">Rp {{ number_format($invoice->subtotal ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @if($invoice->tax > 0)
                    <tr>
                        <td class="py-3 text-gray-900 dark:text-white">Pajak</td>
                        <td class="py-3 text-right text-gray-900 dark:text-white">Rp {{ number_format($invoice->tax ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($invoice->discount > 0)
                    <tr>
                        <td class="py-3 text-gray-900 dark:text-white">Diskon</td>
                        <td class="py-3 text-right text-green-600 dark:text-green-400">- Rp {{ number_format($invoice->discount ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot class="border-t border-gray-200 dark:border-gray-700">
                    <tr>
                        <td class="pt-3 text-lg font-bold text-gray-900 dark:text-white">Total</td>
                        <td class="pt-3 text-right text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($invoice->notes)
        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Catatan</h3>
            <p class="text-gray-900 dark:text-white">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('platform.invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                Edit Invoice
            </a>
        </div>
    </div>
</div>
@endsection
