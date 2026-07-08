@extends('layouts.admin')
@section('title', 'SGR Agent Dashboard')
@section('page_title', 'SGR Agent Dashboard')
@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n); @endphp
<div class="bg-gradient-to-r from-amber-700 to-amber-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-20 w-24 h-24 bg-white/5 rounded-full -mb-12"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <svg class="w-6 h-6 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                <h2 class="text-2xl font-bold">SGR Agent Dashboard</h2>
            </div>
            <p class="text-amber-100 text-sm mt-1">Welcome, {{ auth()->user()->name }} — SGR Action Points Management</p>
        </div>
        <div class="text-right"><p class="text-amber-100 text-xs">{{ now()->format('l, d M Y') }}</p><p class="text-amber-200 text-[10px] mt-1">{{ now()->format('H:i') }}</p></div>
    </div>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach($kpiCards as $card)
    @php $colors = ['emerald' => 'from-emerald-600 to-emerald-700 border-emerald-500', 'sky' => 'from-sky-500 to-sky-600 border-sky-400', 'amber' => 'from-amber-400 to-amber-500 border-amber-300', 'rose' => 'from-rose-500 to-rose-600 border-rose-400', 'violet' => 'from-violet-500 to-violet-600 border-violet-400']; $cc = $colors[$card['color']] ?? $colors['emerald']; @endphp
    <div class="bg-gradient-to-br {{ $cc }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium text-white/80">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>
@include('roles.shared.ai-insights')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">{{ $chartData['title'] }}</h3>
        <canvas id="roleChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
        @foreach($quickActions as $action)
        <a href="{{ route($action['route'], $action['params'] ?? []) }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
            {{ $action['label'] }}
        </a>
        @endforeach
        </div>
    </div>
</div>
@if(!empty($secondaryKpis))
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
    @foreach($secondaryKpis as $kpi)
    <a href="{{ route($kpi['route'], $kpi['params'] ?? []) }}" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <span class="text-[10px] font-medium text-gray-500">{{ $kpi['label'] }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $kpi['value'] }}</p>
    </a>
    @endforeach
</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @if(!empty($recentItems['recentActionPoints']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Action Points</h3>
            <a href="{{ route('role.page', ['module' => 'action-points-reports']) }}" class="text-[10px] text-amber-600 hover:text-amber-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentActionPoints'] as $ap)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full @if($ap->approval_status === 'approved') bg-emerald-500 @elseif($ap->approval_status === 'rejected') bg-red-500 @else bg-amber-400 @endif"></span>
                <div><p class="text-xs font-medium text-gray-900">{{ Str::limit($ap->activity, 40) }}</p><p class="text-[10px] text-gray-400">{{ $ap->responsible_person }} &middot; {{ $ap->due_date?->format('d M') }}</p></div>
            </div>
            <span class="text-[10px] px-2 py-0.5 rounded-full @if($ap->approval_status === 'approved') bg-emerald-50 text-emerald-700 @elseif($ap->approval_status === 'rejected') bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif font-medium">{{ ucfirst($ap->approval_status) }}</span>
        </div>
        @endforeach
        </div>
    </div>
    @endif
    @if(!empty($recentItems['pendingActionPoints']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Pending Approval</h3>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-medium">{{ count($recentItems['pendingActionPoints']) }} items</span>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($recentItems['pendingActionPoints'] as $ap)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
            <div><p class="text-xs font-medium text-gray-900">{{ Str::limit($ap->activity, 45) }}</p><p class="text-[10px] text-gray-400">Due: {{ $ap->due_date?->format('d M Y') ?? 'N/A' }}</p></div>
            <span class="text-[10px] text-gray-400">{{ $ap->created_at->format('d M') }}</span>
        </div>
        @empty
        <div class="px-5 py-6 text-center text-gray-400 text-xs">No pending items</div>
        @endforelse
        </div>
    </div>
    @endif
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('roleChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartData['labels'] ?? array_keys($chartData['values'] ?? [])) !!},
        datasets: [{
            label: '{{ $chartData["title"] }}',
            data: {!! json_encode(array_values($chartData['values'] ?? [])) !!},
            backgroundColor: 'rgba(217, 119, 6, 0.7)',
            borderColor: 'rgba(217, 119, 6, 1)',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endpush