<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantSettingsController extends Controller
{
    public function index()
    {
        return view('tenant.settings.index');
    }

    public function update(Request $request)
    {
        return back()->with('success', 'Settings updated successfully.');
    }
}
