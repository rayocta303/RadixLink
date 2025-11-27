@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoice Platform')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Invoice tagihan langganan tenant.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('platform.invoices.create') }}" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Buat Invoice
        </a>
    </div>
</div>

<div class="mt-6 sm:mt-8 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="p-4 sm:p-6 overflow-x-auto">
        <table id="invoicesTable" class="w-full stripe hover">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Tenant</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Jatuh Tempo</th>
                    <th class="no-sort">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td>
                        <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</span>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                                <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ strtoupper(substr($invoice->tenant->company_name ?? 'N', 0, 2)) }}</span>
                            </div>
                            <span>{{ $invoice->tenant->company_name ?? '-' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'overdue' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $statusColor = $statusColors[$invoice->status] ?? $statusColors['pending'];
                            $statusLabels = [
                                'paid' => 'Lunas',
                                'pending' => 'Pending',
                                'overdue' => 'Jatuh Tempo',
                                'cancelled' => 'Dibatalkan',
                            ];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                            {{ $statusLabels[$invoice->status] ?? ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td data-order="{{ $invoice->due_date?->timestamp ?? 0 }}">
                        @if($invoice->due_date)
                            <span class="{{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-600 dark:text-red-400 font-medium' : '' }}">
                                {{ $invoice->due_date->format('d M Y') }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('platform.invoices.show', $invoice) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300" title="Lihat">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <a href="{{ route('platform.invoices.download', $invoice) }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300" title="Download PDF">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#invoicesTable').DataTable({
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ],
        order: [[4, 'desc']]
    });
});
</script>
@endpush
