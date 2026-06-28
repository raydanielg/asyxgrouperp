@extends('layouts.admin')
@section('title', 'Users - ' . config('app.name'))
@section('page_title', 'User Management')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage all system users</p>
    <a href="{{ route('admin.users-create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add User
    </a>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Email</th><th class="px-5 py-3 font-medium">Role</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Joined</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($users as $user)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs"><div class="flex items-center gap-2"><div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-[10px]">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>{{ $user->name }}</div></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $user->email }}</td>
            <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($user->role === 'admin') ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-sky-50 text-sky-700 border border-sky-100' }} capitalize">{{ $user->role }}</span></td>
            <td class="px-5 py-3">
        @if($user->email_verified_at)
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Verified</span>
        @else
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
        @endif</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.users-edit', $user) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a>
        @if($user->id !== auth()->id())<form method="POST" action="{{ route('admin.users-destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form>
        @endif
            </td>
        
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No users found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $users->links() }}</div>
</div>
@endsection
