<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Template Voucher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .preview-header {
            background: #fff;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .preview-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        {{ $css }}
    </style>
</head>
<body>
    <div class="preview-header">
        <h2>Preview Template Voucher</h2>
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            Print Preview
        </button>
    </div>
    <div class="preview-content">
        @php
            $renderedHtml = $html;
            $variables = [
                'company_name' => 'WiFi Hotspot',
                'plan_name' => $sampleVoucher->servicePlan->name,
                'voucher_code' => $sampleVoucher->code,
                'username' => $sampleVoucher->username,
                'password' => $sampleVoucher->password,
                'validity' => $sampleVoucher->servicePlan->validity_text,
                'bandwidth' => ($sampleVoucher->servicePlan->bandwidth_down ?? 10) . ' Mbps / ' . ($sampleVoucher->servicePlan->bandwidth_up ?? 5) . ' Mbps',
                'price' => number_format($sampleVoucher->price, 0, ',', '.'),
                'logo_url' => 'https://via.placeholder.com/100x40?text=LOGO',
                'qr_code' => 'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' . urlencode($sampleVoucher->code),
                'footer_text' => 'Terima kasih telah menggunakan layanan kami',
            ];
            
            foreach ($variables as $key => $value) {
                $renderedHtml = str_replace('{' . $key . '}', $value, $renderedHtml);
            }
            
            $renderedHtml = preg_replace('/\{if_logo\}(.*?)\{\/if_logo\}/s', '$1', $renderedHtml);
            $renderedHtml = preg_replace('/\{if_qrcode\}(.*?)\{\/if_qrcode\}/s', '$1', $renderedHtml);
        @endphp
        
        {!! $renderedHtml !!}
    </div>
</body>
</html>
