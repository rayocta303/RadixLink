@extends('layouts.app')

@section('title', 'Tambah NAS')
@section('page-title', 'Tambah NAS / Router Baru')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('tenant.nas.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Dasar</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama NAS <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Router Kantor Pusat">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="shortname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Short Name <span class="text-red-500">*</span></label>
                        <input type="text" name="shortname" id="shortname" value="{{ old('shortname') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="router-pusat">
                        @error('shortname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="nasname" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IP Address / Hostname <span class="text-red-500">*</span></label>
                        <input type="text" name="nasname" id="nasname" value="{{ old('nasname') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="192.168.1.1 atau router.domain.com">
                        @error('nasname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Perangkat <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="mikrotik" {{ old('type') == 'mikrotik' ? 'selected' : '' }}>MikroTik</option>
                            <option value="unifi" {{ old('type') == 'unifi' ? 'selected' : '' }}>UniFi</option>
                            <option value="openwrt" {{ old('type') == 'openwrt' ? 'selected' : '' }}>OpenWRT</option>
                            <option value="cisco" {{ old('type') == 'cisco' ? 'selected' : '' }}>Cisco</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="enabled" {{ old('status', 'enabled') == 'enabled' ? 'selected' : '' }}>Enabled</option>
                            <option value="disabled" {{ old('status') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="location_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lokasi</label>
                        <input type="text" name="location_name" id="location_name" value="{{ old('location_name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Gedung A Lantai 2">
                        @error('location_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" id="description" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        placeholder="Catatan tambahan tentang router ini">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Konfigurasi RADIUS</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300">RADIUS Secret <span class="text-red-500">*</span></label>
                        <input type="password" name="secret" id="secret" value="{{ old('secret') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Shared secret untuk RADIUS">
                        @error('secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ports" class="block text-sm font-medium text-gray-700 dark:text-gray-300">RADIUS Ports</label>
                        <input type="number" name="ports" id="ports" value="{{ old('ports') }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="1812">
                        @error('ports')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Konfigurasi API (Opsional)</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Untuk akses ke router via API (MikroTik RouterOS API)</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="api_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Username</label>
                        <input type="text" name="api_username" id="api_username" value="{{ old('api_username') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="admin">
                    </div>
                    <div>
                        <label for="api_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Password</label>
                        <input type="password" name="api_password" id="api_password" value="{{ old('api_password') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="api_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Port</label>
                        <input type="number" name="api_port" id="api_port" value="{{ old('api_port', 8728) }}" min="1" max="65535"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="8728">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="winbox_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">WinBox Port</label>
                        <input type="number" name="winbox_port" id="winbox_port" value="{{ old('winbox_port', 8291) }}" min="1" max="65535"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="8291">
                        @error('winbox_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center pt-6">
                        <input type="checkbox" name="use_ssl" id="use_ssl" value="1" {{ old('use_ssl') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="use_ssl" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Gunakan SSL untuk API
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Lokasi & Koordinat</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Untuk menampilkan posisi router di peta</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude</label>
                        <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="-6.200000">
                        @error('latitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude</label>
                        <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="106.816666">
                        @error('longitude')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="coverage" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coverage (meter)</label>
                        <input type="number" name="coverage" id="coverage" value="{{ old('coverage') }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="500">
                        @error('coverage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div id="mapPicker" class="h-64 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700"></div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Klik pada peta untuk menentukan lokasi, atau masukkan koordinat secara manual.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">VPN RADIUS Tunnel</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aktifkan jika router tidak memiliki IP Publik (menggunakan CGNAT/NAT)</p>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="vpn_enabled" id="vpn_enabled" value="1" {{ old('vpn_enabled') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                            onchange="toggleVpnSettings()">
                        <label for="vpn_enabled" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Aktifkan VPN
                        </label>
                    </div>
                </div>
            </div>
            <div id="vpnSettings" class="p-6 space-y-4 {{ old('vpn_enabled') ? '' : 'hidden' }}">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                VPN Tunnel digunakan untuk menghubungkan Router ke RADIUS Server cloud ketika router tidak memiliki IP Publik, menggunakan CGNAT, atau port RADIUS (1812/1813) terblokir oleh ISP.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="vpn_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe VPN</label>
                        <select name="vpn_type" id="vpn_type"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">-- Pilih Tipe VPN --</option>
                            <option value="l2tp" {{ old('vpn_type') == 'l2tp' ? 'selected' : '' }}>L2TP/IPSec</option>
                            <option value="pptp" {{ old('vpn_type') == 'pptp' ? 'selected' : '' }}>PPTP</option>
                            <option value="sstp" {{ old('vpn_type') == 'sstp' ? 'selected' : '' }}>SSTP</option>
                            <option value="ovpn" {{ old('vpn_type') == 'ovpn' ? 'selected' : '' }}>OpenVPN</option>
                            <option value="wireguard" {{ old('vpn_type') == 'wireguard' ? 'selected' : '' }}>WireGuard</option>
                        </select>
                        @error('vpn_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vpn_server" class="block text-sm font-medium text-gray-700 dark:text-gray-300">VPN Server</label>
                        <input type="text" name="vpn_server" id="vpn_server" value="{{ old('vpn_server') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="vpn.radius-server.com">
                        @error('vpn_server')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="vpn_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">VPN Username</label>
                        <input type="text" name="vpn_username" id="vpn_username" value="{{ old('vpn_username') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('vpn_username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vpn_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">VPN Password</label>
                        <input type="password" name="vpn_password" id="vpn_password" value="{{ old('vpn_password') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        @error('vpn_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vpn_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300">VPN Secret (IPSec)</label>
                        <input type="password" name="vpn_secret" id="vpn_secret" value="{{ old('vpn_secret') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Untuk L2TP/IPSec">
                        @error('vpn_secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="vpn_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300">VPN Port</label>
                        <input type="number" name="vpn_port" id="vpn_port" value="{{ old('vpn_port', 1701) }}" min="1" max="65535"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="1701">
                        @error('vpn_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vpn_local_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Local Address</label>
                        <input type="text" name="vpn_local_address" id="vpn_local_address" value="{{ old('vpn_local_address') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="10.10.10.2">
                        @error('vpn_local_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vpn_remote_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remote Address</label>
                        <input type="text" name="vpn_remote_address" id="vpn_remote_address" value="{{ old('vpn_remote_address') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="10.10.10.1">
                        @error('vpn_remote_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        NAS Aktif
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('tenant.nas.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600">
                Simpan NAS
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
function toggleVpnSettings() {
    const checkbox = document.getElementById('vpn_enabled');
    const settings = document.getElementById('vpnSettings');
    if (checkbox.checked) {
        settings.classList.remove('hidden');
    } else {
        settings.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const defaultLat = -6.200000;
    const defaultLng = 106.816666;
    
    const map = L.map('mapPicker').setView([defaultLat, defaultLng], 10);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    
    let marker = null;
    let circle = null;
    
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const coverageInput = document.getElementById('coverage');
    
    function updateMarker(lat, lng, coverage = null) {
        if (marker) {
            map.removeLayer(marker);
        }
        if (circle) {
            map.removeLayer(circle);
        }
        
        marker = L.marker([lat, lng]).addTo(map);
        
        if (coverage && coverage > 0) {
            circle = L.circle([lat, lng], {
                color: 'blue',
                fillColor: '#30f',
                fillOpacity: 0.2,
                radius: coverage
            }).addTo(map);
        }
    }
    
    map.on('click', function(e) {
        latInput.value = e.latlng.lat.toFixed(8);
        lngInput.value = e.latlng.lng.toFixed(8);
        updateMarker(e.latlng.lat, e.latlng.lng, parseInt(coverageInput.value) || 0);
    });
    
    [latInput, lngInput, coverageInput].forEach(function(input) {
        input.addEventListener('change', function() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            const coverage = parseInt(coverageInput.value) || 0;
            
            if (!isNaN(lat) && !isNaN(lng)) {
                updateMarker(lat, lng, coverage);
                map.setView([lat, lng], 15);
            }
        });
    });
    
    if (latInput.value && lngInput.value) {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        const coverage = parseInt(coverageInput.value) || 0;
        
        if (!isNaN(lat) && !isNaN(lng)) {
            updateMarker(lat, lng, coverage);
            map.setView([lat, lng], 15);
        }
    }
});
</script>
@endpush
