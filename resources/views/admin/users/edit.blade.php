@extends('layouts.admin')
@section('title', 'Edit User - ' . config('app.name'))
@section('page_title', 'Edit User: ' . $user->name)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Users</a>
</div>
<form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
    @csrf @method('PATCH')
    <div class="bg-white rounded-xl border p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3">User Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                <input name="name" required value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                @error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
                <input name="email" type="email" required value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                @error('email')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                <input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">System Role *</label>
                <select name="role" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="user" @selected(old('role', $user->role)==='user')>User</option>
                    <option value="admin" @selected(old('role', $user->role)==='admin')>Admin</option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_enable_login" value="1" {{ old('is_enable_login', $user->is_enable_login ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600">
            <label class="text-xs text-gray-600">Enable Login</label>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3">Assign Roles & Permissions</h3>
        <p class="text-xs text-gray-500">Select roles to assign specific permissions to this user</p>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($roles as $role)
            <div class="flex items-center gap-2 p-3 border rounded-lg hover:bg-emerald-50/30 transition-colors">
                <input type="checkbox" name="assigned_roles[]" value="{{ $role->id }}" id="role-{{ $role->id }}" class="rounded border-gray-300 text-emerald-600" {{ in_array($role->id, old('assigned_roles', $userRoles)) ? 'checked' : '' }}>
                <label for="role-{{ $role->id }}" class="text-xs font-medium text-gray-700 cursor-pointer flex-1">{{ $role->label }}</label>
                <span class="text-[10px] text-gray-400">{{ $role->permissions->count() }} perms</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update User</button>
    </div>
</form>
@endsection
