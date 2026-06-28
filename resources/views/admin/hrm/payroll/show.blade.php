@extends('layouts.admin')
@section('title', 'Payslip - ' . $payroll->payroll_number)
@section('page_title', '')
@section('content')

<div class="max-w-5xl mx-auto">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-6 no-print">
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Payroll
            </a>
            <h3 class="text-sm font-bold text-gray-800 mt-1">Payslip: <span class="font-mono text-emerald-600">{{ $payroll->payroll_number }}</span></h3>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white text-gray-600 text-xs font-bold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="{{ route('admin.payroll.pdf', $payroll) }}" target="_blank" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-xs font-bold rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all flex items-center gap-2 shadow-lg shadow-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- ═══ PAYSLIP A4 SECTION ═══ --}}
    <div id="payslip-a4" class="bg-white rounded-2xl border shadow-sm overflow-hidden print:shadow-none print:border-0 print:rounded-none" style="font-family: 'Inter', 'Nunito', system-ui, sans-serif;">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-emerald-500 px-8 py-6 print:px-10 print:py-7">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center p-2">
                        <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-white tracking-tight">PAYSLIP</h1>
                        <p class="text-emerald-100 text-xs font-medium mt-0.5">{{ $payroll->month }} {{ $payroll->year }} &bull; {{ $payroll->payroll_number }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-white">{{ config('app.name') }}</p>
                    <p class="text-[10px] text-emerald-200 font-medium">{{ auth()->user()->company?->name ?? 'Enterprise ERP' }}</p>
                    <p class="text-[9px] text-emerald-300/60 mt-1 font-mono">{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Employee Info --}}
        <div class="px-8 py-6 print:px-10">
            <div class="grid grid-cols-4 gap-x-8 gap-y-4">
                <div class="col-span-2">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">Employee Name</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $payroll->employee?->full_name ?? 'N/A' }}</p>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-50 text-[10px] font-medium text-gray-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-4 0a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                            {{ $payroll->employee?->employee_id ?? 'N/A' }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-50 text-[10px] font-medium text-gray-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $payroll->employee?->department ?? 'N/A' }}
                        </span>
                    </div>
                </div>
                <div>
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">Designation</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $payroll->employee?->designation ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">Status</p>
                    @php
                        $statusStyles = [
                            'paid' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
                            'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
                            'cancelled' => 'bg-red-50 text-red-700 ring-1 ring-red-600/20',
                        ];
                        $style = $statusStyles[$payroll->status] ?? 'bg-gray-50 text-gray-600 ring-1 ring-gray-600/20';
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[11px] font-bold mt-1 {{ $style }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $payroll->status == 'paid' ? 'bg-emerald-500' : ($payroll->status == 'pending' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                        {{ strtoupper($payroll->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Separator --}}
        <div class="mx-8 print:mx-10 border-t border-gray-100"></div>

        {{-- Salary Breakdown --}}
        <div class="px-8 py-6 print:px-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- EARNINGS --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                        </div>
                        <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider">Earnings</h3>
                    </div>
                    <div class="bg-emerald-50/30 rounded-xl border border-emerald-100/60 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[10px] text-gray-500 bg-emerald-50/50 border-b border-emerald-100">
                                    <th class="px-4 py-2.5 font-semibold">Description</th>
                                    <th class="px-4 py-2.5 font-semibold text-right">Amount (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-emerald-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">Basic Salary</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-gray-900 text-right font-mono">{{ number_format($payroll->basic_salary, 2) }}</td>
                                </tr>
                                <tr class="border-b border-emerald-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">Housing Allowance</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-emerald-600 text-right font-mono">{{ number_format($payroll->allowances * 0.5, 2) }}</td>
                                </tr>
                                <tr class="border-b border-emerald-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">Transport Allowance</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-emerald-600 text-right font-mono">{{ number_format($payroll->allowances * 0.3, 2) }}</td>
                                </tr>
                                <tr class="border-b border-emerald-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">Medical Allowance</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-emerald-600 text-right font-mono">{{ number_format($payroll->allowances * 0.2, 2) }}</td>
                                </tr>
                                <tr class="bg-emerald-50/50">
                                    <td class="px-4 py-3 text-xs font-black text-gray-800">Total Earnings</td>
                                    <td class="px-4 py-3 text-xs font-black text-emerald-700 text-right font-mono">{{ number_format($payroll->basic_salary + $payroll->allowances, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- DEDUCTIONS --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </div>
                        <h3 class="text-xs font-black text-gray-800 uppercase tracking-wider">Deductions</h3>
                    </div>
                    <div class="bg-red-50/30 rounded-xl border border-red-100/60 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[10px] text-gray-500 bg-red-50/50 border-b border-red-100">
                                    <th class="px-4 py-2.5 font-semibold">Description</th>
                                    <th class="px-4 py-2.5 font-semibold text-right">Amount (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-red-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">PAYE Tax</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-red-500 text-right font-mono">{{ number_format($payroll->deductions * 0.6, 2) }}</td>
                                </tr>
                                <tr class="border-b border-red-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">NSSF (Employee)</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-red-500 text-right font-mono">{{ number_format($payroll->deductions * 0.2, 2) }}</td>
                                </tr>
                                <tr class="border-b border-red-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">NHIF / Health Insurance</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-red-500 text-right font-mono">{{ number_format($payroll->deductions * 0.1, 2) }}</td>
                                </tr>
                                <tr class="border-b border-red-50">
                                    <td class="px-4 py-2.5 text-xs text-gray-700">Other Deductions</td>
                                    <td class="px-4 py-2.5 text-xs font-semibold text-red-500 text-right font-mono">{{ number_format($payroll->deductions * 0.1, 2) }}</td>
                                </tr>
                                <tr class="bg-red-50/50">
                                    <td class="px-4 py-3 text-xs font-black text-gray-800">Total Deductions</td>
                                    <td class="px-4 py-3 text-xs font-black text-red-600 text-right font-mono">{{ number_format($payroll->deductions, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- NET SALARY BAR --}}
        <div class="mx-8 mb-6 print:mx-10 print:mb-8">
            <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-emerald-400 rounded-xl shadow-lg shadow-emerald-200/50 p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-emerald-100 uppercase tracking-widest">Net Salary</p>
                            <p class="text-[11px] text-emerald-200/80 font-medium">Amount payable to employee</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-black text-white font-mono tracking-tight">TZS {{ number_format($payroll->net_salary, 2) }}</p>
                        <p class="text-[10px] text-emerald-200/70 font-medium mt-0.5">{{ ucfirst($payroll->month) }} {{ $payroll->year }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Details --}}
        <div class="mx-8 mb-6 print:mx-10">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Payment Method</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Bank Transfer
                        </span>
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Created By</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $payroll->creator?->name ?? 'System' }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Payment Date</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $payroll->created_at?->format('d M Y') ?? now()->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="mx-8 mb-6 print:mx-10">
            <div class="grid grid-cols-2 gap-8 mt-4 pt-6">
                <div class="text-center">
                    <div class="w-40 h-0.5 bg-gray-300 mx-auto mb-2"></div>
                    <p class="text-sm font-bold text-gray-800">{{ $payroll->employee?->full_name ?? 'N/A' }}</p>
                    <p class="text-[10px] text-gray-400 font-medium">Employee Signature</p>
                </div>
                <div class="text-center">
                    <div class="w-40 h-0.5 bg-gray-300 mx-auto mb-2"></div>
                    <p class="text-sm font-bold text-gray-800">{{ $payroll->creator?->name ?? 'Authorized Signatory' }}</p>
                    <p class="text-[10px] text-gray-400 font-medium">Authorized Signature</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50/80 border-t border-gray-100 px-8 py-4 print:px-10 print:py-3">
            <div class="flex items-center justify-between">
                <p class="text-[9px] text-gray-400">This is a computer-generated payslip.</p>
                <p class="text-[9px] text-gray-300 font-mono">Generated {{ now()->format('d M Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    @page { margin: 0; size: A4; }
    body * { visibility: hidden; }
    #payslip-a4, #payslip-a4 * { visibility: visible; }
    #payslip-a4 { position: absolute; left: 0; top: 0; width: 210mm; min-height: 297mm; }
    .no-print { display: none !important; }
}
</style>
@endpush
@endsection
