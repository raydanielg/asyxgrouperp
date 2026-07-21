@extends('admin.reports.layout')

@section('pageTitle', $pageTitle)
@section('pageSubtitle', $pageSubtitle ?? '')

@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

@isset($kpiCards)
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach($kpiCards as $card)
    <div class="bg-gradient-to-br from-{{ $card['from'] ?? 'emerald-600' }} to-{{ $card['to'] ?? 'emerald-700' }} rounded-xl border border-{{ $card['border'] ?? 'emerald-500' }} p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium {{ $card['text'] ?? 'emerald-100' }}">{{ $card['label'] }}</span>
            <p class="text-xl font-bold text-white mt-1">{{ $card['value'] }}</p>
            @isset($card['sub'])<p class="text-[10px] {{ $card['sub_color'] ?? 'emerald-200' }} font-medium mt-1">{{ $card['sub'] }}</p>@endisset
        </div>
    </div>
    @endforeach
</div>
@endisset

@isset($breakdown)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ $breakdownTitle ?? 'Breakdown' }}</h3>
        <div class="space-y-3">
            @foreach($breakdown as $label => $value)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $label) }}</span>
                    <span class="text-xs font-bold text-gray-900">{{ is_numeric($value) ? number_format($value) : $value }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full" style="width: {{ (collect($breakdown)->max(fn($v) => is_numeric($v) ? $v : 0) > 0 && is_numeric($value) ? ($value / collect($breakdown)->max(fn($v) => is_numeric($v) ? $v : 0)) * 100 : 0) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @isset($secondaryBreakdown)
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ $secondaryBreakdownTitle ?? 'Secondary Breakdown' }}</h3>
        <div class="space-y-3">
            @foreach($secondaryBreakdown as $label => $value)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $label) }}</span>
                    <span class="text-xs font-bold text-gray-900">{{ is_numeric($value) ? number_format($value) : $value }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full" style="width: {{ (collect($secondaryBreakdown)->max(fn($v) => is_numeric($v) ? $v : 0) > 0 && is_numeric($value) ? ($value / collect($secondaryBreakdown)->max(fn($v) => is_numeric($v) ? $v : 0)) * 100 : 0) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endisset
</div>
@endisset

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">{{ $tableTitle ?? 'Records' }}</h3>
        @isset($totalLabel)<span class="text-xs text-gray-400">{{ $totalLabel }}</span>@endisset
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                @foreach($columns as $col)
                <th class="px-5 py-2.5 font-medium {{ $col['align'] ?? 'left' }}">{{ $col['label'] }}</th>
                @endforeach
            </tr></thead>
            <tbody>
            @forelse($data as $row)
            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                @foreach($columns as $col)
                @php $val = data_get($row, $col['key']); @endphp
                <td class="px-5 py-2.5 text-xs {{ $col['align'] ?? 'left' }} {{ $col['class'] ?? 'text-gray-700' }}">
                    @if(isset($col['format']) && $col['format'] === 'money')
                        <span class="font-semibold">{{ $money($val) }}</span>
                    @elseif(isset($col['format']) && $col['format'] === 'date')
                        {{ optional($val)->format('d M Y') }}
                    @elseif(isset($col['format']) && $col['format'] === 'badge')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-100 capitalize">{{ str_replace('_', ' ', $val) }}</span>
                    @else
                        {{ $val ?? 'N/A' }}
                    @endif
                </td>
                @endforeach
            </tr>
            @empty
            <tr><td colspan="{{ count($columns) }}" class="px-5 py-8 text-center text-gray-400 text-xs">No records found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($data, 'links'))
    <div class="px-5 py-3 border-t">{{ $data->links() }}</div>
    @endif
</div>
@endsection
