@extends('layouts.admin')

@section('title', 'Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reports & Analytics')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @foreach([
        ['label'=>'Total Users','value'=>number_format($stats['totalUsers']),'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
        ['label'=>'Active','value'=>number_format($stats['activeUsers']),'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'amber-400','to'=>'amber-500','border'=>'amber-300','text'=>'amber-50','sub'=>'amber-100'],
        ['label'=>'Inactive','value'=>number_format($stats['inactiveUsers']),'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'red-400','to'=>'red-500','border'=>'red-300','text'=>'red-50','sub'=>'red-100'],
        ['label'=>'Admins','value'=>number_format($stats['totalAdmins']),'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200']
    ] as $card)
    <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
        </div>
    </div>
        @endforeach
        </div>

<div class="bg-white rounded-xl border p-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">User Distribution</h3>
    <div class="space-y-4">
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm font-medium text-gray-700">Verified Users</span>
                <span class="text-sm font-bold text-emerald-600">{{ $stats['activeUsers'] }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-3 rounded-full transition-all" style="width: {{ $stats['totalAllUsers'] > 0 ? ($stats['activeUsers'] / $stats['totalAllUsers'] * 100) : 0 }}%"></div>
            </div>
        </div>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm font-medium text-gray-700">Unverified Users</span>
                <span class="text-sm font-bold text-amber-600">{{ $stats['inactiveUsers'] }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-3 rounded-full transition-all" style="width: {{ $stats['totalAllUsers'] > 0 ? ($stats['inactiveUsers'] / $stats['totalAllUsers'] * 100) : 0 }}%"></div>
            </div>
        </div>
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm font-medium text-gray-700">Admin Users</span>
                <span class="text-sm font-bold text-sky-600">{{ $stats['totalAdmins'] }}</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="bg-gradient-to-r from-sky-400 to-sky-600 h-3 rounded-full transition-all" style="width: {{ $stats['totalAllUsers'] > 0 ? ($stats['totalAdmins'] / $stats['totalAllUsers'] * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
</div>
@endsection
