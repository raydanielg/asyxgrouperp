@extends('layouts.admin')

@section('title', 'Users - ' . config('app.name', 'Laravel'))
@section('page_title', 'User Management')

@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">All Users</h3>
        <span class="text-xs text-gray-400">{{ $users->total() }} total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Name</th>
                <th class="px-5 py-3 font-medium">Email</th>
                <th class="px-5 py-3 font-medium">Phone</th>
                <th class="px-5 py-3 font-medium">Role</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Joined</th>
            </tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs text-gray-700">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-[10px]">
                                {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->first_name ?? $user->name }} {{ $user->last_name ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $user->email }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500 font-mono">{{ $user->phone ?? 'N/A' }}</td>
                    <td class="px-5 py-3">
                        @if($user->role === 'admin')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">Admin</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-100">User</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Verified</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                </tr>
                @empty
        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">
        {{ $users->links() }}
    </div>
</div>
@endsection
