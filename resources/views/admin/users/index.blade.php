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
  {{-- KPI Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $totalUsers }}</p><p class="text-[11px] text-gray-500 font-medium">Total Users</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $adminCount }}</p><p class="text-[11px] text-gray-500 font-medium">Admins</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-sky-50 to-sky-100 flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $enabledCount }}</p><p class="text-[11px] text-gray-500 font-medium">Active Logins</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $disabledCount }}</p><p class="text-[11px] text-gray-500 font-medium">Blocked</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-50 to-violet-100 flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $activeSessions }}</p><p class="text-[11px] text-gray-500 font-medium">Active Sessions</p></div>
    </div>
    <div class="bg-white rounded-xl border p-4 flex items-center gap-3 hover:shadow-md transition-shadow">
      <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-50 to-indigo-100 flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
      <div><p class="text-2xl font-bold text-gray-900 font-['Fraunces',serif]">{{ $roleCount }}</p><p class="text-[11px] text-gray-500 font-medium">Roles</p></div>
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

  {{-- Bulk Actions Toolbar --}}
  <div id="bulkToolbar" class="hidden bg-white rounded-xl border mb-3 px-4 py-3 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-3">
      <span class="text-sm font-semibold text-gray-700"><span id="selectedCount">0</span> selected</span>
      <div class="h-4 w-px bg-gray-200"></div>
      <button onclick="bulkEnable(true)" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Enable Login
      </button>
      <button onclick="bulkEnable(false)" class="px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        Disable Login
      </button>
    </div>
    <button onclick="clearSelection()" class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      Clear
    </button>
  </div>

  {{-- Users Table --}}
  <div class="bg-white rounded-xl border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-[11px] text-gray-500 bg-gray-50/80 border-b border-gray-100">
            <th class="px-4 py-3.5 w-10">
              <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
            </th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">User</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Email &amp; Phone</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Roles</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Company</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider">Status</th>
            <th class="px-5 py-3.5 font-semibold uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody id="usersTableBody">
          @include('admin.users.partials.user_rows', ['users' => $users])
        </tbody>
      </table>
    </div>
    <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/30">
      <div class="flex items-center gap-2 text-xs text-gray-500">
        <span>Show</span>
        <select id="perPageSelector" onchange="changePerPage(this.value)" class="px-2 py-1 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
          <option value="10" @selected($perPage == 10)>10</option>
          <option value="50" @selected($perPage == 50)>50</option>
          <option value="100" @selected($perPage == 100)>100</option>
          <option value="all" @selected($perPage === 'all')>All</option>
        </select>
        <span>entries</span>
        @if($perPage !== 'all' && $users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <span class="text-gray-400">|</span>
        <span>Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}</span>
        @endif
      </div>
      <div id="usersPagination">
        @if($perPage !== 'all' && $users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $users->hasPages())
        @include('admin.users.partials.pagination', ['users' => $users, 'perPage' => $perPage])
        @endif
      </div>
    </div>
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
const bulkToggleUrl = '{{ route('admin.users.bulk-toggle-login') }}';
const csrfToken = '{{ csrf_token() }}';

function updateSelection() {
    const checked = document.querySelectorAll('.user-checkbox:checked');
    const toolbar = document.getElementById('bulkToolbar');
    document.getElementById('selectedCount').textContent = checked.length;
    toolbar.classList.toggle('hidden', checked.length === 0);
}

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(cb => cb.checked = this.checked);
    updateSelection();
});

document.querySelectorAll('.user-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelection);
});

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
}

function clearSelection() {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelection();
}

function updateStatusBadge(id, enabled) {
    const badge = document.getElementById('user-status-' + id);
    if (enabled) {
        badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200';
        badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Enabled';
    } else {
        badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 ring-1 ring-red-200';
        badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Disabled';
    }
}

function toggleUserLogin(id, enable) {
    const actionText = enable ? 'enable' : 'disable';
    Swal.fire({
        title: (enable ? 'Enable' : 'Disable') + ' login?',
        text: 'This user will ' + (enable ? 'be able to' : 'no longer be able to') + ' log in.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: enable ? '#059669' : '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, ' + actionText,
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(bulkToggleUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ ids: [id], is_enable_login: enable })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateStatusBadge(id, enable);
                    Swal.fire({ icon: 'success', title: 'Updated', text: 'User login ' + actionText + 'd.', confirmButtonColor: '#024938', timer: 2000 });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not update user.', confirmButtonColor: '#024938' });
                }
            });
        }
    });
}

function bulkEnable(enable) {
    const ids = getSelectedIds();
    if (ids.length === 0) return;
    const actionText = enable ? 'enable' : 'disable';
    Swal.fire({
        title: (enable ? 'Enable' : 'Disable') + ' ' + ids.length + ' users?',
        text: 'Selected users will ' + (enable ? 'be able to' : 'no longer be able to') + ' log in.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: enable ? '#059669' : '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, ' + actionText,
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch(bulkToggleUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ ids: ids, is_enable_login: enable })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    ids.forEach(id => updateStatusBadge(id, enable));
                    clearSelection();
                    Swal.fire({ icon: 'success', title: 'Updated', text: data.updated + ' users ' + actionText + 'd.', confirmButtonColor: '#024938', timer: 2000 });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not update users.', confirmButtonColor: '#024938' });
                }
            });
        }
    });
}

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