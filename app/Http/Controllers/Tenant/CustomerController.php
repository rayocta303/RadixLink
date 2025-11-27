<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Customer;
use App\Models\Tenant\ServicePlan;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.customers.index', [
                'customers' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $customers = Customer::with('servicePlan')->orderBy('name')->paginate(15);
        return view('tenant.customers.index', compact('customers'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.customers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $servicePlans = ServicePlan::where('is_active', true)->get();
        return view('tenant.customers.create', compact('servicePlans'));
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.customers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'username' => 'required|string|max:64|unique:tenant.customers,username',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'service_plan_id' => 'required|exists:tenant.service_plans,id',
            'service_type' => 'required|in:hotspot,pppoe,dhcp,hybrid',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';
        $validated['registered_at'] = now();
        
        $servicePlan = ServicePlan::find($validated['service_plan_id']);
        if ($servicePlan) {
            $validated['expires_at'] = now()->addDays($servicePlan->validity);
        }

        Customer::create($validated);

        return redirect()->route('tenant.customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.customers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $customer = Customer::with(['servicePlan', 'invoices', 'payments'])->findOrFail($id);
        return view('tenant.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.customers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $customer = Customer::findOrFail($id);
        $servicePlans = ServicePlan::where('is_active', true)->get();
        return view('tenant.customers.edit', compact('customer', 'servicePlans'));
    }

    public function update(Request $request, $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.customers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'service_plan_id' => 'required|exists:tenant.service_plans,id',
            'service_type' => 'required|in:hotspot,pppoe,dhcp,hybrid',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        return redirect()->route('tenant.customers.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.customers.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('tenant.customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function suspend($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return back()->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $customer = Customer::findOrFail($id);
        $customer->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);

        return back()->with('success', 'Pelanggan berhasil disuspend.');
    }

    public function activate($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return back()->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $customer = Customer::findOrFail($id);
        $customer->update([
            'status' => 'active',
            'suspended_at' => null,
            'suspend_reason' => null,
        ]);

        return back()->with('success', 'Pelanggan berhasil diaktifkan.');
    }
}
