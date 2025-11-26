<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlatformSettingsController extends Controller
{
    public function index()
    {
        return view('platform.settings.index');
    }

    public function update(Request $request)
    {
        return back()->with('success', 'Settings updated successfully.');
    }
}
