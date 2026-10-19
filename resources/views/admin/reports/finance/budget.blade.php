@extends('admin.reports.layout')

@section('pageTitle', 'Budget vs Actual Report')
@section('pageSubtitle', 'Budgeted amounts compared against actual spending')

@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n ?? 0); @endphp

<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl border border-emerald-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-emerald-50">Total Budgeted</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($budgets->sum('amount')) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-amber-50">Total Actual</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($budgets->sum('actual_amount')) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-sky-100">Variance</span>
            <p class="text-xl font-bold text-white mt-1">{{ $money($budgets->sum('amount') - $budgets->sum('actual_amount')) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h3 class="text-sm font-semibold text-gray-900">Budgets</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-2.5 font-medium">Budget</th>
                <th class="px-5 py-2.5 font-medium">Period</th>
                <th class="px-5 py-2.5 font-medium">Cost Centre</th>
                <th class="px-5 py-2.5 font-medium">Status</th>
                <th class="px-5 py-2.5 font-medium text-right">Budgeted</th>
                <th class="px-5 py-2.5 font-medium text-right">Actual</th>
                <th class="px-5 py-2.5 font-medium text-right">Variance</th>
            </tr></thead>
            <tbody>
            @forelse($budgets as $budget)
            @php $variance = ($budget->amount ?? 0) - ($budget->actual_amount ?? 0); @endphp
            <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                <td class="px-5 py-2.5 text-xs font-medium text-gray-900">{{ $budget->name ?? 'Budget #' . $budget->id }}</td>
                <td class="px-5 py-2.5 text-xs text-gray-500">{{ $budget->period ?? '—' }}</td>
                <td class="px-5 py-2.5 text-xs text-gray-500">{{ $budget->cost_center ?? '—' }}</td>
                <td class="px-5 py-2.5"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($budget->status ?? '') === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-600' }}">{{ ucfirst($budget->status ?? 'draft') }}</span></td>
                <td class="px-5 py-2.5 text-xs font-semibold text-emerald-700 text-right">{{ $money($budget->amount) }}</td>
                <td class="px-5 py-2.5 text-xs font-semibold text-amber-700 text-right">{{ $money($budget->actual_amount) }}</td>
                <td class="px-5 py-2.5 text-xs font-semibold {{ $variance >= 0 ? 'text-emerald-700' : 'text-red-600' }} text-right">{{ $money($variance) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No budgets defined yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $budgets->links() }}
</div>
@endsection
