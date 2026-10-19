@extends('layouts.admin')
@section('title', 'Consolidated Report - ' . config('app.name'))
@section('page_title', 'Group Consolidated Report')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.companies.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Companies</a>
    <a href="{{ route('admin.intercompany.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800">Intercompany Transactions →</a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 mb-5">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 divide-x divide-y md:divide-y-0 divide-gray-100">
        <div class="p-5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Revenue (YTD)</p>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($totalRevenue, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="p-5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Expenses (YTD)</p>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($totalExpenses, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="p-5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Net Profit</p>
            <p class="text-lg font-bold {{ $totalRevenue - $totalExpenses >= 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">{{ number_format($totalRevenue - $totalExpenses, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="p-5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Receivables</p>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($totalReceivables, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="p-5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Cash Position</p>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($totalCashPosition, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="p-5">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">IC Pending</p>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ $intercompanyPending }}</p>
            <p class="text-[10px] text-gray-400">{{ number_format($intercompanyAmount, 0) }} unelim.</p>
        </div>
    </div>
</div>

{{-- Per-Company Breakdown --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Per-Company Breakdown</h3>
        <span class="text-[11px] text-gray-400">FY {{ date('Y') }} · TZS</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left border-b border-gray-200 bg-gray-50/50">
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Company</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Revenue</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Expenses</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Net Profit</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Receivables</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-right">Cash Position</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-center">Projects</th>
            <th class="px-5 py-3 text-[11px] font-semibold text-gray-500 uppercase tracking-wide text-center">Employees</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($consolidated as $row)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5">
                <a href="{{ route('admin.companies.show', $row['company']) }}" class="font-medium text-gray-900 hover:underline text-xs">{{ $row['company']->legal_name }}</a>
                <p class="text-[10px] font-mono text-gray-400 mt-0.5">{{ $row['company']->short_code }}</p>
            </td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-700">{{ number_format($row['revenue'], 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-700">{{ number_format($row['expenses'], 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-semibold {{ $row['profit'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($row['profit'], 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-700">{{ number_format($row['receivables'], 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-medium text-gray-700">{{ number_format($row['cash_position'], 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-center text-gray-500">{{ $row['projects'] }}</td>
            <td class="px-5 py-3.5 text-xs text-center text-gray-500">{{ $row['employees'] }}</td>
        </tr>
        @endforeach
        <tr class="border-t-2 border-gray-200 bg-gray-50/70">
            <td class="px-5 py-3.5 text-xs font-bold text-gray-900">GROUP TOTAL</td>
            <td class="px-5 py-3.5 text-xs text-right font-bold text-gray-900">{{ number_format($totalRevenue, 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-bold text-gray-900">{{ number_format($totalExpenses, 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-bold {{ $totalRevenue - $totalExpenses >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($totalRevenue - $totalExpenses, 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-bold text-gray-900">{{ number_format($totalReceivables, 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-right font-bold text-gray-900">{{ number_format($totalCashPosition, 0) }}</td>
            <td class="px-5 py-3.5 text-xs text-center font-bold text-gray-900">{{ $totalProjects }}</td>
            <td class="px-5 py-3.5 text-xs text-center font-bold text-gray-900">{{ $totalEmployees }}</td>
        </tr>
        </tbody>
    </table></div>
</div>
@endsection
