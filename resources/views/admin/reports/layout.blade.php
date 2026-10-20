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
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-gray-100">
        @foreach($kpiCards as $card)
        <div class="px-4 py-3.5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $card['label'] }}</p>
            <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $card['value'] }}</p>
            @isset($card['sub'])<p class="text-[10px] text-gray-400 mt-0.5">{{ $card['sub'] }}</p>@endisset
        </div>
        @endforeach
    </div>
</div>
@endisset

{{-- Main Content --}}
@yield('report_content')
@endsection
