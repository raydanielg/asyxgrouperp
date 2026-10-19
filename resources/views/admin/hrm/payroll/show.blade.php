@extends('layouts.admin')
@section('title', 'Payroll ' . $payroll->payroll_number . ' - ' . config('app.name'))
@section('page_title', 'Payroll ' . $payroll->payroll_number)
@section('content')
@php
  $pc = ['paid' => '#10b981', 'pending' => '#f59e0b', 'cancelled' => '#ef4444'][$payroll->status] ?? '#9ca3af';
@endphp

<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.payroll.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Payroll</a>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.payroll.pdf', $payroll) }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Download PDF</a>
        <form method="POST" action="{{ route('admin.payroll.destroy', $payroll) }}" onsubmit="return confirmAction(this, {title:'Delete {{ $payroll->payroll_number }}?', text:'This payroll record will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 text-xs font-medium text-red-500 hover:text-red-700">Delete</button>
        </form>
    </div>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-xs uppercase">
            {{ substr($payroll->employee?->first_name ?? '?', 0, 1) }}{{ substr($payroll->employee?->last_name ?? '', 0, 1) }}
        </div>
        <div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $payroll->payroll_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $pc }}"></span>{{ ucfirst($payroll->status) }}</span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5">{{ $payroll->employee?->full_name ?? 'N/A' }} · {{ $payroll->month }} {{ $payroll->year }}</p>
        </div>
    </div>
    <div class="text-right">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Net Salary</p>
        <p class="text-xl font-bold text-gray-900">{{ number_format($payroll->net_salary, 2) }} <span class="text-xs font-normal text-gray-400">TZS</span></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    {{-- Details --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h4></div>
        <dl class="p-5 space-y-2.5">
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Employee</dt><dd class="text-xs text-gray-700">{{ $payroll->employee?->full_name ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Emp. ID</dt><dd class="text-xs font-mono text-gray-700">{{ $payroll->employee?->employee_id ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Department</dt><dd class="text-xs text-gray-700">{{ $payroll->employee?->department ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Period</dt><dd class="text-xs text-gray-700">{{ $payroll->month }} {{ $payroll->year }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[11px] text-gray-400">Created</dt><dd class="text-xs text-gray-700">{{ $payroll->created_at?->format('d M Y') ?? '—' }}</dd></div>
        </dl>
    </div>

    {{-- Earnings --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Earnings</h4></div>
        <dl class="p-5 space-y-2.5">
            <div class="flex justify-between gap-4"><dt class="text-xs text-gray-500">Basic Salary</dt><dd class="text-xs font-mono text-gray-800">{{ number_format($payroll->basic_salary, 2) }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-xs text-gray-500">Allowances</dt><dd class="text-xs font-mono text-gray-800">{{ number_format($payroll->allowances ?? 0, 2) }}</dd></div>
            @if(($payroll->overtime ?? 0) > 0)
            <div class="flex justify-between gap-4"><dt class="text-xs text-gray-500">Overtime</dt><dd class="text-xs font-mono text-gray-800">{{ number_format($payroll->overtime, 2) }}</dd></div>
            @endif
            <div class="flex justify-between gap-4 border-t border-gray-100 pt-2.5"><dt class="text-xs font-bold text-gray-900">Gross</dt><dd class="text-xs font-bold font-mono text-gray-900">{{ number_format($payroll->basic_salary + ($payroll->allowances ?? 0) + ($payroll->overtime ?? 0), 2) }}</dd></div>
        </dl>
    </div>

    {{-- Deductions --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Deductions</h4></div>
        <dl class="p-5 space-y-2.5">
            @if(($payroll->deductions ?? 0) > 0)
            <div class="flex justify-between gap-4"><dt class="text-xs text-gray-500">PAYE</dt><dd class="text-xs font-mono text-gray-800">-{{ number_format($payroll->deductions * 0.6, 2) }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-xs text-gray-500">NSSF (10%)</dt><dd class="text-xs font-mono text-gray-800">-{{ number_format($payroll->deductions * 0.2, 2) }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-xs text-gray-500">NHIF</dt><dd class="text-xs font-mono text-gray-800">-{{ number_format($payroll->deductions * 0.1, 2) }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-xs text-gray-500">WCF / Other</dt><dd class="text-xs font-mono text-gray-800">-{{ number_format($payroll->deductions * 0.1, 2) }}</dd></div>
            @else
            <p class="text-xs text-gray-400">No deductions</p>
            @endif
            <div class="flex justify-between gap-4 border-t border-gray-100 pt-2.5"><dt class="text-xs font-bold text-gray-900">Total</dt><dd class="text-xs font-bold font-mono text-gray-900">-{{ number_format($payroll->deductions ?? 0, 2) }}</dd></div>
            <div class="flex justify-between gap-4 border-t-2 border-gray-800 pt-2.5"><dt class="text-xs font-bold text-gray-900">Net Pay</dt><dd class="text-sm font-bold font-mono text-gray-900">{{ number_format($payroll->net_salary, 2) }} <span class="text-[10px] font-normal text-gray-400">TZS</span></dd></div>
        </dl>
    </div>
</div>

{{-- Payslip preview --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Payslip PDF</h4>
        <a href="{{ url('/payslip/' . $payroll->id . '/preview?pdf=1') }}" target="_blank" class="text-xs text-gray-400 hover:text-gray-700">Open full size &rarr;</a>
    </div>
    <iframe src="{{ url('/payslip/' . $payroll->id . '/preview?pdf=1') }}" class="w-full border-0" style="height:900px;"></iframe>
</div>
@endsection
