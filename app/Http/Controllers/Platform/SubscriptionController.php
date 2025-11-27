<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        $subscriptions = TenantSubscription::with(['tenant', 'plan'])->latest()->paginate(15);
        $tenants = Tenant::orderBy('created_at', 'desc')->get();
        return view('platform.subscriptions.index', compact('plans', 'subscriptions', 'tenants'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('platform.subscriptions.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_routers' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'max_vouchers' => 'required|integer|min:1',
            'max_online_users' => 'required|integer|min:1',
            'custom_domain' => 'nullable|boolean',
            'api_access' => 'nullable|boolean',
            'priority_support' => 'nullable|boolean',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['custom_domain'] = $request->has('custom_domain');
        $validated['api_access'] = $request->has('api_access');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        
        $validated['features'] = array_values(array_filter($validated['features'] ?? [], fn($f) => !empty(trim($f))));

        SubscriptionPlan::create($validated);

        return redirect()->route('platform.subscriptions.index')->with('success', 'Paket langganan berhasil dibuat.');
    }

    public function show(SubscriptionPlan $subscription)
    {
        return view('platform.subscriptions.show', compact('subscription'));
    }

    public function edit(SubscriptionPlan $subscription)
    {
        return view('platform.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, SubscriptionPlan $subscription)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('subscription_plans', 'slug')->ignore($subscription->id)],
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_routers' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'max_vouchers' => 'required|integer|min:1',
            'max_online_users' => 'required|integer|min:1',
            'custom_domain' => 'nullable|boolean',
            'api_access' => 'nullable|boolean',
            'priority_support' => 'nullable|boolean',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['custom_domain'] = $request->has('custom_domain');
        $validated['api_access'] = $request->has('api_access');
        $validated['priority_support'] = $request->has('priority_support');
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        
        $validated['features'] = array_values(array_filter($validated['features'] ?? [], fn($f) => !empty(trim($f))));

        $subscription->update($validated);

        return redirect()->route('platform.subscriptions.index')->with('success', 'Paket langganan berhasil diperbarui.');
    }

    public function destroy(SubscriptionPlan $subscription)
    {
        $activeSubscriptions = TenantSubscription::where('plan_id', $subscription->id)
            ->whereIn('status', ['active', 'suspended'])
            ->count();
            
        if ($activeSubscriptions > 0) {
            return redirect()->route('platform.subscriptions.index')
                ->with('error', "Tidak dapat menghapus paket ini karena masih ada {$activeSubscriptions} langganan aktif.");
        }

        $subscription->delete();
        return redirect()->route('platform.subscriptions.index')->with('success', 'Paket langganan berhasil dihapus.');
    }
}
