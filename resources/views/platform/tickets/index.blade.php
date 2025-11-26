@extends('layouts.app')

@section('title', 'Tickets')
@section('page-title', 'Support Tickets')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Manage support tickets from tenants.</p>
    </div>
</div>
<div class="mt-8 flow-root">
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6">Ticket #</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Subject</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Tenant</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Priority</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                @forelse($tickets as $ticket)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">{{ $ticket->ticket_number }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ticket->subject, 40) }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $ticket->tenant->company_name ?? '-' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                            {{ $ticket->priority === 'urgent' ? 'bg-red-50 text-red-700 ring-red-600/20' : ($ticket->priority === 'high' ? 'bg-orange-50 text-orange-700 ring-orange-600/20' : 'bg-gray-50 text-gray-700 ring-gray-600/20') }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                            {{ $ticket->status === 'resolved' || $ticket->status === 'closed' ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-blue-50 text-blue-700 ring-blue-600/20' }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="{{ route('platform.tickets.show', $ticket) }}" class="text-primary-600 hover:text-primary-900">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No tickets found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $tickets->links() }}</div>
</div>
@endsection
