<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Customer;
use App\Models\Tenant\Payment;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.invoices.index', [
                'invoices' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $invoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('tenant.invoices.index', compact('invoices'));
    }

    public function create()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $customers = Customer::where('status', 'active')->get();
        return view('tenant.invoices.create', compact('customers'));
    }

    public function store(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:tenant.customers,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        Invoice::create([
            'customer_id' => $validated['customer_id'],
            'type' => 'manual',
            'subtotal' => $validated['amount'],
            'tax' => 0,
            'discount' => 0,
            'total' => $validated['amount'],
            'status' => 'pending',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => $validated['due_date'],
            'notes' => $validated['description'] ?? null,
        ]);

        return redirect()->route('tenant.invoices.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    public function show($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $invoice = Invoice::with(['customer', 'payments'])->findOrFail($id);
        return view('tenant.invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $invoice = Invoice::findOrFail($id);
        $customers = Customer::where('status', 'active')->get();
        return view('tenant.invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,paid,overdue,cancelled',
        ]);

        $invoice->update([
            'subtotal' => $validated['amount'],
            'total' => $validated['amount'],
            'due_date' => $validated['due_date'],
            'notes' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('tenant.invoices.index')
            ->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->payments()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus invoice yang sudah ada pembayaran.');
        }

        $invoice->delete();

        return redirect()->route('tenant.invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    public function pdf($id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $invoice = Invoice::with(['customer', 'payments'])->findOrFail($id);
        return view('tenant.invoices.pdf', compact('invoice'));
    }

    public function pay(Request $request, $id)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return back()->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,transfer,qris,other',
            'notes' => 'nullable|string|max:255',
        ]);

        Payment::create([
            'payment_id' => 'PAY-' . strtoupper(Str::random(12)),
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_channel' => 'manual',
            'status' => 'success',
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        $totalPaid = $invoice->payments()->where('status', 'success')->sum('amount');
        
        if ($totalPaid >= $invoice->total) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        return back()->with('success', 'Pembayaran berhasil dicatat.');
    }
}
