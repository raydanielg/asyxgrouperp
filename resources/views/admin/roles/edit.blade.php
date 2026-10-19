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
      <div class="max-h-[65vh] overflow-y-auto divide-y divide-gray-100">
        @foreach($permissions as $module => $modulePerms)
        @php
          $moduleSlug = \Str::slug($module);
          $modulePermNames = collect($modulePerms)->pluck('name')->toArray();
          $checkedCount = count(array_intersect($modulePermNames, $rolePermissions));
          $allChecked = $checkedCount === count($modulePermNames);
          $someChecked = $checkedCount > 0;
        @endphp
        <div class="perm-module">
          <div class="flex items-center gap-3 px-5 py-3 cursor-pointer select-none hover:bg-gray-50/60" onclick="toggleModuleRow('{{ $moduleSlug }}', event)">
            <svg id="chev-{{ $moduleSlug }}" class="w-3.5 h-3.5 text-gray-400 transition-transform {{ $someChecked ? '' : '-rotate-90' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            <input type="checkbox" id="module-{{ $moduleSlug }}" class="rounded border-gray-300 text-gray-700 focus:ring-gray-400 module-checkbox w-4 h-4" onchange="toggleModule(this, '{{ $moduleSlug }}')" onclick="event.stopPropagation()" {{ $allChecked ? 'checked' : '' }}>
            <span class="text-xs font-semibold text-gray-800 flex-1">{{ $module }}</span>
            <span class="text-[10px] {{ $checkedCount ? 'text-gray-700 font-medium' : 'text-gray-300' }}">{{ $checkedCount }}/{{ count($modulePerms) }}</span>
          </div>
          <div id="body-{{ $moduleSlug }}" class="{{ $someChecked ? '' : 'hidden' }} grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-2 px-5 pb-4 pt-1">
            @foreach($modulePerms as $perm)
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm-{{ $perm->id }}" class="rounded border-gray-300 text-gray-700 focus:ring-gray-400 perm-checkbox module-{{ $moduleSlug }} w-4 h-4" {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }} onchange="syncModuleCount('{{ $moduleSlug }}')">
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
  syncModuleCount(moduleSlug);
}
function toggleModuleRow(moduleSlug, e) {
  const body = document.getElementById('body-' + moduleSlug);
  const chev = document.getElementById('chev-' + moduleSlug);
  body.classList.toggle('hidden');
  chev.classList.toggle('-rotate-90');
}
function syncModuleCount(moduleSlug) {
  const checks = document.querySelectorAll('.perm-checkbox.module-' + moduleSlug);
  const checked = Array.from(checks).filter(c => c.checked).length;
  const counter = document.querySelector('#module-' + moduleSlug).closest('div').querySelector('span:last-child');
  counter.textContent = checked + '/' + checks.length;
  counter.className = 'text-[10px] ' + (checked ? 'text-gray-700 font-medium' : 'text-gray-300');
  document.getElementById('module-' + moduleSlug).checked = checked === checks.length;
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
