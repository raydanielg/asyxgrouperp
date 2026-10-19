@extends('layouts.admin')
@section('title', 'Payroll - ' . config('app.name'))
@section('page_title', 'Payroll Management')
@section('content')

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-8">
        <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Records</p><p class="text-lg font-bold text-gray-900">{{ number_format($stats['total']) }}</p></div>
        <div class="h-8 w-px bg-gray-100"></div>
        <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total Paid</p><p class="text-lg font-bold text-emerald-600">{{ number_format($stats['paid']) }} <span class="text-xs font-normal text-gray-400">TZS</span></p></div>
        <div class="h-8 w-px bg-gray-100"></div>
        <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pending</p><p class="text-lg font-bold text-amber-600">{{ number_format($stats['pending']) }}</p></div>
        <div class="h-8 w-px bg-gray-100"></div>
        <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Avg Net</p><p class="text-lg font-bold text-gray-900">{{ number_format(round($stats['average'])) }}</p></div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.payroll.generate-form') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50 transition-colors">Generate Payroll</a>
        <button type="button" onclick="openPayDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Create Payroll</button>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <form method="GET" action="{{ route('admin.payroll.index') }}" class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-2">
        <select name="month" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Months</option>
            @foreach($months as $m)
                <option value="{{ $m }}" @selected(request('month') == $m)>{{ $m }}</option>
            @endforeach
        </select>
        <select name="year" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Years</option>
            @foreach($years as $y)
                <option value="{{ $y }}" @selected((string)request('year') == (string)$y)>{{ $y }}</option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Status</option>
            <option value="pending" @selected(request('status') == 'pending')>Pending</option>
            <option value="paid" @selected(request('status') == 'paid')>Paid</option>
            <option value="cancelled" @selected(request('status') == 'cancelled')>Cancelled</option>
        </select>
        <select name="employee_id" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
            <option value="">All Employees</option>
            @foreach($employees as $e)
                <option value="{{ $e->id }}" @selected(request('employee_id') == $e->id)>{{ $e->full_name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
        @if(request()->hasAny(['month','year','status','employee_id']))
        <a href="{{ route('admin.payroll.index') }}" class="text-xs text-gray-400 hover:text-gray-700">Clear</a>
        @endif
        <span class="ml-auto text-xs text-gray-400">{{ $payrolls->total() }} records</span>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Payroll #</th>
                    <th class="px-5 py-3 font-semibold">Employee</th>
                    <th class="px-5 py-3 font-semibold">Period</th>
                    <th class="px-5 py-3 font-semibold text-right">Basic</th>
                    <th class="px-5 py-3 font-semibold text-right">Allowances</th>
                    <th class="px-5 py-3 font-semibold text-right">Deductions</th>
                    <th class="px-5 py-3 font-semibold text-right">Net Salary</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($payrolls as $p)
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3.5"><span class="text-xs font-mono font-medium text-gray-800">{{ $p->payroll_number }}</span></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-[10px] uppercase">
                                {{ substr($p->employee?->first_name ?? '?', 0, 1) }}{{ substr($p->employee?->last_name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">{{ $p->employee?->full_name ?? 'N/A' }}</p>
                                <p class="text-[10px] font-mono text-gray-400">{{ $p->employee?->employee_id ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5"><span class="text-xs text-gray-600">{{ $p->month }} / {{ $p->year }}</span></td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-700">{{ number_format($p->basic_salary) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono text-gray-600">{{ number_format($p->allowances) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-mono {{ $p->deductions > 0 ? 'text-red-500' : 'text-gray-300' }}">{{ number_format($p->deductions) }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-bold font-mono text-gray-900">{{ number_format($p->net_salary) }}</td>
                    <td class="px-5 py-3.5">
                        @php $pc = ['paid' => '#10b981', 'pending' => '#f59e0b', 'cancelled' => '#ef4444'][$p->status] ?? '#9ca3af'; @endphp
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $pc }}"></span>{{ ucfirst($p->status) }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.payroll.show', $p) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                            <a href="{{ route('admin.payroll.pdf', $p) }}" class="text-xs font-medium text-gray-400 hover:text-gray-900" target="_blank">PDF</a>
                            <form method="POST" action="{{ route('admin.payroll.destroy', $p) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete {{ $p->payroll_number }}?', text:'This payroll record will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-5 py-10 text-center text-xs text-gray-400">No payroll records found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($payrolls->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $payrolls->links() }}</div>
    @endif
</div>

{{-- ═══ Create Payroll Drawer ═══ --}}
<div id="payDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePayDrawer()"></div>
    <div id="payDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Create Payroll</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Single employee record</p>
            </div>
            <button type="button" onclick="closePayDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.payroll.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required id="payrollEmployee" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" data-salary="{{ $e->salary }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Payroll Number <span class="text-red-500">*</span></label>
                    <input name="payroll_number" required value="PAY-{{ date('Ymd') }}-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}" class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Month <span class="text-red-500">*</span></label>
                        <select name="month" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            @foreach($months as $m)
                                <option value="{{ $m }}" @selected($m == date('F'))>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Year <span class="text-red-500">*</span></label>
                        <input name="year" type="number" required value="{{ date('Y') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Basic Salary (TZS) <span class="text-red-500">*</span></label>
                    <input name="basic_salary" id="payrollBasic" type="number" step="0.01" min="0" required value="0" oninput="calcPayroll()" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Allowances</label>
                        <input name="allowances" id="payrollAllow" type="number" step="0.01" min="0" value="0" oninput="calcPayroll()" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Deductions</label>
                        <input name="deductions" id="payrollDeduct" type="number" step="0.01" min="0" value="0" oninput="calcPayroll()" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-md px-4 py-3">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-1">Net Salary (TZS)</label>
                    <input name="net_salary" id="payrollNet" type="number" step="0.01" required value="0" readonly class="w-full bg-transparent text-xl font-bold text-gray-900 outline-none">
                    <p class="text-[10px] text-gray-400">Basic + Allowances − Deductions</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Payroll</button>
                <button type="button" onclick="closePayDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openPayDrawer() {
    document.getElementById('payDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('payDrawerPanel').classList.remove('-translate-x-full'));
}
function closePayDrawer() {
    document.getElementById('payDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('payDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closePayDrawer(); });

document.getElementById('payrollEmployee').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    document.getElementById('payrollBasic').value = opt.dataset.salary || 0;
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
