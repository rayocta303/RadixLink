<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('tenant.customers.index');
    }

    public function create()
    {
        return view('tenant.customers.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('tenant.customers.index')->with('success', 'Customer created successfully.');
    }

    public function show($customer)
    {
        return view('tenant.customers.show');
    }

    public function edit($customer)
    {
        return view('tenant.customers.edit');
    }

    public function update(Request $request, $customer)
    {
        return redirect()->route('tenant.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($customer)
    {
        return redirect()->route('tenant.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function suspend($customer)
    {
        return back()->with('success', 'Customer suspended successfully.');
    }

    public function activate($customer)
    {
        return back()->with('success', 'Customer activated successfully.');
    }
}
