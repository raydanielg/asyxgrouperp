@extends('layouts.admin')

@section('title', 'Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reports & Analytics')

@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900">Reports & Analytics</h2>
            <p class="text-xs text-gray-400 mt-0.5">Comprehensive reports across all modules</p>
        </div>
        <span class="text-xs text-gray-400">{{ collect($categories)->sum(fn($c) => count($c['reports'])) }} reports</span>
    </div>
</div>

@foreach($categories as $category)
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">{{ $category['title'] }}</h3>
        <span class="text-[10px] text-gray-400">{{ count($category['reports']) }}</span>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($category['reports'] as $report)
        <a href="{{ route($report['route']) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50/60 group transition-colors">
            <div class="min-w-0">
                <p class="text-xs font-medium text-gray-900 group-hover:text-gray-600">{{ $report['label'] }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $report['desc'] }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-600 transition-colors flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>
</div>
@endforeach
@endsection
