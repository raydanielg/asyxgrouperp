@extends('layouts.admin')
@section('title', 'Users - ' . config('app.name'))
@section('page_title', 'Manage Users')
@section('content')
  {{-- Toolbar --}}
  <div class="bg-white rounded-md border border-gray-200 mb-4">
    <div class="px-5 py-3 flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2">
          <input type="text" name="name" value="{{ request('name') }}" placeholder="Search name..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs outline-none focus:border-gray-400 w-36">
          <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter email..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs outline-none focus:border-gray-400 w-36">
          <select name="role" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs outline-none focus:border-gray-400">
            <option value="">All Roles</option>
            @foreach($roles as $name => $label)
            <option value="{{ $name }}" @selected(request('role')===$name)>{{ $label }}</option>
            @endforeach
          </select>
          <button type="submit" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Filter</button>
          @if(request('name') || request('email') || request('role'))
          <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-gray-700 ml-1">Clear</a>
          @endif
        </form>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.users.login-history') }}" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Login History</a>
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.roles.index') }}" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Roles</a>
        @endif
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors flex items-center gap-1.5">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Add User
        </a>
      </div>
    </div>
  </div>

  {{-- Bulk Actions Toolbar --}}
  <div id="bulkToolbar" class="hidden bg-white rounded-md border border-gray-200 mb-3 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <span class="text-sm font-semibold text-gray-700"><span id="selectedCount">0</span> selected</span>
      <div class="h-4 w-px bg-gray-200"></div>
      <button onclick="bulkEnable(true)" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Enable Login</button>
      <button onclick="bulkEnable(false)" class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Disable Login</button>
    </div>
    <button onclick="clearSelection()" class="text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      Clear
    </button>
  </div>

  {{-- Users Table --}}
  <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-[11px] text-gray-500 bg-gray-50/80 border-b border-gray-100">
            <th class="px-4 py-3.5 w-10">
              <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400 cursor-pointer">
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
        <select id="perPageSelector" onchange="changePerPage(this.value)" class="px-2 py-1 rounded-lg border border-gray-200 text-xs outline-none focus:border-gray-400 bg-white">
          <option value="10" @selected($perPage == 10)>10</option>
          <option value="50" @selected($perPage == 50)>50</option>
          <option value="100" @selected($perPage == 100)>100</option>
          <option value="all" @selected($perPage === 'all')>All</option>
        </select>
        <span>entries</span>
        <span id="usersInfoText" class="@if($perPage === 'all' || !($users instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)) hidden @endif">
          <span class="text-gray-400">|</span>
          <span>Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}</span>
        </span>
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
  <div class="bg-white rounded-md shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
    <div class="flex items-center gap-3 mb-5">
      
      <div><h3 class="text-base font-bold text-gray-900">Change Password</h3><p class="text-xs text-gray-500" id="passwordModalUser"></p></div>
      <button onclick="document.getElementById('passwordModal').classList.add('hidden')" class="ml-auto p-1 rounded hover:bg-gray-100 text-gray-400">&times;</button>
    </div>
    <form id="passwordForm" method="POST" action="" class="space-y-3">
      @csrf @method('PATCH')
      <div><label class="block text-xs font-medium text-gray-600 mb-1">New Password <span class="text-red-400">*</span></label>
        <div class="relative"><input name="password" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-400 pr-10" id="newPass"><button type="button" onclick="const e=document.getElementById('newPass');e.type=e.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button></div>
      </div>
      <div><label class="block text-xs font-medium text-gray-600 mb-1">Confirm Password <span class="text-red-400">*</span></label>
        <input name="password_confirmation" type="password" required minlength="6" class="w-full px-3 py-2.5 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-400">
      </div>
      <div class="flex gap-2 pt-3">
        <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm rounded hover:bg-gray-50 transition-colors">Cancel</button>
        <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Update Password</button>
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

bindCheckboxEvents();

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
        badge.className = 'inline-flex items-center gap-1.5 text-xs text-gray-600';
        badge.innerHTML = '<span style="width:6px;height:6px;border-radius:99px;background:#10b981"></span>Enabled';
    } else {
        badge.className = 'inline-flex items-center gap-1.5 text-xs text-gray-600';
        badge.innerHTML = '<span style="width:6px;height:6px;border-radius:99px;background:#ef4444"></span>Disabled';
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
function loadUsersPage(url) {
    const container = document.getElementById('usersTableBody');
    const paginationContainer = document.getElementById('usersPagination');
    container.style.opacity = '0.5';
    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(async r => {
        const text = await r.text();
        try { return JSON.parse(text); }
        catch (e) { throw new Error('Invalid response'); }
    })
    .then(data => {
        container.innerHTML = data.html;
        container.style.opacity = '1';
        if (paginationContainer) paginationContainer.innerHTML = data.pagination;
        const infoText = document.getElementById('usersInfoText');
        if (infoText) {
            infoText.classList.toggle('hidden', !data.infoText);
            if (data.infoText) infoText.querySelector('span:last-child').textContent = data.infoText;
        }
        document.getElementById('selectAll').checked = false;
        clearSelection();
        bindCheckboxEvents();
        window.history.pushState({}, '', url);
    })
    .catch(() => {
        container.style.opacity = '1';
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load users.', confirmButtonColor: '#024938' });
    });
}

function changePerPage(value) {
    const params = new URLSearchParams(window.location.search);
    params.set('per_page', value);
    params.delete('page');
    const url = '{{ route('admin.users.index') }}?' + params.toString();
    loadUsersPage(url);
}

function bindCheckboxEvents() {
    document.querySelectorAll('.user-checkbox').forEach(cb => {
        cb.removeEventListener('change', updateSelection);
        cb.addEventListener('change', updateSelection);
    });
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.removeEventListener('change', selectAllHandler);
        selectAll.addEventListener('change', selectAllHandler);
    }
}

function selectAllHandler() {
    document.querySelectorAll('.user-checkbox:not(:disabled)').forEach(cb => cb.checked = this.checked);
    updateSelection();
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