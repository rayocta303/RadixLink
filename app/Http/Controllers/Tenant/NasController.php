<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NasController extends Controller
{
    public function index()
    {
        return view('tenant.nas.index');
    }

    public function create()
    {
        return view('tenant.nas.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('tenant.nas.index')->with('success', 'NAS created successfully.');
    }

    public function show($nas)
    {
        return view('tenant.nas.show');
    }

    public function edit($nas)
    {
        return view('tenant.nas.edit');
    }

    public function update(Request $request, $nas)
    {
        return redirect()->route('tenant.nas.index')->with('success', 'NAS updated successfully.');
    }

    public function destroy($nas)
    {
        return redirect()->route('tenant.nas.index')->with('success', 'NAS deleted successfully.');
    }

    public function test($nas)
    {
        return back()->with('success', 'NAS connection test successful.');
    }
}
