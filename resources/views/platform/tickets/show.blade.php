@extends('layouts.app')

@section('title', 'Detail Tiket')
@section('page-title', 'Detail Tiket #{{ $ticket->id }}')

@section('content')
<div class="mb-6">
    <a href="{{ route('platform.tickets.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar Tiket
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $ticket->subject }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Dibuat oleh {{ $ticket->user->name ?? 'Unknown' }} pada {{ $ticket->created_at?->format('d M Y H:i') }}
                </p>
            </div>
            <div class="p-6">
                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($ticket->message)) !!}
                </div>
            </div>
        </div>

        <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Balasan</h3>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($ticket->replies ?? [] as $reply)
                <div class="p-6 {{ $reply->is_internal ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ strtoupper(substr($reply->user->name ?? 'U', 0, 2)) }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $reply->user->name ?? 'Unknown' }}
                                    @if($reply->is_internal)
                                    <span class="ml-2 text-xs text-yellow-600 dark:text-yellow-400">(Internal)</span>
                                    @endif
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at?->format('d M Y H:i') }}</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                                {!! nl2br(e($reply->message)) !!}
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    Belum ada balasan
                </div>
                @endforelse
            </div>
            
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                <form action="{{ route('platform.tickets.reply', $ticket) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Balasan</label>
                            <textarea name="message" id="message" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2"></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Internal note (tidak terlihat oleh tenant)</span>
                            </label>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                                Kirim Balasan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Tiket</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('platform.tickets.update', $ticket) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting" {{ $ticket->status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioritas</label>
                        <select name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Update Tiket
                    </button>
                </form>
                
                <dl class="mt-6 space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Tenant</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->tenant->company_name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Kategori</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($ticket->category ?? '-') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Resolved At</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $ticket->resolved_at?->format('d M Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
