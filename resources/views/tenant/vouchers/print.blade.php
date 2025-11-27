<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Voucher - Batch {{ $batch }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #fff; }
        .voucher-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; padding: 10px; }
        .voucher-card {
            border: 2px dashed #333;
            padding: 15px;
            text-align: center;
            background: #f9f9f9;
            break-inside: avoid;
        }
        .voucher-code {
            font-size: 18px;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 2px;
            margin: 10px 0;
            padding: 8px;
            background: #fff;
            border: 1px solid #ddd;
        }
        .voucher-plan {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .voucher-details {
            font-size: 11px;
            color: #666;
            margin: 5px 0;
        }
        .voucher-credentials {
            font-size: 10px;
            color: #999;
            margin-top: 8px;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
        .voucher-price {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
        }
        @media print {
            .no-print { display: none !important; }
            .voucher-grid { gap: 5px; padding: 5px; }
            .voucher-card { border-width: 1px; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 20px; background: #f0f0f0; margin-bottom: 20px;">
        <h2 style="margin-bottom: 10px;">Batch: {{ $batch }}</h2>
        <p style="margin-bottom: 10px;">Total: {{ $vouchers->count() }} voucher</p>
        <button onclick="window.print()" style="padding: 10px 20px; background: #3b82f6; color: #fff; border: none; cursor: pointer; font-size: 16px;">
            Print Vouchers
        </button>
        <a href="{{ route('tenant.vouchers.index') }}" style="margin-left: 10px; color: #666;">Kembali</a>
    </div>

    <div class="voucher-grid">
        @foreach($vouchers as $voucher)
        <div class="voucher-card">
            <div class="voucher-plan">{{ $voucher->servicePlan->name ?? 'Unknown Plan' }}</div>
            <div class="voucher-code">{{ $voucher->code }}</div>
            <div class="voucher-details">
                Durasi: {{ $voucher->servicePlan->validity_text ?? '-' }}
            </div>
            <div class="voucher-price">
                Rp {{ number_format($voucher->price ?? 0, 0, ',', '.') }}
            </div>
            <div class="voucher-credentials">
                User: {{ $voucher->username }} | Pass: {{ $voucher->password }}
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
