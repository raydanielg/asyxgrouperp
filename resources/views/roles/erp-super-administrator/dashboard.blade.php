@extends('layouts.admin')

@section('title', 'ERP Super Administrator Dashboard')
@section('page_title', 'ERP Super Administrator Dashboard')
@section('page_subtitle', 'Full system control & executive overview')

@section('page_actions')
<a href="{{ route('role.dashboard.report-pdf') }}" onclick="event.preventDefault(); Swal.fire({title:'Generating Report...',text:'Please wait while we prepare your PDF report.',allowOutsideClick:false,allowEscapeKey:false,showConfirmButton:false,willOpen:()=>{Swal.showLoading();fetch('{{ route('role.dashboard.report-pdf') }}').then(r=>r.blob()).then(b=>{const u=URL.createObjectURL(b);const a=document.createElement('a');a.href=u;a.download='super-admin-report-{{ now()->format('Ymd') }}.pdf';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(u);Swal.close();}).catch(()=>Swal.fire({icon:'error',title:'Error',text:'Failed to generate PDF',confirmButtonColor:'#024938'}))})" class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-xs font-bold rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center gap-1.5">
  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
  Export PDF
</a>
@endsection

@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n);
    $colorMap = [
        'emerald' => ['from' => 'from-emerald-600', 'to' => 'to-emerald-700', 'border' => 'border-emerald-500', 'text' => 'text-emerald-100', 'sub' => 'text-emerald-200', 'light' => 'bg-emerald-50 text-emerald-700'],
        'sky' => ['from' => 'from-sky-500', 'to' => 'to-sky-600', 'border' => 'border-sky-400', 'text' => 'text-sky-100', 'sub' => 'text-sky-200', 'light' => 'bg-sky-50 text-sky-700'],
        'amber' => ['from' => 'from-amber-400', 'to' => 'to-amber-500', 'border' => 'border-amber-300', 'text' => 'text-amber-50', 'sub' => 'text-amber-100', 'light' => 'bg-amber-50 text-amber-700'],
        'rose' => ['from' => 'from-rose-500', 'to' => 'to-rose-600', 'border' => 'border-rose-400', 'text' => 'text-rose-100', 'sub' => 'text-rose-200', 'light' => 'bg-rose-50 text-rose-700'],
        'violet' => ['from' => 'from-violet-500', 'to' => 'to-violet-600', 'border' => 'border-violet-400', 'text' => 'text-violet-100', 'sub' => 'text-violet-200', 'light' => 'bg-violet-50 text-violet-700'],
        'indigo' => ['from' => 'from-indigo-500', 'to' => 'to-indigo-600', 'border' => 'border-indigo-400', 'text' => 'text-indigo-100', 'sub' => 'text-indigo-200', 'light' => 'bg-indigo-50 text-indigo-700'],
    ];
@endphp

{{-- Welcome Banner --}}
<div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-56 h-56 bg-emerald-500/10 rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 right-0 w-32 h-32 bg-emerald-500/10 rounded-full -mr-8 -mb-8"></div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded bg-emerald-500 text-[10px] font-bold uppercase tracking-wide">Super Admin</span>
                <span class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</span>
            </div>
            <h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-300 text-sm mt-1">Full system control — manage companies, users, roles, intercompany transactions, and consolidated reports.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-xs text-slate-400">Current Company</p>
                <p class="text-sm font-semibold text-white">{{ auth()->user()->company?->name ?? 'Group' }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Primary KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach($kpiCards as $card)
        @php $c = $colorMap[$card['color']] ?? $colorMap['emerald']; @endphp
        <div class="bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }} rounded-xl border border-{{ $c['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-2">
                    <span class="text-[10px] font-medium {{ $c['text'] }}">{{ $card['label'] }}</span>
                    <svg class="w-4 h-4 {{ $c['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                </div>
                <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- Secondary KPIs & Insights --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Secondary KPIs --}}
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Operational Snapshot
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($secondaryKpis as $kpi)
                @php $c = $colorMap[$kpi['color']] ?? $colorMap['emerald']; @endphp
                <a href="{{ route($kpi['route']) }}" class="group flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50/50 transition-all">
                    <div class="w-9 h-9 rounded-lg {{ $c['light'] }} flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 group-hover:text-emerald-700">{{ $kpi['label'] }}</p>
                        <p class="text-sm font-bold text-gray-900">{{ $kpi['value'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- AI Insights Mini Card --}}
    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-xl border border-emerald-100 p-5 relative">
        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            AI Insights
        </h3>
        <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $aiInsights['message'] ?? 'No actionable insights at this time.' }}</p>
        <button onclick="openAiInsightsModal()" class="w-full px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">View Full Insights</button>
    </div>
</div>

