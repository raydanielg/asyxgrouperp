@extends('layouts.admin')
@section('title', 'Business Flow - ' . config('app.name'))
@section('page_title', 'Business Flow Dashboard')
@section('content')
<div class="mb-6">
    <p class="text-sm text-gray-500">Complete business workflow: Tender → Lead → Deal → Project → Procurement → Client Invoice → Profit</p>
</div>

{{-- Flow Diagram --}}
<div class="bg-white rounded-xl border p-6 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Business Flow Pipeline</h3>
    <div class="flex flex-wrap items-center gap-2 text-xs">
        @php $stages = [
            ['Tenders', 'admin.tenders.index', $stats['tenders'], 'sky'],
            ['Leads', 'admin.crm-leads.index', $stats['activeLeads'], 'emerald'],
            ['Deals', 'admin.crm-deals.index', $stats['openDeals'], 'amber'],
            ['Projects', 'admin.projects.index', $stats['activeProjects'], 'emerald'],
            ['LPOs', 'admin.lpos.index', $stats['pendingLpos'], 'sky'],
            ['GRNs', 'admin.grns.index', $stats['pendingGrns'], 'amber'],
            ['Vendor Invoices', 'admin.vendor-invoices.index', $stats['unpaidVendorInvoices'], 'red'],
            ['Office Expenses', 'admin.office-expenses.index', $stats['pendingExpenses'], 'amber'],
        ];
        @endphp
        @foreach ($stages as $i => $stage)
            <a href="{{ route($stage[1]) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-{{ $stage[3] }}-200 bg-{{ $stage[3] }}-50 hover:bg-{{ $stage[3] }}-100 transition-colors">
                <span class="font-medium text-{{ $stage[3] }}-700">{{ $stage[0] }}</span>
                <span class="px-1.5 py-0.5 rounded-full bg-{{ $stage[3] }}-600 text-white text-[10px] font-bold">{{ $stage[2] }}</span>
            </a>
            @if ($i < count($stages) - 1)<svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>@endif
        @endforeach
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">Total Procurement</p><p class="text-lg font-bold text-sky-700">TZS {{ number_format($stats['totalProcurementValue']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">Vendor Paid</p><p class="text-lg font-bold text-amber-700">TZS {{ number_format($stats['totalVendorPaid']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">Client Receipts</p><p class="text-lg font-bold text-emerald-700">TZS {{ number_format($stats['totalClientReceipts']) }}</p></div>
    <div class="bg-white rounded-xl border p-4"><p class="text-[10px] text-gray-400 uppercase">Office Expenses</p><p class="text-lg font-bold text-red-700">TZS {{ number_format($stats['totalOfficeExpenses']) }}</p></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Recent Tenders --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3"><h3 class="text-sm font-bold text-gray-900">Recent Tenders</h3><a href="{{ route('admin.tenders.index') }}" class="text-[10px] text-emerald-600">View All</a></div>
        <div class="space-y-2">@forelse($recentTenders as $t)
            <div class="flex items-center justify-between text-xs border-b pb-2">
                <div><p class="font-medium text-gray-900">{{ $t->title }}</p><p class="text-[10px] text-gray-400">{{ $t->tender_number }} • {{ $t->client_name }}</p></div>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($t->status=='converted')bg-emerald-50 text-emerald-700 @elseif($t->status=='rejected')bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span>
            </div>
        @empty<p class="text-xs text-gray-400 text-center py-4">No tenders yet</p>
        @endforelse
        </div>
    </div>

    {{-- Recent LPOs --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3"><h3 class="text-sm font-bold text-gray-900">Recent LPOs</h3><a href="{{ route('admin.lpos.index') }}" class="text-[10px] text-emerald-600">View All</a></div>
        <div class="space-y-2">@forelse($recentLpos as $l)
            <div class="flex items-center justify-between text-xs border-b pb-2">
                <div><p class="font-medium text-gray-900">{{ $l->lpo_number }}</p><p class="text-[10px] text-gray-400">{{ $l->supplier?->name ?? 'N/A' }} • {{ $l->project?->title ?? 'No Project' }}</p></div>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] @if($l->status=='received')bg-emerald-50 text-emerald-700 @elseif($l->status=='draft')bg-gray-50 text-gray-700 @else bg-amber-50 text-amber-700 @endif">{{ ucfirst(str_replace('_',' ',$l->status)) }}</span>
            </div>
        @empty<p class="text-xs text-gray-400 text-center py-4">No LPOs yet</p>
        @endforelse
        </div>
    </div>

    {{-- Recent Projects --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-3"><h3 class="text-sm font-bold text-gray-900">Recent Projects</h3><a href="{{ route('admin.projects.index') }}" class="text-[10px] text-emerald-600">View All</a></div>
        <div class="space-y-2">@forelse($recentProjects as $p)
            <div class="flex items-center justify-between text-xs border-b pb-2">
                <div><p class="font-medium text-gray-900">{{ $p->title }}</p><p class="text-[10px] text-gray-400">{{ $p->project_number }} • Budget: TZS {{ number_format($p->budget) }}</p></div>
                <a href="{{ route('admin.projects.profit', $p) }}" class="text-[10px] text-emerald-600 hover:underline">Profit</a>
            </div>
        @empty<p class="text-xs text-gray-400 text-center py-4">No projects yet</p>
        @endforelse
        </div>
    </div>
</div>
@endsection
