@extends('layouts.admin')
@section('title', 'Roles & Permissions - ' . config('app.name'))
@section('page_title', 'Roles & Permissions')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage user roles and their permissions</p>
    <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Role
    </a>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b bg-gray-50/50">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="flex items-center gap-3">
            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search roles..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-64">
            <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700">Search</button>
        @if(request('name'))<a href="{{ route('admin.roles.index') }}" class="text-xs text-gray-500 hover:text-gray-700">Clear</a>
        @endif
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Name</th>
                <th class="px-5 py-3 font-medium">Label</th>
                <th class="px-5 py-3 font-medium">Permissions</th>
                <th class="px-5 py-3 font-medium">Users</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
        @forelse($roles as $role)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs font-mono font-medium text-gray-900">{{ $role->name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $role->label }}</td>
                    <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $role->permissions_count ?? 0 }}</span></td>
                    <td class="px-5 py-3">
                        <div class="flex flex-wrap gap-1">
        @foreach($role->users->take(3) as $u)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] bg-sky-50 text-sky-700">{{ $u->name }}</span>
        @endforeach
                            @if($role->users->count() > 3)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600">+{{ $role->users->count() - 3 }}</span>
        @endif
                            @if($role->users->isEmpty())<span class="text-[10px] text-gray-400">No users</span>
        @endif
                        </div>
                    </td>
                    <td class="px-5 py-3 flex items-center gap-2">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a>
        @if($role->editable)
        <form id="del-role-{{ $role->id }}" method="POST" action="{{ route('admin.roles.destroy', $role) }}">@csrf @method('DELETE')</form>
                            <button onclick="confirmDelete('del-role-{{ $role->id }}', 'Delete role?', 'This will permanently delete the role {{ $role->label }}.')" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
        @else
        <span class="text-[10px] text-gray-400 italic">System</span>
        @endif
                    </td>
                </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No roles found</td></tr>
        @endforelse
        </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $roles->links() }}</div>
</div>
@endsection
