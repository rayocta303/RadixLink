<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 40px;
            background: #fff;
            border: 1px solid #e5e7eb;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .company-info h1 {
            font-size: 24px;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        .company-info p {
            color: #6b7280;
            font-size: 12px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            font-size: 28px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-title .invoice-number {
            font-size: 14px;
            color: #6b7280;
            margin-top: 5px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            width: 48%;
        }
        .info-box h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .info-box p {
            margin-bottom: 5px;
        }
        .info-box .label {
            color: #6b7280;
            font-size: 12px;
        }
        .info-box .value {
            color: #1f2937;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; }
        .status-draft { background: #dbeafe; color: #1e40af; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead {
            background: #f9fafb;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        td {
            color: #1f2937;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals-row.total {
            border-bottom: none;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 700;
        }
        .totals-row .label {
            color: #6b7280;
        }
        .totals-row .value {
            color: #1f2937;
            font-weight: 600;
        }
        .totals-row.total .value {
            color: #3b82f6;
        }
        .payment-history {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .payment-history h3 {
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 15px;
        }
        .payment-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            background: #f9fafb;
            border-radius: 6px;
            margin-bottom: 8px;
        }
        .payment-item .info {
            font-size: 13px;
        }
        .payment-item .amount {
            color: #059669;
            font-weight: 600;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .notes h4 {
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 8px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .invoice-container {
                border: none;
                margin: 0;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .print-button:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Cetak Invoice</button>
    
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1>{{ config('app.name', 'ISP Manager') }}</h1>
                <p>Internet Service Provider Management System</p>
            </div>
            <div class="invoice-title">
                <h2>Invoice</h2>
                <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h3>Tagihan Untuk</h3>
                <p class="value">{{ $invoice->customer->name ?? '-' }}</p>
                <p><span class="label">Username:</span> {{ $invoice->customer->username ?? '-' }}</p>
                <p><span class="label">Email:</span> {{ $invoice->customer->email ?? '-' }}</p>
                <p><span class="label">Telepon:</span> {{ $invoice->customer->phone ?? '-' }}</p>
            </div>
            <div class="info-box">
                <h3>Detail Invoice</h3>
                <p><span class="label">Tanggal:</span> <span class="value">{{ $invoice->issue_date?->format('d M Y') ?? $invoice->created_at?->format('d M Y') }}</span></p>
                <p><span class="label">Jatuh Tempo:</span> <span class="value">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</span></p>
                <p><span class="label">Status:</span> 
                    @php
                        $statusClass = match($invoice->status) {
                            'paid' => 'status-paid',
                            'pending' => 'status-pending',
                            'overdue' => 'status-overdue',
                            'cancelled' => 'status-cancelled',
                            'draft' => 'status-draft',
                            default => 'status-pending'
                        };
                        $statusLabel = match($invoice->status) {
                            'paid' => 'Lunas',
                            'pending' => 'Belum Dibayar',
                            'overdue' => 'Jatuh Tempo',
                            'cancelled' => 'Dibatalkan',
                            'draft' => 'Draft',
                            default => ucfirst($invoice->status)
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </p>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->items && is_array($invoice->items))
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item['description'] ?? $item['name'] ?? 'Item' }}</td>
                        <td class="text-right">Rp {{ number_format($item['amount'] ?? $item['price'] ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td>
                            {{ $invoice->servicePlan->name ?? 'Layanan Internet' }}
                            @if($invoice->notes)
                                <br><small style="color: #6b7280;">{{ $invoice->notes }}</small>
                            @endif
                        </td>
                        <td class="text-right">Rp {{ number_format($invoice->subtotal ?? $invoice->total ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        <div class="totals">
            <div class="totals-row">
                <span class="label">Subtotal</span>
                <span class="value">Rp {{ number_format($invoice->subtotal ?? $invoice->total ?? 0, 0, ',', '.') }}</span>
            </div>
            @if(($invoice->tax ?? 0) > 0)
            <div class="totals-row">
                <span class="label">Pajak</span>
                <span class="value">Rp {{ number_format($invoice->tax ?? 0, 0, ',', '.') }}</span>
            </div>
            @endif
            @if(($invoice->discount ?? 0) > 0)
            <div class="totals-row">
                <span class="label">Diskon</span>
                <span class="value">- Rp {{ number_format($invoice->discount ?? 0, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="totals-row total">
                <span class="label">Total</span>
                <span class="value">Rp {{ number_format($invoice->total ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
        
        @if($invoice->payments && $invoice->payments->count() > 0)
        <div class="payment-history">
            <h3>Riwayat Pembayaran</h3>
            @foreach($invoice->payments->where('status', 'success') as $payment)
            <div class="payment-item">
                <div class="info">
                    <strong>{{ $payment->payment_id }}</strong>
                    <br>
                    <small>{{ $payment->paid_at?->format('d M Y H:i') }} - {{ ucfirst($payment->payment_method) }}</small>
                </div>
                <div class="amount">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
        @endif
        
        @if($invoice->notes && !$invoice->items)
        <div class="notes">
            <h4>Catatan</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif
        
        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda.</p>
            <p>Dokumen ini dicetak secara otomatis oleh sistem {{ config('app.name', 'ISP Manager') }}</p>
        </div>
    </div>
</body>
</html>
