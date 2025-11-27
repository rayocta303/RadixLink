@extends('layouts.app')

@section('title', 'Peta NAS / Router')
@section('page-title', 'Peta Lokasi NAS / Router')

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

<div class="sm:flex sm:items-center sm:justify-between mb-6">
    <div class="sm:flex-auto">
        <p class="text-sm text-gray-700 dark:text-gray-300">Menampilkan {{ $nasList->count() }} router dengan lokasi yang terdaftar</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('tenant.nas.index') }}" class="inline-flex items-center gap-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
            </svg>
            Kembali ke Daftar
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-3">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div id="nasMap" class="h-[600px] w-full"></div>
        </div>
    </div>
    
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Daftar Router</h3>
            </div>
            <div class="max-h-[540px] overflow-y-auto">
                @forelse($nasList as $nas)
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer nas-list-item" 
                     data-lat="{{ $nas->latitude }}" data-lng="{{ $nas->longitude }}" data-id="{{ $nas->id }}">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            @if($nas->is_online ?? false)
                                <span class="h-3 w-3 rounded-full bg-green-500 block"></span>
                            @else
                                <span class="h-3 w-3 rounded-full bg-red-500 block"></span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $nas->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $nas->location_name ?? $nas->nasname }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            @if($nas->vpn_enabled)
                                <span class="inline-flex items-center rounded bg-purple-100 px-1.5 py-0.5 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                    VPN
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada router</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada router dengan koordinat lokasi yang terdaftar.</p>
                    <div class="mt-4">
                        <a href="{{ route('tenant.nas.create') }}" class="inline-flex items-center rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Router
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        
        <div class="mt-4 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">Legenda</h3>
            </div>
            <div class="px-4 py-3 space-y-2">
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-green-500"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Online</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-red-500"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Offline</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-blue-500 opacity-30"></span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Coverage Area</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
.leaflet-popup-content-wrapper {
    border-radius: 8px;
}
.nas-popup {
    min-width: 200px;
}
.nas-popup h4 {
    font-weight: 600;
    margin-bottom: 8px;
}
.nas-popup .info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
    font-size: 13px;
}
.nas-popup .info-label {
    color: #6b7280;
}
.nas-popup .info-value {
    font-weight: 500;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nasData = @json($nasList);
    
    let centerLat = -6.200000;
    let centerLng = 106.816666;
    let zoomLevel = 10;
    
    if (nasData.length > 0) {
        let totalLat = 0;
        let totalLng = 0;
        nasData.forEach(function(nas) {
            totalLat += parseFloat(nas.latitude);
            totalLng += parseFloat(nas.longitude);
        });
        centerLat = totalLat / nasData.length;
        centerLng = totalLng / nasData.length;
        zoomLevel = 12;
    }
    
    const map = L.map('nasMap').setView([centerLat, centerLng], zoomLevel);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    
    const markers = {};
    const circles = {};
    
    const greenIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #22c55e; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    const redIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #ef4444; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    nasData.forEach(function(nas) {
        const isOnline = nas.is_online || false;
        const icon = isOnline ? greenIcon : redIcon;
        
        const marker = L.marker([nas.latitude, nas.longitude], { icon: icon }).addTo(map);
        markers[nas.id] = marker;
        
        const popupContent = `
            <div class="nas-popup">
                <h4>${nas.name}</h4>
                <div class="info-row">
                    <span class="info-label">IP Address:</span>
                    <span class="info-value">${nas.nasname}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lokasi:</span>
                    <span class="info-value">${nas.location_name || '-'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tipe:</span>
                    <span class="info-value">${nas.type || 'MikroTik'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">${isOnline ? '<span style="color: #22c55e;">Online</span>' : '<span style="color: #ef4444;">Offline</span>'}</span>
                </div>
                ${nas.vpn_enabled ? `
                <div class="info-row">
                    <span class="info-label">VPN:</span>
                    <span class="info-value">${(nas.vpn_type || 'VPN').toUpperCase()}</span>
                </div>
                ` : ''}
                ${nas.coverage ? `
                <div class="info-row">
                    <span class="info-label">Coverage:</span>
                    <span class="info-value">${nas.coverage}m</span>
                </div>
                ` : ''}
                <div style="margin-top: 12px;">
                    <a href="/tenant/nas/${nas.id}/edit" style="color: #3b82f6; text-decoration: none; font-size: 13px;">Edit Router &rarr;</a>
                </div>
            </div>
        `;
        
        marker.bindPopup(popupContent);
        
        if (nas.coverage && nas.coverage > 0) {
            const circle = L.circle([nas.latitude, nas.longitude], {
                color: isOnline ? '#22c55e' : '#ef4444',
                fillColor: isOnline ? '#22c55e' : '#ef4444',
                fillOpacity: 0.1,
                radius: nas.coverage
            }).addTo(map);
            circles[nas.id] = circle;
        }
    });
    
    document.querySelectorAll('.nas-list-item').forEach(function(item) {
        item.addEventListener('click', function() {
            const lat = parseFloat(this.dataset.lat);
            const lng = parseFloat(this.dataset.lng);
            const id = this.dataset.id;
            
            map.setView([lat, lng], 16);
            if (markers[id]) {
                markers[id].openPopup();
            }
        });
    });
    
    if (nasData.length > 1) {
        const group = new L.featureGroup(Object.values(markers));
        map.fitBounds(group.getBounds().pad(0.1));
    }
});
</script>
@endpush
