@extends('layouts.admin')
@section('title', $company->legal_name . ' - ' . config('app.name'))
@section('page_title', $company->legal_name)
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.companies.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Companies</a>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Company Info --}}
    <div class="bg-white rounded-xl border p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-lg">{{ strtoupper(substr($company->name, 0, 1)) }}</div>
            <div>
                <h3 class="font-bold text-gray-800">{{ $company->legal_name }}</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $company->short_code }}</span>
            </div>
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-400">Type</dt><dd class="text-gray-700">{{ $company->is_group ? 'Group' : 'Operating' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Currency</dt><dd class="text-gray-700">{{ $company->currency }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Reg. Number</dt><dd class="text-gray-700">{{ $company->registration_number ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Tax ID</dt><dd class="text-gray-700">{{ $company->tax_id ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Phone</dt><dd class="text-gray-700">{{ $company->phone ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Email</dt><dd class="text-gray-700">{{ $company->email ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Address</dt><dd class="text-gray-700 text-right max-w-[200px]">{{ $company->address ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-400">Parent</dt><dd class="text-gray-700">{{ $company->parent?->short_code ?? '—' }}</dd></div>
        </dl>
        <div class="mt-4 flex gap-2">
            <a href="{{ route('admin.companies.edit', $company) }}" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700">Edit</a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="bg-white rounded-xl border p-6 lg:col-span-2">
        <h3 class="font-bold text-gray-800 mb-4">Company Statistics</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['users'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Users</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['employees'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Employees</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['projects'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Projects</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['tenders'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Tenders</p>
            </div>
            <div class="bg-emerald-50 rounded-lg p-4 text-center">
                <p class="text-lg font-bold text-emerald-700">{{ number_format($stats['revenue_ytd'], 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">Revenue (YTD)</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <p class="text-lg font-bold text-red-600">{{ number_format($stats['expenses_ytd'], 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">Expenses (YTD)</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-lg font-bold text-gray-700">{{ number_format($stats['revenue_ytd'] - $stats['expenses_ytd'], 0) }}</p>
                <p class="text-xs text-gray-500 mt-1">Net Profit</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-700">{{ $stats['bank_accounts'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Bank Accounts</p>
            </div>
        </div>

        @if($company->children->isNotEmpty())
        <div class="mt-6">
            <h4 class="text-sm font-bold text-gray-700 mb-3">Subsidiaries</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($company->children as $child)
                <a href="{{ route('admin.companies.show', $child) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-50 rounded-lg text-xs text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">
                    <span class="font-medium">{{ $child->short_code }}</span> — {{ $child->name }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Intercompany Transactions --}}
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Outgoing Intercompany</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Number</th><th class="px-4 py-2 font-medium">To</th><th class="px-4 py-2 font-medium">Amount</th><th class="px-4 py-2 font-medium">Status</th></tr></thead>
            <tbody>
            @forelse($intercompanyOut as $ict)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs"><a href="{{ route('admin.intercompany.show', $ict) }}" class="text-emerald-600 hover:underline">{{ $ict->transaction_number }}</a></td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $ict->toCompany?->short_code }}</td>
                <td class="px-4 py-2 text-xs font-medium">{{ number_format($ict->amount, 0) }} {{ $ict->currency }}</td>
                <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ict->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($ict->status === 'eliminated' ? 'bg-gray-50 text-gray-500' : 'bg-amber-50 text-amber-700') }}">{{ $ict->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No outgoing transactions</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-3 border-b bg-gray-50/50"><h4 class="text-sm font-bold text-gray-700">Incoming Intercompany</h4></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500"><th class="px-4 py-2 font-medium">Number</th><th class="px-4 py-2 font-medium">From</th><th class="px-4 py-2 font-medium">Amount</th><th class="px-4 py-2 font-medium">Status</th></tr></thead>
            <tbody>
            @forelse($intercompanyIn as $ict)
            <tr class="border-t border-gray-100">
                <td class="px-4 py-2 text-xs"><a href="{{ route('admin.intercompany.show', $ict) }}" class="text-emerald-600 hover:underline">{{ $ict->transaction_number }}</a></td>
                <td class="px-4 py-2 text-xs text-gray-600">{{ $ict->fromCompany?->short_code }}</td>
                <td class="px-4 py-2 text-xs font-medium">{{ number_format($ict->amount, 0) }} {{ $ict->currency }}</td>
                <td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $ict->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : ($ict->status === 'eliminated' ? 'bg-gray-50 text-gray-500' : 'bg-amber-50 text-amber-700') }}">{{ $ict->status }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400 text-xs">No incoming transactions</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
</div>
@endsection
