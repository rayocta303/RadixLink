<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Voucher - {{ $batch }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
        }

        .print-controls {
            background: #1f2937;
            color: white;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        @media print {
            .print-controls {
                display: none;
            }
            body {
                background: white;
            }
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .control-group label {
            font-size: 14px;
            color: #9ca3af;
        }

        .control-group select, .control-group input[type="number"] {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #374151;
            background: #374151;
            color: white;
            font-size: 14px;
        }

        .control-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-secondary {
            background: #4b5563;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .voucher-container {
            padding: 20px;
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(var(--cols, 3), 1fr);
        }

        @media print {
            .voucher-container {
                padding: 5mm;
                gap: 3mm;
            }
        }

        .voucher-card {
            background: white;
            border: 1px dashed #9ca3af;
            padding: 12px;
            page-break-inside: avoid;
            break-inside: avoid;
            text-align: center;
        }

        @if($selectedTemplate ?? null)
        {!! $selectedTemplate->css_styles !!}
        @else
        .voucher-header {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .voucher-plan {
            font-size: 11px;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .voucher-code {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            padding: 6px;
            background: #f3f4f6;
            border-radius: 4px;
            margin-bottom: 6px;
            letter-spacing: 1px;
        }

        .voucher-credentials {
            display: flex;
            justify-content: center;
            gap: 15px;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .voucher-price {
            font-size: 13px;
            font-weight: bold;
            color: #059669;
        }

        .voucher-footer {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 5px;
        }

        .qr-code {
            width: 60px;
            height: 60px;
            margin: 6px auto;
        }

        .qr-code img {
            width: 100%;
            height: 100%;
        }
        @endif

        @media print {
            @page {
                size: A4;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <div class="control-group">
            <a href="{{ route('tenant.vouchers.index') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <span style="color: #9ca3af;">{{ $batch }} - {{ $vouchers->count() }} voucher</span>
        </div>
        <div class="control-group">
            @if(isset($templates) && $templates->count() > 0)
            <label>Template:</label>
            <select id="template-select" onchange="changeTemplate(this.value)">
                @foreach($templates as $template)
                <option value="{{ $template->id }}" {{ (isset($selectedTemplate) && $selectedTemplate && $selectedTemplate->id == $template->id) ? 'selected' : '' }}>
                    {{ $template->name }}
                </option>
                @endforeach
            </select>
            @endif
            <label>Kolom:</label>
            <input type="number" id="cols-input" value="3" min="1" max="6" style="width: 60px;" onchange="changeCols(this.value)">
            <label>QR:</label>
            <input type="checkbox" id="show-qr" checked onchange="toggleQR()">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <div class="voucher-container" id="voucher-container">
        @foreach($vouchers as $voucher)
        <div class="voucher-card">
            @if(isset($selectedTemplate) && $selectedTemplate)
                {!! $selectedTemplate->renderHtml($voucher) !!}
            @else
                <div class="voucher-header">Hotspot Voucher</div>
                <div class="voucher-plan">{{ $voucher->servicePlan->name ?? 'Paket' }}</div>
                
                <div class="qr-code" data-qr>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode($voucher->code) }}" alt="QR">
                </div>
                
                <div class="voucher-code">{{ $voucher->code }}</div>
                
                <div class="voucher-credentials">
                    <span>User: {{ $voucher->username }}</span>
                    <span>Pass: {{ $voucher->password }}</span>
                </div>
                
                <div class="voucher-price">Rp {{ number_format($voucher->price ?? 0, 0, ',', '.') }}</div>
                
                <div class="voucher-footer">
                    Durasi: {{ $voucher->servicePlan->validity_text ?? '-' }}
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <script>
        function changeTemplate(templateId) {
            const url = new URL(window.location.href);
            url.searchParams.set('template_id', templateId);
            window.location.href = url.toString();
        }

        function changeCols(cols) {
            document.getElementById('voucher-container').style.setProperty('--cols', cols);
        }

        function toggleQR() {
            const showQR = document.getElementById('show-qr').checked;
            document.querySelectorAll('[data-qr]').forEach(el => {
                el.style.display = showQR ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
