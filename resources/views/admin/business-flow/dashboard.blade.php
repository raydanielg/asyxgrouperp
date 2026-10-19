@extends('layouts.admin')
@section('title', 'Business Flow - ' . config('app.name'))
@section('page_title', 'Business Flow Dashboard')
@section('content')
<div class="mb-5">
    <p class="text-xs text-gray-400">Tender → Lead → Deal → Project → Procurement → Client Invoice → Profit</p>
</div>

{{-- Flow Pipeline --}}
<div class="bg-white rounded-md border border-gray-200 p-5 mb-5">
    <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-4">Pipeline</h3>
    <div class="flex flex-wrap items-center gap-1.5 text-xs">
        @php $stages = [
            ['Tenders', 'admin.tenders.index', $stats['tenders']],
            ['Leads', 'admin.crm-leads.index', $stats['activeLeads']],
            ['Deals', 'admin.crm-deals.index', $stats['openDeals']],
            ['Projects', 'admin.projects.index', $stats['activeProjects']],
            ['LPOs', 'admin.lpos.index', $stats['pendingLpos']],
            ['GRNs', 'admin.grns.index', $stats['pendingGrns']],
            ['Vendor Invoices', 'admin.vendor-invoices.index', $stats['unpaidVendorInvoices']],
            ['Office Expenses', 'admin.office-expenses.index', $stats['pendingExpenses']],
        ];
        @endphp
        @foreach ($stages as $i => $stage)
            <a href="{{ route($stage[1]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded border border-gray-200 bg-white hover:bg-gray-50 transition-colors">
                <span class="font-medium text-gray-700">{{ $stage[0] }}</span>
                <span class="text-[10px] font-bold {{ $stage[2] > 0 ? 'text-gray-900' : 'text-gray-300' }}">{{ $stage[2] }}</span>
            </a>
            @if ($i < count($stages) - 1)<svg class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @endif
        @endforeach
        </div>
</div>

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 mb-5">
    <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-y md:divide-y-0 divide-gray-100">
        <div class="p-5"><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Procurement</p><p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($stats['totalProcurementValue']) }}</p><p class="text-[10px] text-gray-400">TZS</p></div>
        <div class="p-5"><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Vendor Paid</p><p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($stats['totalVendorPaid']) }}</p><p class="text-[10px] text-gray-400">TZS</p></div>
        <div class="p-5"><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Client Receipts</p><p class="text-lg font-bold text-emerald-600 mt-1">{{ number_format($stats['totalClientReceipts']) }}</p><p class="text-[10px] text-gray-400">TZS</p></div>
        <div class="p-5"><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Office Expenses</p><p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($stats['totalOfficeExpenses']) }}</p><p class="text-[10px] text-gray-400">TZS</p></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    {{-- Recent Tenders --}}
    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Recent Tenders</h3>
            <a href="{{ route('admin.tenders.index') }}" class="text-[11px] text-gray-400 hover:text-gray-700">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
        @forelse($recentTenders as $t)
        <div class="flex items-center justify-between px-5 py-3">
                <div class="min-w-0"><p class="text-xs font-medium text-gray-900 truncate">{{ $t->title }}</p><p class="text-[10px] text-gray-400 mt-0.5">{{ $t->tender_number }} · {{ $t->client_name }}</p></div>
                @php $td = $t->status=='converted' ? '#10b981' : ($t->status=='rejected' ? '#ef4444' : '#f59e0b'); @endphp
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-600 flex-shrink-0 ml-3"><span style="width:6px;height:6px;border-radius:99px;background:{{ $td }}"></span>{{ ucfirst(str_replace('_',' ',$t->status)) }}</span>
            </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-6">No tenders yet</p>
        @endforelse
        </div>
    </div>

    {{-- Recent LPOs --}}
    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Recent LPOs</h3>
            <a href="{{ route('admin.lpos.index') }}" class="text-[11px] text-gray-400 hover:text-gray-700">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
        @forelse($recentLpos as $l)
        <div class="flex items-center justify-between px-5 py-3">
                <div class="min-w-0"><p class="text-xs font-medium text-gray-900">{{ $l->lpo_number }}</p><p class="text-[10px] text-gray-400 mt-0.5">{{ $l->supplier?->name ?? 'N/A' }} · {{ $l->project?->title ?? 'No project' }}</p></div>
                @php $ld = $l->status=='received' ? '#10b981' : ($l->status=='draft' ? '#9ca3af' : '#f59e0b'); @endphp
                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-600 flex-shrink-0 ml-3"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ld }}"></span>{{ ucfirst(str_replace('_',' ',$l->status)) }}</span>
            </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-6">No LPOs yet</p>
        @endforelse
        </div>
    </div>

    {{-- Recent Projects --}}
    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-[11px] font-bold text-gray-500 uppercase tracking-widest">Recent Projects</h3>
            <a href="{{ route('admin.projects.index') }}" class="text-[11px] text-gray-400 hover:text-gray-700">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
        @forelse($recentProjects as $p)
        <div class="flex items-center justify-between px-5 py-3">
                <div class="min-w-0"><p class="text-xs font-medium text-gray-900 truncate">{{ $p->title }}</p><p class="text-[10px] text-gray-400 mt-0.5">{{ $p->project_number }} · TZS {{ number_format($p->budget) }}</p></div>
                <a href="{{ route('admin.projects.profit', $p) }}" class="text-[11px] font-medium text-gray-500 hover:text-gray-900 flex-shrink-0 ml-3">Profit →</a>
            </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-6">No projects yet</p>
        @endforelse
        </div>
    </div>
</div>
@endsection
