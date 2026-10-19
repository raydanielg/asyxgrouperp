@extends('layouts.admin')
@section('title', $company->legal_name . ' - ' . config('app.name'))
@section('page_title', $company->legal_name)
@section('content')
@php $isSuper = auth()->user()?->isSuperAdmin(); @endphp

<div class="mb-4">
    <a href="{{ route('admin.companies.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Companies</a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

@if($company->isLockedOut())
<div class="mb-4 px-4 py-3 rounded bg-red-50 border border-red-200 text-sm text-red-700 flex items-center gap-2">
    <span style="width:7px;height:7px;border-radius:99px;background:#ef4444" class="flex-shrink-0"></span>
    <span><strong>Locked out.</strong> {{ $company->suspensionMessage() }}@if($company->suspended_at) Suspended {{ $company->suspended_at->diffForHumans() }}.@endif</span>
</div>
@endif

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 p-5 mb-5 flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-4 min-w-0">
        <div class="w-11 h-11 rounded bg-slate-900 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">{{ strtoupper($company->short_code ? substr($company->short_code, 0, 2) : substr($company->name, 0, 2)) }}</div>
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-bold text-gray-900 truncate">{{ $company->legal_name }}</h3>
                @php
                    $st = !$company->is_active ? ['Inactive','#9ca3af']
                        : ($company->subscription_status === 'suspended' ? ['Suspended','#ef4444']
                        : ($company->subscription_status === 'past_due' ? ['Past Due','#f59e0b']
                        : ($company->subscription_status === 'trial' ? ['Trial','#8b5cf6']
                        : ($company->subscription_status === 'cancelled' ? ['Cancelled','#ef4444']
                        : ['Active','#10b981']))));
                @endphp
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-500"><span style="width:6px;height:6px;border-radius:99px;background:{{ $st[1] }}"></span>{{ $st[0] }}</span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">{{ $company->short_code }} · {{ $company->is_group ? 'Group holding' : 'Operating company' }}@if($company->parent) · {{ $company->parent->short_code }}@endif · {{ $company->currency }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.companies.switch', ['company' => $company->id]) }}" class="px-3 py-2 text-xs font-medium text-gray-600 border border-gray-200 rounded hover:bg-gray-50">Switch Context</a>
        <a href="{{ route('admin.reports') }}?company={{ $company->id }}" class="px-3 py-2 text-xs font-medium text-gray-600 border border-gray-200 rounded hover:bg-gray-50">Reports</a>
        @if($isSuper && !$company->is_group)
            @if($company->subscription_status === 'suspended')
            <form method="POST" action="{{ route('admin.companies.activate', $company) }}" onsubmit="return confirm('Reactivate {{ addslashes($company->name) }}? All its users will be able to log in again.')">@csrf
                <button class="px-3 py-2 text-xs font-medium bg-emerald-600 text-white rounded hover:bg-emerald-700">Activate</button>
            </form>
            @else
            <button type="button" onclick="document.getElementById('suspendBox').classList.toggle('hidden')" class="px-3 py-2 text-xs font-medium bg-red-600 text-white rounded hover:bg-red-700">Suspend</button>
            @endif
        @endif
    </div>
</div>

@if($isSuper && !$company->is_group && $company->subscription_status !== 'suspended')
<div id="suspendBox" class="hidden mb-5 bg-white rounded-md border border-red-200 p-4">
    <form method="POST" action="{{ route('admin.companies.suspend', $company) }}" class="flex items-end gap-3 flex-wrap">
        @csrf
        <div class="flex-1 min-w-[220px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Suspension reason (optional)</label>
            <input type="text" name="suspension_reason" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400" placeholder="e.g. Unpaid subscription invoice">
        </div>
        <button class="px-4 py-2 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700" onclick="return confirm('Suspend {{ addslashes($company->name) }}? ALL {{ $company->users()->count() }} of its users will be locked out immediately.')">Confirm Suspension</button>
    </form>
    <p class="text-[11px] text-red-500 mt-2">Suspending instantly logs out active sessions and blocks logins for every {{ $company->name }} user until reactivation.</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Company info --}}
    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
        <dl class="p-5 space-y-3 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-gray-400 text-xs">Registration</dt><dd class="text-gray-800 text-xs text-right">{{ $company->registration_number ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-400 text-xs">Tax ID (TIN)</dt><dd class="text-gray-800 text-xs text-right">{{ $company->tax_id ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-400 text-xs">VRN</dt><dd class="text-gray-800 text-xs text-right">{{ $company->vrn ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-400 text-xs">Phone</dt><dd class="text-gray-800 text-xs text-right">{{ $company->phone ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-400 text-xs">Email</dt><dd class="text-gray-800 text-xs text-right truncate">{{ $company->email ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-400 text-xs">Address</dt><dd class="text-gray-800 text-xs text-right max-w-[180px]">{{ $company->address ?? '—' }}{{ $company->city ? ', ' . $company->city : '' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-400 text-xs">Website</dt><dd class="text-gray-800 text-xs text-right">{{ $company->website ?? '—' }}</dd></div>
        </dl>
    </div>

    {{-- Stats --}}
    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Statistics</h4></div>
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100">
            <div class="p-5"><p class="text-2xl font-bold text-gray-900">{{ $stats['users'] }}</p><p class="text-xs text-gray-400 mt-1">Users</p></div>
            <div class="p-5"><p class="text-2xl font-bold text-gray-900">{{ $stats['employees'] }}</p><p class="text-xs text-gray-400 mt-1">Employees</p></div>
            <div class="p-5"><p class="text-2xl font-bold text-gray-900">{{ $stats['projects'] }}</p><p class="text-xs text-gray-400 mt-1">Projects</p></div>
            <div class="p-5"><p class="text-2xl font-bold text-gray-900">{{ $stats['tenders'] }}</p><p class="text-xs text-gray-400 mt-1">Tenders</p></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100 border-t border-gray-100">
            <div class="p-5"><p class="text-base font-bold text-emerald-600">{{ number_format($stats['revenue_ytd'], 0) }}</p><p class="text-xs text-gray-400 mt-1">Revenue YTD</p></div>
            <div class="p-5"><p class="text-base font-bold text-red-500">{{ number_format($stats['expenses_ytd'], 0) }}</p><p class="text-xs text-gray-400 mt-1">Expenses YTD</p></div>
            <div class="p-5"><p class="text-base font-bold text-gray-900">{{ number_format($stats['revenue_ytd'] - $stats['expenses_ytd'], 0) }}</p><p class="text-xs text-gray-400 mt-1">Net Profit</p></div>
            <div class="p-5"><p class="text-base font-bold text-gray-900">{{ $stats['bank_accounts'] }}</p><p class="text-xs text-gray-400 mt-1">Bank Accounts</p></div>
        </div>

        @if($company->children->isNotEmpty())
        <div class="px-5 py-4 border-t border-gray-100">
            <p class="text-xs font-semibold text-gray-500 mb-2">Subsidiaries</p>
            <div class="flex flex-wrap gap-2">
                @foreach($company->children as $child)
                <a href="{{ route('admin.companies.show', $child) }}" class="text-xs text-gray-600 border border-gray-200 rounded px-2.5 py-1.5 hover:bg-gray-50"><span class="font-mono text-gray-400">{{ $child->short_code }}</span> — {{ $child->name }}</a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@if($isSuper)
{{-- Subscription control --}}
<div class="mt-5 bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 bg-slate-900 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-white">Subscription &amp; Access Control</h4>
        @if($company->is_group)
        <span class="text-[10px] font-bold uppercase tracking-widest text-gold-400">Protected</span>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.companies.subscription', $company) }}" class="p-5">
        @csrf
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Plan</label>
                <select name="subscription_plan" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400">
                    @foreach(\App\Models\Company::PLANS as $k => $v)
                        <option value="{{ $k }}" {{ $company->subscription_plan === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="subscription_status" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400" {{ $company->is_group ? 'disabled' : '' }}>
                    @foreach(\App\Models\Company::SUBSCRIPTION_STATUSES as $k => $v)
                        <option value="{{ $k }}" {{ $company->subscription_status === $k ? 'selected' : '' }} {{ $company->is_group && $k === 'suspended' ? 'disabled' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                @if($company->is_group)<input type="hidden" name="subscription_status" value="{{ $company->subscription_status }}">@endif
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Billing Cycle</label>
                <select name="billing_cycle" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400">
                    <option value="">—</option>
                    @foreach(['monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'yearly' => 'Yearly'] as $k => $v)
                        <option value="{{ $k }}" {{ $company->billing_cycle === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">MRR ({{ $company->currency }})</label>
                <input type="number" step="0.01" min="0" name="mrr" value="{{ $company->mrr }}" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400" placeholder="0.00">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Max Users</label>
                <input type="number" min="1" name="max_users" value="{{ $company->max_users }}" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400" placeholder="Unlimited">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Starts</label>
                <input type="date" name="subscription_starts_at" value="{{ optional($company->subscription_starts_at)->format('Y-m-d') }}" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Ends</label>
                <input type="date" name="subscription_ends_at" value="{{ optional($company->subscription_ends_at)->format('Y-m-d') }}" class="w-full px-3 py-2 rounded border border-gray-200 text-sm outline-none focus:border-gray-400">
            </div>
            <div class="flex items-end">
                <button class="w-full px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save</button>
            </div>
        </div>
        @if($errors->any())
            <p class="text-xs text-red-600 mt-3">{{ $errors->first() }}</p>
        @endif
        <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-1 text-[11px] text-gray-400">
            <span>Status: <strong class="text-gray-600">{{ \App\Models\Company::SUBSCRIPTION_STATUSES[$company->subscription_status] ?? ucfirst($company->subscription_status ?? '—') }}</strong></span>
            @if($company->suspended_at)<span>Suspended at: <strong class="text-red-500">{{ $company->suspended_at->format('d M Y H:i') }}</strong></span>@endif
            @if($company->suspension_reason)<span>Reason: <strong class="text-gray-600">{{ $company->suspension_reason }}</strong></span>@endif
            <span>Users: <strong class="text-gray-600">{{ $stats['users'] }}</strong></span>
        </div>
    </form>
</div>
@endif

{{-- Intercompany Transactions --}}
<div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Outgoing Intercompany</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left border-b border-gray-100"><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Number</th><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">To</th><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Amount</th><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
            @forelse($intercompanyOut as $ict)
            <tr>
                <td class="px-4 py-2.5 text-xs"><a href="{{ route('admin.intercompany.show', $ict) }}" class="text-gray-700 hover:underline">{{ $ict->transaction_number }}</a></td>
                <td class="px-4 py-2.5 text-xs text-gray-500">{{ $ict->toCompany?->short_code }}</td>
                <td class="px-4 py-2.5 text-xs font-medium text-gray-800">{{ number_format($ict->amount, 0) }} {{ $ict->currency }}</td>
                <td class="px-4 py-2.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $ict->status === 'completed' ? 'bg-emerald-500' : ($ict->status === 'eliminated' ? 'bg-gray-300' : 'bg-amber-400') }}"></span>{{ ucfirst($ict->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No outgoing transactions</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Incoming Intercompany</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left border-b border-gray-100"><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Number</th><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">From</th><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Amount</th><th class="px-4 py-2 text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
            @forelse($intercompanyIn as $ict)
            <tr>
                <td class="px-4 py-2.5 text-xs"><a href="{{ route('admin.intercompany.show', $ict) }}" class="text-gray-700 hover:underline">{{ $ict->transaction_number }}</a></td>
                <td class="px-4 py-2.5 text-xs text-gray-500">{{ $ict->fromCompany?->short_code }}</td>
                <td class="px-4 py-2.5 text-xs font-medium text-gray-800">{{ number_format($ict->amount, 0) }} {{ $ict->currency }}</td>
                <td class="px-4 py-2.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $ict->status === 'completed' ? 'bg-emerald-500' : ($ict->status === 'eliminated' ? 'bg-gray-300' : 'bg-amber-400') }}"></span>{{ ucfirst($ict->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No incoming transactions</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
