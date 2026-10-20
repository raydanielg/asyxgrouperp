@extends('layouts.admin')
@section('title', $project->title . ' - Financing - ' . config('app.name'))
@section('page_title', 'Project Financing')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Project</a>
    <button type="button" onclick="openDrawer('financingDrawer','financingDrawerPanel')" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">New Financing</button>
</div>

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-200 rounded-md px-5 py-3 mb-4 text-xs text-red-700">{{ session('error') }}</div>
@endif

@php
    $totalFinancing = $financings->sum('amount');
    $totalRepaid = $financings->sum(fn($f) => $f->totalPaid());
    $activeCount = $financings->where('status', 'active')->count();
    $statusColors = ['active' => '#f59e0b', 'repaid' => '#10b981', 'defaulted' => '#ef4444'];
@endphp

{{-- Header + summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h3 class="text-base font-bold text-gray-900">{{ $project->title }} — Financing</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Internal loans and inter-project financing</p>
        </div>
    </div>
    <div class="grid grid-cols-3 divide-x divide-gray-100">
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Financing</p><p class="text-lg font-bold text-gray-900 mt-1">TZS {{ number_format($totalFinancing, 2) }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Repaid</p><p class="text-lg font-bold text-emerald-600 mt-1">TZS {{ number_format($totalRepaid, 2) }}</p></div>
        <div class="px-5 py-4"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Active Loans</p><p class="text-lg font-bold text-gray-900 mt-1">{{ $activeCount }}</p></div>
    </div>
</div>

@forelse($financings as $financing)
@php $fsc = $statusColors[$financing->status] ?? '#9ca3af'; @endphp
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <h3 class="text-sm font-bold text-gray-900">{{ $financing->type === 'inter_project' ? 'Inter-Project Loan' : 'Internal Financing' }}</h3>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $fsc }}"></span>{{ ucfirst($financing->status) }}</span>
        </div>
        <div class="text-right">
            <p class="text-sm font-bold text-gray-900">TZS {{ number_format($financing->amount, 2) }}</p>
            <p class="text-[10px] text-gray-400">{{ $financing->disbursed_at->format('d M Y') }}</p>
        </div>
    </div>
    <div class="px-5 py-4">
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-x-4 gap-y-3 mb-4 text-xs">
            <div><p class="text-gray-400">Type</p><p class="font-medium text-gray-800 mt-0.5">{{ ucfirst(str_replace('_', ' ', $financing->type)) }}</p></div>
            @if($financing->sourceProject)
            <div><p class="text-gray-400">Source Project</p><p class="font-medium text-gray-800 mt-0.5">{{ $financing->sourceProject->title }}</p></div>
            @endif
            @if($financing->interest_rate)
            <div><p class="text-gray-400">Interest</p><p class="font-medium text-gray-800 mt-0.5">{{ $financing->interest_rate }}%</p></div>
            @endif
            @if($financing->repayment_period_months)
            <div><p class="text-gray-400">Tenure</p><p class="font-medium text-gray-800 mt-0.5">{{ $financing->repayment_period_months }} mo</p></div>
            @endif
            <div><p class="text-gray-400">Repaid</p><p class="font-medium text-emerald-600 mt-0.5">TZS {{ number_format($financing->totalPaid(), 2) }}</p></div>
            <div><p class="text-gray-400">Balance Due</p><p class="font-medium {{ $financing->balanceDue() > 0 ? 'text-red-500' : 'text-emerald-600' }} mt-0.5">TZS {{ number_format($financing->balanceDue(), 2) }}</p></div>
        </div>

        @if($financing->notes)
        <p class="text-[11px] text-gray-400 mb-4 italic">{{ $financing->notes }}</p>
        @endif

        @if($financing->repayments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-3 py-2 font-semibold">Due Date</th><th class="px-3 py-2 font-semibold text-right">Due Amount</th><th class="px-3 py-2 font-semibold text-right">Paid</th><th class="px-3 py-2 font-semibold">Status</th><th class="px-3 py-2 font-semibold text-right"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($financing->repayments as $rep)
                    @php $rsc = ['paid' => '#10b981', 'partial' => '#f59e0b'][$rep->status] ?? '#9ca3af'; @endphp
                    <tr>
                        <td class="px-3 py-2.5 text-xs text-gray-500">{{ $rep->due_date->format('d M Y') }}</td>
                        <td class="px-3 py-2.5 text-xs text-right font-mono text-gray-900">{{ number_format($rep->amount, 2) }}</td>
                        <td class="px-3 py-2.5 text-xs text-right font-mono text-gray-500">{{ number_format($rep->paid_amount, 2) }}</td>
                        <td class="px-3 py-2.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $rsc }}"></span>{{ ucfirst($rep->status) }}</span></td>
                        <td class="px-3 py-2.5 text-right">
                            @if($rep->status !== 'paid')
                            <button type="button" onclick="openRepayDrawer({{ $rep->id }}, '{{ $rep->due_date->format('d M Y') }}', {{ $rep->amount }}, {{ $rep->paid_amount }})" class="text-xs font-medium text-gray-600 hover:text-gray-900">Record Payment</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@empty
<div class="bg-white rounded-md border border-gray-200 px-5 py-10 text-center">
    <p class="text-sm text-gray-400">No financing records for this project yet.</p>
    <button type="button" onclick="openDrawer('financingDrawer','financingDrawerPanel')" class="mt-4 px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Add Financing</button>
</div>
@endforelse

{{-- New Financing Drawer --}}
<div id="financingDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('financingDrawer','financingDrawerPanel')"></div>
    <div id="financingDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Financing</h3><p class="text-[11px] text-slate-400 mt-0.5">{{ $project->title }}</p></div>
            <button type="button" onclick="closeDrawer('financingDrawer','financingDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.projects.financing.store', $project) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="financingType" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white" onchange="document.getElementById('sourceProjectGroup').style.display=this.value==='inter_project'?'block':'none'">
                        <option value="internal">Internal Financing</option>
                        <option value="inter_project">Inter-Project Loan</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div id="sourceProjectGroup" style="display:none">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Source Project <span class="text-red-500">*</span></label>
                    <select name="source_project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select project…</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Disbursement Date <span class="text-red-500">*</span></label><input name="disbursed_at" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Interest (%)</label><input name="interest_rate" type="number" step="0.01" min="0" max="100" placeholder="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Repayment Period (months)</label><input name="repayment_period_months" type="number" min="1" max="360" placeholder="Empty = lump-sum" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Financing</button>
                <button type="button" onclick="closeDrawer('financingDrawer','financingDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Repayment Drawer --}}
<div id="repayDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('repayDrawer','repayDrawerPanel')"></div>
    <div id="repayDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Record Repayment</h3><p class="text-[11px] text-slate-400 mt-0.5" id="repayDueLabel">Due date</p></div>
            <button type="button" onclick="closeDrawer('repayDrawer','repayDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @if($financings->isNotEmpty())
        <form id="repayForm" method="POST" action="" class="flex-1 overflow-y-auto p-5">
            @csrf
            <input type="hidden" name="repayment_id" id="repayId">
            <div class="space-y-4">
                <div class="bg-gray-50 border border-gray-200 rounded px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Payment for due</p>
                    <p class="text-sm font-bold text-gray-900 mt-1" id="repayDueDate"></p>
                    <p class="text-[11px] text-gray-500 mt-1">Due: TZS <span id="repayDueAmt" class="font-mono"></span> · Outstanding: TZS <span id="repayOutstanding" class="font-mono"></span></p>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount Paying <span class="text-red-500">*</span></label><input name="paid_amount" id="repayPaidAmount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><input name="notes" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Record Payment</button>
                <button type="button" onclick="closeDrawer('repayDrawer','repayDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer(drawerId, panelId) {
    document.getElementById(drawerId).classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById(panelId).classList.remove('-translate-x-full'));
}
function closeDrawer(drawerId, panelId) {
    document.getElementById(panelId).classList.add('-translate-x-full');
    setTimeout(() => document.getElementById(drawerId).classList.add('hidden'), 300);
}
function openRepayDrawer(repId, dueDate, amount, paid) {
    const outstanding = amount - paid;
    document.getElementById('repayId').value = repId;
    document.getElementById('repayDueLabel').textContent = 'Due ' + dueDate;
    document.getElementById('repayDueDate').textContent = dueDate;
    document.getElementById('repayDueAmt').textContent = amount.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('repayOutstanding').textContent = outstanding.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('repayPaidAmount').max = outstanding;
    document.getElementById('repayPaidAmount').value = outstanding;
    document.getElementById('repayForm').action = '{{ route('admin.projects.financing.repayment', ':financing') }}'.replace(':financing', repId);
    openDrawer('repayDrawer','repayDrawerPanel');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { ['financingDrawer','repayDrawer'].forEach(id => closeDrawer(id, id + 'Panel')); } });
</script>
@endpush
