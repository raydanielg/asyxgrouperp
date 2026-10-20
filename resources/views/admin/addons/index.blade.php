@extends('layouts.admin')
@section('title', 'Add-ons - ' . config('app.name'))
@section('page_title', 'Modules & Add-ons')
@section('content')
@php $enabled = $addons->where('is_enable', true)->count(); @endphp

<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $addons->count() }} add-ons · {{ $enabled }} enabled</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
        @forelse($addons as $addon)
        <div class="border border-gray-200 rounded-md overflow-hidden hover:border-gray-400 transition-colors">
            <div class="px-4 py-3.5 border-b border-gray-100 flex items-start justify-between">
                <div class="min-w-0">
                    <h3 class="text-sm font-bold text-gray-900">{{ $addon->name }}</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5 capitalize">{{ str_replace('_', ' ', $addon->module) }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500 flex-shrink-0"><span style="width:6px;height:6px;border-radius:99px;background:{{ $addon->is_enable ? '#10b981' : '#d1d5db' }}"></span>{{ $addon->is_enable ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="px-4 py-3">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-lg font-bold text-gray-900 font-mono">{{ number_format($addon->monthly_price) }}</span>
                    <span class="text-[11px] text-gray-400">TZS/mo</span>
                    @if($addon->yearly_price)
                    <span class="text-[11px] text-gray-400 ml-2">· {{ number_format($addon->yearly_price) }}/yr</span>
                    @endif
                </div>
            </div>
            <div class="px-4 py-2.5 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.add-ons.toggle', $addon) }}" class="inline" onsubmit="return confirmAction(this, {title: '{{ $addon->is_enable ? 'Disable' : 'Enable' }} this add-on?'})">@csrf @method('PATCH')
                    <button class="text-xs font-medium {{ $addon->is_enable ? 'text-gray-400 hover:text-red-600' : 'text-emerald-600 hover:text-emerald-700' }}">{{ $addon->is_enable ? 'Disable' : 'Enable' }}</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-10 text-gray-400 text-xs">No add-ons found. Add-ons will appear here when modules are installed.</div>
        @endforelse
    </div>
</div>
@endsection
