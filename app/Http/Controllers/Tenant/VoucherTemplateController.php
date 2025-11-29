<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\VoucherTemplate;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class VoucherTemplateController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.voucher-templates.index', [
                'templates' => new LengthAwarePaginator([], 0, 20),
                'dbError' => 'Database tenant belum dikonfigurasi. Silakan hubungi administrator.',
            ]);
        }

        $templates = VoucherTemplate::orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('tenant.voucher-templates.index', compact('templates'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $paperSizes = $this->getPaperSizes();
        $defaultTemplate = $this->getDefaultHtmlTemplate();
        $defaultCss = $this->getDefaultCssStyles();
        
        return view('tenant.voucher-templates.create', compact('paperSizes', 'defaultTemplate', 'defaultCss'));
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_template' => 'required|string',
            'css_styles' => 'nullable|string',
            'paper_size' => 'required|integer|in:1,2,3,4',
            'orientation' => 'required|in:portrait,landscape',
            'vouchers_per_page' => 'required|integer|min:1|max:20',
            'show_qr_code' => 'boolean',
            'show_logo' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['show_qr_code'] = $request->boolean('show_qr_code');
        $validated['show_logo'] = $request->boolean('show_logo');
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_default']) {
            VoucherTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        VoucherTemplate::create($validated);

        return redirect()->route('tenant.voucher-templates.index')
            ->with('success', 'Template voucher berhasil dibuat.');
    }

    public function show($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $template = VoucherTemplate::findOrFail($id);
        return view('tenant.voucher-templates.show', compact('template'));
    }

    public function edit($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $template = VoucherTemplate::findOrFail($id);
        $paperSizes = $this->getPaperSizes();
        
        return view('tenant.voucher-templates.edit', compact('template', 'paperSizes'));
    }

    public function update(Request $request, $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $template = VoucherTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_template' => 'required|string',
            'css_styles' => 'nullable|string',
            'paper_size' => 'required|integer|in:1,2,3,4',
            'orientation' => 'required|in:portrait,landscape',
            'vouchers_per_page' => 'required|integer|min:1|max:20',
            'show_qr_code' => 'boolean',
            'show_logo' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['show_qr_code'] = $request->boolean('show_qr_code');
        $validated['show_logo'] = $request->boolean('show_logo');
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_default'] && !$template->is_default) {
            VoucherTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        $template->update($validated);

        return redirect()->route('tenant.voucher-templates.index')
            ->with('success', 'Template voucher berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $template = VoucherTemplate::findOrFail($id);
        
        if ($template->is_default) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Template default tidak dapat dihapus.');
        }

        $template->delete();

        return redirect()->route('tenant.voucher-templates.index')
            ->with('success', 'Template voucher berhasil dihapus.');
    }

    public function setDefault($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        VoucherTemplate::where('is_default', true)->update(['is_default' => false]);
        VoucherTemplate::where('id', $id)->update(['is_default' => true]);

        return redirect()->route('tenant.voucher-templates.index')
            ->with('success', 'Template default berhasil diubah.');
    }

    public function preview(Request $request)
    {
        $html = $request->input('html_template', $this->getDefaultHtmlTemplate());
        $css = $request->input('css_styles', $this->getDefaultCssStyles());
        
        $sampleVoucher = (object) [
            'code' => 'WIFI-ABC12345',
            'username' => 'v123456',
            'password' => 'pass1234',
            'price' => 10000,
            'servicePlan' => (object) [
                'name' => 'Paket Harian',
                'validity_text' => '24 Jam',
                'bandwidth_up' => 5,
                'bandwidth_down' => 10,
            ],
        ];

        return view('tenant.voucher-templates.preview', compact('html', 'css', 'sampleVoucher'));
    }

    public function duplicate($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.voucher-templates.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $template = VoucherTemplate::findOrFail($id);
        
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->save();

        return redirect()->route('tenant.voucher-templates.edit', $newTemplate->id)
            ->with('success', 'Template berhasil diduplikasi.');
    }

    private function getPaperSizes(): array
    {
        return [
            1 => 'A4',
            2 => 'A5',
            3 => 'Letter',
            4 => 'Custom (80mm thermal)',
        ];
    }

    private function getDefaultHtmlTemplate(): string
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

    private function getDefaultCssStyles(): string
    {
        return <<<'CSS'
.voucher-card {
    border: 2px dashed #333;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    max-width: 300px;
    margin: 5px;
    break-inside: avoid;
    page-break-inside: avoid;
}
.voucher-header {
    margin-bottom: 10px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}
.voucher-logo {
    max-height: 40px;
    margin-bottom: 5px;
}
.voucher-title {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin: 0;
}
.plan-name {
    font-size: 16px;
    font-weight: bold;
    color: #2563eb;
    margin-bottom: 10px;
}
.voucher-code {
    font-size: 20px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
    letter-spacing: 3px;
    background: #fff;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0;
    border: 1px solid #ddd;
}
.voucher-info {
    text-align: left;
    margin: 10px 0;
    font-size: 12px;
}
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 3px 0;
    border-bottom: 1px dotted #ccc;
}
.label {
    color: #666;
}
.value {
    font-weight: 600;
    font-family: monospace;
}
.voucher-price {
    font-size: 18px;
    font-weight: bold;
    color: #16a34a;
    margin: 10px 0;
}
.voucher-qr {
    margin: 10px 0;
}
.voucher-qr img {
    max-width: 80px;
    height: auto;
}
.voucher-footer {
    font-size: 10px;
    color: #666;
    margin-top: 10px;
    border-top: 1px solid #ddd;
    padding-top: 10px;
}
@media print {
    .voucher-card {
        border-width: 1px;
        box-shadow: none;
    }
}
CSS;
    }
}
