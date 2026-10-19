@extends('layouts.admin')
@section('title', 'Profit & Loss - ' . config('app.name'))
@section('page_title', 'Profit & Loss')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.financial-reports.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Reports</a>
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="date" name="from" value="{{ $from }}" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
        <input type="date" name="to" value="{{ $to }}" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
        <select name="project_id" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Projects</option>
            @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected($projectId == $p->id)>{{ $p->title }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Run</button>
    </form>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Revenue</p><p class="text-lg font-bold text-emerald-600 mt-1">TZS {{ number_format($total_revenue, 2) }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Expenses</p><p class="text-lg font-bold text-red-500 mt-1">TZS {{ number_format($total_expense, 2) }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Net Profit</p><p class="text-lg font-bold {{ $net_profit >= 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">TZS {{ number_format($net_profit, 2) }}</p></div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Revenue</h3></div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
            @forelse($revenue as $r)
            <tr><td class="px-5 py-3 text-xs text-gray-700">{{ $r->code }} - {{ $r->name }}</td><td class="px-5 py-3 text-xs text-right font-mono text-emerald-600">{{ number_format($r->amount, 2) }}</td></tr>
            @empty
            <tr><td colspan="2" class="px-5 py-6 text-center text-gray-400 text-xs">No revenue in this period</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Expenses</h3></div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
            @forelse($expenses as $e)
            <tr><td class="px-5 py-3 text-xs text-gray-700">{{ $e->code }} - {{ $e->name }}</td><td class="px-5 py-3 text-xs text-right font-mono text-red-500">{{ number_format($e->amount, 2) }}</td></tr>
            @empty
            <tr><td colspan="2" class="px-5 py-6 text-center text-gray-400 text-xs">No expenses in this period</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
