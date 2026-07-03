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

    {{-- AI Insights --}}
    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-xl border border-emerald-100 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            AI Insights
        </h3>
        <p class="text-xs text-gray-600 mb-3">{{ $aiInsights['message'] ?? 'No actionable insights at this time.' }}</p>
        @if(!empty($aiInsights['suggestions']))
            <ul class="space-y-2">
                @foreach($aiInsights['suggestions'] as $suggestion)
                    <li class="flex items-start gap-2 text-xs text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                        <span>{{ $suggestion }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

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

{{-- Quick Actions --}}
@if(!empty($quickActions))
<div class="bg-white rounded-xl border p-5 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Quick Actions
    </h3>
    <div class="flex flex-wrap gap-3">
        @foreach($quickActions as $action)
        <a href="{{ route($action['route']) }}" class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors border border-emerald-100">
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
document.addEventListener('DOMContentLoaded', function() {
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
