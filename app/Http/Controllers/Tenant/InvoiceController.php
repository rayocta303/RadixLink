<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('tenant.invoices.index');
    }

    public function create()
    {
        return view('tenant.invoices.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('tenant.invoices.index')->with('success', 'Invoice created successfully.');
    }

    public function show($invoice)
    {
        return view('tenant.invoices.show');
    }

    public function edit($invoice)
    {
        return view('tenant.invoices.edit');
    }

    public function update(Request $request, $invoice)
    {
        return redirect()->route('tenant.invoices.index')->with('success', 'Invoice updated successfully.');
    }

    public function destroy($invoice)
    {
        return redirect()->route('tenant.invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function pdf($invoice)
    {
        return response()->json(['message' => 'PDF generation']);
    }
}
