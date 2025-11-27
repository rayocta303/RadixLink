<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Nas;
use App\Models\Tenant\Customer;
use App\Models\Tenant\ServicePlan;
use App\Services\RouterScriptService;
use App\Services\TenantDatabaseManager;
use Illuminate\Http\Request;

class RouterScriptController extends Controller
{
    protected RouterScriptService $scriptService;

    public function __construct(RouterScriptService $scriptService)
    {
        $this->scriptService = $scriptService;
    }

    public function index()
    {
        if (!TenantDatabaseManager::isConnected()) {
            return view('tenant.router-scripts.index', [
                'routers' => collect(),
                'dbError' => 'Database tenant belum dikonfigurasi.',
            ]);
        }

        $routers = Nas::where('is_active', true)->orderBy('name')->get();
        return view('tenant.router-scripts.index', compact('routers'));
    }

    public function generate(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return back()->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'nas_id' => 'required|exists:tenant.nas,id',
            'script_type' => 'required|in:full,radius,pppoe,hotspot,firewall,profiles',
            'radius_server' => 'nullable|ip',
            'pppoe_interface' => 'nullable|string|max:50',
            'hotspot_interface' => 'nullable|string|max:50',
            'pool_range' => 'nullable|string|max:100',
            'hotspot_network' => 'nullable|string|max:50',
            'dns_servers' => 'nullable|string|max:100',
            'wan_interface' => 'nullable|string|max:50',
        ]);

        $nas = Nas::findOrFail($validated['nas_id']);
        
        $options = array_filter([
            'radius_server' => $validated['radius_server'] ?? null,
            'pppoe_interface' => $validated['pppoe_interface'] ?? null,
            'hotspot_interface' => $validated['hotspot_interface'] ?? null,
            'pool_range' => $validated['pool_range'] ?? null,
            'hotspot_network' => $validated['hotspot_network'] ?? null,
            'dns_servers' => $validated['dns_servers'] ?? null,
            'wan_interface' => $validated['wan_interface'] ?? null,
        ]);

        $script = match($validated['script_type']) {
            'full' => $this->scriptService->generateFullScript($nas, $options),
            'radius' => $this->scriptService->generateRadiusConfig($nas, $options),
            'pppoe' => $this->scriptService->generatePPPoEServerConfig($nas, $options),
            'hotspot' => $this->scriptService->generateHotspotConfig($nas, $options),
            'firewall' => $this->scriptService->generateFirewallRules($options),
            'profiles' => $this->scriptService->generateProfilesConfig($options),
            default => '',
        };

        return view('tenant.router-scripts.result', [
            'script' => $script,
            'nas' => $nas,
            'scriptType' => $validated['script_type'],
        ]);
    }

    public function generateCustomerScript(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return back()->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:tenant.customers,id',
        ]);

        $customers = Customer::with('servicePlan')
            ->whereIn('id', $validated['customer_ids'])
            ->get();

        $script = $this->scriptService->generateBulkCustomerScript($customers->all());

        return view('tenant.router-scripts.result', [
            'script' => $script,
            'scriptType' => 'customers',
            'customerCount' => $customers->count(),
        ]);
    }

    public function generateNasClient(Request $request)
    {
        if (!TenantDatabaseManager::isConnected()) {
            return back()->with('error', 'Database tenant belum dikonfigurasi.');
        }

        $validated = $request->validate([
            'nas_id' => 'required|exists:tenant.nas,id',
        ]);

        $nas = Nas::findOrFail($validated['nas_id']);
        $script = $this->scriptService->generateNasRegistration($nas);

        return view('tenant.router-scripts.result', [
            'script' => $script,
            'nas' => $nas,
            'scriptType' => 'radius-client',
        ]);
    }

    public function download(Request $request)
    {
        $script = $request->input('script', '');
        $filename = $request->input('filename', 'router-script.rsc');

        return response($script)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
