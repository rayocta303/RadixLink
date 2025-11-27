@extends('layouts.app')

@section('title', 'Tickets')
@section('page-title', 'Tiket Support')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Kelola tiket support dari tenant.</p>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-4">
    <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Tiket</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tickets->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tickets->where('status', 'open')->count() }}</dd>
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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Selesai</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tickets->whereIn('status', ['resolved', 'closed'])->count() }}</dd>
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
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Urgent</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tickets->where('priority', 'urgent')->count() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 sm:mt-8 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="p-4 sm:p-6 overflow-x-auto">
        <table id="ticketsTable" class="w-full stripe hover">
            <thead>
                <tr>
                    <th>No. Tiket</th>
                    <th>Subjek</th>
                    <th>Tenant</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th class="no-sort">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>
                        <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->ticket_number }}</span>
                    </td>
                    <td>
                        <div class="max-w-xs truncate" title="{{ $ticket->subject }}">{{ $ticket->subject }}</div>
                    </td>
                    <td>{{ $ticket->tenant->company_name ?? '-' }}</td>
                    <td>
                        @php
                            $priorityColors = [
                                'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $priorityColor = $priorityColors[$ticket->priority] ?? $priorityColors['low'];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $priorityColor }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'open' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'in_progress' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $statusColor = $statusColors[$ticket->status] ?? $statusColors['open'];
                            $statusLabels = [
                                'open' => 'Baru',
                                'in_progress' => 'Diproses',
                                'resolved' => 'Selesai',
                                'closed' => 'Ditutup',
                            ];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                            {{ $statusLabels[$ticket->status] ?? ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </td>
                    <td data-order="{{ $ticket->created_at?->timestamp ?? 0 }}">{{ $ticket->created_at?->format('d M Y H:i') }}</td>
                    <td>
                        <a href="{{ route('platform.tickets.show', $ticket) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300" title="Lihat Detail">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
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
    $('#ticketsTable').DataTable({
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ],
        order: [[5, 'desc']]
    });
});
</script>
@endpush
