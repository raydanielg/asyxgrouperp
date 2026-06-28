@extends('layouts.admin')
@section('title', 'HR Officer Dashboard')
@section('page_title', 'HR Officer Dashboard')
@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n); @endphp
<div class="bg-gradient-to-r from-violet-600 to-violet-800 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div><h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2><p class="text-violet-100 text-sm mt-1">HR Officer Dashboard - Human Resources</p></div>
        <div class="text-right"><p class="text-violet-100 text-xs">{{ now()->format('l, d M Y') }}</p><p class="text-violet-200 text-[10px] mt-1">{{ now()->format('H:i') }}</p></div>
    </div>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 border border-emerald-500 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-emerald-100">Total Employees</span><p class="text-xl font-bold mt-1">{{ $stats['totalEmployees'] ?? 0 }}</p></div>
    </div>
    <div class="bg-gradient-to-br from-sky-500 to-sky-600 border border-sky-400 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-sky-100">Active</span><p class="text-xl font-bold mt-1">{{ $stats['activeEmployees'] ?? 0 }}</p></div>
    </div>
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 border border-amber-300 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-amber-50">Pending Leaves</span><p class="text-xl font-bold mt-1">{{ $stats['pendingLeaves'] ?? 0 }}</p></div>
    </div>
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 border border-emerald-500 rounded-xl p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10"><span class="text-[10px] font-medium text-emerald-100">Present Today</span><p class="text-xl font-bold mt-1">{{ $stats['todayAttendance'] ?? 0 }}</p></div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Attendance Trend (14 days)</h3>
        <canvas id="hrChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Employees
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-sky-50 hover:bg-sky-100 text-sky-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-6 9l2 2 4-4"/></svg>
                Attendance
            </a>
            <a href="{{ route('admin.leaves.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Leaves
            </a>
            <a href="{{ route('admin.payroll.index') }}" class="flex items-center gap-3 px-4 py-2.5 bg-violet-50 hover:bg-violet-100 text-violet-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8"/></svg>
                Payroll
            </a>
        </div>
    </div>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @foreach($secondaryKpis as $kpi)
        <a href="{{ route($kpi['route']) }}" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <span class="text-[10px] font-medium text-gray-500">{{ $kpi['label'] }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $kpi['value'] }}</p>
    </a>
        @endforeach
        </div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Employees</h3>
            <a href="{{ route('admin.employees.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @if(!empty($recentItems['recentEmployees']))
            @foreach($recentItems['recentEmployees']->take(5) as $emp)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</p><p class="text-[10px] text-gray-400">{{ $emp->position ?? $emp->department ?? '' }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $emp->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
            @else
        <div class="px-5 py-8 text-center text-gray-400 text-xs">No employees</div>
        @endif
        </div>
    </div>
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Pending Leave Requests</h3>
            <a href="{{ route('admin.leaves.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @if(!empty($recentItems['pendingLeaves']))
            @foreach($recentItems['pendingLeaves']->take(5) as $leave)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $leave->employee?->first_name ?? '' }} {{ $leave->employee?->last_name ?? '' }}</p><p class="text-[10px] text-gray-400">{{ $leave->leave_type ?? '' }}</p></div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">Pending</span>
            </div>
        @endforeach
            @else
        <div class="px-5 py-8 text-center text-gray-400 text-xs">No pending leaves</div>
        @endif
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('hrChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: { labels: @json($chartData['labels']), datasets: [{ label: 'Present', data: @json($chartData['values']), borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,0.1)', fill: true, tension: 0.3 }] },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } }, scales: { y: { beginAtZero: true, ticks: { font: { size: 9 } } }, x: { ticks: { font: { size: 8 } } } } }
    });
}
</script>
@endsection
