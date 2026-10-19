@extends('layouts.admin')
@section('title', 'Roles & Permissions - ' . config('app.name'))
@section('page_title', 'Roles & Permissions')
@section('content')
@php
  $totalRoles = $roles instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator ? $roles->total() : $roles->count();
  $totalPerms = \Spatie\Permission\Models\Permission::count();
  $editableRoles = $roles->where('editable', true)->count();
@endphp

<p class="text-xs text-gray-400 mb-3">Role access is defined in code (<code class="bg-gray-100 px-1 rounded text-[10px]">app/Support/RolePermissions.php</code>) — edit that file to change actual access.</p>

<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
  <div class="flex items-center gap-6">
    <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Roles</p><p class="text-lg font-bold text-gray-900">{{ number_format($totalRoles) }}</p></div>
    <div class="h-8 w-px bg-gray-100"></div>
    <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Permissions</p><p class="text-lg font-bold text-gray-900">{{ number_format($totalPerms) }}</p></div>
    <div class="h-8 w-px bg-gray-100"></div>
    <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Custom</p><p class="text-lg font-bold text-gray-900">{{ $editableRoles }}</p></div>
  </div>
  <div class="flex items-center gap-2">
    <form method="GET" action="{{ route('admin.roles.index') }}" class="flex items-center gap-2">
      <input type="text" name="name" value="{{ request('name') }}" placeholder="Search roles..." class="px-3 py-2 text-xs border border-gray-200 rounded outline-none focus:border-gray-400 w-44">
      <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Search</button>
      @if(request('name'))<a href="{{ route('admin.roles.index') }}" class="text-xs text-gray-400 hover:text-gray-700">Clear</a>@endif
    </form>
    <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 flex items-center gap-1.5">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Create Role
    </a>
  </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
        <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Name</th>
        <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Label</th>
        <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Permissions</th>
        <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Users</th>
        <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Type</th>
        <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Actions</th>
      </tr></thead>
      <tbody class="divide-y divide-gray-100">
      @forelse($roles as $role)
      <tr class="hover:bg-gray-50/60">
        <td class="px-5 py-3.5"><code class="text-xs font-mono font-semibold text-gray-900">{{ $role->name }}</code></td>
        <td class="px-5 py-3.5"><span class="text-xs font-medium text-gray-700">{{ $role->label }}</span></td>
        <td class="px-5 py-3.5"><span class="text-xs text-gray-600">{{ $role->permissions_count ?? $role->permissions->count() ?? 0 }} permission{{ ($role->permissions_count ?? $role->permissions->count()) !== 1 ? 's' : '' }}</span></td>
        <td class="px-5 py-3.5">
          @if($role->users->count())
          <span class="text-xs text-gray-600">{{ $role->users->take(2)->pluck('name')->join(', ') }}@if($role->users->count() > 2) +{{ $role->users->count() - 2 }}@endif</span>
          @else
          <span class="text-xs text-gray-400">—</span>
          @endif
        </td>
        <td class="px-5 py-3.5">
          @php $rd = $role->editable ? '#3b82f6' : '#f59e0b'; @endphp
          <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $rd }}"></span>{{ $role->editable ? 'Custom' : 'System' }}</span>
        </td>
        <td class="px-5 py-3.5">
          <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.roles.edit', $role) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a>
            @if($role->editable)
            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete {{ $role->label }}?', text:'Users assigned this role will lose its permissions. This cannot be undone.', confirmText:'Yes, delete'})">@csrf @method('DELETE')<button class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button></form>
            @endif
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">No roles found
        @if(request('name')) · <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:underline">Clear search</a>@endif
      </td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/40">
    <form method="GET" action="{{ route('admin.roles.index') }}" class="flex items-center gap-2 text-xs text-gray-500">
      @if(request('name'))<input type="hidden" name="name" value="{{ request('name') }}">@endif
      <span>Show</span>
      <select name="per_page" onchange="this.form.submit()" class="px-2 py-1.5 text-xs border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
        <option value="10" @selected(($perPage ?? 10) == 10 && $perPage !== 'all')>10</option>
        <option value="25" @selected($perPage == 25)>25</option>
        <option value="50" @selected($perPage == 50)>50</option>
        <option value="all" @selected($perPage === 'all')>All</option>
      </select>
      <span>entries</span>
      @if($roles instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
      <span class="text-gray-300">|</span>
      <span>Showing {{ $roles->firstItem() ?? 0 }}–{{ $roles->lastItem() ?? 0 }} of {{ $roles->total() }}</span>
      @endif
    </form>
    <div>@if($roles instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $roles->hasPages()){{ $roles->links() }}@endif</div>
  </div>
</div>
@endsection
