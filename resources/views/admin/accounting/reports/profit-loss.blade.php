@extends('layouts.admin')
@section('title', 'Profit & Loss - ' . config('app.name'))
@section('page_title', 'Profit & Loss')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.financial-reports.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Reports</a>
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="date" name="from" value="{{ $from }}" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
        <input type="date" name="to" value="{{ $to }}" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
        <select name="project_id" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
            <option value="">All Projects</option>
            @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected($projectId == $p->id)>{{ $p->title }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200">Run</button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl border p-5"><p class="text-[10px] text-gray-400 uppercase">Total Revenue</p><p class="text-xl font-bold text-emerald-700">TZS {{ number_format($total_revenue, 2) }}</p></div>
    <div class="bg-white rounded-xl border p-5"><p class="text-[10px] text-gray-400 uppercase">Total Expenses</p><p class="text-xl font-bold text-red-600">TZS {{ number_format($total_expense, 2) }}</p></div>
    <div class="bg-white rounded-xl border p-5"><p class="text-[10px] text-gray-400 uppercase">Net Profit</p><p class="text-xl font-bold {{ $net_profit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">TZS {{ number_format($net_profit, 2) }}</p></div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-800">Revenue</h3></div>
        <table class="w-full text-sm">
            <tbody>
            @forelse($revenue as $r)
            <tr class="border-t border-gray-100"><td class="px-5 py-2 text-xs text-gray-700">{{ $r->code }} - {{ $r->name }}</td><td class="px-5 py-2 text-xs text-right font-semibold text-emerald-700">{{ number_format($r->amount, 2) }}</td></tr>
            @empty
            <tr><td colspan="2" class="px-5 py-6 text-center text-gray-400 text-xs">No revenue in this period</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h3 class="text-sm font-bold text-gray-800">Expenses</h3></div>
        <table class="w-full text-sm">
            <tbody>
            @forelse($expenses as $e)
            <tr class="border-t border-gray-100"><td class="px-5 py-2 text-xs text-gray-700">{{ $e->code }} - {{ $e->name }}</td><td class="px-5 py-2 text-xs text-right font-semibold text-red-600">{{ number_format($e->amount, 2) }}</td></tr>
            @empty
            <tr><td colspan="2" class="px-5 py-6 text-center text-gray-400 text-xs">No expenses in this period</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
