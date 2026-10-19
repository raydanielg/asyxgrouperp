@extends('layouts.admin')
@section('title', 'Login History - ' . config('app.name'))
@section('page_title', 'User Login History')
@section('content')
<div>
  <div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Users</a>
  </div>

  @php
    $totalLogins = $histories->total();
    $uniqueUsers = $histories->pluck('user_id')->unique()->filter()->count();
    $todayLogins = $histories->filter(fn($h) => $h->login_at?->isToday() ?? $h->created_at->isToday())->count();
  @endphp

  <div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-6">
      <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Logins</p><p class="text-lg font-bold text-gray-900">{{ number_format($totalLogins) }}</p></div>
      <div class="h-8 w-px bg-gray-100"></div>
      <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Unique Users</p><p class="text-lg font-bold text-gray-900">{{ $uniqueUsers }}</p></div>
      <div class="h-8 w-px bg-gray-100"></div>
      <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Today</p><p class="text-lg font-bold text-gray-900">{{ $todayLogins }}</p></div>
    </div>
    <form method="GET" action="{{ route('admin.users.login-history') }}" class="flex items-center gap-2">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user or IP..." class="px-3 py-2 text-xs border border-gray-200 rounded outline-none focus:border-gray-400 w-52">
      <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Search</button>
      @if(request('search'))<a href="{{ route('admin.users.login-history') }}" class="text-xs text-gray-400 hover:text-gray-700">Clear</a>@endif
    </form>
  </div>

  <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
          <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">User</th>
          <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">IP Address</th>
          <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Device / Browser</th>
          <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Login At</th>
          <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($histories as $h)
        @php
          $ua = $h->user_agent ?? $h->details['user_agent'] ?? '';
          $isMobile = str_contains($ua, 'Mobile') || str_contains($ua, 'Android');
          $device = $isMobile ? 'Mobile' : (str_contains($ua, 'Chrome') ? 'Chrome' : (str_contains($ua, 'Safari') ? 'Safari' : (str_contains($ua, 'Firefox') ? 'Firefox' : (str_contains($ua, 'Edge') ? 'Edge' : 'Browser'))));
        @endphp
        <tr class="hover:bg-gray-50/60">
          <td class="px-5 py-3.5">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-[10px]">{{ strtoupper(substr($h->user?->name ?? '?', 0, 1)) }}</div>
              <div><p class="text-xs font-medium text-gray-900">{{ $h->user?->name ?? 'Deleted User' }}</p><p class="text-[10px] text-gray-400">{{ $h->user?->email ?? '—' }}</p></div>
            </div>
          </td>
          <td class="px-5 py-3.5"><span class="text-xs font-mono text-gray-600">{{ $h->ip ?? $h->ip_address ?? '—' }}</span></td>
          <td class="px-5 py-3.5">
            <p class="text-xs text-gray-600">{{ $ua ? $device : '—' }}</p>
            <p class="text-[10px] text-gray-400 max-w-[220px] truncate" title="{{ $ua }}">{{ $ua ?: '' }}</p>
          </td>
          <td class="px-5 py-3.5"><span class="text-xs text-gray-500">{{ ($h->login_at ?? $h->created_at)->format('d M Y H:i') }}</span></td>
          <td class="px-5 py-3.5 text-right">
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:#10b981"></span>Success</span>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-xs">No login history found
          @if(request('search')) · <a href="{{ route('admin.users.login-history') }}" class="text-gray-600 hover:underline">Clear search</a>@endif
        </td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if($histories->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $histories->links() }}</div>
    @endif
  </div>
</div>
@endsection
