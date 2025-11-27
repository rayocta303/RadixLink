@extends('layouts.app')

@section('title', 'Detail Invoice')
@section('page-title', 'Detail Invoice #' . ($invoice->invoice_number ?? ''))

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Invoice</h3>
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                    'draft' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                    'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                    'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                ];
                $statusLabels = [
                    'pending' => 'Belum Dibayar',
                    'paid' => 'Lunas',
                    'draft' => 'Draft',
                    'overdue' => 'Jatuh Tempo',
                    'cancelled' => 'Dibatalkan',
                ];
            @endphp
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusColors[$invoice->status] ?? $statusColors['pending'] }}">
                {{ $statusLabels[$invoice->status] ?? ucfirst($invoice->status) }}
            </span>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">No. Invoice</dt>
                    <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $invoice->issue_date?->format('d M Y') ?? $invoice->created_at?->format('d M Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $invoice->customer->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Jatuh Tempo</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white {{ $invoice->due_date?->isPast() && $invoice->status !== 'paid' ? 'text-red-600 dark:text-red-400' : '' }}">
                        {{ $invoice->due_date?->format('d M Y') ?? '-' }}
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $invoice->notes ?? '-' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Rincian Pembayaran</h3>
        </div>
        <div class="p-6">
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                <span class="text-lg font-medium text-gray-900 dark:text-white">Total Tagihan</span>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }}</span>
            </div>
            @php
                $totalPaid = $invoice->payments?->where('status', 'success')->sum('amount') ?? 0;
                $remaining = ($invoice->total ?? 0) - $totalPaid;
            @endphp
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Sudah Dibayar</span>
                    <span class="text-green-600 dark:text-green-400 font-medium">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Sisa Tagihan</span>
                    <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format(max(0, $remaining), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($invoice->payments && $invoice->payments->count() > 0)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Riwayat Pembayaran</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($invoice->payments as $payment)
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->payment_id }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->paid_at?->format('d M Y H:i') }} - {{ ucfirst($payment->payment_method) }}</div>
                    </div>
                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                        + Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Catat Pembayaran</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('tenant.invoices.pay', $invoice) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Pembayaran <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="amount" value="{{ $remaining > 0 ? $remaining : '' }}" min="1" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <select name="payment_method" id="payment_method" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                    <input type="text" name="notes" id="notes"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Opsional">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        Catat Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="flex justify-between">
        <a href="{{ route('tenant.invoices.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
            Kembali
        </a>
        <a href="{{ route('tenant.invoices.pdf', $invoice) }}" target="_blank" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            Download PDF
        </a>
    </div>
</div>
@endsection
