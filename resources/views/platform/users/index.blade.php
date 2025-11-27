@extends('layouts.app')

@section('title', 'Platform Users')
@section('page-title', 'Pengguna Platform')

@section('content')
<div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Kelola administrator dan staff support platform.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="{{ route('platform.users.create') }}" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-primary-500">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah User
        </a>
    </div>
</div>

<div class="mt-6 sm:mt-8 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
    <div class="p-4 sm:p-6 overflow-x-auto">
        <table id="usersTable" class="w-full stripe hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Login Terakhir</th>
                    <th class="no-sort">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php
                            $role = $user->roles->first()?->name ?? 'user';
                            $roleColors = [
                                'super_admin' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'platform_admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                'platform_support' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                            ];
                            $roleColor = $roleColors[$role] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleColor }}">
                            {{ ucwords(str_replace('_', ' ', $role)) }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td data-order="{{ $user->last_login_at?->timestamp ?? 0 }}">{{ $user->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('platform.users.edit', $user) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300" title="Edit">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('platform.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Hapus">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                            @endif
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
    $('#usersTable').DataTable({
        columnDefs: [
            { targets: 'no-sort', orderable: false }
        ],
        order: [[0, 'asc']]
    });
});
</script>
@endpush
