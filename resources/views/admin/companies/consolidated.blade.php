@extends('layouts.admin')
@section('title', 'Consolidated Report - ' . config('app.name'))
@section('page_title', 'Group Consolidated Report')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.companies.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Companies</a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Revenue (YTD)</p>
        <p class="text-2xl font-bold text-emerald-600">{{ number_format($totalRevenue, 0) }} TZS</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Expenses (YTD)</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($totalExpenses, 0) }} TZS</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Net Profit</p>
        <p class="text-2xl font-bold {{ $totalRevenue - $totalExpenses >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($totalRevenue - $totalExpenses, 0) }} TZS</p>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Intercompany Pending</p>
        <p class="text-2xl font-bold text-amber-600">{{ $intercompanyPending }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ number_format($intercompanyAmount, 0) }} TZS uneliminated</p>
    </div>
</div>

{{-- Per-Company Breakdown --}}
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-3 border-b bg-gray-50/50">
        <h3 class="text-sm font-bold text-gray-700">Per-Company Breakdown ({{ date('Y') }})</h3>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
            <th class="px-5 py-3 font-medium">Company</th>
            <th class="px-5 py-3 font-medium text-right">Revenue</th>
            <th class="px-5 py-3 font-medium text-right">Expenses</th>
            <th class="px-5 py-3 font-medium text-right">Net Profit</th>
            <th class="px-5 py-3 font-medium text-center">Projects</th>
            <th class="px-5 py-3 font-medium text-center">Employees</th>
        </tr></thead>
        <tbody>
        @foreach($consolidated as $row)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs">
                <a href="{{ route('admin.companies.show', $row['company']) }}" class="font-medium text-gray-800 hover:text-emerald-600">{{ $row['company']->legal_name }}</a>
                <span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-medium bg-emerald-50 text-emerald-700">{{ $row['company']->short_code }}</span>
            </td>
            <td class="px-5 py-3 text-xs text-right font-medium text-emerald-600">{{ number_format($row['revenue'], 0) }}</td>
            <td class="px-5 py-3 text-xs text-right font-medium text-red-600">{{ number_format($row['expenses'], 0) }}</td>
            <td class="px-5 py-3 text-xs text-right font-medium {{ $row['profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($row['profit'], 0) }}</td>
            <td class="px-5 py-3 text-xs text-center text-gray-600">{{ $row['projects'] }}</td>
            <td class="px-5 py-3 text-xs text-center text-gray-600">{{ $row['employees'] }}</td>
        </tr>
        @endforeach
        <tr class="border-t-2 border-gray-200 bg-gray-50">
            <td class="px-5 py-3 text-xs font-bold text-gray-800">GROUP TOTAL</td>
            <td class="px-5 py-3 text-xs text-right font-bold text-emerald-700">{{ number_format($totalRevenue, 0) }}</td>
            <td class="px-5 py-3 text-xs text-right font-bold text-red-700">{{ number_format($totalExpenses, 0) }}</td>
            <td class="px-5 py-3 text-xs text-right font-bold {{ $totalRevenue - $totalExpenses >= 0 ? 'text-emerald-700' : 'text-red-700' }}">{{ number_format($totalRevenue - $totalExpenses, 0) }}</td>
            <td class="px-5 py-3 text-xs text-center font-bold text-gray-700">{{ $totalProjects }}</td>
            <td class="px-5 py-3 text-xs text-center font-bold text-gray-700">{{ $totalEmployees }}</td>
        </tr>
        </tbody>
    </table></div>
</div>

<div class="flex gap-3">
    <a href="{{ route('admin.intercompany.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        Manage Intercompany Transactions
    </a>
</div>
@endsection
