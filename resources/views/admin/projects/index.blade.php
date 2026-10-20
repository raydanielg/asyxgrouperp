@extends('layouts.admin')
@section('title', 'Projects - ' . config('app.name'))
@section('page_title', 'Projects')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Manage projects, tasks, and progress</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Project</button>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Project #</th><th class="px-5 py-3 font-semibold">Title</th><th class="px-5 py-3 font-semibold">Manager</th><th class="px-5 py-3 font-semibold">Staff</th><th class="px-5 py-3 font-semibold">Progress</th><th class="px-5 py-3 font-semibold">Due Date</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($projects as $p)
        @php $sc = ['planning'=>'#3b82f6','in_progress'=>'#f59e0b','completed'=>'#10b981','on_hold'=>'#9ca3af','cancelled'=>'#ef4444'][$p->status] ?? '#9ca3af'; @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $p->project_number }}</td>
            <td class="px-5 py-3.5"><a href="{{ route('admin.projects.show', $p) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600">{{ $p->title }}</a></td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $p->manager?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $p->employees_count ?? 0 }}</td>
            <td class="px-5 py-3.5"><div class="flex items-center gap-2"><div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-gray-800 rounded-full" style="width:{{ $p->progress }}%"></div></div><span class="text-[10px] text-gray-500">{{ $p->progress }}%</span></div></td>
            <td class="px-5 py-3.5 text-xs {{ $p->due_date && $p->due_date->isPast() && $p->status !== 'completed' ? 'text-red-600' : 'text-gray-400' }}">{{ $p->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
            <td class="px-5 py-3.5">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.projects.show', $p) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                    <form id="del-prj-{{ $p->id }}" method="POST" action="{{ route('admin.projects.destroy', $p) }}" class="inline">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-prj-{{ $p->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No projects found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($projects->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $projects->links() }}</div>
    @endif
</div>

{{-- New Project Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-lg bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">New Project</h3><p class="text-[11px] text-slate-400 mt-0.5">Create a project and assign staff</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.projects.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Website Redesign" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" placeholder="Project scope..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Manager</label><select name="manager_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">— Unassigned —</option>
                @foreach($managers as $m)
                <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
                </select></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="planning">Planning</option><option value="in_progress">In Progress</option><option value="completed">Completed</option><option value="on_hold">On Hold</option><option value="cancelled">Cancelled</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority</label><select name="priority" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Progress (%)</label><input name="progress" type="number" min="0" max="100" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Budget (TZS)</label><input name="budget" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Invoicing</h4>
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <label class="flex flex-col items-center gap-1 p-3 rounded border-2 border-gray-100 hover:border-gray-400 cursor-pointer transition-all invoicing-option" data-type="recurring">
                        <input type="radio" name="invoicing_type" value="recurring" class="sr-only" onchange="selectInvoicingType('recurring')">
                        <span class="text-xs font-medium text-gray-600">Recurring</span>
                        <span class="text-[10px] text-gray-400">Monthly auto</span>
                    </label>
                    <label class="flex flex-col items-center gap-1 p-3 rounded border-2 border-gray-100 hover:border-gray-400 cursor-pointer transition-all invoicing-option" data-type="one_time">
                        <input type="radio" name="invoicing_type" value="one_time" class="sr-only" onchange="selectInvoicingType('one_time')">
                        <span class="text-xs font-medium text-gray-600">One-Time</span>
                        <span class="text-[10px] text-gray-400">Single invoice</span>
                    </label>
                    <label class="flex flex-col items-center gap-1 p-3 rounded border-2 border-gray-900 cursor-pointer transition-all invoicing-option" data-type="none">
                        <input type="radio" name="invoicing_type" value="none" class="sr-only" onchange="selectInvoicingType('none')" checked>
                        <span class="text-xs font-medium text-gray-600">None</span>
                        <span class="text-[10px] text-gray-400">Manual only</span>
                    </label>
                </div>
                <div id="recurringFields" class="hidden grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Monthly Amount (TZS)</label><input name="billing_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Billing Day (1-28)</label><input name="billing_day" type="number" min="1" max="28" value="1" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Invoicing End Date (optional)</label><input name="invoicing_end_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div id="oneTimeFields" class="hidden grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Amount (TZS)</label><input name="one_time_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Generate After</label><select name="one_time_when" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="immediately">Immediately (on create)</option><option value="manual">Manual (from project page)</option><option value="completion">When project completes</option></select></div>
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-gray-100">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Assign Staff</h4>
                    <span class="text-[10px] text-gray-400">Select employees & roles</span>
                </div>
                <p class="text-[11px] text-gray-400 mb-3">Salary costs zitatrackwa kwenye settlements.</p>
                <div class="space-y-2 max-h-56 overflow-y-auto">
                    @foreach($employees as $emp)
                    <div class="flex items-center gap-3 p-2.5 rounded border border-gray-100 hover:border-gray-300 transition-all">
                        <input type="checkbox" name="project_employee_ids[]" value="{{ $emp->id }}" id="emp-{{ $emp->id }}" class="w-4 h-4 rounded border-gray-300 text-gray-700 focus:ring-gray-400 flex-shrink-0">
                        <label for="emp-{{ $emp->id }}" class="flex items-center gap-3 flex-1 cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-[10px] flex-shrink-0">{{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800 truncate">{{ $emp->full_name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $emp->department ?? 'N/A' }} · {{ $emp->designation ?? 'N/A' }} · {{ number_format($emp->salary ?? 0, 0) }} TZS</p>
                            </div>
                        </label>
                        <input type="text" name="project_employee_roles[{{ $emp->id }}]" placeholder="Role" class="w-28 px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400 flex-shrink-0" value="">
                    </div>
                    @endforeach
                    @if($employees->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">No active employees. Add employees first.</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 pt-5 mt-5 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Project</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
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

function selectInvoicingType(type) {
    document.querySelectorAll('.invoicing-option').forEach(el => {
        if (el.dataset.type === type) {
            el.classList.add('border-gray-900');
            el.classList.remove('border-gray-100');
        } else {
            el.classList.remove('border-gray-900');
            el.classList.add('border-gray-100');
        }
    });
    document.getElementById('recurringFields').classList.toggle('hidden', type !== 'recurring');
    document.getElementById('oneTimeFields').classList.toggle('hidden', type !== 'one_time');
}
</script>
@endpush
@endsection
