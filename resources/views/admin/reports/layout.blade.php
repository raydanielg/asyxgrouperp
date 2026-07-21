@extends('layouts.admin')

@section('title', $title ?? 'Report - ' . config('app.name', 'Laravel'))
@section('page_title', $pageTitle ?? 'Report')

@section('content')
{{-- Report Header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.reports.index') }}" class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-bold text-gray-900">{{ $pageTitle }}</h2>
            @isset($pageSubtitle)<p class="text-xs text-gray-400">{{ $pageSubtitle }}</p>@endisset
        </div>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="window.print()" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Print</span>
        </button>
    </div>
</div>

{{-- Summary KPI Cards --}}
@isset($kpiCards)
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach($kpiCards as $card)
    <div class="bg-gradient-to-br from-{{ $card['from'] ?? 'emerald-600' }} to-{{ $card['to'] ?? 'emerald-700' }} rounded-xl border border-{{ $card['border'] ?? 'emerald-500' }} p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium {{ $card['text'] ?? 'emerald-100' }}">{{ $card['label'] }}</span>
            <p class="text-xl font-bold tracking-tight text-white mt-1">{{ $card['value'] }}</p>
            @isset($card['sub'])<p class="text-[10px] {{ $card['sub_color'] ?? 'emerald-200' }} font-medium mt-1">{{ $card['sub'] }}</p>@endisset
        </div>
    </div>
    @endforeach
</div>
@endisset

{{-- Main Content --}}
@yield('report_content')
@endsection
