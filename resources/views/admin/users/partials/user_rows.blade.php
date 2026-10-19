@forelse($users as $user)

<tr class="border-t border-gray-50 hover:bg-gray-50/60 transition-colors group" id="user-row-{{ $user->id }}">
  <td class="px-4 py-3.5">
    <input type="checkbox" class="user-checkbox w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400 cursor-pointer" value="{{ $user->id }}" @if($user->id === auth()->id()) disabled @endif>
  </td>
  <td class="px-5 py-3.5">
    <div class="flex items-center gap-3">
      @if($user->avatar)
      <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm">
      @else
      <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-sm ">
        {{ strtoupper(substr($user->name, 0, 1)) }}
      </div>
      @endif
      <div>
        <p class="text-sm font-semibold text-gray-900  transition-colors">{{ $user->name }}</p>
        <div class="flex items-center gap-1.5 mt-0.5">
          @if($user->isAdmin())
          <span class="text-[9px] font-bold text-gray-500 tracking-wider">ADMIN</span>
          @endif
          <span class="text-[10px] text-gray-400">{{ $user->role }}</span>
        </div>
      </div>
    </div>
  </td>
  <td class="px-5 py-3.5">
    <p class="text-xs text-gray-700">{{ $user->email }}</p>
    <p class="text-xs text-gray-400 mt-0.5">{{ $user->phone ?? '—' }}</p>
  </td>
  <td class="px-5 py-3.5">
    <div class="flex flex-wrap gap-1">
      @forelse($user->roles as $r)
      <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $r->name === 'admin' || $r->name === 'superadmin' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600' }}">{{ $r->label }}</span>
      @empty
      <span class="text-[10px] text-gray-400 italic">No role</span>
      @endforelse
    </div>
  </td>
  <td class="px-5 py-3.5">
    @if($user->company)
    <span class="text-xs text-gray-600">{{ $user->company->short_code ?? $user->company->name }}</span>
    @else
    <span class="text-[10px] text-gray-400 italic">—</span>
    @endif
  </td>
  <td class="px-5 py-3.5 user-status-cell">
    @if($user->is_enable_login ?? true)
    <span id="user-status-{{ $user->id }}" class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:#10b981"></span>Enabled</span>
    @else
    <span id="user-status-{{ $user->id }}" class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:#ef4444"></span>Disabled</span>
    @endif
  </td>
  <td class="px-5 py-3.5 text-right">
    <div class="flex items-center justify-end gap-1 ">
      @if($user->id !== auth()->id())
      <button onclick="impersonateUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 " title="Login as {{ $user->name }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
      </button>
      <button onclick="toggleUserLogin({{ $user->id }}, {{ ($user->is_enable_login ?? true) ? 'false' : 'true' }})" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 " title="{{ ($user->is_enable_login ?? true) ? 'Disable' : 'Enable' }} login">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ ($user->is_enable_login ?? true) ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
      </button>
      @endif
      <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 " title="Edit {{ $user->name }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
      </a>
      @if($user->id !== auth()->id())
      <button onclick="showChangePassword({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 " title="Change password">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </button>
      <form id="del-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')</form>
      <button onclick="confirmDelete('del-user-{{ $user->id }}', 'Delete {{ $user->name }}?', 'This action cannot be undone. All data associated with this user will be permanently removed.')" class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-600 " title="Delete {{ $user->name }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      </button>
      @endif
    </div>
  </td>
</tr>
@empty
<tr><td colspan="6" class="px-5 py-12 text-center">
  <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
  <p class="text-sm text-gray-400">No users found</p>
</td></tr>
@endforelse
