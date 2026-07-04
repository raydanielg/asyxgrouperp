@forelse($users as $user)
<tr class="border-t border-gray-50 hover:bg-amber-50/20 transition-colors group" id="user-row-{{ $user->id }}">
  <td class="px-4 py-3.5">
    <input type="checkbox" class="user-checkbox w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" value="{{ $user->id }}" @if($user->id === auth()->id()) disabled @endif>
  </td>
  <td class="px-5 py-3.5">
    <div class="flex items-center gap-3">
      @if($user->avatar)
      <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
      @else
      <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $user->isAdmin() ? 'from-emerald-400 to-emerald-600' : 'from-sky-400 to-sky-600' }} flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white">
        {{ strtoupper(substr($user->name, 0, 1)) }}
      </div>
      @endif
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
    @if($user->company)
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200">{{ $user->company->short_code ?? $user->company->name }}</span>
    @else
    <span class="text-[10px] text-gray-400 italic">—</span>
    @endif
  </td>
  <td class="px-5 py-3.5 user-status-cell">
    @if($user->is_enable_login ?? true)
    <span id="user-status-{{ $user->id }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
      Enabled
    </span>
    @else
    <span id="user-status-{{ $user->id }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 ring-1 ring-red-200">
      <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
      Disabled
    </span>
    @endif
  </td>
  <td class="px-5 py-3.5 text-right">
    <div class="flex items-center justify-end gap-1 opacity-80 group-hover:opacity-100 transition-opacity">
      @if($user->id !== auth()->id())
      <button onclick="impersonateUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg hover:bg-violet-50 text-violet-500 hover:text-violet-700 transition-all" title="Login as {{ $user->name }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
      </button>
      @endif
      <button onclick="showChangePassword({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-500 hover:text-amber-700 transition-all" title="Change Password">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </button>
      <button onclick="toggleUserLogin({{ $user->id }}, {{ ($user->is_enable_login ?? true) ? 'false' : 'true' }})" class="p-1.5 rounded-lg {{ ($user->is_enable_login ?? true) ? 'hover:bg-red-50 text-red-500 hover:text-red-700' : 'hover:bg-emerald-50 text-emerald-500 hover:text-emerald-700' }} transition-all" title="{{ ($user->is_enable_login ?? true) ? 'Disable login' : 'Enable login' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ ($user->is_enable_login ?? true) ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M5 13l4 4L19 7' }}"/></svg>
      </button>
      <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 rounded-lg hover:bg-sky-50 text-sky-500 hover:text-sky-700 transition-all" title="Edit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
      </a>
      <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" id="delete-form-{{ $user->id }}">
        @csrf @method('DELETE')
        <button type="button" onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 hover:text-red-700 transition-all" title="Delete">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </form>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
    <div class="flex flex-col items-center gap-2">
      <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
      <p>No users found.</p>
    </div>
  </td>
</tr>
@endforelse
