<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServicePlanController extends Controller
{
    public function index()
    {
        return view('tenant.services.index');
    }

    public function create()
    {
        return view('tenant.services.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('tenant.services.index')->with('success', 'Service plan created successfully.');
    }

    public function show($service)
    {
        return view('tenant.services.show');
    }

    public function edit($service)
    {
        return view('tenant.services.edit');
    }

    public function update(Request $request, $service)
    {
        return redirect()->route('tenant.services.index')->with('success', 'Service plan updated successfully.');
    }

    public function destroy($service)
    {
        return redirect()->route('tenant.services.index')->with('success', 'Service plan deleted successfully.');
    }
}
