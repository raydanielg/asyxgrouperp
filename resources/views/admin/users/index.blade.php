@extends('layouts.admin')
@section('title', 'Users - ' . config('app.name'))
@section('page_title', 'Manage Users')
@section('content')
@php
$totalUsers = $users->total();
$adminCount = $users->filter(fn($u) => $u->isAdmin())->count();
$enabledCount = $users->filter(fn($u) => $u->is_enable_login ?? true)->count();
$roleCount = count($roles);
@endphp
<div class="animate-fade">
  {{-- Stats Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $totalUsers }}</p><p class="text-[11px] text-gray-500">Total Users</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $adminCount }}</p><p class="text-[11px] text-gray-500">Admins</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $enabledCount }}</p><p class="text-[11px] text-gray-500">Active Logins</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $roleCount }}</p><p class="text-[11px] text-gray-500">Roles</p></div>
    </div>
  </div>

  {{-- Toolbar --}}
  <div class="bg-white rounded-xl border mb-4">
    <div class="px-5 py-3 flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2">
          <input type="text" name="name" value="{{ request('name') }}" placeholder="Search name..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-36">
          <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter email..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-36">
          <select name="role" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            <option value="">All Roles</option>
            @foreach($roles as $name => $label)
            <option value="{{ $name }}" @selected(request('role')===$name)>{{ $label }}</option>
            @endforeach
          </select>
          <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700 transition-colors">Filter</button>
          @if(request('name') || request('email') || request('role'))
          <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-gray-700 ml-1">Clear</a>
          @endif
        </form>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.users.login-history') }}" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Login History
        </a>
        <a href="{{ route('admin.roles.index') }}" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          Roles
        </a>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-1.5 shadow-sm shadow-emerald-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add User
        </a>
      </div>
    </div>
  </div>

  {{-- Users Table --}}
  <div class="bg-white rounded-xl border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-[11px] text-gray-500 bg-gray-50/80 border-b border-gray-100">
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">User</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Email &amp; Phone</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Roles</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Company</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Status</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          <tr class="border-t border-gray-50 hover:bg-amber-50/20 transition-colors group">
            <td class="px-5 py-3.5">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $user->isAdmin() ? 'from-emerald-400 to-emerald-600' : 'from-sky-400 to-sky-600' }} flex items-center justify-center text-white font-bold text-xs shadow-sm">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-900 group-hover:text-emerald-700 transition-colors">{{ $user->name }}</p>
                  <div class="flex items-center gap-1.5 mt-0.5">
                    @if($user->isAdmin())
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100"><svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a6 6 0 00-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 00.515 1.076 32.91 32.91 0 003.256.508 3.5 3.5 0 004.972 0 32.91 32.91 0 003.256-.508.75.75 0 00.515-1.076A11.448 11.448 0 0116 8a6 6 0 00-6-6z"/></svg>ADMIN</span>
                    @endif
                    <span class="text-[10px] text-gray-400">{{ $user->role }}</span>
                  </div>
                </div>
              </div>
            </td>
            <td class="px-5 py-3.5">
              <p class="text-xs text-gray-700 flex items-center gap-1.5"><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ $user->email }}</p>
              <p class="text-xs text-gray-500 flex items-center gap-1.5 mt-1"><svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ $user->phone ?? '—' }}</p>
            </td>
            <td class="px-5 py-3.5">
              <div class="flex flex-wrap gap-1">
                @forelse($user->roles as $r)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $r->name === 'admin' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : ($r->name === 'manager' ? 'bg-sky-50 text-sky-700 ring-1 ring-sky-200' : 'bg-gray-50 text-gray-600 ring-1 ring-gray-200') }}">
                  <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                  {{ $r->label }}
                </span>
                @empty
                <span class="text-[10px] text-gray-400 italic">No role</span>
                @endforelse
              </div>
            </td>
            <td class="px-5 py-3.5">
              @if($user->is_enable_login ?? true)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Enabled
              </span>
              @else
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 ring-1 ring-red-200">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                Disabled
              </span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-right">
              <div class="flex items-center justify-end gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
                @if($user->id !== auth()->id())
                <button onclick="impersonateUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg hover:bg-violet-50 text-violet-500 hover:text-violet-700 transition-all" title="Login as {{ $user->name }}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </button>
                @endif
                <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 rounded-lg hover:bg-sky-50 text-sky-500 hover:text-sky-700 transition-all" title="Edit {{ $user->name }}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                @if($user->id !== auth()->id())
                <button onclick="showChangePassword({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-500 hover:text-amber-700 transition-all" title="Change password">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </button>
                <form id="del-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-user-{{ $user->id }}', 'Delete {{ $user->name }}?', 'This action cannot be undone. All data associated with this user will be permanently removed.')" class="p-1.5 rounded-lg hover:bg-red-50 text-red-400 hover:text-red-600 transition-all" title="Delete {{ $user->name }}">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="px-5 py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <p class="text-sm text-gray-400">No users found</p>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/30">{{ $users->links() }}</div>
    @endif
  </div>
</div>

{{-- Change Password Modal --}}
<div id="passwordModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 animate-fade" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
    <div class="flex items-center gap-3 mb-5">
      <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></div>
      <div><h3 class="text-base font-bold text-gray-900">Change Password</h3><p class="text-xs text-gray-500" id="passwordModalUser"></p></div>
      <button onclick="document.getElementById('passwordModal').classList.add('hidden')" class="ml-auto p-1 rounded-lg hover:bg-gray-100 text-gray-400">&times;</button>
    </div>
    <form id="passwordForm" method="POST" action="" class="space-y-3">
      @csrf @method('PATCH')
      <div><label class="block text-xs font-medium text-gray-600 mb-1">New Password <span class="text-red-400">*</span></label>
        <div class="relative"><input name="password" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none pr-10" id="newPass"><button type="button" onclick="const e=document.getElementById('newPass');e.type=e.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button></div>
      </div>
      <div><label class="block text-xs font-medium text-gray-600 mb-1">Confirm Password <span class="text-red-400">*</span></label>
        <input name="password_confirmation" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
      </div>
      <div class="flex gap-2 pt-3">
        <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
        <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-sm font-bold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200">Update Password</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function showChangePassword(userId, userName) {
  document.getElementById('passwordModalUser').textContent = 'User: ' + userName;
  document.getElementById('passwordForm').action = '{{ route("admin.users.change-password", ":id") }}'.replace(':id', userId);
  document.getElementById('passwordModal').classList.remove('hidden');
}
function impersonateUser(userId, userName) {
  Swal.fire({
    title: 'Login as ' + userName + '?',
    text: 'You will be logged in as this user. You can return to your account by clicking "Stop Impersonating" in the top bar.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#7C3AED',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Yes, Login As',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    showLoaderOnConfirm: true,
    preConfirm: () => {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route("admin.users.impersonate", ":id") }}'.replace(':id', userId);
      form.innerHTML = '@csrf';
      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>
@endpush
@endsection