{{-- AI Insights Modal --}}
<div id="aiInsightsModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeAiInsightsModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2" id="modal-title">
                        <svg class="w-6 h-6 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        AI Power Insights
                    </h3>
                    <button onclick="closeAiInsightsModal()" class="text-emerald-100 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 p-4">
                        <p class="text-sm text-gray-700 font-medium">{{ $aiInsights['message'] ?? 'No actionable insights at this time.' }}</p>
                    </div>
                    @if(!empty($aiInsights['suggestions']))
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Actionable Suggestions</h4>
                    <ul class="space-y-3">
                        @foreach($aiInsights['suggestions'] as $suggestion)
                            <li class="flex items-start gap-3 text-sm text-gray-700 bg-gray-50 rounded-lg p-3">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-xs font-bold">{{ $loop->iteration }}</span>
                                <span>{{ $suggestion }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button onclick="closeAiInsightsModal()" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Got it</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Floating Power Button --}}
<button onclick="openAiInsightsModal()" id="aiPowerButton" class="fixed bottom-6 right-6 z-40 group flex items-center gap-2 px-4 py-3 bg-emerald-600 text-white rounded-full shadow-lg hover:bg-emerald-700 hover:shadow-xl transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" title="Open AI Power Insights">
    <svg class="w-6 h-6 text-yellow-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
    <span class="text-sm font-semibold max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-300 whitespace-nowrap">Power</span>
</button>

{{-- Chart --}}
<div class="bg-white rounded-xl border p-5 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
        {{ $chartData['title'] ?? 'System Activity' }} (14 days)
    </h3>
    <div class="relative h-64">
        <canvas id="superAdminChart"></canvas>
    </div>
</div>

{{-- System Control & Backup --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            System Control
        </h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div><p class="text-xs font-semibold text-gray-700">System Mode</p><p class="text-[10px] text-gray-500">{{ $systemMode ?? 'Online' }}</p></div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Online</span>
            </div>
            <div class="flex gap-2">
                <button onclick="toggleSystemMode('maintenance')" class="flex-1 px-3 py-2 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg text-xs font-medium hover:bg-amber-100 transition-colors">Maintenance Mode</button>
                <button onclick="toggleSystemMode('online')" class="flex-1 px-3 py-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg text-xs font-medium hover:bg-emerald-100 transition-colors">Enable System</button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Database Backup
        </h3>
        <p class="text-xs text-gray-500 mb-3">Download full database backup (SQL). This includes all companies and users.</p>
        <button onclick="downloadBackup()" class="w-full px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-xs font-bold rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download Backup
        </button>
    </div>

    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Quick Reports
        </h3>
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('admin.reports') }}" class="px-3 py-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg text-xs font-medium hover:bg-emerald-100 transition-colors text-center">All Reports</a>
            <a href="{{ route('admin.companies.consolidated') }}" class="px-3 py-2 bg-sky-50 text-sky-700 border border-sky-100 rounded-lg text-xs font-medium hover:bg-sky-100 transition-colors text-center">Consolidated</a>
            <a href="{{ route('admin.users.login-history') }}" class="px-3 py-2 bg-violet-50 text-violet-700 border border-violet-100 rounded-lg text-xs font-medium hover:bg-violet-100 transition-colors text-center">Login History</a>
            <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-2 bg-amber-50 text-amber-700 border border-amber-100 rounded-lg text-xs font-medium hover:bg-amber-100 transition-colors text-center">Audit Logs</a>
        </div>
    </div>
</div>

