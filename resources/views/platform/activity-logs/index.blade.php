@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Log Aktivitas Platform')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Riwayat semua aktivitas yang terjadi di platform.</p>
    </div>
</div>

<div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ route('platform.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                <select name="user_id" id="user_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="action" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Aksi</label>
                <select name="action" id="action" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Aksi</option>
                    @foreach($actionTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('action') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="entity_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Entitas</label>
                <select name="entity_type" id="entity_type" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Semua Entitas</option>
                    @foreach($entityTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('entity_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    Filter
                </button>
                <a href="{{ route('platform.activity-logs.index') }}" class="inline-flex items-center gap-2 rounded-md bg-gray-100 dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="p-4 sm:p-6 overflow-x-auto">
        <table id="activityLogsTable" class="w-full stripe hover">
            <thead>
                <tr>
                    <th class="no-sort w-8"></th>
                    <th>User</th>
                    <th>Aksi</th>
                    <th>Entitas</th>
                    <th>Deskripsi</th>
                    <th>Waktu</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="log-row" data-log-id="{{ $log->id }}">
                    <td class="text-center">
                        @if($log->old_values || $log->new_values)
                        <button type="button" class="toggle-details text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" data-log-id="{{ $log->id }}">
                            <svg class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                @if($log->user)
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($log->user->name, 0, 2)) }}</span>
                                @else
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="text-sm">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $log->user?->name ?? 'System' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->user?->email ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $actionColors = [
                                'green' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'red' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                'orange' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                'emerald' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $colorClass = $actionColors[$log->action_color] ?? $actionColors['gray'];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colorClass }}">
                            {{ $log->action_label }}
                        </span>
                    </td>
                    <td>
                        @if($log->entity_type)
                            <div class="text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $log->entity_type_label }}</span>
                                @if($log->entity_id)
                                    <span class="text-gray-500 dark:text-gray-400">#{{ $log->entity_id }}</span>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400 dark:text-gray-500">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="text-sm text-gray-900 dark:text-white max-w-xs truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </div>
                    </td>
                    <td data-order="{{ $log->created_at->timestamp }}">
                        <div class="text-sm">
                            <div class="text-gray-900 dark:text-white">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </div>
                    </td>
                    <td>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $log->ip_address ?? '-' }}
                        </div>
                    </td>
                </tr>
                @if($log->old_values || $log->new_values)
                <tr class="details-row hidden bg-gray-50 dark:bg-gray-900" id="details-{{ $log->id }}">
                    <td colspan="7" class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($log->old_values)
                            <div>
                                <h4 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-2 flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Nilai Lama
                                </h4>
                                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-sm overflow-x-auto">
                                    <pre class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap break-words text-xs">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                            @endif
                            @if($log->new_values)
                            <div>
                                <h4 class="text-sm font-semibold text-green-600 dark:text-green-400 mb-2 flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Nilai Baru
                                </h4>
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-sm overflow-x-auto">
                                    <pre class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap break-words text-xs">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                            @endif
                        </div>
                        @if($log->user_agent)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                <span class="font-medium">User Agent:</span> {{ $log->user_agent }}
                            </span>
                        </div>
                        @endif
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="mt-2 text-sm">Belum ada log aktivitas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#activityLogsTable').DataTable({
        paging: false,
        info: false,
        searching: true,
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ],
        order: [[5, 'desc']]
    });

    $(document).on('click', '.toggle-details', function() {
        var logId = $(this).data('log-id');
        var detailsRow = $('#details-' + logId);
        var icon = $(this).find('svg');
        
        if (detailsRow.hasClass('hidden')) {
            detailsRow.removeClass('hidden');
            icon.addClass('rotate-90');
        } else {
            detailsRow.addClass('hidden');
            icon.removeClass('rotate-90');
        }
    });
});
</script>
@endpush
