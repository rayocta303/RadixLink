@extends('layouts.app')

@section('title', 'Edit Role')
@section('page-title', 'Edit Permission Role')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <div class="flex items-center gap-3">
            @php
                $roleColors = [
                    'super_admin' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                    'platform_admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                    'platform_cashier' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                    'platform_technician' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                    'platform_support' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                ];
                $roleColor = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
            @endphp
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $roleColor }}">
                {{ ucwords(str_replace('_', ' ', $role->name)) }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $role->permissions->count() }} permission aktif
            </span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('platform.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    @if($role->name === 'super_admin')
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Perhatian</h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>Role Super Admin memiliki akses penuh ke semua fitur. Mengubah permission role ini dapat mempengaruhi keamanan sistem.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Permissions</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($permissionGroups as $group => $groupPermissions)
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <input type="checkbox" id="group_{{ $group }}" class="group-checkbox h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" data-group="{{ $group }}">
                                    <label for="group_{{ $group }}" class="ml-2 block text-sm font-semibold text-gray-900 dark:text-white capitalize">{{ $group }}</label>
                                </div>
                                <div class="space-y-2 ml-6">
                                    @foreach($groupPermissions as $permission)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" class="permission-checkbox h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" data-group="{{ $group }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                        <label for="permission_{{ $permission->id }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                            {{ ucwords(str_replace('.', ' ', explode('.', $permission->name)[1] ?? $permission->name)) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('platform.roles.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600">Batal</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.group-checkbox').on('change', function() {
        var group = $(this).data('group');
        var isChecked = $(this).is(':checked');
        $('.permission-checkbox[data-group="' + group + '"]').prop('checked', isChecked);
    });

    $('.permission-checkbox').on('change', function() {
        var group = $(this).data('group');
        var total = $('.permission-checkbox[data-group="' + group + '"]').length;
        var checked = $('.permission-checkbox[data-group="' + group + '"]:checked').length;
        $('.group-checkbox[data-group="' + group + '"]').prop('checked', total === checked);
    });

    $('.group-checkbox').each(function() {
        var group = $(this).data('group');
        var total = $('.permission-checkbox[data-group="' + group + '"]').length;
        var checked = $('.permission-checkbox[data-group="' + group + '"]:checked').length;
        $(this).prop('checked', total === checked && total > 0);
    });
});
</script>
@endpush
