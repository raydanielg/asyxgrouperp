@extends('layouts.admin')
@section('title', 'Employee Bonuses - ' . config('app.name'))
@section('page_title', 'Employee Bonuses')
@section('content')

@php
    $totalPending = $bonuses->where('status', 'pending')->sum('amount');
    $totalApproved = $bonuses->where('status', 'approved')->sum('amount');
    $totalPaid = $bonuses->where('status', 'paid')->sum('amount');
@endphp

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-8">
        <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pending</p><p class="text-lg font-bold text-amber-600">{{ number_format($totalPending) }} <span class="text-xs font-normal text-gray-400">TZS</span></p></div>
        <div class="h-8 w-px bg-gray-100"></div>
        <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Approved</p><p class="text-lg font-bold text-blue-600">{{ number_format($totalApproved) }} <span class="text-xs font-normal text-gray-400">TZS</span></p></div>
        <div class="h-8 w-px bg-gray-100"></div>
        <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Paid</p><p class="text-lg font-bold text-emerald-600">{{ number_format($totalPaid) }} <span class="text-xs font-normal text-gray-400">TZS</span></p></div>
    </div>
    <button type="button" onclick="openBonusDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Add Bonus</button>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Bonus #</th>
                    <th class="px-5 py-3 font-semibold">Employee</th>
                    <th class="px-5 py-3 font-semibold">Project</th>
                    <th class="px-5 py-3 font-semibold">Type</th>
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold text-right">Amount</th>
                    <th class="px-5 py-3 font-semibold">Date</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bonuses as $bonus)
                @php $bc = ['paid' => '#10b981', 'approved' => '#3b82f6', 'pending' => '#f59e0b', 'rejected' => '#ef4444'][$bonus->status] ?? '#9ca3af'; @endphp
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3.5 text-xs font-mono text-gray-600">{{ $bonus->bonus_number }}</td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('admin.employees.show', $bonus->employee_id) }}" class="text-xs font-medium text-gray-800 hover:text-gray-900">{{ $bonus->employee?->full_name ?? '—' }}</a>
                        <p class="text-[10px] text-gray-400">{{ $bonus->employee?->department ?? '' }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($bonus->project)
                            <a href="{{ route('admin.projects.show', $bonus->project_id) }}" class="text-xs text-gray-600 hover:text-gray-900">{{ $bonus->project->title }}</a>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-600">{{ str_replace('_', ' ', ucfirst($bonus->type)) }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-700">{{ $bonus->title }}</td>
                    <td class="px-5 py-3.5 text-xs text-right font-medium font-mono text-gray-900">{{ number_format($bonus->amount) }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $bonus->bonus_date->format('d M Y') }}</td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $bc }}"></span>{{ ucfirst($bonus->status) }}</span></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-4">
                            @if($bonus->status === 'pending')
                            <form method="POST" action="{{ route('admin.bonuses.approve', $bonus) }}" class="inline" onsubmit="return confirmAction(this, {title:'Approve bonus?', text:'{{ number_format($bonus->amount) }} TZS for {{ addslashes($bonus->employee?->full_name ?? 'employee') }}', icon:'question', confirmText:'Yes, approve', color:'#059669'})">@csrf
                                <button type="submit" class="text-xs font-medium text-gray-600 hover:text-gray-900">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.bonuses.reject', $bonus) }}" class="inline" onsubmit="return confirmAction(this, {title:'Reject bonus?', text:'This bonus will be rejected.', icon:'warning', confirmText:'Yes, reject', color:'#d97706'})">@csrf
                                <button type="submit" class="text-xs font-medium text-amber-600 hover:text-amber-800">Reject</button>
                            </form>
                            @elseif($bonus->status === 'approved')
                            <form method="POST" action="{{ route('admin.bonuses.paid', $bonus) }}" class="inline" onsubmit="return confirmAction(this, {title:'Mark as paid?', text:'Mark this bonus as paid out.', icon:'question', confirmText:'Yes, mark paid', color:'#059669'})">@csrf
                                <button type="submit" class="text-xs font-medium text-gray-600 hover:text-gray-900">Mark Paid</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.bonuses.destroy', $bonus) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete bonus?', text:'This bonus will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-5 py-10 text-center text-xs text-gray-400">No bonuses yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bonuses->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $bonuses->links() }}</div>
    @endif
</div>

<div id="bonusDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeBonusDrawer()"></div>
    <div id="bonusDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">Add Employee Bonus</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Performance or milestone reward</p>
            </div>
            <button type="button" onclick="closeBonusDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.bonuses.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select...</option>
                        @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->department ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Project</label>
                    <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— No specific project —</option>
                        @foreach($projects as $proj)
                        <option value="{{ $proj->id }}">{{ $proj->title }} ({{ $proj->project_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bonus Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        @foreach(['performance' => 'Performance', 'project_completion' => 'Project Completion', 'milestone' => 'Milestone', 'special' => 'Special'] as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Q1 Performance Bonus" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) <span class="text-red-500">*</span></label><input name="amount" type="number" step="0.01" min="0" required placeholder="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Bonus Date <span class="text-red-500">*</span></label><input name="bonus_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" placeholder="Optional..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Bonus</button>
                <button type="button" onclick="closeBonusDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openBonusDrawer() {
    document.getElementById('bonusDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('bonusDrawerPanel').classList.remove('-translate-x-full'));
}
function closeBonusDrawer() {
    document.getElementById('bonusDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('bonusDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeBonusDrawer(); });
</script>
@endpush
@endsection
