<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantVoucherTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Template Standar',
                'description' => 'Template voucher standar dengan layout sederhana',
                'html_template' => $this->getStandardTemplate(),
                'css_styles' => $this->getStandardCss(),
                'paper_size' => 1,
                'orientation' => 'portrait',
                'vouchers_per_page' => 6,
                'show_qr_code' => true,
                'show_logo' => true,
                'is_default' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Template Compact',
                'description' => 'Template compact untuk cetak banyak voucher',
                'html_template' => $this->getCompactTemplate(),
                'css_styles' => $this->getCompactCss(),
                'paper_size' => 1,
                'orientation' => 'portrait',
                'vouchers_per_page' => 12,
                'show_qr_code' => false,
                'show_logo' => false,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Template Thermal 80mm',
                'description' => 'Template untuk printer thermal 80mm',
                'html_template' => $this->getThermalTemplate(),
                'css_styles' => $this->getThermalCss(),
                'paper_size' => 4,
                'orientation' => 'portrait',
                'vouchers_per_page' => 1,
                'show_qr_code' => true,
                'show_logo' => true,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Template Premium',
                'description' => 'Template voucher dengan desain premium',
                'html_template' => $this->getPremiumTemplate(),
                'css_styles' => $this->getPremiumCss(),
                'paper_size' => 1,
                'orientation' => 'portrait',
                'vouchers_per_page' => 4,
                'show_qr_code' => true,
                'show_logo' => true,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('voucher_templates')->updateOrInsert(
                ['name' => $template['name']],
                $template
            );
        }
    }

    private function getStandardTemplate(): string
    {
        return <<<'HTML'
<div class="voucher-card">
    <div class="voucher-header">
        {if_logo}<img src="{logo_url}" class="voucher-logo" alt="Logo">{/if_logo}
        <h3 class="voucher-title">{company_name}</h3>
    </div>
    <div class="voucher-body">
        <div class="plan-name">{plan_name}</div>
        <div class="voucher-code">{voucher_code}</div>
        <div class="voucher-info">
            <div class="info-row">
                <span class="label">Username:</span>
                <span class="value">{username}</span>
            </div>
            <div class="info-row">
                <span class="label">Password:</span>
                <span class="value">{password}</span>
            </div>
            <div class="info-row">
                <span class="label">Durasi:</span>
                <span class="value">{validity}</span>
            </div>
            <div class="info-row">
                <span class="label">Bandwidth:</span>
                <span class="value">{bandwidth}</span>
            </div>
        </div>
        <div class="voucher-price">Rp {price}</div>
    </div>
    {if_qrcode}
    <div class="voucher-qr">
        <img src="{qr_code}" alt="QR Code">
    </div>
    {/if_qrcode}
    <div class="voucher-footer">
        <p>{footer_text}</p>
    </div>
</div>
HTML;
    }

    private function getStandardCss(): string
    {
        return <<<'CSS'
.voucher-card {
    border: 2px dashed #333;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
    width: 280px;
    margin: 8px;
    break-inside: avoid;
    page-break-inside: avoid;
    float: left;
}
.voucher-header {
    margin-bottom: 10px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 8px;
}
.voucher-logo {
    max-height: 35px;
    margin-bottom: 5px;
}
.voucher-title {
    font-size: 13px;
    font-weight: bold;
    color: #333;
    margin: 0;
}
.plan-name {
    font-size: 15px;
    font-weight: bold;
    color: #2563eb;
    margin-bottom: 8px;
}
.voucher-code {
    font-size: 18px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    letter-spacing: 2px;
    background: #fff;
    padding: 8px;
    border-radius: 4px;
    margin: 8px 0;
    border: 1px solid #ddd;
}
.voucher-info {
    text-align: left;
    margin: 8px 0;
    font-size: 11px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 2px 0;
    border-bottom: 1px dotted #ccc;
}
.label { color: #666; }
.value { font-weight: 600; font-family: monospace; }
.voucher-price {
    font-size: 16px;
    font-weight: bold;
    color: #16a34a;
    margin: 8px 0;
}
.voucher-qr { margin: 8px 0; }
.voucher-qr img { max-width: 70px; height: auto; }
.voucher-footer {
    font-size: 9px;
    color: #666;
    margin-top: 8px;
    border-top: 1px solid #ddd;
    padding-top: 6px;
}
@media print {
    .voucher-card { border-width: 1px; box-shadow: none; }
}
CSS;
    }

    private function getCompactTemplate(): string
    {
        return <<<'HTML'
<div class="voucher-compact">
    <div class="voucher-left">
        <div class="plan">{plan_name}</div>
        <div class="code">{voucher_code}</div>
    </div>
    <div class="voucher-right">
        <div class="credentials">
            <span>U: {username}</span>
            <span>P: {password}</span>
        </div>
        <div class="price">Rp {price}</div>
    </div>
</div>
HTML;
    }

    private function getCompactCss(): string
    {
        return <<<'CSS'
.voucher-compact {
    border: 1px dashed #666;
    padding: 8px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9f9f9;
    width: 320px;
    margin: 4px;
    float: left;
    break-inside: avoid;
}
.voucher-left {
    flex: 1;
}
.plan {
    font-size: 11px;
    font-weight: bold;
    color: #333;
}
.code {
    font-size: 14px;
    font-weight: bold;
    font-family: monospace;
    letter-spacing: 1px;
    color: #2563eb;
}
.voucher-right {
    text-align: right;
}
.credentials {
    font-size: 9px;
    color: #666;
    font-family: monospace;
}
.credentials span {
    display: block;
}
.price {
    font-size: 12px;
    font-weight: bold;
    color: #16a34a;
    margin-top: 2px;
}
CSS;
    }

    private function getThermalTemplate(): string
    {
        return <<<'HTML'
<div class="thermal-voucher">
    {if_logo}<div class="logo"><img src="{logo_url}" alt="Logo"></div>{/if_logo}
    <div class="company">{company_name}</div>
    <div class="divider">========================</div>
    <div class="plan">{plan_name}</div>
    <div class="code">{voucher_code}</div>
    <div class="divider">------------------------</div>
    <div class="info-line">
        <span>Username:</span>
        <span>{username}</span>
    </div>
    <div class="info-line">
        <span>Password:</span>
        <span>{password}</span>
    </div>
    <div class="info-line">
        <span>Durasi:</span>
        <span>{validity}</span>
    </div>
    <div class="info-line">
        <span>Bandwidth:</span>
        <span>{bandwidth}</span>
    </div>
    <div class="divider">------------------------</div>
    <div class="price">Rp {price}</div>
    {if_qrcode}
    <div class="qr"><img src="{qr_code}" alt="QR"></div>
    {/if_qrcode}
    <div class="divider">========================</div>
    <div class="footer">{footer_text}</div>
</div>
HTML;
    }

    private function getThermalCss(): string
    {
        return <<<'CSS'
.thermal-voucher {
    font-family: 'Courier New', monospace;
    width: 280px;
    padding: 10px;
    text-align: center;
    background: #fff;
}
.logo img { max-height: 40px; }
.company { font-size: 14px; font-weight: bold; margin: 5px 0; }
.divider { font-size: 10px; color: #666; margin: 5px 0; }
.plan { font-size: 12px; font-weight: bold; margin: 5px 0; }
.code {
    font-size: 20px;
    font-weight: bold;
    letter-spacing: 2px;
    padding: 8px;
    background: #f0f0f0;
    margin: 8px 0;
}
.info-line {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    padding: 2px 0;
}
.price {
    font-size: 18px;
    font-weight: bold;
    margin: 10px 0;
}
.qr img { max-width: 80px; }
.footer { font-size: 9px; color: #666; margin-top: 5px; }
@page { size: 80mm auto; margin: 0; }
CSS;
    }

    private function getPremiumTemplate(): string
    {
        return <<<'HTML'
<div class="premium-voucher">
    <div class="premium-header">
        {if_logo}<img src="{logo_url}" class="premium-logo" alt="Logo">{/if_logo}
        <div class="premium-company">{company_name}</div>
        <div class="premium-tagline">Internet Cepat & Handal</div>
    </div>
    <div class="premium-body">
        <div class="premium-plan">{plan_name}</div>
        <div class="premium-code-wrapper">
            <div class="premium-code-label">Kode Voucher</div>
            <div class="premium-code">{voucher_code}</div>
        </div>
        <div class="premium-details">
            <div class="premium-detail-item">
                <div class="detail-icon">&#128100;</div>
                <div class="detail-content">
                    <div class="detail-label">Username</div>
                    <div class="detail-value">{username}</div>
                </div>
            </div>
            <div class="premium-detail-item">
                <div class="detail-icon">&#128274;</div>
                <div class="detail-content">
                    <div class="detail-label">Password</div>
                    <div class="detail-value">{password}</div>
                </div>
            </div>
            <div class="premium-detail-item">
                <div class="detail-icon">&#128337;</div>
                <div class="detail-content">
                    <div class="detail-label">Durasi</div>
                    <div class="detail-value">{validity}</div>
                </div>
            </div>
            <div class="premium-detail-item">
                <div class="detail-icon">&#128640;</div>
                <div class="detail-content">
                    <div class="detail-label">Kecepatan</div>
                    <div class="detail-value">{bandwidth}</div>
                </div>
            </div>
        </div>
        <div class="premium-price">Rp {price}</div>
    </div>
    {if_qrcode}
    <div class="premium-qr">
        <img src="{qr_code}" alt="QR Code">
        <div class="qr-label">Scan untuk aktivasi</div>
    </div>
    {/if_qrcode}
    <div class="premium-footer">
        <p>{footer_text}</p>
    </div>
</div>
HTML;
    }

    private function getPremiumCss(): string
    {
        return <<<'CSS'
.premium-voucher {
    border: 3px solid #2563eb;
    border-radius: 12px;
    width: 320px;
    margin: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    float: left;
    break-inside: avoid;
}
.premium-header {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    color: #fff;
    padding: 15px;
    text-align: center;
}
.premium-logo { max-height: 40px; margin-bottom: 5px; filter: brightness(0) invert(1); }
.premium-company { font-size: 16px; font-weight: bold; }
.premium-tagline { font-size: 10px; opacity: 0.8; }
.premium-body { padding: 15px; }
.premium-plan {
    font-size: 16px;
    font-weight: bold;
    color: #2563eb;
    text-align: center;
    margin-bottom: 12px;
}
.premium-code-wrapper {
    background: #f0f7ff;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
    margin-bottom: 12px;
}
.premium-code-label { font-size: 10px; color: #666; text-transform: uppercase; }
.premium-code {
    font-size: 22px;
    font-weight: bold;
    font-family: monospace;
    letter-spacing: 3px;
    color: #1d4ed8;
}
.premium-details { margin: 12px 0; }
.premium-detail-item {
    display: flex;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid #eee;
}
.detail-icon { font-size: 16px; width: 30px; text-align: center; }
.detail-content { flex: 1; }
.detail-label { font-size: 9px; color: #666; text-transform: uppercase; }
.detail-value { font-size: 12px; font-weight: 600; font-family: monospace; }
.premium-price {
    font-size: 22px;
    font-weight: bold;
    color: #16a34a;
    text-align: center;
    margin: 12px 0;
}
.premium-qr {
    text-align: center;
    padding: 10px;
    background: #f9f9f9;
}
.premium-qr img { max-width: 80px; }
.qr-label { font-size: 9px; color: #666; margin-top: 3px; }
.premium-footer {
    background: #333;
    color: #fff;
    padding: 8px;
    text-align: center;
    font-size: 9px;
}
@media print {
    .premium-voucher { box-shadow: none; border-width: 2px; }
}
CSS;
    }
}
