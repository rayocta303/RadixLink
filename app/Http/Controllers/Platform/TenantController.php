<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
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
            'subscription_plan' => 'required|string',
        ]);

        $tenant = Tenant::create([
            'id' => $validated['subdomain'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_name' => $validated['company_name'],
            'subdomain' => $validated['subdomain'],
            'phone' => $validated['phone'] ?? null,
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_expires_at' => now()->addMonth(),
            'is_active' => true,
        ]);

        return redirect()->route('platform.tenants.index')->with('success', 'Tenant created successfully.');
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
        $tenant->delete();
        return redirect()->route('platform.tenants.index')->with('success', 'Tenant deleted successfully.');
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
