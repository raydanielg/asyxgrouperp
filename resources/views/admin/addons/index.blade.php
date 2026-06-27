@extends('layouts.admin')
@section('title', 'Add-ons - ' . config('app.name'))
@section('page_title', 'Modules & Add-ons')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($addons as $addon)
    <div class="bg-white rounded-xl border p-5 hover:shadow-lg transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div><h3 class="text-sm font-bold text-gray-900">{{ $addon->name }}</h3><p class="text-xs text-gray-400 mt-0.5">{{ $addon->module }}</p></div>
            @if($addon->is_enable)<span class="w-2 h-2 bg-emerald-500 rounded-full mt-2"></span>@else<span class="w-2 h-2 bg-gray-300 rounded-full mt-2"></span>@endif
        </div>
        <div class="flex items-baseline gap-1 mb-3">
            <span class="text-lg font-bold text-gray-900">TZS {{ number_format($addon->monthly_price) }}</span><span class="text-xs text-gray-400">/mo</span>
            <span class="text-xs text-gray-400 ml-2">TZS {{ number_format($addon->yearly_price) }}/yr</span>
        </div>
        <form method="POST" action="{{ route('admin.add-ons.toggle', $addon) }}" class="inline">@csrf @method('PATCH')
            <button class="text-xs {{ $addon->is_enable ? 'text-red-500 hover:text-red-700' : 'text-emerald-600 hover:text-emerald-700' }}">{{ $addon->is_enable ? 'Disable' : 'Enable' }}</button>
        </form>
    </div>
    @empty
    <div class="col-span-full text-center py-8 text-gray-400 text-sm">No add-ons found. Add-ons will appear here when modules are installed.</div>
    @endforelse
</div>
@endsection
