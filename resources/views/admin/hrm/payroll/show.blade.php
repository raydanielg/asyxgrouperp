@extends('layouts.admin')
@section('title', 'Payslip - ' . $payroll->payroll_number)
@section('page_title', 'Payslip Detail')
@section('content')

<div class="max-w-4xl mx-auto">
    {{-- Actions --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.payroll.index') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">&larr; Back to Payroll</a>
            <h3 class="text-sm font-bold text-gray-800 mt-1">Payslip: {{ $payroll->payroll_number }}</h3>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.payroll.pdf', $payroll) }}" target="_blank" class="px-4 py-2 bg-red-500 text-white text-xs font-bold rounded-xl hover:bg-red-600 transition-colors shadow-lg shadow-red-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-50 text-gray-600 text-xs font-bold rounded-xl border border-gray-200 hover:bg-gray-100 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    {{-- Salary Slip Card --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden print:shadow-none print:border-none" id="payslip">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-8 py-6 print:px-6 print:py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">PAYSLIP</h1>
                    <p class="text-emerald-100 text-xs mt-1">{{ $payroll->month }} {{ $payroll->year }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-white">{{ config('app.name') }}</p>
                    <p class="text-emerald-100 text-[10px]">{{ auth()->user()->company?->name ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Employee Info --}}
        <div class="px-8 py-5 border-b border-gray-100 print:px-6 print:py-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Employee Name</p>
                    <p class="text-sm font-bold text-gray-900 mt-1">{{ $payroll->employee?->full_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Employee ID</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">{{ $payroll->employee?->employee_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Department</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1">{{ $payroll->employee?->department ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Payroll Number</p>
                    <p class="text-sm font-semibold text-gray-700 mt-1 font-mono">{{ $payroll->payroll_number }}</p>
                </div>
            </div>
        </div>

        {{-- Salary Details --}}
        <div class="px-8 py-5 print:px-6 print:py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Earnings --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Earnings
                    </h3>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">Basic Salary</span>
                            <span class="text-xs font-semibold text-gray-900 font-mono">TZS {{ number_format($payroll->basic_salary, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">Housing Allowance</span>
                            <span class="text-xs font-semibold text-emerald-600 font-mono">TZS {{ number_format($payroll->allowances * 0.5, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">Transport Allowance</span>
                            <span class="text-xs font-semibold text-emerald-600 font-mono">TZS {{ number_format($payroll->allowances * 0.3, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">Medical Allowance</span>
                            <span class="text-xs font-semibold text-emerald-600 font-mono">TZS {{ number_format($payroll->allowances * 0.2, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs font-bold text-gray-800">Total Earnings</span>
                            <span class="text-xs font-bold text-emerald-600 font-mono">TZS {{ number_format($payroll->basic_salary + $payroll->allowances, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Deductions --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        Deductions
                    </h3>
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">PAYE Tax</span>
                            <span class="text-xs font-semibold text-red-500 font-mono">TZS {{ number_format($payroll->deductions * 0.6, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">NSSF (Employee)</span>
                            <span class="text-xs font-semibold text-red-500 font-mono">TZS {{ number_format($payroll->deductions * 0.2, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">NHIF / Health Insurance</span>
                            <span class="text-xs font-semibold text-red-500 font-mono">TZS {{ number_format($payroll->deductions * 0.1, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-50">
                            <span class="text-xs text-gray-600">Other Deductions</span>
                            <span class="text-xs font-semibold text-red-500 font-mono">TZS {{ number_format($payroll->deductions * 0.1, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs font-bold text-gray-800">Total Deductions</span>
                            <span class="text-xs font-bold text-red-500 font-mono">TZS {{ number_format($payroll->deductions, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Net Salary --}}
        <div class="mx-8 mb-5 print:mx-6 print:mb-4">
            <div class="bg-gradient-to-r from-emerald-50 to-emerald-100/50 rounded-xl px-6 py-4 border border-emerald-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider">Net Salary</p>
                        <p class="text-[10px] text-emerald-500 mt-0.5">Amount payable to employee</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-emerald-800 font-mono">TZS {{ number_format($payroll->net_salary, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status & Footer --}}
        <div class="px-8 py-4 border-t border-gray-100 print:px-6 print:py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div>
                        <p class="text-[10px] font-medium text-gray-400 uppercase">Status</p>
                        @php
                            $statusStyles = [
                                'paid' => 'bg-emerald-100 text-emerald-700',
                                'pending' => 'bg-amber-100 text-amber-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $style = $statusStyles[$payroll->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold mt-0.5 {{ $style }}">{{ ucfirst($payroll->status) }}</span>
                    </div>
                    <div>
                        <p class="text-[10px] font-medium text-gray-400 uppercase">Payment Method</p>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5">Bank Transfer</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-medium text-gray-400 uppercase">Created By</p>
                    <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ $payroll->creator?->name ?? 'System' }}</p>
                </div>
            </div>
        </div>

        {{-- Signature Area --}}
        <div class="px-8 py-5 border-t border-gray-100 print:px-6 print:py-4">
            <div class="grid grid-cols-2 gap-8">
                <div class="text-center">
                    <div class="border-t border-gray-300 pt-2 mt-8">
                        <p class="text-xs font-semibold text-gray-700">{{ $payroll->employee?->full_name ?? 'N/A' }}</p>
                        <p class="text-[10px] text-gray-400">Employee Signature</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="border-t border-gray-300 pt-2 mt-8">
                        <p class="text-xs font-semibold text-gray-700">{{ $payroll->creator?->name ?? 'Authorized Signatory' }}</p>
                        <p class="text-[10px] text-gray-400">Authorized Signature</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="px-8 py-3 bg-gray-50/50 border-t border-gray-100 text-center print:px-6 print:py-2">
            <p class="text-[10px] text-gray-400">This is a computer-generated payslip. For any discrepancies, please contact the Finance/HR department.</p>
            <p class="text-[10px] text-gray-300 mt-0.5">Generated on {{ now()->format('d M Y H:i:s') }} | {{ config('app.name') }}</p>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #payslip, #payslip * { visibility: visible; }
    #payslip { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>
@endpush
@endsection
