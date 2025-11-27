@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filter Laporan</h3>
            <form class="flex flex-wrap gap-4">
                <input type="date" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                <input type="date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
                <button type="submit" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Filter</button>
                <button type="button" onclick="window.print()" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">Cetak</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Penjualan</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalSales ?? 2500000, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Voucher Terjual</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $voucherSold ?? 156 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pelanggan Baru</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $newCustomers ?? 23 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                            <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185zM9.75 9h.008v.008H9.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 4.5h.008v.008h-.008V13.5zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Transaksi</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalTransactions ?? 189 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Riwayat Penjualan</h3>
            <table id="salesTable" class="w-full stripe hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>ID Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Tipe</th>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $dummySales = [
                        ['date' => '2024-01-15 10:30', 'id' => 'TRX-001', 'customer' => 'Andi Pratama', 'type' => 'Voucher', 'item' => 'Paket 10 GB', 'amount' => 25000, 'status' => 'success'],
                        ['date' => '2024-01-15 11:45', 'id' => 'TRX-002', 'customer' => 'Budi Setiawan', 'type' => 'Langganan', 'item' => 'Paket Bulanan 50 Mbps', 'amount' => 350000, 'status' => 'success'],
                        ['date' => '2024-01-15 14:20', 'id' => 'TRX-003', 'customer' => 'Dewi Anggraini', 'type' => 'Voucher', 'item' => 'Paket 5 GB', 'amount' => 15000, 'status' => 'success'],
                        ['date' => '2024-01-15 15:00', 'id' => 'TRX-004', 'customer' => 'Eko Prasetyo', 'type' => 'Langganan', 'item' => 'Paket Bulanan 20 Mbps', 'amount' => 200000, 'status' => 'pending'],
                        ['date' => '2024-01-14 09:15', 'id' => 'TRX-005', 'customer' => 'Fitri Handayani', 'type' => 'Voucher', 'item' => 'Paket 20 GB', 'amount' => 45000, 'status' => 'success'],
                    ];
                    @endphp
                    @foreach($dummySales as $sale)
                    <tr>
                        <td>{{ $sale['date'] }}</td>
                        <td><span class="font-mono text-sm">{{ $sale['id'] }}</span></td>
                        <td>{{ $sale['customer'] }}</td>
                        <td>{{ $sale['type'] }}</td>
                        <td>{{ $sale['item'] }}</td>
                        <td class="text-green-600 dark:text-green-400 font-semibold">Rp {{ number_format($sale['amount'], 0, ',', '.') }}</td>
                        <td>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $sale['status'] === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ ucfirst($sale['status']) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#salesTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endpush
