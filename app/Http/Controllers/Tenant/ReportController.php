<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('tenant.reports.index');
    }

    public function sales()
    {
        return view('tenant.reports.sales');
    }

    public function customers()
    {
        return view('tenant.reports.customers');
    }

    public function revenue()
    {
        return view('tenant.reports.revenue');
    }
}
