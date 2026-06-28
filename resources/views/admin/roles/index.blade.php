@extends('layouts.admin')
@section('title', 'Roles & Permissions - ' . config('app.name'))
@section('page_title', 'Roles & Permissions')
@section('content')
<div class="animate-fade">
  {{-- Stats Cards --}}
  @php
    $totalRoles = $roles->total();
    $totalPerms = \Spatie\Permission\Models\Permission::count();
    $editableRoles = $roles->where('editable', true)->count();
    $systemRoles = $totalRoles - $editableRoles;
  @endphp
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">Total Roles</p><p class="text-xl font-bold text-gray-900">{{ number_format($totalRoles) }}</p></div>
      </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center"><svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">Permissions</p><p class="text-xl font-bold text-gray-900">{{ number_format($totalPerms) }}</p></div>
      </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center"><svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">Custom Roles</p><p class="text-xl font-bold text-gray-900">{{ $editableRoles }}</p></div>
      </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">System Roles</p><p class="text-xl font-bold text-gray-900">{{ $systemRoles }}</p></div>
      </div>
    </div>
  </div>

  {{-- Roles Table --}}
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <div class="flex items-center gap-2"><h3 class="text-sm font-bold text-gray-900">All Roles</h3><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border">{{ number_format($totalRoles) }}</span></div>
      <div class="flex items-center gap-3">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="flex items-center gap-2">
          <input type="text" name="name" value="{{ request('name') }}" placeholder="Search roles..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-48">
          <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition-colors">Search</button>
          @if(request('name'))<a href="{{ route('admin.roles.index') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>@endif
        </form>
        <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-xs font-bold rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Create Role
        </a>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b border-gray-100">
          <th class="px-5 py-3 font-medium">Name</th>
          <th class="px-5 py-3 font-medium">Label</th>
          <th class="px-5 py-3 font-medium">Permissions</th>
          <th class="px-5 py-3 font-medium">Users</th>
          <th class="px-5 py-3 font-medium">Type</th>
          <th class="px-5 py-3 font-medium text-right">Actions</th>
        </tr></thead>
        <tbody>
        @forelse($roles as $role)
        <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
          <td class="px-5 py-3">
            <div class="flex items-center gap-2">
              <div class="w-7 h-7 rounded-lg {{ $role->editable ? 'bg-emerald-50' : 'bg-amber-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 {{ $role->editable ? 'text-emerald-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              </div>
              <code class="text-xs font-mono font-semibold text-gray-900">{{ $role->name }}</code>
            </div>
          </td>
          <td class="px-5 py-3"><span class="text-xs text-gray-700 font-medium">{{ $role->label }}</span></td>
          <td class="px-5 py-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($role->permissions_count ?? $role->permissions->count()) > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-500 border border-gray-100' }}">
              {{ $role->permissions_count ?? $role->permissions->count() ?? 0 }} perm{{ ($role->permissions_count ?? $role->permissions->count()) !== 1 ? 's' : '' }}
            </span>
          </td>
          <td class="px-5 py-3">
            <div class="flex flex-wrap gap-1 items-center">
              @foreach($role->users->take(3) as $u)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] bg-sky-50 text-sky-700 border border-sky-100 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                {{ $u->name }}
              </span>
              @endforeach
              @if($role->users->count() > 3)
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] bg-gray-100 text-gray-600 border border-gray-100 font-medium">+{{ $role->users->count() - 3 }}</span>
              @endif
              @if($role->users->isEmpty())<span class="text-[10px] text-gray-400 italic">No users assigned</span>@endif
            </div>
          </td>
          <td class="px-5 py-3">
            @if($role->editable)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">Custom</span>
            @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">
              <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              System
            </span>
            @endif
          </td>
          <td class="px-5 py-3 text-right">
            <div class="flex items-center justify-end gap-1.5">
              <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-[10px] font-medium text-emerald-600 hover:bg-emerald-50 transition-colors gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
              </a>
              @if($role->editable)
              <form id="del-role-{{ $role->id }}" method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline">@csrf @method('DELETE')</form>
              <button onclick="confirmDelete('del-role-{{ $role->id }}', 'Delete {{ $role->label }}?', 'This will permanently delete the role «{{ $role->label }}». Users assigned this role will lose its permissions.')" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-[10px] font-medium text-red-500 hover:bg-red-50 transition-colors gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete
              </button>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-12 text-center">
          <div class="flex flex-col items-center gap-3">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <p class="text-xs text-gray-400 font-medium">No roles found</p>
            @if(request('name'))<a href="{{ route('admin.roles.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700">Clear search filter</a>@endif
          </div>
        </td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if($roles->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $roles->links() }}</div>
    @endif
  </div>
</div>
@endsection