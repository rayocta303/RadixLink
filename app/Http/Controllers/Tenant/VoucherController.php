<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\VoucherBatch;
use App\Models\Tenant\VoucherTemplate;
use App\Models\Tenant\ServicePlan;
use App\Services\TenantDatabaseManager;
use App\Services\TenantUsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    protected TenantUsageService $usageService;

    public function __construct(TenantUsageService $usageService)
    {
        $this->usageService = $usageService;
    }

    public function index(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.vouchers.index', [
                'vouchers' => collect(),
                'stats' => ['total' => 0, 'unused' => 0, 'used' => 0, 'expired' => 0],
                'batches' => collect(),
                'servicePlans' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $query = Voucher::with('servicePlan')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service_plan_id')) {
            $query->where('service_plan_id', $request->service_plan_id);
        }
        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $vouchers = $query->paginate(50)->withQueryString();
        
        $stats = [
            'total' => Voucher::count(),
            'unused' => Voucher::where('status', 'unused')->count(),
            'used' => Voucher::where('status', 'used')->count(),
            'expired' => Voucher::where('status', 'expired')->count(),
        ];

        $batches = Voucher::select('batch_id')
            ->distinct()
            ->whereNotNull('batch_id')
            ->orderBy('batch_id', 'desc')
            ->pluck('batch_id');

        $servicePlans = ServicePlan::where('is_active', true)->get();
        
        return view('tenant.vouchers.index', compact('vouchers', 'stats', 'batches', 'servicePlans'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $tenant = TenantDatabaseManager::getTenant();
        $remaining = $tenant ? $this->usageService->getRemainingVouchers($tenant) : 50;

        if ($remaining <= 0) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Anda telah mencapai batas maksimum voucher pada paket Anda. Silakan upgrade paket untuk menambah kapasitas.');
        }

        $servicePlans = ServicePlan::where('is_active', true)->get();
        return view('tenant.vouchers.create', compact('servicePlans', 'remaining'));
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $tenant = TenantDatabaseManager::getTenant();
        $quantity = (int) $request->input('quantity', 1);
        
        if ($tenant) {
            $remaining = $this->usageService->getRemainingVouchers($tenant);
            
            if (!$this->usageService->canAddVoucher($tenant, $quantity)) {
                $errorMessage = $remaining > 0
                    ? "Anda hanya dapat membuat {$remaining} voucher lagi pada paket Anda."
                    : 'Anda telah mencapai batas maksimum voucher pada paket Anda.';
                $errorMessage .= ' Silakan upgrade paket untuk menambah kapasitas.';
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'remaining' => $remaining,
                        'upgrade_url' => route('tenant.settings.subscription'),
                    ], 403);
                }
                
                return redirect()->route('tenant.vouchers.index')
                    ->with('error', $errorMessage);
            }
        }

        $validated = $request->validate([
            'service_plan_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:500',
            'type' => 'required|in:single,multi',
            'max_usage' => 'required_if:type,multi|integer|min:2|max:100',
            'prefix' => 'nullable|string|max:10',
            'code_length' => 'required|integer|min:4|max:16',
            'code_type' => 'nullable|in:alphanumeric,numeric,alpha',
        ]);

        $servicePlan = ServicePlan::find($validated['service_plan_id']);
        if (!$servicePlan) {
            return redirect()->route('tenant.vouchers.create')
                ->with('error', 'Paket layanan tidak ditemukan.')
                ->withInput();
        }

        $batchId = 'BATCH-' . strtoupper(Str::random(8));
        $quantity = $validated['quantity'];
        $prefix = $validated['prefix'] ?? '';
        $codeLength = $validated['code_length'];
        $codeType = $validated['code_type'] ?? 'alphanumeric';
        
        $prefixLength = strlen($prefix);
        if ($prefixLength >= $codeLength) {
            return redirect()->route('tenant.vouchers.create')
                ->with('error', 'Prefix terlalu panjang. Maksimal ' . ($codeLength - 1) . ' karakter untuk panjang kode ' . $codeLength . '.')
                ->withInput();
        }
        
        $randomLength = $codeLength - $prefixLength;

        $vouchers = [];
        for ($i = 0; $i < $quantity; $i++) {
            $code = Voucher::generateCode($randomLength, $codeType, $prefix);
            $vouchers[] = [
                'code' => $code,
                'username' => 'v' . Str::random(6),
                'password' => Str::random(8),
                'service_plan_id' => $validated['service_plan_id'],
                'status' => 'unused',
                'type' => $validated['type'],
                'max_usage' => $validated['type'] === 'multi' ? (int)$validated['max_usage'] : 1,
                'used_count' => 0,
                'price' => $servicePlan->price,
                'batch_id' => $batchId,
                'generated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Voucher::insert($vouchers);

        return redirect()->route('tenant.vouchers.index')
            ->with('success', "{$quantity} voucher berhasil dibuat dengan batch: {$batchId}");
    }

    public function show($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $voucher = Voucher::with(['servicePlan', 'customer'])->findOrFail($id);
        return view('tenant.vouchers.show', compact('voucher'));
    }

    public function edit($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $voucher = Voucher::findOrFail($id);
        $servicePlans = ServicePlan::where('is_active', true)->get();
        return view('tenant.vouchers.edit', compact('voucher', 'servicePlans'));
    }

    public function update(Request $request, $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $voucher = Voucher::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:unused,used,expired,disabled',
            'service_plan_id' => 'required|integer',
        ]);

        $servicePlan = ServicePlan::find($validated['service_plan_id']);
        if (!$servicePlan) {
            return redirect()->route('tenant.vouchers.edit', $id)
                ->with('error', 'Paket layanan tidak ditemukan.')
                ->withInput();
        }

        $voucher->update($validated);

        return redirect()->route('tenant.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return redirect()->route('tenant.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }

    public function showGenerate()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $tenant = TenantDatabaseManager::getTenant();
        $remaining = $tenant ? $this->usageService->getRemainingVouchers($tenant) : 50;

        if ($remaining <= 0) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Anda telah mencapai batas maksimum voucher pada paket Anda. Silakan upgrade paket untuk menambah kapasitas.');
        }

        $servicePlans = ServicePlan::where('is_active', true)->get();
        return view('tenant.vouchers.generate', compact('servicePlans', 'remaining'));
    }

    public function generate(Request $request)
    {
        return $this->store($request);
    }

    public function print(Request $request, $batch)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $vouchers = Voucher::with('servicePlan')
            ->where('batch_id', $batch)
            ->get();

        $templates = VoucherTemplate::active()->orderBy('is_default', 'desc')->get();
        $selectedTemplate = null;
        
        if ($request->filled('template_id')) {
            $selectedTemplate = VoucherTemplate::find($request->template_id);
        }
        
        if (!$selectedTemplate) {
            $selectedTemplate = VoucherTemplate::where('is_default', true)->first();
        }

        return view('tenant.vouchers.print', compact('vouchers', 'batch', 'templates', 'selectedTemplate'));
    }

    public function printSelected(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $vouchers = Voucher::with('servicePlan')
            ->whereIn('id', $ids)
            ->get();

        if ($vouchers->isEmpty()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Tidak ada voucher yang dipilih.');
        }

        $templates = VoucherTemplate::active()->orderBy('is_default', 'desc')->get();
        $selectedTemplate = null;
        
        if ($request->filled('template_id')) {
            $selectedTemplate = VoucherTemplate::find($request->template_id);
        }
        
        if (!$selectedTemplate) {
            $selectedTemplate = VoucherTemplate::where('is_default', true)->first();
        }

        $batch = 'SELECTED-' . count($ids);

        return view('tenant.vouchers.print', compact('vouchers', 'batch', 'templates', 'selectedTemplate'));
    }

    public function bulkDelete(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return response()->json([
                'success' => false,
                'message' => 'Database tenant belum dikonfigurasi.'
            ], 400);
        }

        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada voucher yang dipilih.'
            ], 400);
        }

        $deleted = Voucher::whereIn('id', $ids)
            ->where('status', 'unused')
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deleted} voucher berhasil dihapus.",
            'deleted_count' => $deleted
        ]);
    }

    public function batches()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.vouchers.batches', [
                'batches' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $batches = Voucher::select('batch_id', DB::raw('COUNT(*) as total'), 
                DB::raw("SUM(CASE WHEN status = 'unused' THEN 1 ELSE 0 END) as unused"),
                DB::raw("SUM(CASE WHEN status = 'used' THEN 1 ELSE 0 END) as used"),
                DB::raw('MIN(created_at) as created_at'))
            ->whereNotNull('batch_id')
            ->groupBy('batch_id')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('tenant.vouchers.batches', compact('batches'));
    }
}
