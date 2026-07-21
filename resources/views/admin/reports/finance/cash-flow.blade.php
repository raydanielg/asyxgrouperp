@extends('admin.reports.layout')

@section('pageTitle', 'Cash Flow Statement')
@section('pageSubtitle', 'Cash inflows and outflows (last 30 days)')

@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-emerald-100">Total Inflows</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($totalIn) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-amber-50">Total Outflows</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($totalOut) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-{{ $netFlow >= 0 ? 'sky-500' : 'red-500' }} to-{{ $netFlow >= 0 ? 'sky-600' : 'red-600' }} rounded-xl border border-{{ $netFlow >= 0 ? 'sky-400' : 'red-400' }} p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-{{ $netFlow >= 0 ? 'sky-100' : 'red-100' }}">Net Cash Flow</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($netFlow) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b bg-emerald-50/50">
            <h3 class="text-sm font-semibold text-gray-900">Cash Inflows (Last 30 days)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Date</th>
                    <th class="px-5 py-2.5 font-medium text-right">Amount</th>
                </tr></thead>
                <tbody>
                @forelse($inflows as $in)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ $in->date }}</td>
                    <td class="px-5 py-2.5 text-xs font-semibold text-emerald-700 text-right">{{ $money($in->amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-5 py-8 text-center text-gray-400 text-xs">No inflow records</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b bg-amber-50/50">
            <h3 class="text-sm font-semibold text-gray-900">Cash Outflows (Last 30 days)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Date</th>
                    <th class="px-5 py-2.5 font-medium text-right">Amount</th>
                </tr></thead>
                <tbody>
                @forelse($outflows as $out)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ $out->date }}</td>
                    <td class="px-5 py-2.5 text-xs font-semibold text-amber-700 text-right">{{ $money($out->amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-5 py-8 text-center text-gray-400 text-xs">No outflow records</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
