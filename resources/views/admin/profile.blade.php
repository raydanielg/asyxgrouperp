@extends('layouts.admin')
@section('title', 'Profile - ' . config('app.name'))
@section('page_title', 'My Profile')
@section('content')
<div class="max-w-4xl grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Info --}}
    <div class="bg-white rounded-xl border p-6 lg:col-span-2">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Profile Information</h3>
        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')
            <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-xl">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                <div><p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p><p class="text-xs text-gray-400">{{ $user->email }}</p><span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100 capitalize">{{ $user->role }}</span></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Email *</label><input name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Phone</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Changes</button>
        </form>
    </div>
    {{-- Password Update --}}
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Change Password</h3>
        <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-4">
            @csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Current Password *</label><input name="current_password" type="password" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">New Password *</label><input name="password" type="password" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Confirm Password *</label><input name="password_confirmation" type="password" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Password</button>
        </form>
    </div>
</div>
@endsection
