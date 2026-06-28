@extends('layouts.admin')
@section('title', 'Payroll - ' . config('app.name'))
@section('page_title', 'Payroll')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage employee payroll and payslips</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Payroll
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Payroll #</th><th class="px-5 py-3 font-medium">Employee</th><th class="px-5 py-3 font-medium">Month</th><th class="px-5 py-3 font-medium">Basic</th><th class="px-5 py-3 font-medium">Allowances</th><th class="px-5 py-3 font-medium">Deductions</th><th class="px-5 py-3 font-medium">Net Salary</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($payrolls as $p)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $p->payroll_number }}</td>
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $p->employee?->full_name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $p->month }} {{ $p->year }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">TZS {{ number_format($p->basic_salary) }}</td>
            <td class="px-5 py-3 text-xs text-emerald-600">TZS {{ number_format($p->allowances) }}</td>
            <td class="px-5 py-3 text-xs text-red-500">TZS {{ number_format($p->deductions) }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($p->net_salary) }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium @if($p->status=='paid')bg-emerald-50 text-emerald-700 @elseif($p->status=='pending')bg-amber-50 text-amber-700 @else bg-gray-50 text-gray-600 @endif">{{ ucfirst($p->status) }}</span></td>
            <td class="px-5 py-3"><form id="del-pay-{{ $p->id }}" method="POST" action="{{ route('admin.payroll.destroy', $p) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-pay-{{ $p->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="9" class="px-5 py-8 text-center text-gray-400 text-xs">No payroll records</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $payrolls->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Payroll</h3>
        <form method="POST" action="{{ route('admin.payroll.store') }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label><select name="employee_id" required id="payrollEmployee" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Select...</option>
        @foreach($employees as $e)
        <option value="{{ $e->id }}" data-salary="{{ $e->salary }}">{{ $e->full_name }} ({{ $e->employee_id }})</option>
        @endforeach
        </select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Payroll Number *</label><input name="payroll_number" required value="PAY-{{ date('Ymd') }}-{{ strtoupper(\Illuminate\Support\Str::random(4)) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Month *</label><select name="month" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">@for($m=1;$m<=12;$m++)<option value="{{ date('F', mktime(0,0,0,$m,1)) }}" @selected($m==date('n'))>{{ date('F', mktime(0,0,0,$m,1)) }}</option>@endfor</select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Year *</label><input name="year" type="number" required value="{{ date('Y') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Basic Salary *</label><input name="basic_salary" id="payrollBasic" type="number" step="0.01" required value="0" oninput="calcPayroll()" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Allowances</label><input name="allowances" id="payrollAllow" type="number" step="0.01" value="0" oninput="calcPayroll()" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Deductions</label><input name="deductions" id="payrollDeduct" type="number" step="0.01" value="0" oninput="calcPayroll()" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Net Salary *</label><input name="net_salary" id="payrollNet" type="number" step="0.01" required value="0" readonly class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-bold bg-gray-50 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="pending">Pending</option><option value="paid">Paid</option><option value="cancelled">Cancelled</option></select></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create</button></div>
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
