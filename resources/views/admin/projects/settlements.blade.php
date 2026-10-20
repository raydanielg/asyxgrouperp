@extends('layouts.admin')

@section('title', 'Settlements - ' . $project->title)
@section('page_title', 'Project Settlements')

@section('content')
@php $psc = ['completed'=>'#10b981','in_progress'=>'#f59e0b','planning'=>'#3b82f6','on_hold'=>'#9ca3af','cancelled'=>'#ef4444'][$project->status] ?? '#9ca3af'; @endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-400 hover:text-gray-700">← Back to Project</a>
</div>

{{-- Project Header --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-3">
    <div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-mono text-sm font-bold text-gray-900">{{ $project->project_number }}</span>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $psc }}"></span>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
        </div>
        <h2 class="text-base font-bold text-gray-900 mt-1">{{ $project->title }}</h2>
        <p class="text-xs text-gray-400 mt-0.5">
            @if($project->start_date){{ $project->start_date->format('d M Y') }}@endif
            @if($project->due_date) — {{ $project->due_date->format('d M Y') }}@endif
            @if($project->recurring_invoicing) · Recurring: {{ number_format($project->billing_amount, 0) }} TZS/mo@endif
        </p>
    </div>
</div>

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="grid grid-cols-2 md:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Invoiced</p><p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($totals['invoiced'], 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Received</p><p class="text-lg font-bold text-emerald-600 mt-1">{{ number_format($totals['received'], 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Outstanding</p><p class="text-lg font-bold text-amber-600 mt-1">{{ number_format($totals['outstanding'], 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Costs</p><p class="text-lg font-bold text-red-500 mt-1">{{ number_format($totals['total_cost'], 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Net · {{ number_format($totals['margin'], 1) }}%</p><p class="text-lg font-bold {{ $totals['net'] >= 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">{{ number_format($totals['net'], 0) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></p></div>
    </div>
</div>

{{-- Cost breakdown --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Cost Breakdown</h3></div>
    <div class="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
        <div class="px-5 py-4"><p class="text-[11px] text-gray-500">Vendor / Procurement</p><p class="text-base font-bold font-mono text-gray-900 mt-1">{{ number_format($totals['vendor_cost'], 0) }}</p></div>
        <div class="px-5 py-4"><p class="text-[11px] text-gray-500">Office Expenses</p><p class="text-base font-bold font-mono text-gray-900 mt-1">{{ number_format($totals['office_cost'], 0) }}</p></div>
        <div class="px-5 py-4"><p class="text-[11px] text-gray-500">Staff · {{ number_format($totals['staff_hours'], 0) }} hrs</p><p class="text-base font-bold font-mono text-gray-900 mt-1">{{ number_format($totals['staff_cost'], 0) }}</p></div>
        <div class="px-5 py-4"><p class="text-[11px] text-gray-500">Bonuses</p><p class="text-base font-bold font-mono text-amber-600 mt-1">{{ number_format($totals['bonus_cost'], 0) }}</p></div>
    </div>
</div>

{{-- Monthly table --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Monthly Breakdown</h3>
        <span class="text-[10px] text-gray-400">Invoiced · received · costs · net per month</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Month</th>
                    <th class="px-5 py-3 font-semibold text-right">Invoiced</th>
                    <th class="px-5 py-3 font-semibold text-right">Received</th>
                    <th class="px-5 py-3 font-semibold text-right">Outstanding</th>
                    <th class="px-5 py-3 font-semibold text-right">Vendor</th>
                    <th class="px-5 py-3 font-semibold text-right">Office</th>
                    <th class="px-5 py-3 font-semibold text-right">Staff</th>
                    <th class="px-5 py-3 font-semibold text-right">Bonuses</th>
                    <th class="px-5 py-3 font-semibold text-right">Costs</th>
                    <th class="px-5 py-3 font-semibold text-right">Net</th>
                    <th class="px-5 py-3 font-semibold text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($months as $m)
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $m['label'] }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-700">{{ $m['invoiced'] > 0 ? number_format($m['invoiced'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $m['received'] > 0 ? 'text-emerald-600' : 'text-gray-300' }}">{{ $m['received'] > 0 ? number_format($m['received'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $m['outstanding'] > 0 ? 'text-amber-600' : 'text-gray-300' }}">{{ $m['outstanding'] > 0 ? number_format($m['outstanding'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $m['vendor_cost'] > 0 ? 'text-gray-700' : 'text-gray-300' }}">{{ $m['vendor_cost'] > 0 ? number_format($m['vendor_cost'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $m['office_cost'] > 0 ? 'text-gray-700' : 'text-gray-300' }}">{{ $m['office_cost'] > 0 ? number_format($m['office_cost'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $m['staff_cost'] > 0 ? 'text-gray-700' : 'text-gray-300' }}">{{ $m['staff_hours'] > 0 ? number_format($m['staff_hours'], 0) . 'h' : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $m['bonus_cost'] > 0 ? 'text-amber-600' : 'text-gray-300' }}">{{ $m['bonus_cost'] > 0 ? number_format($m['bonus_cost'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $m['total_cost'] > 0 ? 'text-red-500' : 'text-gray-300' }}">{{ $m['total_cost'] > 0 ? number_format($m['total_cost'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono font-bold {{ $m['net'] != 0 ? ($m['net'] >= 0 ? 'text-emerald-600' : 'text-red-500') : 'text-gray-300' }}">{{ $m['net'] != 0 ? number_format($m['net'], 0) : '—' }}</td>
                    <td class="px-5 py-3.5 text-right">
                        @if($m['invoiced'] > 0 || $m['received'] > 0 || $m['vendor_cost'] > 0 || $m['office_cost'] > 0 || $m['staff_hours'] > 0)
                        <button onclick="toggleMonth('month-{{ $m['key'] }}')" class="text-xs font-medium text-gray-600 hover:text-gray-900">Expand</button>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                <tr id="month-{{ $m['key'] }}" class="hidden bg-gray-50/50">
                    <td colspan="11" class="px-5 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Invoices ({{ $m['invoices']->count() }})</p>
                                @if($m['invoices']->isNotEmpty())
                                <div class="space-y-1.5">
                                    @foreach($m['invoices'] as $inv)
                                    <div class="text-xs">
                                        <div class="flex justify-between">
                                            <a href="{{ route('admin.sales-invoices.show', $inv) }}" class="font-mono text-gray-700 hover:text-gray-900">{{ $inv->invoice_number }}</a>
                                            <span class="font-mono text-gray-900">{{ number_format($inv->total_amount, 0) }}</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400">Paid {{ number_format($inv->paid_amount, 0) }} · Balance {{ number_format($inv->balance_amount, 0) }} · {{ ucfirst($inv->status) }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-xs text-gray-400">No invoices this month</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Client Receipts ({{ $m['receipts']->count() }})</p>
                                @if($m['receipts']->isNotEmpty())
                                <div class="space-y-1.5">
                                    @foreach($m['receipts'] as $r)
                                    <div class="text-xs">
                                        <div class="flex justify-between">
                                            <span class="font-mono text-gray-700">{{ $r->receipt_number }}</span>
                                            <span class="font-mono text-emerald-600">+{{ number_format($r->amount, 0) }}</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400">{{ $r->client_name }} · {{ $r->receipt_date->format('d M') }} · {{ $r->payment_method }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-xs text-gray-400">No receipts this month</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Vendor Invoices ({{ $m['vendor_invoices']->count() }})</p>
                                @if($m['vendor_invoices']->isNotEmpty())
                                <div class="space-y-1.5">
                                    @foreach($m['vendor_invoices'] as $vi)
                                    <div class="text-xs">
                                        <div class="flex justify-between">
                                            <span class="font-mono text-gray-700">{{ $vi->vendor_invoice_number }}</span>
                                            <span class="font-mono text-red-500">-{{ number_format($vi->total, 0) }}</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400">Paid {{ number_format($vi->amount_paid, 0) }} · {{ ucfirst($vi->status) }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-xs text-gray-400">No vendor invoices this month</p>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" class="px-5 py-10 text-center text-gray-400 text-xs">No settlement data available for this project</td></tr>
                @endforelse
            </tbody>
            @if(count($months))
            <tfoot>
                <tr class="border-t-2 border-gray-800 font-bold">
                    <td class="px-5 py-3.5 text-xs">TOTALS</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($totals['invoiced'], 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-emerald-600">{{ number_format($totals['received'], 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-amber-600">{{ number_format($totals['outstanding'], 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($totals['vendor_cost'], 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($totals['office_cost'], 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($totals['staff_hours'], 0) }}h</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono">{{ number_format($totals['bonus_cost'], 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-red-500">{{ number_format($totals['total_cost'], 0) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $totals['net'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ number_format($totals['net'], 0) }}</td>
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
        <p><span class="font-medium text-gray-700">Invoiced</span> — billed to client that month</p>
        <p><span class="font-medium text-gray-700">Received</span> — payments received (client receipts)</p>
        <p><span class="font-medium text-gray-700">Outstanding</span> — invoiced but not yet paid</p>
        <p><span class="font-medium text-gray-700">Staff Costs</span> — timesheet hours × hourly rate (salary ÷ 160)</p>
        <p><span class="font-medium text-gray-700">Bonuses</span> — approved/paid bonuses on this project</p>
        <p><span class="font-medium text-gray-700">Net</span> — Received − Total Costs (actual profit)</p>
    </div>
</div>

<script>
function toggleMonth(id) {
    document.getElementById(id).classList.toggle('hidden');
}
</script>
@endsection
