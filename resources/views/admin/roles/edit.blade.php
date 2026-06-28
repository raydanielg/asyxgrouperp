@extends('layouts.admin')
@section('title', 'Edit Role - ' . config('app.name'))
@section('page_title', 'Edit Role: ' . $role->label)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.roles.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Roles</a>
</div>
<form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-4">
    @csrf @method('PATCH')
    <div class="bg-white rounded-xl border p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3">Role Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                <input name="name" required value="{{ old('name', $role->name) }}" {{ $role->editable ? '' : 'disabled' }} class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none {{ $role->editable ? '' : 'bg-gray-100' }}">
        @if(!$role->editable)
        <p class="text-[10px] text-gray-400 mt-1">System role - name cannot be changed</p>
        @endif
                @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Label *</label>
                <input name="label" required value="{{ old('label', $role->label) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                @error('label')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-6 space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-sm font-bold text-gray-900">Permissions</h3>
            <div class="flex gap-2">
                <button type="button" onclick="selectAllPermissions()" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">Select All</button>
                <button type="button" onclick="deselectAllPermissions()" class="text-[10px] text-gray-500 hover:text-gray-700 font-medium">Deselect All</button>
            </div>
        </div>
        <div class="space-y-4 max-h-[60vh] overflow-y-auto">
        @foreach($permissions as $module => $modulePerms)
            @php $moduleSlug = \Str::slug($module); @endphp
            <div class="border rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3 pb-2 border-b">
                    @php
                    $modulePermNames = $modulePerms->pluck('name')->toArray();
                    $checkedCount = count(array_intersect($modulePermNames, $rolePermissions));
                    $allChecked = $checkedCount === count($modulePermNames);
                    @endphp
                    <input type="checkbox" id="module-{{ $moduleSlug }}" class="rounded border-gray-300 text-emerald-600 module-checkbox" onchange="toggleModule(this, '{{ $moduleSlug }}')" {{ $allChecked ? 'checked' : '' }}>
                    <label for="module-{{ $moduleSlug }}" class="text-xs font-bold text-gray-900 cursor-pointer">{{ $module }}</label>
                    <span class="text-[10px] text-gray-400">({{ $checkedCount }}/{{ count($modulePerms) }})</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
        @foreach($modulePerms as $perm)
        <div class="flex items-center gap-2">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm-{{ $perm->id }}" class="rounded border-gray-300 text-emerald-600 perm-checkbox module-{{ $moduleSlug }}" {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                        <label for="perm-{{ $perm->id }}" class="text-xs text-gray-600 cursor-pointer">{{ $perm->label }}</label>
                    </div>
        @endforeach
        </div>
            </div>
        @endforeach
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Role</button>
    </div>
</form>
@push('scripts')
<script>
function toggleModule(el, moduleSlug) {
    document.querySelectorAll('.perm-checkbox.module-' + moduleSlug).forEach(cb => cb.checked = el.checked);
}
function selectAllPermissions() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
    document.querySelectorAll('.module-checkbox').forEach(cb => cb.checked = true);
}
function deselectAllPermissions() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.module-checkbox').forEach(cb => cb.checked = false);
}
</script>
@endpush
@endsection
