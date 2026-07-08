@extends('layouts.admin')
@section('title', $project->title . ' - Financing - ' . config('app.name'))
@section('page_title', 'Project Financing')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.projects.show', $project) }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to {{ $project->title }}</a>
</div>

<div class="flex items-center justify-between mb-4">
    <div>
        <h3 class="text-sm font-bold text-gray-900">{{ $project->title }} &mdash; Financing</h3>
        <p class="text-xs text-gray-500">Internal loans and inter-project financing</p>
    </div>
    <button onclick="document.getElementById('financingModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">+ New Financing</button>
</div>

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3 mb-4 text-sm text-emerald-800">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-rose-50 border border-rose-200 rounded-xl px-5 py-3 mb-4 text-sm text-rose-800">{{ session('error') }}</div>
@endif

@php
    $totalFinancing = $financings->sum('amount');
    $totalRepaid = $financings->sum(fn($f) => $f->totalPaid());
    $activeCount = $financings->where('status', 'active')->count();
@endphp

<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Total Financing</p>
        <p class="text-xl font-bold text-gray-900 mt-1">TZS {{ number_format($totalFinancing, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border border-emerald-200 p-4">
        <p class="text-[10px] text-emerald-600 uppercase tracking-wide">Total Repaid</p>
        <p class="text-xl font-bold text-emerald-700 mt-1">TZS {{ number_format($totalRepaid, 2) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Active Loans</p>
        <p class="text-xl font-bold text-gray-900 mt-1">{{ $activeCount }}</p>
    </div>
</div>

@forelse($financings as $financing)
<div class="bg-white rounded-xl border overflow-hidden mb-4">
    <div class="px-5 py-3 border-b flex items-center justify-between {{ $financing->status === 'repaid' ? 'bg-emerald-50/50' : ($financing->status === 'defaulted' ? 'bg-rose-50/50' : 'bg-gray-50/50') }}">
        <div class="flex items-center gap-3">
            <h3 class="text-sm font-bold text-gray-900">{{ $financing->type === 'inter_project' ? 'Inter-Project Loan' : 'Internal Financing' }}</h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{
                $financing->status === 'repaid' ? 'bg-emerald-50 text-emerald-700' :
                ($financing->status === 'defaulted' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')
            }}">{{ ucfirst($financing->status) }}</span>
        </div>
        <div class="text-right">
            <p class="text-sm font-bold text-gray-900">TZS {{ number_format($financing->amount, 2) }}</p>
            <p class="text-[10px] text-gray-400">{{ $financing->disbursed_at->format('d M Y') }}</p>
        </div>
    </div>
    <div class="px-5 py-3">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-3 text-xs">
            <div><span class="text-gray-400">Type</span><p class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $financing->type)) }}</p></div>
            @if($financing->sourceProject)
            <div><span class="text-gray-400">Source Project</span><p class="font-medium text-gray-700">{{ $financing->sourceProject->title }}</p></div>
            @endif
            @if($financing->interest_rate)
            <div><span class="text-gray-400">Interest Rate</span><p class="font-medium text-gray-700">{{ $financing->interest_rate }}%</p></div>
            @endif
            @if($financing->repayment_period_months)
            <div><span class="text-gray-400">Tenure</span><p class="font-medium text-gray-700">{{ $financing->repayment_period_months }} months</p></div>
            @endif
            <div><span class="text-gray-400">Repaid</span><p class="font-medium text-emerald-700">TZS {{ number_format($financing->totalPaid(), 2) }}</p></div>
            <div><span class="text-gray-400">Balance Due</span><p class="font-medium {{ $financing->balanceDue() > 0 ? 'text-rose-600' : 'text-emerald-600' }}">TZS {{ number_format($financing->balanceDue(), 2) }}</p></div>
        </div>

        @if($financing->notes)
        <div class="text-xs text-gray-500 mb-3 italic">{{ $financing->notes }}</div>
        @endif

        @if($financing->repayments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead><tr class="text-left text-gray-400"><th class="pb-1 font-medium">Due Date</th><th class="pb-1 font-medium">Due Amount</th><th class="pb-1 font-medium">Paid</th><th class="pb-1 font-medium">Status</th><th class="pb-1 font-medium"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($financing->repayments as $rep)
                    <tr>
                        <td class="py-1.5 text-gray-600">{{ $rep->due_date->format('d M Y') }}</td>
                        <td class="py-1.5 text-gray-900 font-medium">TZS {{ number_format($rep->amount, 2) }}</td>
                        <td class="py-1.5 text-gray-600">TZS {{ number_format($rep->paid_amount, 2) }}</td>
                        <td class="py-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{
                                $rep->status === 'paid' ? 'bg-emerald-50 text-emerald-700' :
                                ($rep->status === 'partial' ? 'bg-amber-50 text-amber-700' : 'bg-gray-50 text-gray-600')
                            }}">{{ ucfirst($rep->status) }}</span>
                        </td>
                        <td class="py-1.5 text-right">
                            @if($rep->status !== 'paid')
                            <button onclick="document.getElementById('repayModal-{{ $rep->id }}').classList.remove('hidden')" class="text-sky-600 hover:text-sky-700 font-medium">Record Payment</button>
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

@foreach($financing->repayments as $rep)
<div id="repayModal-{{ $rep->id }}" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Record Repayment</h3>
        <form method="POST" action="{{ route('admin.projects.financing.repayment', $financing) }}" class="space-y-3">@csrf
            <input type="hidden" name="repayment_id" value="{{ $rep->id }}">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payment for due {{ $rep->due_date->format('d M Y') }}</label>
                <p class="text-sm text-gray-900 font-semibold">Due: TZS {{ number_format($rep->amount, 2) }} | Outstanding: TZS {{ number_format(max(0, $rep->amount - $rep->paid_amount), 2) }}</p>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount Paying *</label><input name="paid_amount" type="number" step="0.01" min="1" max="{{ $rep->amount - $rep->paid_amount }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><input name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('repayModal-{{ $rep->id }}').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg hover:bg-sky-700">Record Payment</button></div>
        </form>
    </div>
</div>
@endforeach
@empty
<div class="bg-white rounded-xl border p-8 text-center">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    <p class="text-sm text-gray-500">No financing records for this project yet.</p>
    <button onclick="document.getElementById('financingModal').classList.remove('hidden')" class="mt-3 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">+ Add Financing</button>
</div>
@endforelse

<div id="financingModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Financing for {{ $project->title }}</h3>
        <form method="POST" action="{{ route('admin.projects.financing.store', $project) }}" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                    <select name="type" id="financingType" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 outline-none" onchange="document.getElementById('sourceProjectGroup').style.display=this.value==='inter_project'?'block':'none'">
                        <option value="internal">Internal Financing</option>
                        <option value="inter_project">Inter-Project Loan</option>
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label><input name="amount" type="number" step="0.01" min="1" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 outline-none"></div>
            </div>
            <div id="sourceProjectGroup" style="display:none">
                <label class="block text-xs font-medium text-gray-600 mb-1">Source Project *</label>
                <select name="source_project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 outline-none">
                    <option value="">Select project…</option>
                    @foreach($projects as $p)
                    <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Disbursement Date *</label><input name="disbursed_at" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Interest Rate (%)</label><input name="interest_rate" type="number" step="0.01" min="0" max="100" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Repayment Period (months)</label><input name="repayment_period_months" type="number" min="1" max="360" placeholder="Leave empty for lump-sum repayment" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 outline-none" rows="2"></textarea></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('financingModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Save Financing</button></div>
        </form>
    </div>
</div>
@endsection
