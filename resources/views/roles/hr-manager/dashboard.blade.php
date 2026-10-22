@extends('layouts.admin')
@section('title', 'HR Manager')
@section('page_title', 'People Command')
@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n ?? 0);
    $attRate = ($stats['activeEmployees'] ?? 0) > 0 ? round(($stats['todayAttendance'] ?? 0) / max($stats['activeEmployees'],1) * 100) : 0;
@endphp

<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">People Command</p>
            <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-slate-400 text-xs mt-1">Workforce, attendance, leave, payroll.</p>
        </div>
        <div class="text-right">
            <p class="text-slate-300 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-slate-500 text-[10px] mt-1">Attendance today: {{ $attRate }}%</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Employees</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['activeEmployees'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">of {{ $stats['totalEmployees'] ?? 0 }} total</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Present today</span>
        <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $stats['todayAttendance'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $attRate }}% rate</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Absent today</span>
        <p class="text-2xl font-bold {{ ($stats['absentToday'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $stats['absentToday'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">recorded absences</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pending leave</span>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pendingLeaves'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">awaiting decision</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Approvals</span>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pendingApprovals'] ?? 0 }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">awaiting you</p>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Payroll</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $money($stats['totalPayroll']) }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">total base salary</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
    <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Today's Attendance</h3>
            <a href="{{ route('role.page', ['module' => 'attendance']) }}" class="text-[10px] text-slate-600 hover:text-slate-800">All records →</a>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div class="border border-gray-100 rounded p-4 text-center">
                <p class="text-2xl font-bold text-emerald-700">{{ $stats['todayAttendance'] ?? 0 }}</p>
                <p class="text-[10px] text-gray-400 mt-1 uppercase">Present</p>
            </div>
            <div class="border border-gray-100 rounded p-4 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $stats['absentToday'] ?? 0 }}</p>
                <p class="text-[10px] text-gray-400 mt-1 uppercase">Absent</p>
            </div>
            <div class="border border-gray-100 rounded p-4 text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $stats['pendingLeaves'] ?? 0 }}</p>
                <p class="text-[10px] text-gray-400 mt-1 uppercase">On leave</p>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-100">
            <span class="text-[10px] uppercase text-gray-400">Attendance rate</span>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1.5"><div class="bg-emerald-600 h-1.5 rounded-full" style="width:{{ $attRate }}%"></div></div>
        </div>
    </div>
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">People Pulse</h3>
        <div class="space-y-2.5">
            @php
            $pulse = [
                ['label' => 'Pending leave requests', 'value' => $stats['pendingLeaves'] ?? 0, 'link' => 'leaves'],
                ['label' => 'Pending approvals', 'value' => $stats['pendingApprovals'] ?? 0, 'link' => 'approvals'],
                ['label' => 'Absent today', 'value' => $stats['absentToday'] ?? 0, 'link' => 'attendance'],
            ];
            $dot = fn($v) => $v > 0 ? '#f59e0b' : '#94a3b8';
            @endphp
            @foreach($pulse as $p)
            <a href="{{ route('role.page', ['module' => $p['link']]) }}" class="flex items-center justify-between hover:bg-gray-50 -mx-2 px-2 py-1 rounded">
                <span class="text-xs text-gray-600">{{ $p['label'] }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-900"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dot($p['value']) }}"></span>{{ $p['value'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5 mb-5">
    <h3 class="text-sm font-bold text-gray-900 mb-4">HR Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
        @php
        $actions = [
            ['module' => 'employees', 'label' => 'Employees'],
            ['module' => 'leaves', 'label' => 'Leave Requests'],
            ['module' => 'attendance', 'label' => 'Attendance'],
            ['module' => 'payroll', 'label' => 'Payroll'],
            ['module' => 'recruitment', 'label' => 'Recruitment'],
            ['module' => 'performance', 'label' => 'Performance'],
            ['module' => 'training', 'label' => 'Training'],
            ['module' => 'approvals', 'label' => 'Approvals'],
        ];
        @endphp
        @foreach($actions as $a)
        <a href="{{ route('role.page', ['module' => $a['module']]) }}" class="flex items-center justify-between px-3 py-2 bg-gray-50 hover:bg-gray-100 rounded text-xs font-medium text-gray-700">
            {{ $a['label'] }}
            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>
</div>

@include('roles.shared.ai-insights')
@endsection
