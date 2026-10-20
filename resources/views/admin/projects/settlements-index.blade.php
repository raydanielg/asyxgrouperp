@extends('layouts.admin')

@section('title', 'Project Settlements')
@section('page_title', 'Project Settlements')

@section('content')
@php
    $totalInvoiced = array_sum(array_column($projectSummaries, 'invoiced'));
    $totalReceived = array_sum(array_column($projectSummaries, 'received'));
    $totalOutstanding = array_sum(array_column($projectSummaries, 'outstanding'));
    $totalCosts = array_sum(array_column($projectSummaries, 'total_cost'));
    $totalNet = array_sum(array_column($projectSummaries, 'net'));
@endphp

<p class="text-xs text-gray-400 mb-4">Track invoices vs receipts vs costs for each project — invoicing, costs, and net settlement</p>

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="grid grid-cols-2 md:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Invoiced</p><p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($totalInvoiced, 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Received</p><p class="text-lg font-bold text-emerald-600 mt-1">{{ number_format($totalReceived, 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Outstanding</p><p class="text-lg font-bold text-amber-600 mt-1">{{ number_format($totalOutstanding, 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Costs</p><p class="text-lg font-bold text-red-500 mt-1">{{ number_format($totalCosts, 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Net Settlement</p><p class="text-lg font-bold {{ $totalNet >= 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">{{ number_format($totalNet, 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
    </div>
</div>

{{-- Projects Table --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">All Projects — Settlement Summary</h3></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Project</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold">Recurring</th>
                    <th class="px-5 py-3 font-semibold text-right">Invoiced</th>
                    <th class="px-5 py-3 font-semibold text-right">Received</th>
                    <th class="px-5 py-3 font-semibold text-right">Outstanding</th>
                    <th class="px-5 py-3 font-semibold text-right">Vendor</th>
                    <th class="px-5 py-3 font-semibold text-right">Office</th>
                    <th class="px-5 py-3 font-semibold text-right">Costs</th>
                    <th class="px-5 py-3 font-semibold text-right">Net</th>
                    <th class="px-5 py-3 font-semibold text-right">Margin</th>
                    <th class="px-5 py-3 font-semibold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($projectSummaries as $p)
                @php $psc = ['completed'=>'#10b981','in_progress'=>'#f59e0b','planning'=>'#3b82f6','active'=>'#3b82f6','on_hold'=>'#9ca3af','cancelled'=>'#ef4444'][$p['status']] ?? '#9ca3af'; @endphp
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3.5">
                        <p class="text-xs font-medium text-gray-900">{{ $p['title'] }}</p>
                        <p class="text-[10px] font-mono text-gray-400">{{ $p['project_number'] }}</p>
                    </td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $psc }}"></span>{{ ucfirst(str_replace('_', ' ', $p['status'])) }}</span></td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">
                        @if($p['recurring']){{ number_format($p['billing_amount'], 0) }}/mo
                        @else<span class="text-gray-300">—</span>@endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-700">{{ $p['invoiced'] > 0 ? number_format($p['invoiced'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $p['received'] > 0 ? 'text-emerald-600' : 'text-gray-300' }}">{{ $p['received'] > 0 ? number_format($p['received'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $p['outstanding'] > 0 ? 'text-amber-600' : 'text-gray-300' }}">{{ $p['outstanding'] > 0 ? number_format($p['outstanding'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $p['vendor_cost'] > 0 ? 'text-gray-700' : 'text-gray-300' }}">{{ $p['vendor_cost'] > 0 ? number_format($p['vendor_cost'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $p['office_cost'] > 0 ? 'text-gray-700' : 'text-gray-300' }}">{{ $p['office_cost'] > 0 ? number_format($p['office_cost'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $p['total_cost'] > 0 ? 'text-red-500' : 'text-gray-300' }}">{{ $p['total_cost'] > 0 ? number_format($p['total_cost'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono font-bold {{ $p['net'] != 0 ? ($p['net'] >= 0 ? 'text-emerald-600' : 'text-red-500') : 'text-gray-300' }}">{{ $p['net'] != 0 ? number_format($p['net'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-400">{{ $p['margin'] != 0 ? number_format($p['margin'], 1) . '%' : '—' }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('admin.projects.settlements', $p['id']) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Breakdown →</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="12" class="px-5 py-10 text-center text-gray-400 text-xs">No projects found</td></tr>
                @endforelse
            </tbody>
            @if(count($projectSummaries))
            <tfoot>
                <tr class="border-t-2 border-gray-800 font-bold">
                    <td class="px-5 py-3.5 text-xs" colspan="3">TOTALS</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($totalInvoiced, 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-emerald-600">{{ number_format($totalReceived, 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-amber-600">{{ number_format($totalOutstanding, 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format(array_sum(array_column($projectSummaries, 'vendor_cost')), 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format(array_sum(array_column($projectSummaries, 'office_cost')), 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-red-500">{{ number_format($totalCosts, 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $totalNet >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($totalNet, 0) }}</td>
                    <td class="px-5 py-3.5"></td>
                    <td class="px-5 py-3.5"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- Legend --}}
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-4">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">How settlements work</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1 text-[11px] text-gray-500">
        <p><span class="font-medium text-gray-700">Invoiced</span> — billed to client (monthly invoices)</p>
        <p><span class="font-medium text-gray-700">Received</span> — payments received (client receipts)</p>
        <p><span class="font-medium text-gray-700">Outstanding</span> — invoiced but not yet paid</p>
        <p><span class="font-medium text-gray-700">Vendor / Office</span> — procurement and office expenses</p>
        <p><span class="font-medium text-gray-700">Staff Costs</span> — timesheet hours × hourly rate (salary ÷ 160)</p>
        <p><span class="font-medium text-gray-700">Net</span> — Received − Total Costs (actual profit)</p>
    </div>
</div>
@endsection
