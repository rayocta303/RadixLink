@extends('layouts.app')

@section('title', 'Router Script Generator')
@section('page-title', 'Generator Script Router')

@section('content')
@if(isset($dbError))
<div class="mb-6 rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Perhatian</h3>
            <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">{{ $dbError }}</p>
        </div>
    </div>
</div>
@endif

<div class="space-y-6">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Generate Script MikroTik RouterOS</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat script konfigurasi untuk router MikroTik dengan integrasi RADIUS/FreeRADIUS</p>
        </div>
        <form action="{{ route('tenant.router-scripts.generate') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="nas_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Router <span class="text-red-500">*</span></label>
                    <select name="nas_id" id="nas_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">-- Pilih Router --</option>
                        @foreach($routers ?? [] as $router)
                            <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->nasname }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="script_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Script <span class="text-red-500">*</span></label>
                    <select name="script_type" id="script_type" required onchange="toggleOptions()"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="full">Full Configuration (Semua)</option>
                        <option value="radius">RADIUS Server Only</option>
                        <option value="pppoe">PPPoE Server Only</option>
                        <option value="hotspot">Hotspot Server Only</option>
                        <option value="firewall">Firewall & NAT Rules</option>
                        <option value="profiles">Service Plan Profiles</option>
                    </select>
                </div>
            </div>

            <div id="radiusOptions" class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg space-y-4">
                <h4 class="font-medium text-gray-900 dark:text-white">Pengaturan RADIUS</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="radius_server" class="block text-sm font-medium text-gray-700 dark:text-gray-300">RADIUS Server IP</label>
                        <input type="text" name="radius_server" id="radius_server" placeholder="Contoh: 192.168.1.100"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="dns_servers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">DNS Servers</label>
                        <input type="text" name="dns_servers" id="dns_servers" placeholder="Contoh: 8.8.8.8,8.8.4.4"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div id="pppoeOptions" class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg space-y-4">
                <h4 class="font-medium text-gray-900 dark:text-white">Pengaturan PPPoE</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="pppoe_interface" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interface PPPoE</label>
                        <input type="text" name="pppoe_interface" id="pppoe_interface" placeholder="Contoh: ether1"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="pool_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Pool Range</label>
                        <input type="text" name="pool_range" id="pool_range" placeholder="Contoh: 10.10.0.2-10.10.255.254"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div id="hotspotOptions" class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg space-y-4">
                <h4 class="font-medium text-gray-900 dark:text-white">Pengaturan Hotspot</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="hotspot_interface" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Interface Hotspot</label>
                        <input type="text" name="hotspot_interface" id="hotspot_interface" placeholder="Contoh: ether2"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="hotspot_network" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hotspot Network</label>
                        <input type="text" name="hotspot_network" id="hotspot_network" placeholder="Contoh: 192.168.100.0/24"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div id="firewallOptions" class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg space-y-4">
                <h4 class="font-medium text-gray-900 dark:text-white">Pengaturan Firewall</h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="wan_interface" class="block text-sm font-medium text-gray-700 dark:text-gray-300">WAN Interface</label>
                        <input type="text" name="wan_interface" id="wan_interface" placeholder="Contoh: ether1-WAN"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                    </svg>
                    Generate Script
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Generate Client RADIUS (FreeRADIUS)</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Buat konfigurasi client untuk ditambahkan ke file clients.conf FreeRADIUS</p>
        </div>
        <form action="{{ route('tenant.router-scripts.generate-nas-client') }}" method="POST" class="p-6">
            @csrf
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <label for="nas_id_client" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Router</label>
                    <select name="nas_id" id="nas_id_client" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">-- Pilih Router --</option>
                        @foreach($routers ?? [] as $router)
                            <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->nasname }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Generate Client Config
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Panduan Integrasi FreeRADIUS</h3>
        </div>
        <div class="p-6 prose dark:prose-invert max-w-none">
            <h4>1. Instalasi FreeRADIUS</h4>
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto"><code>sudo apt update
sudo apt install freeradius freeradius-mysql freeradius-utils</code></pre>

            <h4>2. Konfigurasi MySQL/MariaDB</h4>
            <p>Edit file <code>/etc/freeradius/3.0/mods-available/sql</code> dan sesuaikan koneksi database tenant.</p>

            <h4>3. Enable SQL Module</h4>
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto"><code>sudo ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/
sudo chown -h freerad:freerad /etc/freeradius/3.0/mods-enabled/sql</code></pre>

            <h4>4. Konfigurasi Site</h4>
            <p>Edit <code>/etc/freeradius/3.0/sites-available/default</code>:</p>
            <ul>
                <li>Uncomment <code>sql</code> di section authorize{}</li>
                <li>Uncomment <code>sql</code> di section accounting{}</li>
                <li>Uncomment <code>sql</code> di section session{}</li>
            </ul>

            <h4>5. Restart FreeRADIUS</h4>
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto"><code>sudo systemctl restart freeradius
sudo systemctl enable freeradius</code></pre>

            <h4>6. Test Koneksi</h4>
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto"><code>radtest username password localhost 0 testing123</code></pre>
        </div>
    </div>
</div>

<script>
function toggleOptions() {
    const scriptType = document.getElementById('script_type').value;
    const radiusOpts = document.getElementById('radiusOptions');
    const pppoeOpts = document.getElementById('pppoeOptions');
    const hotspotOpts = document.getElementById('hotspotOptions');
    const firewallOpts = document.getElementById('firewallOptions');
    
    radiusOpts.classList.toggle('hidden', !['full', 'radius'].includes(scriptType));
    pppoeOpts.classList.toggle('hidden', !['full', 'pppoe'].includes(scriptType));
    hotspotOpts.classList.toggle('hidden', !['full', 'hotspot'].includes(scriptType));
    firewallOpts.classList.toggle('hidden', !['full', 'firewall'].includes(scriptType));
}

document.addEventListener('DOMContentLoaded', toggleOptions);
</script>
@endsection
