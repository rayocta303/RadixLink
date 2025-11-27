@extends('layouts.app')

@section('title', 'NAS / Router')
@section('page-title', 'Manajemen NAS / Router')

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

<div class="sm:flex sm:items-center sm:justify-between">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Kelola perangkat NAS dan router (MikroTik, UniFi, dll.)</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex flex-wrap gap-3">
        <a href="{{ route('tenant.nas.map') }}" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500 {{ isset($dbError) ? 'opacity-50 pointer-events-none' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
            </svg>
            Lihat Peta
        </a>
        <a href="{{ route('tenant.router-scripts.index') }}" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-500 {{ isset($dbError) ? 'opacity-50 pointer-events-none' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Download Script
        </a>
        <a href="{{ route('tenant.nas.create') }}" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500 {{ isset($dbError) ? 'opacity-50 pointer-events-none' : '' }}">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah NAS
        </a>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-4">
    <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total NAS</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $nasList->count() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Online</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $nasList->where('is_online', true)->count() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Offline</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $nasList->where('is_online', false)->count() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">VPN Aktif</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $nasList->where('vpn_enabled', true)->count() ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 sm:mt-8 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="p-4 sm:p-6 overflow-x-auto">
        <table id="nasTable" class="w-full stripe hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>IP Address</th>
                    <th>Lokasi</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>VPN</th>
                    <th>Koneksi</th>
                    <th>Last Seen</th>
                    <th class="no-sort">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nasList ?? [] as $nas)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $nas->shortname }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $nas->description ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="font-mono text-sm">{{ $nas->nasname }}</span>
                    </td>
                    <td>
                        @if($nas->location_name)
                            <div class="flex items-center gap-1">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                <span class="text-sm">{{ $nas->location_name }}</span>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                            {{ $nas->type ?? 'MikroTik' }}
                        </span>
                    </td>
                    <td>
                        @if(($nas->status ?? 'enabled') == 'enabled')
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-400">
                                Disabled
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($nas->vpn_enabled ?? false)
                            <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                {{ strtoupper($nas->vpn_type ?? 'VPN') }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td>
                        @if($nas->is_online ?? false)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Online
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Offline
                            </span>
                        @endif
                    </td>
                    <td data-order="{{ $nas->last_seen_at?->timestamp ?? 0 }}">{{ $nas->last_seen_at?->diffForHumans() ?? 'Belum pernah' }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <button type="button" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300" title="Test Koneksi" onclick="testConnection({{ $nas->id }})">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                </svg>
                            </button>
                            <a href="{{ route('tenant.router-scripts.index', ['nas' => $nas->id]) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Download Script">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                            </a>
                            <a href="{{ route('tenant.nas.edit', $nas) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300" title="Edit">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            <form action="{{ route('tenant.nas.destroy', $nas) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus NAS ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Hapus">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#nasTable').DataTable({
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ],
        order: [[0, 'asc']]
    });
});

function testConnection(id) {
    alert('Testing connection to NAS #' + id);
}
</script>
@endpush
