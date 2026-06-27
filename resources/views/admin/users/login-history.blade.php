@extends('layouts.admin')
@section('title', 'Login History - ' . config('app.name'))
@section('page_title', 'User Login History')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Users</a>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">User</th><th class="px-5 py-3 font-medium">IP Address</th><th class="px-5 py-3 font-medium">User Agent</th><th class="px-5 py-3 font-medium">Login At</th></tr></thead>
        <tbody>@forelse($histories as $h)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $h->user?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ $h->ip ?? $h->ip_address ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400 max-w-xs truncate">{{ $h->user_agent ?? $h->details['user_agent'] ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $h->login_at?->format('d M Y H:i') ?? $h->created_at->format('d M Y H:i') }}</td>
        </tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No login history</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $histories->links() }}</div>
</div>
@endsection
