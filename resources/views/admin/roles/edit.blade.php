@extends('layouts.admin')
@section('title', 'Edit Role - ' . config('app.name'))
@section('page_title', 'Edit Role: ' . $role->label)
@section('content')
<div class="max-w-4xl">
  <div class="mb-4">
    <a href="{{ route('admin.roles.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Roles</a>
  </div>

  <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-5">
    @csrf @method('PATCH')

    {{-- Role Information --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Role Information</h4>
        <span class="inline-flex items-center gap-1.5 text-xs text-gray-500"><span class="w-1.5 h-1.5 rounded-full {{ $role->editable ? 'bg-blue-400' : 'bg-amber-400' }}"></span>{{ $role->editable ? 'Custom role' : 'System role' }}</span>
      </div>
      <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">System Name <span class="text-red-500">*</span></label>
            <input name="name" required value="{{ old('name', $role->name) }}" {{ $role->editable ? '' : 'disabled' }} class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 {{ $role->editable ? '' : 'bg-gray-50 text-gray-400' }}">
            @if(!$role->editable)<p class="text-[10px] text-gray-400 mt-1">System role name is protected.</p>@endif
            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Display Label <span class="text-red-500">*</span></label>
            <input name="label" required value="{{ old('label', $role->label) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
            @error('label')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
        </div>
      </div>
    </div>

    {{-- Permissions --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Permissions <span class="normal-case font-medium text-gray-400 tracking-normal">({{ count($rolePermissions) }} selected)</span></h4>
        <div class="flex items-center gap-3">
          <button type="button" onclick="selectAllPermissions()" class="text-xs font-medium text-gray-600 hover:text-gray-900">Select All</button>
          <button type="button" onclick="deselectAllPermissions()" class="text-xs font-medium text-gray-400 hover:text-gray-600">Clear</button>
        </div>
      </div>
      <div class="p-5 space-y-4 max-h-[65vh] overflow-y-auto">
        @foreach($permissions as $module => $modulePerms)
        @php
          $moduleSlug = \Str::slug($module);
          $modulePermNames = collect($modulePerms)->pluck('name')->toArray();
          $checkedCount = count(array_intersect($modulePermNames, $rolePermissions));
          $allChecked = $checkedCount === count($modulePermNames);
        @endphp
        <div class="border border-gray-200 rounded-md overflow-hidden">
          <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50/60 border-b border-gray-100">
            <input type="checkbox" id="module-{{ $moduleSlug }}" class="rounded border-gray-300 text-gray-700 focus:ring-gray-400 module-checkbox w-4 h-4" onchange="toggleModule(this, '{{ $moduleSlug }}')" {{ $allChecked ? 'checked' : '' }}>
            <label for="module-{{ $moduleSlug }}" class="text-xs font-semibold text-gray-800 cursor-pointer flex-1">{{ $module }}</label>
            <span class="text-[10px] text-gray-400">{{ $checkedCount }}/{{ count($modulePerms) }}</span>
          </div>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-2 p-4">
            @foreach($modulePerms as $perm)
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm-{{ $perm->id }}" class="rounded border-gray-300 text-gray-700 focus:ring-gray-400 perm-checkbox module-{{ $moduleSlug }} w-4 h-4" {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
              <span class="text-xs text-gray-600 select-none">{{ $perm->label }}</span>
            </label>
            @endforeach
          </div>
        </div>
        @endforeach
        @error('permissions')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between gap-3 bg-white rounded-md border border-gray-200 px-5 py-3.5">
      <a href="{{ route('admin.roles.index') }}" class="text-xs text-gray-500 hover:text-gray-800">Cancel</a>
      <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Update Role</button>
    </div>
  </form>
</div>
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
