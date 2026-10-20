@extends('layouts.admin')
@section('title', 'Job Cards')
@section('page_title', 'Job Cards')
@section('content')

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Manage work orders and job assignments</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Job Card</button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $stats['total'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Open</p><p class="text-lg font-bold text-amber-600 mt-0.5">{{ $stats['open'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">In Progress</p><p class="text-lg font-bold text-blue-600 mt-0.5">{{ $stats['in_progress'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Resolved</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $stats['resolved'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pending Pay</p><p class="text-lg font-bold text-red-500 mt-0.5">{{ $stats['pending_payment'] }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Job #</th><th class="px-5 py-3 font-semibold">Title</th><th class="px-5 py-3 font-semibold">Project</th><th class="px-5 py-3 font-semibold">Assigned To</th><th class="px-5 py-3 font-semibold">Priority</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Due Date</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($jobCards as $jc)
        @php
            $pc = ['low'=>'#9ca3af','medium'=>'#f59e0b','high'=>'#ef4444','critical'=>'#dc2626'][$jc->priority] ?? '#9ca3af';
            $sc = ['open'=>'#f59e0b','in_progress'=>'#3b82f6','resolved'=>'#10b981','closed'=>'#9ca3af'][$jc->status] ?? '#9ca3af';
        @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $jc->job_number }}</td>
            <td class="px-5 py-3.5"><a href="{{ route('admin.job-cards.show', $jc) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600">{{ Str::limit($jc->title, 40) }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $jc->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $jc->assignedTo?->name ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $pc }}"></span>{{ $jc->priority }}</span></td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ str_replace('_', ' ', ucfirst($jc->status)) }}</span></td>
            <td class="px-5 py-3.5 text-xs {{ $jc->due_date && $jc->due_date->isPast() && $jc->status !== 'resolved' ? 'text-red-600 font-medium' : 'text-gray-400' }}">{{ $jc->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.job-cards.show', $jc) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <a href="{{ route('admin.job-cards.print', $jc) }}" target="_blank" class="text-xs font-medium text-gray-400 hover:text-gray-600">Print</a>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No job cards found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($jobCards->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $jobCards->links() }}</div>
    @endif
</div>

{{-- New Job Card Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-2xl bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Job Card / Service Call Report</h3><p class="text-[11px] text-slate-400 mt-0.5">Fill in the service call details</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.job-cards.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf

            <div class="mb-5">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">General</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Printer repair - HQ office" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">CSR No</label><input name="csr_no" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Report Date</label><input name="report_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
            </div>

            <div class="mb-5 pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Customer</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Name</label><input name="customer_name" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Address</label><input name="customer_address" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Branch Name</label><input name="branch_name" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Department</label><input name="department" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
            </div>

            <div class="mb-5 pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Equipment</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Equipment Type</label><input name="equipment_type" placeholder="e.g. Printer, Server" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Make / Brand</label><input name="make_brand" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Model</label><input name="model" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Serial Number</label><input name="serial_number" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Call Type</label><select name="call_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">Select</option><option value="corrective">Corrective Maintenance</option><option value="corrective_preventive">Corrective &amp; Preventive Maintenance</option><option value="preventive">Preventive Maintenance</option><option value="installation">Installation</option></select></div>
                </div>
            </div>

            <div class="mb-5 pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Assignment</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">— None —</option>@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach</select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label><select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">— Unassigned —</option>@foreach($technicians as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority <span class="text-red-500">*</span></label><select name="priority" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
            </div>

            <div class="mb-5 pt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Findings</h4>
                <div class="space-y-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Problem Reported</label><textarea name="problem_reported" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Defects Found</label><textarea name="defects_found" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Action Taken</label><textarea name="action_taken" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                </div>
            </div>

            <div class="pt-5 border-t border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Parts Required / Replaced</h4>
                    <button type="button" onclick="addPartRow()" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Add part</button>
                </div>
                <div id="partsContainer" class="space-y-2">
                    <div class="grid grid-cols-12 gap-2 part-row">
                        <div class="col-span-4"><input name="parts[0][part_name]" placeholder="Part name" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                        <div class="col-span-2"><input name="parts[0][quantity]" type="number" step="0.01" value="1" placeholder="Qty" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                        <div class="col-span-3"><input name="parts[0][model]" placeholder="Model" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                        <div class="col-span-3"><input name="parts[0][part_number]" placeholder="Part number" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-5 mt-5 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Job Card</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

let partIndex = 1;
function addPartRow() {
    const container = document.getElementById('partsContainer');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 part-row';
    row.innerHTML = `
        <div class="col-span-4"><input name="parts[${partIndex}][part_name]" placeholder="Part name" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
        <div class="col-span-2"><input name="parts[${partIndex}][quantity]" type="number" step="0.01" value="1" placeholder="Qty" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
        <div class="col-span-3"><input name="parts[${partIndex}][model]" placeholder="Model" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
        <div class="col-span-3 flex gap-1"><input name="parts[${partIndex}][part_number]" placeholder="Part number" class="flex-1 px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"><button type="button" onclick="this.closest('.part-row').remove()" class="text-gray-400 hover:text-red-600 px-1">×</button></div>
    `;
    container.appendChild(row);
    partIndex++;
}
</script>
@endpush
