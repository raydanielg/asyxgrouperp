@extends('layouts.admin')

@section('title', 'Settlements - ' . $project->title)
@section('page_title', 'Project Settlements')
@section('page_actions')
    <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">← Back to Project</a>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Project Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $project->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Project #: {{ $project->project_number }}
                    @if($project->start_date) | Start: {{ $project->start_date->format('d M Y') }} @endif
                    @if($project->due_date) | End: {{ $project->due_date->format('d M Y') }} @endif
                    @if($project->recurring_invoicing) | <span class="text-bronze font-semibold">Recurring Invoicing: {{ number_format($project->billing_amount, 0) }} TZS/month</span> @endif
                </p>
            </div>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $project->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($project->status === 'in_progress' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total Invoiced</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ number_format($totals['invoiced'], 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total Received</p>
            <p class="text-xl font-bold text-emerald-600 mt-1">{{ number_format($totals['received'], 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Outstanding</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ number_format($totals['outstanding'], 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS (invoiced - paid)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total Costs</p>
            <p class="text-xl font-bold text-red-600 mt-1">{{ number_format($totals['total_cost'], 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS (vendor + office + staff)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Net Settlement</p>
            <p class="text-xl font-bold {{ $totals['net'] >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1">{{ number_format($totals['net'], 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS ({{ number_format($totals['margin'], 1) }}% margin)</p>
        </div>
    </div>

    {{-- Cost Breakdown --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs font-semibold text-gray-500 mb-2">Vendor / Procurement Costs</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totals['vendor_cost'], 0) }} <span class="text-xs text-gray-400">TZS</span></p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs font-semibold text-gray-500 mb-2">Office Expenses</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totals['office_cost'], 0) }} <span class="text-xs text-gray-400">TZS</span></p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs font-semibold text-gray-500 mb-2">Staff Costs ({{ number_format($totals['staff_hours'], 0) }} hrs)</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totals['staff_cost'], 0) }} <span class="text-xs text-gray-400">TZS</span></p>
        </div>
        <div class="bg-white rounded-xl border p-4">
            <p class="text-xs font-semibold text-gray-500 mb-2">Staff Bonuses</p>
            <p class="text-2xl font-bold text-amber-600">{{ number_format($totals['bonus_cost'], 0) }} <span class="text-xs text-gray-400">TZS</span></p>
        </div>
    </div>

    {{-- Monthly Settlements Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-700">Monthly Settlement Breakdown</h3>
            <p class="text-[10px] text-gray-400 mt-0.5">Kila mwezi: invoiced, received, costs, na net settlement</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50 border-b">
                        <th class="px-4 py-3 font-medium">Month</th>
                        <th class="px-4 py-3 font-medium text-right">Invoiced</th>
                        <th class="px-4 py-3 font-medium text-right">Received</th>
                        <th class="px-4 py-3 font-medium text-right">Outstanding</th>
                        <th class="px-4 py-3 font-medium text-right">Vendor Costs</th>
                        <th class="px-4 py-3 font-medium text-right">Office Exp.</th>
                        <th class="px-4 py-3 font-medium text-right">Staff (hrs)</th>
                        <th class="px-4 py-3 font-medium text-right">Bonuses</th>
                        <th class="px-4 py-3 font-medium text-right">Total Costs</th>
                        <th class="px-4 py-3 font-medium text-right">Net</th>
                        <th class="px-4 py-3 font-medium text-center">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($months as $m)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-semibold text-gray-700">{{ $m['label'] }}</td>
                        <td class="px-4 py-3 text-xs text-right text-blue-600 font-medium">{{ $m['invoiced'] > 0 ? number_format($m['invoiced'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-emerald-600 font-medium">{{ $m['received'] > 0 ? number_format($m['received'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right {{ $m['outstanding'] > 0 ? 'text-amber-600 font-medium' : 'text-gray-400' }}">{{ $m['outstanding'] > 0 ? number_format($m['outstanding'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-600">{{ $m['vendor_cost'] > 0 ? number_format($m['vendor_cost'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-600">{{ $m['office_cost'] > 0 ? number_format($m['office_cost'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-600">{{ $m['staff_hours'] > 0 ? number_format($m['staff_hours'], 0) . 'h (' . number_format($m['staff_cost'], 0) . ')' : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-amber-600">{{ $m['bonus_cost'] > 0 ? number_format($m['bonus_cost'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-red-600 font-medium">{{ $m['total_cost'] > 0 ? number_format($m['total_cost'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold {{ $m['net'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $m['net'] != 0 ? number_format($m['net'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($m['invoiced'] > 0 || $m['received'] > 0 || $m['vendor_cost'] > 0 || $m['office_cost'] > 0 || $m['staff_hours'] > 0)
                            <button onclick="toggleMonth('month-{{ $m['key'] }}')" class="text-bronze text-xs hover:underline font-semibold">Expand</button>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    <tr id="month-{{ $m['key'] }}" class="hidden bg-gray-50/30">
                        <td colspan="11" class="px-4 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- Invoices --}}
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-2">Invoices ({{ $m['invoices']->count() }})</p>
                                    @if($m['invoices']->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($m['invoices'] as $inv)
                                        <div class="flex justify-between text-xs">
                                            <a href="{{ route('admin.sales-invoices.show', $inv) }}" class="text-blue-600 hover:underline">{{ $inv->invoice_number }}</a>
                                            <span class="text-gray-600">{{ number_format($inv->total_amount, 0) }} TZS</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 pl-2">Paid: {{ number_format($inv->paid_amount, 0) }} | Balance: {{ number_format($inv->balance_amount, 0) }} | {{ ucfirst($inv->status) }}</p>
                                        @endforeach
                                    </div>
                                    @else
                                    <p class="text-xs text-gray-400">No invoices this month</p>
                                    @endif
                                </div>
                                {{-- Receipts --}}
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-2">Client Receipts ({{ $m['receipts']->count() }})</p>
                                    @if($m['receipts']->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($m['receipts'] as $r)
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-700">{{ $r->receipt_number }}</span>
                                            <span class="text-emerald-600 font-medium">{{ number_format($r->amount, 0) }} TZS</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 pl-2">{{ $r->client_name }} | {{ $r->receipt_date->format('d M') }} | {{ $r->payment_method }}</p>
                                        @endforeach
                                    </div>
                                    @else
                                    <p class="text-xs text-gray-400">No receipts this month</p>
                                    @endif
                                </div>
                                {{-- Vendor Invoices --}}
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-gray-400 mb-2">Vendor Invoices ({{ $m['vendor_invoices']->count() }})</p>
                                    @if($m['vendor_invoices']->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($m['vendor_invoices'] as $vi)
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-700">{{ $vi->vendor_invoice_number }}</span>
                                            <span class="text-red-600 font-medium">{{ number_format($vi->total, 0) }} TZS</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 pl-2">Paid: {{ number_format($vi->amount_paid, 0) }} | {{ ucfirst($vi->status) }}</p>
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
                    <tr><td colspan="11" class="px-4 py-12 text-center text-gray-400 text-sm">No settlement data available for this project</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50">
                        <td class="px-4 py-3 text-xs font-bold text-gray-900">TOTALS</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-blue-600">{{ number_format($totals['invoiced'], 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-emerald-600">{{ number_format($totals['received'], 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-amber-600">{{ number_format($totals['outstanding'], 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-gray-700">{{ number_format($totals['vendor_cost'], 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-gray-700">{{ number_format($totals['office_cost'], 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-gray-700">{{ number_format($totals['staff_hours'], 0) }}h ({{ number_format($totals['staff_cost'], 0) }})</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-amber-600">{{ number_format($totals['bonus_cost'], 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-red-600">{{ number_format($totals['total_cost'], 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold {{ $totals['net'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($totals['net'], 0) }}</td>
                        <td class="px-4 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-xs text-blue-700">
                <p class="font-semibold mb-1">How settlements work:</p>
                <ul class="space-y-0.5 list-disc list-inside">
                    <li><strong>Invoiced</strong> = Total amount billed to client that month (recurring invoices)</li>
                    <li><strong>Received</strong> = Actual payments received from client (client receipts)</li>
                    <li><strong>Outstanding</strong> = Invoiced but not yet paid by client</li>
                    <li><strong>Vendor Costs</strong> = Procurement / supplier invoices for this project</li>
                    <li><strong>Office Expenses</strong> = Approved office expenses linked to this project</li>
                    <li><strong>Staff Costs</strong> = Calculated from timesheet hours × employee hourly rate (salary ÷ 160 hrs)</li>
                    <li><strong>Bonuses</strong> = Approved/paid employee bonuses linked to this project</li>
                    <li><strong>Net</strong> = Received − Total Costs (actual profit for that month)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMonth(id) {
    const row = document.getElementById(id);
    row.classList.toggle('hidden');
}
</script>
@endsection
