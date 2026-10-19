@extends('layouts.admin')
@section('title', 'Financial Reports - ' . config('app.name'))
@section('page_title', 'Financial Reports')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200"><span class="text-xs text-gray-400">Select a report to run</span></div>
    <div class="divide-y divide-gray-100">
        <a href="{{ route('admin.financial-reports.trial-balance') }}" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50/60 transition-colors">
            <div>
                <h3 class="text-sm font-medium text-gray-900">Trial Balance</h3>
                <p class="text-xs text-gray-400 mt-0.5">Debit/credit balance of every ledger account as of a given date.</p>
            </div>
            <span class="text-xs text-gray-400">&rarr;</span>
        </a>
        <a href="{{ route('admin.financial-reports.profit-loss') }}" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50/60 transition-colors">
            <div>
                <h3 class="text-sm font-medium text-gray-900">Profit &amp; Loss</h3>
                <p class="text-xs text-gray-400 mt-0.5">Revenue vs expenses over a date range, overall or per project.</p>
            </div>
            <span class="text-xs text-gray-400">&rarr;</span>
        </a>
        <a href="{{ route('admin.journal-entries.index') }}" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50/60 transition-colors">
            <div>
                <h3 class="text-sm font-medium text-gray-900">General Ledger</h3>
                <p class="text-xs text-gray-400 mt-0.5">Every journal entry posted across the system, filterable by project.</p>
            </div>
            <span class="text-xs text-gray-400">&rarr;</span>
        </a>
    </div>
</div>
@endsection
