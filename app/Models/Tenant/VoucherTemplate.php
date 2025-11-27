<?php

namespace App\Models\Tenant;

class VoucherTemplate extends TenantModel
{
    protected $fillable = [
        'name',
        'description',
        'html_template',
        'css_styles',
        'paper_size',
        'orientation',
        'vouchers_per_page',
        'columns_per_row',
        'show_qr_code',
        'show_logo',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'show_qr_code' => 'boolean',
        'show_logo' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'vouchers_per_page' => 'integer',
        'columns_per_row' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function renderHtml($voucher)
    {
        $html = $this->html_template;
        
        $replacements = [
            '{{code}}' => $voucher->code ?? '',
            '{{username}}' => $voucher->username ?? '',
            '{{password}}' => $voucher->password ?? '',
            '{{price}}' => 'Rp ' . number_format($voucher->price ?? 0, 0, ',', '.'),
            '{{price_raw}}' => $voucher->price ?? 0,
            '{{plan_name}}' => $voucher->servicePlan->name ?? 'Paket',
            '{{plan_speed}}' => $voucher->servicePlan->bandwidth_text ?? '',
            '{{plan_duration}}' => $voucher->servicePlan->validity_text ?? '',
            '{{batch_id}}' => $voucher->batch_id ?? '',
            '{{created_date}}' => $voucher->created_at?->format('d/m/Y') ?? '',
            '{{qr_code}}' => $this->show_qr_code ? '<div class="qr-code"><img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data=' . urlencode($voucher->code) . '" alt="QR"></div>' : '',
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    public static function getDefaultTemplates()
    {
        return [
            [
                'name' => 'Classic',
                'description' => 'Template klasik dengan layout sederhana',
                'html_template' => '<div class="voucher-classic">
    <div class="header">HOTSPOT VOUCHER</div>
    <div class="plan">{{plan_name}}</div>
    {{qr_code}}
    <div class="code">{{code}}</div>
    <div class="credentials">
        <span>User: {{username}}</span>
        <span>Pass: {{password}}</span>
    </div>
    <div class="price">{{price}}</div>
    <div class="duration">Durasi: {{plan_duration}}</div>
</div>',
                'css_styles' => '.voucher-classic {
    text-align: center;
    padding: 10px;
}
.voucher-classic .header {
    font-size: 12px;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 5px;
}
.voucher-classic .plan {
    font-size: 11px;
    color: #4b5563;
    margin-bottom: 8px;
}
.voucher-classic .qr-code {
    margin: 8px auto;
}
.voucher-classic .qr-code img {
    width: 60px;
    height: 60px;
}
.voucher-classic .code {
    font-family: monospace;
    font-size: 16px;
    font-weight: bold;
    color: #1f2937;
    padding: 6px;
    background: #f3f4f6;
    border-radius: 4px;
    margin-bottom: 6px;
    letter-spacing: 1px;
}
.voucher-classic .credentials {
    display: flex;
    justify-content: center;
    gap: 15px;
    font-size: 10px;
    color: #6b7280;
    margin-bottom: 6px;
}
.voucher-classic .price {
    font-size: 14px;
    font-weight: bold;
    color: #059669;
}
.voucher-classic .duration {
    font-size: 9px;
    color: #9ca3af;
    margin-top: 5px;
}',
                'paper_size' => 1,
                'orientation' => 'portrait',
                'vouchers_per_page' => 12,
                'columns_per_row' => 3,
                'show_qr_code' => true,
                'show_logo' => false,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Modern',
                'description' => 'Template modern dengan gradien dan shadow',
                'html_template' => '<div class="voucher-modern">
    <div class="header-bar"></div>
    <div class="content">
        <div class="plan-badge">{{plan_name}}</div>
        <div class="code-section">
            {{qr_code}}
            <div class="code">{{code}}</div>
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">User</span>
                <span class="value">{{username}}</span>
            </div>
            <div class="info-item">
                <span class="label">Pass</span>
                <span class="value">{{password}}</span>
            </div>
        </div>
        <div class="footer">
            <span class="price">{{price}}</span>
            <span class="duration">{{plan_duration}}</span>
        </div>
    </div>
</div>',
                'css_styles' => '.voucher-modern {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.voucher-modern .header-bar {
    height: 6px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
}
.voucher-modern .content {
    padding: 12px;
    text-align: center;
}
.voucher-modern .plan-badge {
    display: inline-block;
    background: #e0e7ff;
    color: #4338ca;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    margin-bottom: 8px;
}
.voucher-modern .code-section {
    margin-bottom: 8px;
}
.voucher-modern .qr-code {
    margin-bottom: 6px;
}
.voucher-modern .qr-code img {
    width: 50px;
    height: 50px;
}
.voucher-modern .code {
    font-family: monospace;
    font-size: 15px;
    font-weight: bold;
    color: #1e293b;
    letter-spacing: 2px;
}
.voucher-modern .info-grid {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 8px;
}
.voucher-modern .info-item {
    text-align: center;
}
.voucher-modern .info-item .label {
    display: block;
    font-size: 8px;
    color: #94a3b8;
    text-transform: uppercase;
}
.voucher-modern .info-item .value {
    font-size: 10px;
    color: #334155;
    font-weight: 500;
}
.voucher-modern .footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
}
.voucher-modern .price {
    font-size: 14px;
    font-weight: bold;
    color: #059669;
}
.voucher-modern .duration {
    font-size: 9px;
    color: #64748b;
}',
                'paper_size' => 1,
                'orientation' => 'portrait',
                'vouchers_per_page' => 12,
                'columns_per_row' => 3,
                'show_qr_code' => true,
                'show_logo' => false,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Minimalist',
                'description' => 'Template minimalis dengan fokus pada kode voucher',
                'html_template' => '<div class="voucher-minimal">
    <div class="main-code">{{code}}</div>
    <div class="divider"></div>
    <div class="details">
        <span>{{plan_name}} | {{plan_duration}}</span>
    </div>
    <div class="credentials">
        <span>{{username}} / {{password}}</span>
    </div>
    <div class="price">{{price}}</div>
</div>',
                'css_styles' => '.voucher-minimal {
    text-align: center;
    padding: 15px 10px;
}
.voucher-minimal .main-code {
    font-family: monospace;
    font-size: 20px;
    font-weight: bold;
    color: #0f172a;
    letter-spacing: 3px;
    margin-bottom: 8px;
}
.voucher-minimal .divider {
    width: 40px;
    height: 2px;
    background: #3b82f6;
    margin: 0 auto 8px;
}
.voucher-minimal .details {
    font-size: 10px;
    color: #64748b;
    margin-bottom: 4px;
}
.voucher-minimal .credentials {
    font-size: 9px;
    color: #94a3b8;
    margin-bottom: 6px;
}
.voucher-minimal .price {
    font-size: 14px;
    font-weight: bold;
    color: #059669;
}',
                'paper_size' => 1,
                'orientation' => 'portrait',
                'vouchers_per_page' => 16,
                'columns_per_row' => 4,
                'show_qr_code' => false,
                'show_logo' => false,
                'is_default' => false,
                'is_active' => true,
            ],
        ];
    }
}
