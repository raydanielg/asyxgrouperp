@extends('layouts.admin')
@section('title', 'Companies')
@section('page_title', 'Group Companies')
@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n ?? 0);
    // Group oversight — MD sees ALL companies (not just their own).
    $cos = \App\Models\Company::where('is_active', true)->orderBy('name')->get();
    $rows = $cos->map(function ($co) {
        return [
            'co' => $co,
            'employees' => \App\Models\Employee::withoutGlobalScopes()->where('company_id', $co->id)->count(),
            'users' => \App\Models\User::withoutGlobalScopes()->where('company_id', $co->id)->count(),
            'revenue' => \App\Models\Revenue::withoutGlobalScopes()->where('company_id', $co->id)->sum('amount'),
            'expenses' => \App\Models\Expense::withoutGlobalScopes()->where('company_id', $co->id)->sum('amount'),
            'sales' => \App\Models\SalesInvoice::withoutGlobalScopes()->where('company_id', $co->id)->sum('total_amount'),
            'receivable' => \App\Models\SalesInvoice::withoutGlobalScopes()->where('company_id', $co->id)->sum('balance_amount'),
            'projects' => \App\Models\Project::withoutGlobalScopes()->where('company_id', $co->id)->count(),
            'activeProjects' => \App\Models\Project::withoutGlobalScopes()->where('company_id', $co->id)->where('status', 'in_progress')->count(),
            'openDeals' => \App\Models\CrmDeal::withoutGlobalScopes()->where('company_id', $co->id)->where('status', 'open')->count(),
        ];
    });
    $totRev = $rows->sum('revenue');
    $totExp = $rows->sum('expenses');
@endphp

{{-- Group summary band --}}
<div class="bg-slate-800 rounded-md p-5 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Group Oversight</p>
            <h2 class="text-lg font-bold">{{ $rows->count() }} {{ Str::plural('company', $rows->count()) }}</h2>
        </div>
        <div class="flex gap-6 text-right">
            <div><p class="text-[9px] uppercase text-slate-400">Group revenue</p><p class="text-base font-bold">{{ $money($totRev) }}</p></div>
            <div><p class="text-[9px] uppercase text-slate-400">Group expenses</p><p class="text-base font-bold">{{ $money($totExp) }}</p></div>
            <div><p class="text-[9px] uppercase text-slate-400">Net</p><p class="text-base font-bold {{ $totRev - $totExp >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $money($totRev - $totExp) }}</p></div>
        </div>
    </div>
</div>

{{-- Per-company cards --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-5">
    @foreach($rows as $r)
    @php $net = $r['revenue'] - $r['expenses']; @endphp
    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-md bg-slate-800 text-white flex items-center justify-center text-sm font-bold">{{ strtoupper(substr($r['co']->short_code ?? $r['co']->name, 0, 2)) }}</span>
                <div>
                    <p class="text-sm font-bold text-gray-900">{{ $r['co']->name }}</p>
                    <p class="text-[10px] text-gray-400">{{ $r['co']->legal_name }} · {{ $r['employees'] }} staff · {{ $r['users'] }} users</p>
                </div>
            </div>
            @if($r['co']->is_group)<span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-medium">GROUP HQ</span>@endif
        </div>
        <div class="px-5 py-4 grid grid-cols-3 gap-3">
            <div><p class="text-[9px] uppercase text-gray-400">Revenue</p><p class="text-sm font-bold text-gray-900">{{ $money($r['revenue']) }}</p></div>
            <div><p class="text-[9px] uppercase text-gray-400">Expenses</p><p class="text-sm font-bold text-gray-900">{{ $money($r['expenses']) }}</p></div>
            <div><p class="text-[9px] uppercase text-gray-400">Net</p><p class="text-sm font-bold {{ $net >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $money($net) }}</p></div>
            <div><p class="text-[9px] uppercase text-gray-400">Invoiced</p><p class="text-sm font-bold text-gray-900">{{ $money($r['sales']) }}</p></div>
            <div><p class="text-[9px] uppercase text-gray-400">Receivable</p><p class="text-sm font-bold {{ $r['receivable'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ $money($r['receivable']) }}</p></div>
            <div><p class="text-[9px] uppercase text-gray-400">Projects</p><p class="text-sm font-bold text-gray-900">{{ $r['activeProjects'] }}<span class="text-xs text-gray-400 font-normal"> / {{ $r['projects'] }}</span></p></div>
        </div>
        <div class="px-5 py-3 border-t border-gray-50 flex items-center justify-between">
            <span class="inline-flex items-center gap-1.5 text-[10px] text-gray-500"><span style="width:6px;height:6px;border-radius:99px;background:{{ $r['openDeals'] > 0 ? '#f59e0b' : '#94a3b8' }}"></span>{{ $r['openDeals'] }} open {{ Str::plural('deal', $r['openDeals']) }}</span>
            <a href="{{ route('role.page', ['module' => 'intercompany']) }}" class="text-[10px] text-slate-600 hover:text-slate-800">Intercompany →</a>
        </div>
    </div>
    @endforeach
</div>

{{-- Comparison table --}}
<div class="bg-white rounded-md border border-gray-200">
    <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-800">Company Comparison</h3></div>
    <table class="w-full">
        <thead><tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
            <th class="px-5 py-2.5">Company</th><th class="px-5 py-2.5 text-right">Revenue</th><th class="px-5 py-2.5 text-right">Expenses</th>
            <th class="px-5 py-2.5 text-right">Net</th><th class="px-5 py-2.5 text-right">Receivable</th><th class="px-5 py-2.5">Performance</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($rows->sortByDesc('revenue') as $r)
            @php
                $net = $r['revenue'] - $r['expenses'];
                $pct = $totRev > 0 ? round($r['revenue'] / $totRev * 100) : 0;
            @endphp
            <tr>
                <td class="px-5 py-3 text-xs font-medium text-gray-800">{{ $r['co']->name }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono">{{ $money($r['revenue']) }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ $money($r['expenses']) }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono {{ $net >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $money($net) }}</td>
                <td class="px-5 py-3 text-xs text-right font-mono text-gray-500">{{ $money($r['receivable']) }}</td>
                <td class="px-5 py-3 w-40">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-1.5"><div class="bg-slate-700 h-1.5 rounded-full" style="width:{{ $pct }}%"></div></div>
                        <span class="text-[10px] text-gray-500 w-8">{{ $pct }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
