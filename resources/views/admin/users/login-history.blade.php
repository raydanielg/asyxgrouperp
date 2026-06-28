@extends('layouts.admin')
@section('title', 'Login History - ' . config('app.name'))
@section('page_title', 'User Login History')
@section('content')
<div class="animate-fade">
  <div class="mb-4 flex items-center gap-2">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-emerald-600 transition-colors">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      Back to Users
    </a>
  </div>

  {{-- Stats Cards --}}
  @php
    $totalLogins = $histories->total();
    $uniqueUsers = $histories->pluck('user_id')->unique()->filter()->count();
    $todayLogins = $histories->filter(fn($h) => $h->login_at?->isToday() ?? $h->created_at->isToday())->count();
  @endphp
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">Total Logins</p><p class="text-xl font-bold text-gray-900">{{ number_format($totalLogins) }}</p></div>
      </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center"><svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">Unique Users</p><p class="text-xl font-bold text-gray-900">{{ $uniqueUsers }}</p></div>
      </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center"><svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">Today</p><p class="text-xl font-bold text-gray-900">{{ $todayLogins }}</p></div>
      </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center"><svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
        <div><p class="text-xs text-gray-500 font-medium">Avg Daily</p><p class="text-xl font-bold text-gray-900">{{ $totalLogins > 0 ? ceil($totalLogins / max($uniqueUsers, 1)) : 0 }}</p></div>
      </div>
    </div>
  </div>

  {{-- Login History Table --}}
  <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <div class="flex items-center gap-2"><h3 class="text-sm font-bold text-gray-900">Login Records</h3><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border">{{ number_format($totalLogins) }} entries</span></div>
      <form method="GET" action="{{ route('admin.users.login-history') }}" class="flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user/IP..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-48">
        <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700">Search</button>
        @if(request('search'))<a href="{{ route('admin.users.login-history') }}" class="text-xs text-gray-400 hover:text-gray-600">Clear</a>@endif
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b border-gray-100">
          <th class="px-5 py-3 font-medium">User</th>
          <th class="px-5 py-3 font-medium">IP Address</th>
          <th class="px-5 py-3 font-medium">Device / Browser</th>
          <th class="px-5 py-3 font-medium">Login At</th>
          <th class="px-5 py-3 font-medium text-right">Status</th>
        </tr></thead>
        <tbody>
        @forelse($histories as $h)
        @php
          $ua = $h->user_agent ?? $h->details['user_agent'] ?? '';
          $isMobile = str_contains($ua, 'Mobile') || str_contains($ua, 'Android');
          $isChrome = str_contains($ua, 'Chrome');
          $isFirefox = str_contains($ua, 'Firefox');
          $deviceIcon = $isMobile ? 'smartphone' : ($isChrome ? 'globe' : 'monitor');
        @endphp
        <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-[10px]">{{ strtoupper(substr($h->user?->name ?? '?', 0, 1)) }}</div>
              <div><p class="text-xs font-semibold text-gray-900">{{ $h->user?->name ?? 'Deleted User' }}</p><p class="text-[10px] text-gray-400">{{ $h->user?->email ?? '—' }}</p></div>
            </div>
          </td>
          <td class="px-5 py-3"><code class="text-xs font-mono px-2 py-1 bg-gray-50 rounded text-gray-600">{{ $h->ip ?? $h->ip_address ?? '—' }}</code></td>
          <td class="px-5 py-3">
            <div class="flex items-center gap-2">
              <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $deviceIcon === 'smartphone' ? 'M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z' : ($deviceIcon === 'globe' ? 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9' : 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z') }}"/>
              </svg>
              <span class="text-xs text-gray-500 max-w-[200px] truncate" title="{{ $ua }}">{{ $ua ? (strlen($ua) > 60 ? substr($ua, 0, 60) . '...' : $ua) : '—' }}</span>
            </div>
          </td>
          <td class="px-5 py-3">
            <div class="flex items-center gap-2">
              <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span class="text-xs text-gray-600">{{ ($h->login_at ?? $h->created_at)->format('d M Y H:i') }}</span>
            </div>
          </td>
          <td class="px-5 py-3 text-right">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              Success
            </span>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-12 text-center">
          <div class="flex flex-col items-center gap-3">
            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <p class="text-xs text-gray-400 font-medium">No login history found</p>
            @if(request('search'))<a href="{{ route('admin.users.login-history') }}" class="text-xs text-emerald-600 hover:text-emerald-700">Clear search filter</a>@endif
          </div>
        </td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if($histories->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $histories->links() }}</div>
    @endif
  </div>
</div>
@endsection