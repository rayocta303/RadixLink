@extends('layouts.app')

@section('title', 'Tambah Role')
@section('page-title', 'Tambah Role Tenant')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('tenant.roles.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Role</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="Contoh: viewer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Nama role dalam huruf kecil tanpa spasi.</p>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <input type="text" name="description" id="description" value="{{ old('description') }}" placeholder="Deskripsi singkat tentang role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm px-3 py-2">
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

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
                                        <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}" class="permission-checkbox h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600" data-group="{{ $group }}" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label for="permission_{{ $permission->id }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('tenant.roles.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600">Batal</a>
                        <button type="submit" class="rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500">Tambah Role</button>
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
