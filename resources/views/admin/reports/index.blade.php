@extends('layouts.admin')

@section('title', 'Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reports & Analytics')

@section('content')
<div class="mb-6">
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-24 h-24 bg-white/5 rounded-full -mr-8 -mb-8"></div>
        <div class="relative z-10">
            <h2 class="text-xl font-bold mb-1">Reports & Analytics Center</h2>
            <p class="text-sm text-emerald-100">Comprehensive reports across all modules — Finance, Sales, Purchases, Inventory, HR, CRM, Projects, POS, Bookings, Fleet, Helpdesk & more</p>
        </div>
    </div>
</div>

@foreach($categories as $category)
<div class="mb-6">
    <div class="flex items-center gap-2 mb-3">
        <div class="w-8 h-8 rounded-lg bg-{{ $category['color'] }}-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-{{ $category['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $category['icon'] }}"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900">{{ $category['title'] }}</h3>
        <span class="text-xs text-gray-400">({{ count($category['reports']) }} reports)</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
        @foreach($category['reports'] as $report)
        <a href="{{ route($report['route']) }}" class="bg-white rounded-xl border p-4 hover:shadow-md hover:border-{{ $category['color'] }}-300 transition-all group">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-{{ $category['color'] }}-50 flex items-center justify-center shrink-0 group-hover:bg-{{ $category['color'] }}-100 transition-colors">
                    <svg class="w-4 h-4 text-{{ $category['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 group-hover:text-{{ $category['color'] }}-700 transition-colors">{{ $report['label'] }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $report['desc'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endforeach
@endsection
