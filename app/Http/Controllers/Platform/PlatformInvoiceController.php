<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PlatformInvoice;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PlatformInvoiceController extends Controller
{
    public function index()
    {
        $invoices = PlatformInvoice::with('tenant')->latest()->paginate(15);
        return view('platform.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $tenants = Tenant::where('is_active', true)->get();
        return view('platform.invoices.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['total'] = $validated['subtotal'] + ($validated['tax'] ?? 0) - ($validated['discount'] ?? 0);
        $validated['issue_date'] = now();

        PlatformInvoice::create($validated);

        return redirect()->route('platform.invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show(PlatformInvoice $invoice)
    {
        return view('platform.invoices.show', compact('invoice'));
    }

    public function edit(PlatformInvoice $invoice)
    {
        $tenants = Tenant::where('is_active', true)->get();
        return view('platform.invoices.edit', compact('invoice', 'tenants'));
    }

    public function update(Request $request, PlatformInvoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,pending,paid,overdue,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'paid' && $invoice->status !== 'paid') {
            $validated['paid_at'] = now();
        }

        $invoice->update($validated);

        return redirect()->route('platform.invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy(PlatformInvoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('platform.invoices.index')->with('success', 'Invoice deleted successfully.');
    }
}
