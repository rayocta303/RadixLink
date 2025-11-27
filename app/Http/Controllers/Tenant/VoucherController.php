<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Voucher;
use App\Models\Tenant\ServicePlan;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.vouchers.index', [
                'vouchers' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $vouchers = Voucher::with('servicePlan')->orderBy('created_at', 'desc')->paginate(20);
        return view('tenant.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $servicePlans = ServicePlan::where('is_active', true)->get();
        return view('tenant.vouchers.create', compact('servicePlans'));
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'service_plan_id' => 'required|exists:tenant.service_plans,id',
            'quantity' => 'required|integer|min:1|max:500',
            'type' => 'required|in:single,multi',
            'max_usage' => 'required_if:type,multi|integer|min:1',
            'prefix' => 'nullable|string|max:10',
            'code_length' => 'required|integer|min:4|max:16',
        ]);

        $servicePlan = ServicePlan::find($validated['service_plan_id']);
        $batchId = 'BATCH-' . strtoupper(Str::random(8));
        $quantity = $validated['quantity'];
        $prefix = $validated['prefix'] ?? '';
        $codeLength = $validated['code_length'];

        $vouchers = [];
        for ($i = 0; $i < $quantity; $i++) {
            $code = $prefix . Voucher::generateCode($codeLength - strlen($prefix));
            $vouchers[] = [
                'code' => $code,
                'username' => 'v' . Str::random(6),
                'password' => Str::random(8),
                'service_plan_id' => $validated['service_plan_id'],
                'status' => 'unused',
                'type' => $validated['type'],
                'max_usage' => $validated['type'] === 'multi' ? $validated['max_usage'] : 1,
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
            'service_plan_id' => 'required|exists:tenant.service_plans,id',
        ]);

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

        $servicePlans = ServicePlan::where('is_active', true)->get();
        return view('tenant.vouchers.generate', compact('servicePlans'));
    }

    public function generate(Request $request)
    {
        return $this->store($request);
    }

    public function print($batch)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.vouchers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $vouchers = Voucher::with('servicePlan')
            ->where('batch_id', $batch)
            ->get();

        return view('tenant.vouchers.print', compact('vouchers', 'batch'));
    }
}
