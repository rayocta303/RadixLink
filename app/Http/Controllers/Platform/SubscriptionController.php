<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        $subscriptions = TenantSubscription::with(['tenant', 'plan'])->latest()->paginate(15);
        return view('platform.subscriptions.index', compact('plans', 'subscriptions'));
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
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_routers' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'max_vouchers' => 'required|integer|min:1',
            'max_online_users' => 'required|integer|min:1',
        ]);

        SubscriptionPlan::create($validated);

        return redirect()->route('platform.subscriptions.index')->with('success', 'Subscription plan created successfully.');
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
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_routers' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'max_vouchers' => 'required|integer|min:1',
            'max_online_users' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $subscription->update($validated);

        return redirect()->route('platform.subscriptions.index')->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscription)
    {
        $subscription->delete();
        return redirect()->route('platform.subscriptions.index')->with('success', 'Subscription plan deleted successfully.');
    }
}
