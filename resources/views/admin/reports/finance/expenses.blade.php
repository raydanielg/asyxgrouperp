@extends('admin.reports.layout')

@section('pageTitle', 'Expense Report')
@section('pageSubtitle', 'All expenses by category and period')

@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-amber-50">Total Expenses</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($total) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-red-400 to-red-500 rounded-xl border border-red-300 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-red-50">This Month</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($monthTotal) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-sky-100">Avg / Month</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($byMonth->avg('total') ?? 0) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Breakdown</h3>
        <div class="space-y-3">
            @foreach($byMonth as $m)
            @php $monthName = date('M Y', mktime(0,0,0,$m->month,1,$m->year)); @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-gray-700">{{ $monthName }}</span>
                    <span class="text-xs font-bold text-amber-600">{{ $money($m->total) }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full" style="width: {{ ($byMonth->max('total') > 0 ? ($m->total / $byMonth->max('total')) * 100 : 0) }}%"></div>
                </div>
            </div>
            @endforeach
            @if($byMonth->isEmpty())
            <p class="text-xs text-gray-400 text-center py-4">No data available</p>
            @endif
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-semibold text-gray-900">Expense Entries</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Date</th>
                    <th class="px-5 py-2.5 font-medium">Description</th>
                    <th class="px-5 py-2.5 font-medium text-right">Amount</th>
                </tr></thead>
                <tbody>
                @forelse($expenses as $exp)
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
        {{ $expenses->links() }}
    </div>
</div>
@endsection
