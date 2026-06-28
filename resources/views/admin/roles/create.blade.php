@extends('layouts.admin')
@section('title', 'Create Role - ' . config('app.name'))
@section('page_title', 'Create New Role')
@section('content')
<div class="animate-fade max-w-4xl">
  <div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-emerald-600 transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Back to Roles
    </a>
  </div>

  <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-5">
    @csrf

    {{-- Role Information --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-violet-50 to-white border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center"><svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
          <div><h3 class="text-sm font-bold text-gray-900">Role Information</h3><p class="text-[11px] text-gray-500">Define the role name and display label</p></div>
        </div>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">System Name <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              <input name="name" required value="{{ old('name') }}" placeholder="e.g. editor, accountant" class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <p class="text-[10px] text-gray-400 mt-1">Lowercase, no spaces. Used internally.</p>
            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Display Label <span class="text-red-400">*</span></label>
            <div class="relative">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
              <input name="label" required value="{{ old('label') }}" placeholder="e.g. Editor, Accountant" class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <p class="text-[10px] text-gray-400 mt-1">Human-readable name shown in the UI.</p>
            @error('label')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
        </div>
      </div>
    </div>

    {{-- Permissions --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-emerald-50 to-white border-b border-gray-100">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
            <div><h3 class="text-sm font-bold text-gray-900">Permissions</h3><p class="text-[11px] text-gray-500">Select which permissions this role grants</p></div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" onclick="selectAllPermissions()" class="px-3 py-1.5 text-[10px] font-medium text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors inline-flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Select All
            </button>
            <button type="button" onclick="deselectAllPermissions()" class="px-3 py-1.5 text-[10px] font-medium text-gray-500 hover:bg-gray-50 rounded-lg transition-colors inline-flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
              Deselect All
            </button>
          </div>
        </div>
      </div>
      <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
        @foreach($permissions as $module => $modulePerms)
        @php $moduleSlug = \Str::slug($module); @endphp
        <div class="border border-gray-100 rounded-xl overflow-hidden">
          <div class="flex items-center gap-3 px-5 py-3 bg-gray-50/50 border-b border-gray-100">
            <input type="checkbox" id="module-{{ $moduleSlug }}" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 module-checkbox w-4 h-4" onchange="toggleModule(this, '{{ $moduleSlug }}')">
            <label for="module-{{ $moduleSlug }}" class="text-xs font-bold text-gray-900 cursor-pointer flex-1">{{ $module }}</label>
            <span class="text-[10px] text-gray-400 font-medium">{{ count($modulePerms) }} permission{{ count($modulePerms) !== 1 ? 's' : '' }}</span>
          </div>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1.5 p-4">
            @foreach($modulePerms as $perm)
            <label class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-emerald-50/30 transition-colors cursor-pointer">
              <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm-{{ $perm->id }}" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 perm-checkbox module-{{ $moduleSlug }} w-4 h-4">
              <label for="perm-{{ $perm->id }}" class="text-xs text-gray-600 cursor-pointer select-none">{{ $perm->label }}</label>
            </label>
            @endforeach
          </div>
        </div>
        @endforeach
        @error('permissions')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between gap-3 bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-4">
      <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Cancel
      </a>
      <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-bold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Role
      </button>
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