@extends('admin.reports.layout')

@section('pageTitle', 'Profit & Loss Statement')
@section('pageSubtitle', 'Revenue, expenses and net profit overview')

@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label' => 'Total Revenue', 'value' => $money($totalRevenues + $totalSales), 'from' => 'emerald-600', 'to' => 'emerald-700', 'border' => 'emerald-500', 'text' => 'emerald-100', 'sub' => 'All time', 'sub_color' => 'emerald-200'],
        ['label' => 'Total Expenses', 'value' => $money($totalExpenses + $totalPurchases), 'from' => 'amber-400', 'to' => 'amber-500', 'border' => 'amber-300', 'text' => 'amber-50', 'sub' => 'All time', 'sub_color' => 'amber-100'],
        ['label' => 'Gross Profit', 'value' => $money($grossProfit), 'from' => $grossProfit >= 0 ? 'sky-500' : 'red-500', 'to' => $grossProfit >= 0 ? 'sky-600' : 'red-600', 'border' => $grossProfit >= 0 ? 'sky-400' : 'red-400', 'text' => 'sky-100', 'sub' => 'All time', 'sub_color' => 'sky-200'],
        ['label' => 'Month Profit', 'value' => $money($monthProfit), 'from' => $monthProfit >= 0 ? 'violet-500' : 'red-500', 'to' => $monthProfit >= 0 ? 'violet-600' : 'red-600', 'border' => $monthProfit >= 0 ? 'violet-400' : 'red-400', 'text' => 'violet-100', 'sub' => 'This month', 'sub_color' => 'violet-200'],
    ] as $card)
    <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
            <p class="text-xl font-bold tracking-tight text-white mt-1">{{ $card['value'] }}</p>
            <p class="text-[10px] {{ $card['sub_color'] }} font-medium mt-1">{{ $card['sub'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b bg-emerald-50/50">
            <h3 class="text-sm font-semibold text-gray-900">Recent Revenues</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Date</th>
                    <th class="px-5 py-2.5 font-medium">Description</th>
                    <th class="px-5 py-2.5 font-medium text-right">Amount</th>
                </tr></thead>
                <tbody>
                @forelse($recentRevenues as $rev)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ optional($rev->revenue_date ?? $rev->created_at)->format('d M Y') }}</td>
                    <td class="px-5 py-2.5 text-xs text-gray-700">{{ $rev->description ?? 'N/A' }}</td>
                    <td class="px-5 py-2.5 text-xs font-semibold text-emerald-700 text-right">{{ $money($rev->amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No revenue records</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b bg-amber-50/50">
            <h3 class="text-sm font-semibold text-gray-900">Recent Expenses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Date</th>
                    <th class="px-5 py-2.5 font-medium">Description</th>
                    <th class="px-5 py-2.5 font-medium text-right">Amount</th>
                </tr></thead>
                <tbody>
                @forelse($recentExpenses as $exp)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ optional($exp->expense_date ?? $exp->created_at)->format('d M Y') }}</td>
                    <td class="px-5 py-2.5 text-xs text-gray-700">{{ $exp->description ?? 'N/A' }}</td>
                    <td class="px-5 py-2.5 text-xs font-semibold text-amber-700 text-right">{{ $money($exp->amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No expense records</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
