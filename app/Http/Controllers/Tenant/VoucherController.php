<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        return view('tenant.vouchers.index');
    }

    public function create()
    {
        return view('tenant.vouchers.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('tenant.vouchers.index')->with('success', 'Voucher created successfully.');
    }

    public function show($voucher)
    {
        return view('tenant.vouchers.show');
    }

    public function edit($voucher)
    {
        return view('tenant.vouchers.edit');
    }

    public function update(Request $request, $voucher)
    {
        return redirect()->route('tenant.vouchers.index')->with('success', 'Voucher updated successfully.');
    }

    public function destroy($voucher)
    {
        return redirect()->route('tenant.vouchers.index')->with('success', 'Voucher deleted successfully.');
    }

    public function showGenerate()
    {
        return view('tenant.vouchers.generate');
    }

    public function generate(Request $request)
    {
        return redirect()->route('tenant.vouchers.index')->with('success', 'Vouchers generated successfully.');
    }

    public function print($batch)
    {
        return view('tenant.vouchers.print');
    }
}
