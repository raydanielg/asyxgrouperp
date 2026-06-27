@extends('layouts.admin')
@section('title', 'Edit User - ' . config('app.name'))
@section('page_title', 'Edit User')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Edit User</h3>
        <form method="POST" action="{{ route('admin.users-update', $user) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Email *</label><input name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">New Password</label><input name="password" type="password" placeholder="Leave blank to keep current" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Role *</label><select name="role" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option><option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option></select></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.users-index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
