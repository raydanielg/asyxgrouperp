@extends('layouts.admin')

@section('title', 'Project Settlements')
@section('page_title', 'Project Settlements')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">Project Settlements</h2>
        <p class="text-sm text-gray-500 mt-1">Track invoices vs receipts vs costs for each project (mkataba) — monthly recurring invoicing, costs, and net settlement</p>
    </div>

    {{-- Summary Cards --}}
    @php
        $totalInvoiced = array_sum(array_column($projectSummaries, 'invoiced'));
        $totalReceived = array_sum(array_column($projectSummaries, 'received'));
        $totalOutstanding = array_sum(array_column($projectSummaries, 'outstanding'));
        $totalCosts = array_sum(array_column($projectSummaries, 'total_cost'));
        $totalNet = array_sum(array_column($projectSummaries, 'net'));
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total Invoiced</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ number_format($totalInvoiced, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total Received</p>
            <p class="text-xl font-bold text-emerald-600 mt-1">{{ number_format($totalReceived, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Outstanding</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ number_format($totalOutstanding, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total Costs</p>
            <p class="text-xl font-bold text-red-600 mt-1">{{ number_format($totalCosts, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Net Settlement</p>
            <p class="text-xl font-bold {{ $totalNet >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1">{{ number_format($totalNet, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-700">All Projects — Settlement Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50 border-b">
                        <th class="px-4 py-3 font-medium">Project</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-center">Recurring</th>
                        <th class="px-4 py-3 font-medium text-right">Invoiced</th>
                        <th class="px-4 py-3 font-medium text-right">Received</th>
                        <th class="px-4 py-3 font-medium text-right">Outstanding</th>
                        <th class="px-4 py-3 font-medium text-right">Vendor Costs</th>
                        <th class="px-4 py-3 font-medium text-right">Office Exp.</th>
                        <th class="px-4 py-3 font-medium text-right">Total Costs</th>
                        <th class="px-4 py-3 font-medium text-right">Net</th>
                        <th class="px-4 py-3 font-medium text-right">Margin</th>
                        <th class="px-4 py-3 font-medium text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projectSummaries as $p)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-4 py-3">
                            <p class="text-xs font-semibold text-gray-800">{{ $p['title'] }}</p>
                            <p class="text-[10px] text-gray-400">{{ $p['project_number'] }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $p['status'] === 'completed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $p['status'] === 'in_progress' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $p['status'] === 'planning' ? 'bg-amber-50 text-amber-700' : '' }}
                                {{ $p['status'] === 'active' ? 'bg-blue-50 text-blue-700' : '' }}">{{ ucfirst(str_replace('_', ' ', $p['status'])) }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($p['recurring'])
                                <span class="text-[10px] text-bronze font-semibold">{{ number_format($p['billing_amount'], 0) }}/mo</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-right text-blue-600 font-medium">{{ $p['invoiced'] > 0 ? number_format($p['invoiced'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-emerald-600 font-medium">{{ $p['received'] > 0 ? number_format($p['received'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right {{ $p['outstanding'] > 0 ? 'text-amber-600 font-medium' : 'text-gray-400' }}">{{ $p['outstanding'] > 0 ? number_format($p['outstanding'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-600">{{ $p['vendor_cost'] > 0 ? number_format($p['vendor_cost'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-600">{{ $p['office_cost'] > 0 ? number_format($p['office_cost'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-red-600 font-medium">{{ $p['total_cost'] > 0 ? number_format($p['total_cost'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold {{ $p['net'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $p['net'] != 0 ? number_format($p['net'], 0) : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-500">{{ $p['margin'] != 0 ? number_format($p['margin'], 1) . '%' : '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.projects.settlements', $p['id']) }}" class="text-bronze text-xs hover:underline font-semibold">Monthly Breakdown →</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="px-4 py-12 text-center text-gray-400 text-sm">No projects found</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50">
                        <td class="px-4 py-3 text-xs font-bold text-gray-900" colspan="3">TOTALS</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-blue-600">{{ number_format($totalInvoiced, 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-emerald-600">{{ number_format($totalReceived, 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-amber-600">{{ number_format($totalOutstanding, 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-gray-700">{{ number_format(array_sum(array_column($projectSummaries, 'vendor_cost')), 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-gray-700">{{ number_format(array_sum(array_column($projectSummaries, 'office_cost')), 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-red-600">{{ number_format($totalCosts, 0) }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold {{ $totalNet >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($totalNet, 0) }}</td>
                        <td class="px-4 py-3"></td>
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
                    <li><strong>Invoiced</strong> = Total amount billed to client (recurring monthly invoices)</li>
                    <li><strong>Received</strong> = Actual payments received from client (client receipts)</li>
                    <li><strong>Outstanding</strong> = Invoiced but not yet paid by client</li>
                    <li><strong>Vendor Costs</strong> = Procurement / supplier invoices for this project</li>
                    <li><strong>Office Expenses</strong> = Approved office expenses linked to this project</li>
                    <li><strong>Staff Costs</strong> = Calculated from timesheet hours × employee hourly rate (salary ÷ 160 hrs) — shown in monthly breakdown</li>
                    <li><strong>Net</strong> = Received − Total Costs (actual profit)</li>
                    <li>Click <strong>Monthly Breakdown</strong> to see month-by-month details for each project</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
