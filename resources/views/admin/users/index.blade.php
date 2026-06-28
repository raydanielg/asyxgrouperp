@extends('layouts.admin')
@section('title', 'Users - ' . config('app.name'))
@section('page_title', 'Manage Users')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage system users and their roles</p>
    <div class="flex gap-2">
        <a href="{{ route('admin.users.login-history') }}" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded-lg hover:bg-gray-50 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Login History
        </a>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </a>
    </div>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b bg-gray-50/50">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search name..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-40">
            <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter email..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-40">
            <select name="role" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <option value="">All Roles</option>
                @foreach($roles as $name => $label)<option value="{{ $name }}" @selected(request('role')===$name)>{{ $label }}</option>@endforeach
            </select>
            <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700">Filter</button>
            @if(request('name') || request('email') || request('role'))<a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-gray-700">Clear</a>@endif
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Name</th>
                <th class="px-5 py-3 font-medium">Email</th>
                <th class="px-5 py-3 font-medium">Phone</th>
                <th class="px-5 py-3 font-medium">Roles</th>
                <th class="px-5 py-3 font-medium">Login</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <span class="text-xs font-medium text-gray-900">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $user->email }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $user->phone ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->roles as $r)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium @if($r->name==='admin')bg-emerald-50 text-emerald-700 @elseif($r->name==='manager')bg-sky-50 text-sky-700 @else bg-gray-50 text-gray-600 @endif">{{ $r->label }}</span>
                            @endforeach
                            @if($user->roles->isEmpty())<span class="text-[10px] text-gray-400">No role</span>@endif
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($user->is_enable_login ?? true)<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">Enabled</span>@else<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700">Disabled</span>@endif
                    </td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.impersonate', $user) }}">@csrf<button class="text-violet-600 hover:text-violet-700 text-xs" title="Login as user">Login As</button></form>
                        @endif
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a>
                        <button onclick="showChangePassword({{ $user->id }}, '{{ $user->name }}')" class="text-amber-600 hover:text-amber-700 text-xs">Password</button>
                        @if($user->id !== auth()->id())
                            <form id="del-user-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}">@csrf @method('DELETE')</form>
                            <button onclick="confirmDelete('del-user-{{ $user->id }}', 'Delete user?', 'This will permanently delete {{ $user->name }}.')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                        @endif
                    </td>
                </tr>
                @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $users->links() }}</div>
</div>

{{-- Change Password Modal --}}
<div id="passwordModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Change Password</h3>
        <p class="text-xs text-gray-500 mb-4" id="passwordModalUser"></p>
        <form id="passwordForm" method="POST" action="" class="space-y-3">
            @csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">New Password *</label><input name="password" type="password" required minlength="6" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Confirm Password *</label><input name="password_confirmation" type="password" required minlength="6" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('passwordModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Change Password</button></div>
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
</script>
@endpush
@endsection
