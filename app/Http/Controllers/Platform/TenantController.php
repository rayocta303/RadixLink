<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    protected TenantProvisioningService $provisioningService;

    public function __construct(TenantProvisioningService $provisioningService)
    {
        $this->provisioningService = $provisioningService;
    }

    public function index()
    {
        $tenants = Tenant::with('subscription')->latest()->paginate(15);
        return view('platform.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('platform.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'company_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:63|alpha_dash|unique:tenants,subdomain',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'subscription_plan' => 'required|string',
            'password' => 'nullable|string|min:8',
        ]);

        try {
            $tenant = $this->provisioningService->provision($validated);

            return redirect()->route('platform.tenants.index')
                ->with('success', "Tenant {$tenant->company_name} berhasil dibuat dengan database terpisah.");
        } catch (\Exception $e) {
            Log::error("Failed to create tenant: " . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Gagal membuat tenant: ' . $e->getMessage());
        }
    }

    public function show(Tenant $tenant)
    {
        return view('platform.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('platform.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id . ',id',
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'subscription_plan' => 'required|string',
            'max_routers' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'max_vouchers' => 'required|integer|min:1',
        ]);

        $tenant->update($validated);

        return redirect()->route('platform.tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        try {
            $this->provisioningService->deprovision($tenant);
            $tenant->delete();
            return redirect()->route('platform.tenants.index')
                ->with('success', 'Tenant dan database berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Failed to delete tenant: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus tenant: ' . $e->getMessage());
        }
    }

    public function suspend(Tenant $tenant, Request $request)
    {
        $tenant->update([
            'is_suspended' => true,
            'suspend_reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'Tenant suspended successfully.');
    }

    public function activate(Tenant $tenant)
    {
        $tenant->update([
            'is_suspended' => false,
            'suspend_reason' => null,
        ]);

        return back()->with('success', 'Tenant activated successfully.');
    }
}
