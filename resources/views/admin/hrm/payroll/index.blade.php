@extends('layouts.admin')
@section('title', 'Payroll - ' . config('app.name'))
@section('page_title', 'Payroll Management')
@section('content')

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Records</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Paid (TZS)</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['paid']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['pending']) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Average Net (TZS)</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format(round($stats['average'])) }}</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<div class="bg-white rounded-xl border mb-6 p-4">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h3 class="text-sm font-semibold text-gray-800">Payroll Records</h3>
            <span class="text-xs text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">{{ $payrolls->total() }} total</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.payroll.generate-form') }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-medium rounded-lg hover:bg-indigo-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Generate Payroll
            </a>
            <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 text-xs font-medium rounded-lg hover:bg-emerald-100 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Payroll
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.payroll.index') }}" class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-500">Month</label>
            <select name="month" class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                <option value="">All</option>
                @foreach($months as $m)
                    <option value="{{ $m }}" @selected(request('month') == $m)>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-500">Year</label>
            <select name="year" class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                <option value="">All</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" @selected((string)request('year') == (string)$y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-500">Status</label>
            <select name="status" class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                <option value="">All</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="paid" @selected(request('status') == 'paid')>Paid</option>
                <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelled</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs font-medium text-gray-500">Employee</label>
            <select name="employee_id" class="px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                <option value="">All</option>
                @foreach($employees as $e)
                    <option value="{{ $e->id }}" @selected(request('employee_id') == $e->id)>{{ $e->full_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-3 py-1.5 bg-gray-50 text-gray-600 text-xs font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">Filter</button>
        <a href="{{ route('admin.payroll.index') }}" class="px-3 py-1.5 text-gray-400 text-xs hover:text-gray-600 transition-colors">Clear</a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50/50 border-b border-gray-100">
                    <th class="px-5 py-3.5 font-semibold">Payroll #</th>
                    <th class="px-5 py-3.5 font-semibold">Employee</th>
                    <th class="px-5 py-3.5 font-semibold">Period</th>
                    <th class="px-5 py-3.5 font-semibold text-right">Basic</th>
                    <th class="px-5 py-3.5 font-semibold text-right text-emerald-600">Allowances</th>
                    <th class="px-5 py-3.5 font-semibold text-right text-red-500">Deductions</th>
                    <th class="px-5 py-3.5 font-semibold text-right">Net Salary</th>
                    <th class="px-5 py-3.5 font-semibold text-center">Status</th>
                    <th class="px-5 py-3.5 font-semibold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payrolls as $p)
                <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-mono font-medium text-gray-700">{{ $p->payroll_number }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-xs font-bold text-emerald-600 uppercase">
                                {{ substr($p->employee?->first_name ?? '?', 0, 1) }}{{ substr($p->employee?->last_name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">{{ $p->employee?->full_name ?? 'N/A' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $p->employee?->employee_id ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs text-gray-600">{{ $p->month }}</span>
                        <span class="text-xs text-gray-400">/</span>
                        <span class="text-xs text-gray-600">{{ $p->year }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-right text-gray-700 font-mono">TZS {{ number_format($p->basic_salary, 2) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right text-emerald-600 font-mono">TZS {{ number_format($p->allowances, 2) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right text-red-500 font-mono">TZS {{ number_format($p->deductions, 2) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-bold text-gray-900 font-mono">TZS {{ number_format($p->net_salary, 2) }}</td>
                    <td class="px-5 py-3.5 text-center">
                        @php
                            $statusStyles = [
                                'paid' => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20',
                                'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20',
                                'cancelled' => 'bg-red-50 text-red-700 ring-1 ring-red-600/20',
                            ];
                            $style = $statusStyles[$p->status] ?? 'bg-gray-50 text-gray-600 ring-1 ring-gray-600/20';
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $style }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.payroll.show', $p) }}" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.payroll.pdf', $p) }}" class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors" title="Download PDF" target="_blank">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </a>
                            <form id="del-pay-{{ $p->id }}" method="POST" action="{{ route('admin.payroll.destroy', $p) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('del-pay-{{ $p->id }}')" class="p-1.5 rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-gray-400 font-medium">No payroll records found</p>
                            <p class="text-xs text-gray-300">Create a new payroll or adjust your filters</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/30">
        {{ $payrolls->links() }}
    </div>
</div>

{{-- Create Modal --}}
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900">Create Payroll</h3>
            <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="p-1 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.payroll.store') }}" class="space-y-4">@csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Employee *</label>
                <select name="employee_id" required id="payrollEmployee" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                    <option value="">Select Employee</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" data-salary="{{ $e->salary }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Payroll Number *</label>
                <input name="payroll_number" required value="PAY-{{ date('Ymd') }}-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Month *</label>
                    <select name="month" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                        @foreach($months as $m)
                            <option value="{{ $m }}" @selected($m == date('F'))>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Year *</label>
                    <input name="year" type="number" required value="{{ date('Y') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Basic Salary *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">TZS</span>
                    <input name="basic_salary" id="payrollBasic" type="number" step="0.01" required value="0" oninput="calcPayroll()" class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Allowances</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">TZS</span>
                        <input name="allowances" id="payrollAllow" type="number" step="0.01" value="0" oninput="calcPayroll()" class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deductions</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">TZS</span>
                        <input name="deductions" id="payrollDeduct" type="number" step="0.01" value="0" oninput="calcPayroll()" class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Net Salary *</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">TZS</span>
                    <input name="net_salary" id="payrollNet" type="number" step="0.01" required value="0" readonly class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-emerald-200 text-sm font-bold bg-emerald-50/50 text-emerald-800 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white">
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-200">Create Payroll</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('payrollEmployee').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var salary = opt.dataset.salary || 0;
    document.getElementById('payrollBasic').value = salary;
    calcPayroll();
});
function calcPayroll() {
    var basic = parseFloat(document.getElementById('payrollBasic').value) || 0;
    var allow = parseFloat(document.getElementById('payrollAllow').value) || 0;
    var deduct = parseFloat(document.getElementById('payrollDeduct').value) || 0;
    document.getElementById('payrollNet').value = (basic + allow - deduct).toFixed(2);
}
</script>
@endpush
@endsection