{{-- Companies Overview --}}
<div class="bg-white rounded-xl border overflow-hidden mb-6">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            All Companies
        </h3>
        <a href="{{ route('admin.companies.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">Manage Companies</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 bg-gray-50/80 border-b border-gray-100">
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Company</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Users</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 font-semibold uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies ?? [] as $company)
                <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</div>
                            <div>
                                <p class="text-xs font-semibold text-gray-900">{{ $company->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $company->short_code ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($company->is_group)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Group</span>
                        @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">Subsidiary</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $company->users_count ?? 0 }}</td>
                    <td class="px-5 py-3">
                        @if($company->is_active)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 ring-1 ring-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.companies.switch', ['company' => $company->id]) }}" class="p-1.5 rounded-lg hover:bg-emerald-50 text-emerald-600 hover:text-emerald-700" title="Switch to this company"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></a>
                            <a href="{{ route('admin.companies.show', $company) }}" class="p-1.5 rounded-lg hover:bg-sky-50 text-sky-600 hover:text-sky-700" title="View Dashboard"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-xs text-gray-400">No companies found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Quick Actions --}}
@if(!empty($quickActions))
<div class="bg-white rounded-xl border p-5 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Quick Actions
    </h3>
    <div class="flex flex-wrap gap-3">
        @foreach($quickActions as $action)
        <a href="{{ route($action['route'], $action['params'] ?? []) }}" class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors border border-emerald-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
            {{ $action['label'] }}
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Recent Items Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @if(!empty($recentItems['recentUsers']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentItems['recentUsers'] as $user)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <div>
                            <p class="text-xs font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-gray-400">{{ $user->created_at->format('d M Y') }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($recentItems['recentCompanies']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Companies</h3>
            <a href="{{ route('admin.companies.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentItems['recentCompanies'] as $company)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</div>
                        <div>
                            <p class="text-xs font-medium text-gray-900">{{ $company->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $company->is_group ? 'Group' : 'Subsidiary' }}</p>
                        </div>
                    </div>
                    <span class="text-[10px] text-gray-400">{{ $company->created_at->format('d M Y') }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($recentItems['recentSales']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Sales Invoices</h3>
            <a href="{{ route('admin.sales-invoices.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentItems['recentSales'] as $invoice)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                        <p class="text-[10px] text-gray-400">{{ $invoice->customer?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($invoice->total_amount) }}</p>
                        <p class="text-[10px] text-gray-400">{{ $invoice->invoice_date->format('d M Y') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($recentItems['recentTickets']) || !empty($recentItems['openTickets']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Support Tickets</h3>
            <a href="{{ route('admin.helpdesk-tickets.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach(($recentItems['recentTickets'] ?? $recentItems['openTickets'] ?? collect())->take(5) as $ticket)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? $ticket->title ?? 'Ticket #' . $ticket->id }}</p>
                        <p class="text-[10px] text-gray-400">{{ ucfirst($ticket->status ?? '') }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                        @if(($ticket->status ?? '') === 'open') bg-rose-50 text-rose-700
                        @elseif(($ticket->status ?? '') === 'in_progress') bg-amber-50 text-amber-700
                        @elseif(($ticket->status ?? '') === 'resolved') bg-emerald-50 text-emerald-700
                        @else bg-gray-50 text-gray-700 @endif">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status ?? 'unknown')) }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($recentItems['activeProjects']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Active Projects</h3>
            <a href="{{ route('admin.projects.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentItems['activeProjects'] as $project)
                <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $project->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</p>
                    </div>
                    <span class="text-[10px] text-gray-400">{{ $project->due_date?->format('d M Y') ?? '' }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleSystemMode(mode) {
    const title = mode === 'maintenance' ? 'Enable Maintenance Mode?' : 'Bring System Online?';
    const text = mode === 'maintenance' ? 'All non-admin users will be logged out and see a maintenance page.' : 'The system will be available to all users.';
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: mode === 'maintenance' ? '#d97706' : '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, proceed',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('{{ route('admin.system-mode') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ mode: mode })
            })
            .then(r => r.json())
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Updated', text: data.message, timer: 2000, showConfirmButton: false });
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update system mode.', confirmButtonColor: '#024938' }));
        }
    });
}

function downloadBackup() {
    Swal.fire({
        title: 'Download Database Backup?',
        text: 'This will generate and download a SQL backup of the entire database.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Download',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Preparing backup...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            fetch('{{ route('admin.backup.download') }}', {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => {
                if (!r.ok) throw new Error('Backup failed');
                return r.blob();
            })
            .then(blob => {
                Swal.close();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'backup-' + new Date().toISOString().slice(0,10) + '.sql';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            })
            .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Backup failed. Please check server configuration.', confirmButtonColor: '#024938' }));
        }
    });
}

function openAiInsightsModal() {
    const modal = document.getElementById('aiInsightsModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeAiInsightsModal() {
    const modal = document.getElementById('aiInsightsModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

function markAiInsightsSeen() {
    try {
        localStorage.setItem('aiInsightsSeen', 'true');
        localStorage.setItem('aiInsightsDate', new Date().toISOString().split('T')[0]);
    } catch (e) {}
}

function shouldShowAiInsights() {
    try {
        const seen = localStorage.getItem('aiInsightsSeen');
        const date = localStorage.getItem('aiInsightsDate');
        const today = new Date().toISOString().split('T')[0];
        return !seen || date !== today;
    } catch (e) {
        return true;
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAiInsightsModal();
});

document.addEventListener('DOMContentLoaded', function() {
    // Auto-show AI Insights on first visit of the day
    if (shouldShowAiInsights()) {
        setTimeout(() => {
            openAiInsightsModal();
            markAiInsightsSeen();
        }, 800);
    }

    const ctx = document.getElementById('superAdminChart');
    if (!ctx) return;

    const labels = @json($chartData['labels'] ?? []);
    const values = @json($chartData['values'] ?? []);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : Array.from({length: 14}, (_, i) => 'Day ' + (i + 1)),
            datasets: [{
                label: '{{ $chartData['title'] ?? 'Activity' }}',
                data: values.length ? values : Array(14).fill(0),
                borderColor: '#059669',
                backgroundColor: 'rgba(5, 150, 105, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#059669',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });
});
</script>
@endsection
