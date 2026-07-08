@extends('layouts.admin')
@section('title', 'Financial Reports - ' . config('app.name'))
@section('page_title', 'Financial Reports')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('admin.financial-reports.trial-balance') }}" class="bg-white rounded-xl border p-5 hover:border-emerald-400 transition-colors">
        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900">Trial Balance</h3>
        <p class="text-xs text-gray-500 mt-1">Debit/credit balance of every ledger account as of a given date.</p>
    </a>
    <a href="{{ route('admin.financial-reports.profit-loss') }}" class="bg-white rounded-xl border p-5 hover:border-emerald-400 transition-colors">
        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900">Profit &amp; Loss</h3>
        <p class="text-xs text-gray-500 mt-1">Revenue vs expenses over a date range, overall or per project.</p>
    </a>
    <a href="{{ route('admin.journal-entries.index') }}" class="bg-white rounded-xl border p-5 hover:border-emerald-400 transition-colors">
        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900">General Ledger</h3>
        <p class="text-xs text-gray-500 mt-1">Every journal entry posted across the system, filterable by project.</p>
    </a>
</div>
@endsection
