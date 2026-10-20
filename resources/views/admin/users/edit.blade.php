@extends('layouts.admin')
@section('title', 'Edit User - ' . config('app.name'))
@section('page_title', 'Edit User: ' . $user->name)
@section('content')
<div class="max-w-4xl">
  <div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Users</a>
  </div>

  <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5" enctype="multipart/form-data">
    @csrf @method('PATCH')

    {{-- Profile Photo --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Profile Photo</h4></div>
      <div class="p-5 flex items-center gap-4">
        @if($user->avatar)
        <img id="userAvatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover border border-gray-200">
        @else
        <div id="userAvatarPreviewFallback" class="w-16 h-16 rounded-full bg-slate-700 flex items-center justify-center text-white text-lg font-bold">
          {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        @endif
        <div class="flex-1">
          <input type="file" name="avatar" accept="image/*" onchange="previewUserAvatar(this)" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
          @error('avatar')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- User Information --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">User Information</h4>
        <span class="inline-flex items-center gap-1.5 text-xs text-gray-500"><span class="w-1.5 h-1.5 rounded-full {{ $user->isAdmin() ? 'bg-gray-800' : 'bg-gray-300' }}"></span>{{ $user->isAdmin() ? 'Administrator' : 'Standard User' }} · {{ $user->email }}</span>
      </div>
      <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input name="name" required value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
            @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email Address <span class="text-red-500">*</span></label>
            <input name="email" type="email" required value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
            @error('email')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +255 700 000 000" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">System Role <span class="text-red-500">*</span></label>
            <select name="role" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
              <option value="user" @selected(old('role', $user->role)==='user')>User</option>
              <option value="admin" @selected(old('role', $user->role)==='admin')>Admin</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
            <select name="company_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
              <option value="">— No company —</option>
              @foreach($companies as $company)
              <option value="{{ $company->id }}" @selected(old('company_id', $user->company_id)==$company->id)>{{ $company->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <label class="inline-flex items-center gap-2.5 mt-4 cursor-pointer">
          <input type="checkbox" name="is_enable_login" value="1" {{ old('is_enable_login', $user->is_enable_login ?? true) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400">
          <span class="text-xs font-medium text-gray-700">Enable Login <span class="font-normal text-gray-400">— allow this user to access the system</span></span>
        </label>
      </div>
    </div>

    @if($companyBranches->isNotEmpty())
    {{-- Branches --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Branches</h4>
        <p class="text-[10px] text-gray-400 mt-0.5">Restrict this user to specific branches — no selection means company-wide access.</p>
      </div>
      <div class="p-5">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
          @foreach($companyBranches as $b)
          <label class="flex items-start gap-2.5 p-3 rounded border border-gray-200 cursor-pointer hover:border-gray-300 has-[:checked]:border-gray-400 has-[:checked]:bg-gray-50">
            <input type="checkbox" name="assigned_branches[]" value="{{ $b->id }}" {{ in_array($b->id, old('assigned_branches', $userBranches)) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-gray-300 text-gray-700">
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-900 leading-tight">{{ $b->name }}</p>
              @if($b->code)<p class="text-[10px] text-gray-400 mt-0.5 font-mono">{{ $b->code }}</p>@endif
            </div>
          </label>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    {{-- Roles --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Roles &amp; Permissions</h4></div>
      <div class="p-5">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
          @foreach($roles as $role)
          <label class="flex items-start gap-2.5 p-3 rounded border border-gray-200 cursor-pointer hover:border-gray-300 has-[:checked]:border-gray-400 has-[:checked]:bg-gray-50">
            <input type="checkbox" name="assigned_roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('assigned_roles', $userRoles)) ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400">
            <div class="flex-1 min-w-0">
              <p class="text-xs font-medium text-gray-900 leading-tight">{{ $role->label }}</p>
              <p class="text-[10px] text-gray-400 mt-0.5">{{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }}</p>
            </div>
          </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Effective Access --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Effective Access</h4>
        <span class="text-[10px] text-gray-400">
          {{ count($resolution['role']) }} role-derived · {{ count($resolution['granted']) }} granted · {{ count($resolution['denied']) }} denied · {{ count($resolution['effective']) }} effective
        </span>
      </div>
      <div class="p-5">
        <div class="flex flex-wrap gap-x-6 gap-y-1.5 mb-4 text-xs">
          <span class="text-gray-400">System role: <span class="font-mono text-gray-800">{{ $user->role }}</span></span>
          <span class="text-gray-400">Assigned roles: <span class="font-medium text-gray-800">{{ $user->roles->pluck('label')->implode(', ') ?: '—' }}</span></span>
          <span class="text-gray-400">Company: <span class="font-medium text-gray-800">{{ $user->company?->name ?? '—' }}</span></span>
          <span class="text-gray-400">Branches: <span class="font-medium text-gray-800">{{ $user->branches->pluck('name')->implode(', ') ?: 'Company-wide' }}</span></span>
          <span class="text-gray-400">Login: <span class="font-medium text-gray-800">{{ $user->is_enable_login ? 'Enabled' : 'Disabled' }}</span></span>
        </div>

        {{-- Direct overrides --}}
        @if($overrides->isNotEmpty())
        <div class="mb-4 border border-gray-200 rounded overflow-hidden">
          <p class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 bg-gray-50 border-b border-gray-100">Direct Overrides · {{ $overrides->count() }}</p>
          <div class="divide-y divide-gray-100">
            @foreach($overrides as $o)
            <div class="px-4 py-2.5 flex items-center justify-between gap-3">
              <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $o->granted ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                <span class="text-xs font-mono text-gray-800">{{ $o->permission?->name }}</span>
                <span class="text-[10px] {{ $o->granted ? 'text-emerald-600' : 'text-red-500' }} font-medium">{{ $o->granted ? 'GRANTED' : 'DENIED' }}</span>
                @if($o->reason)<span class="text-[10px] text-gray-400 truncate">— {{ $o->reason }}</span>@endif
              </div>
              <div class="flex items-center gap-3 flex-shrink-0">
                <span class="text-[10px] text-gray-400">{{ $o->granter?->name ?? 'system' }} · {{ $o->created_at->format('d M Y') }}</span>
                @if(auth()->user()->isSuperAdmin())
                <form method="POST" action="{{ route('admin.users.update-permission', $user) }}" onsubmit="return confirmAction(this, {title: 'Remove this override?'})">
                  @csrf
                  <input type="hidden" name="permission" value="{{ $o->permission?->name }}">
                  <input type="hidden" name="action" value="revoke">
                  <button class="text-[10px] font-medium text-gray-500 hover:text-red-600">Revoke</button>
                </form>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>
        @endif

        @if($effectivePerms === null)
        <p class="text-xs text-gray-500 italic">Administrator — bypasses the permission catalogue.</p>
        @elseif($effectivePerms->isEmpty())
        <p class="text-xs text-gray-400">No permissions resolved for this user.</p>
        @else
        <div class="space-y-3">
          @foreach($effectivePerms as $module => $perms)
          <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">{{ $module ?: 'general' }} <span class="font-normal">· {{ $perms->count() }}</span></p>
            <div class="flex flex-wrap gap-1">
              @foreach($perms as $p)
              <span class="px-1.5 py-0.5 text-[10px] font-mono border rounded {{ in_array($p->name, $resolution['granted']) ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-gray-50 border-gray-100 text-gray-600' }}" @if(in_array($p->name, $resolution['granted'])) title="Direct grant"@endif>{{ $p->name }}</span>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>

      {{-- Manage direct permissions (superadmin, non-privileged target) --}}
      @if(auth()->user()->isSuperAdmin() && !$isPrivileged)
      <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/40">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">Add Override</p>
        <form method="POST" action="{{ route('admin.users.update-permission', $user) }}" class="flex flex-wrap items-end gap-3">
          @csrf
          <div class="flex-1 min-w-56">
            <label class="block text-xs font-medium text-gray-600 mb-1">Permission <span class="text-red-500">*</span></label>
            <select name="permission" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white font-mono">
              <option value="">— Select permission —</option>
              @foreach($allPermissions as $module => $perms)
              <optgroup label="{{ $module ?: 'general' }}">
                @foreach($perms as $p)
                <option value="{{ $p->name }}">{{ $p->name }}{{ in_array($p->name, $resolution['effective']) ? ' (has)' : '' }}</option>
                @endforeach
              </optgroup>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Action <span class="text-red-500">*</span></label>
            <select name="action" required class="px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
              <option value="grant">Grant</option>
              <option value="deny">Deny</option>
            </select>
          </div>
          <div class="flex-1 min-w-44">
            <label class="block text-xs font-medium text-gray-600 mb-1">Reason</label>
            <input name="reason" placeholder="e.g. Temporary coverage" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
          </div>
          <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">Apply</button>
        </form>
        <p class="text-[10px] text-gray-400 mt-2">Denials override role permissions. Grants add permissions on top. Overrides are audited.</p>
      </div>
      @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between gap-3 bg-white rounded-md border border-gray-200 px-5 py-3.5">
      <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-gray-800">Cancel</a>
      <div class="flex items-center gap-2">
        <button type="button" onclick="showChangePassword({{ $user->id }}, '{{ addslashes($user->name) }}')" class="px-4 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50">Change Password</button>
        <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Save Changes</button>
      </div>
    </div>
  </form>
</div>

{{-- Change Password Modal --}}
<div id="passwordModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
  <div class="bg-white rounded-md shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
    <div class="flex items-start justify-between mb-5">
      <div><h3 class="text-base font-bold text-gray-900">Change Password</h3><p class="text-xs text-gray-400 mt-0.5" id="passwordModalUser"></p></div>
      <button onclick="document.getElementById('passwordModal').classList.add('hidden')" class="p-1 rounded hover:bg-gray-100 text-gray-400">&times;</button>
    </div>
    <form id="passwordForm" method="POST" action="" class="space-y-3">
      @csrf @method('PATCH')
      <div><label class="block text-xs font-medium text-gray-600 mb-1">New Password <span class="text-red-500">*</span></label>
        <input name="password" type="password" required minlength="6" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
      </div>
      <div><label class="block text-xs font-medium text-gray-600 mb-1">Confirm Password <span class="text-red-500">*</span></label>
        <input name="password_confirmation" type="password" required minlength="6" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
      </div>
      <div class="flex gap-2 pt-3">
        <button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded hover:bg-gray-50">Cancel</button>
        <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700">Update Password</button>
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

function previewUserAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById('userAvatarPreview');
            if (!img) {
                const fallback = document.getElementById('userAvatarPreviewFallback');
                if (fallback) {
                    img = document.createElement('img');
                    img.id = 'userAvatarPreview';
                    img.className = 'w-16 h-16 rounded-full object-cover border border-gray-200';
                    fallback.parentNode.replaceChild(img, fallback);
                }
            }
            if (img) img.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
