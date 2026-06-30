@extends('layouts.admin')
@section('title', 'Projects - ' . config('app.name'))
@section('page_title', 'Projects')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage projects, tasks, and progress</p>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Project
    </button>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Project #</th><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Manager</th><th class="px-5 py-3 font-medium">Staff</th><th class="px-5 py-3 font-medium">Progress</th><th class="px-5 py-3 font-medium">Due Date</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($projects as $p)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $p->project_number }}</td>
            <td class="px-5 py-3"><a href="{{ route('admin.projects.show', $p) }}" class="text-xs font-medium text-gray-900 hover:text-emerald-600">{{ $p->title }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $p->manager?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3"><span class="inline-flex items-center gap-1 text-xs text-gray-600"><svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM6 3v2m12-2v2M3 8h2m14 0h2"/></svg>{{ $p->employees_count ?? 0 }}</span></td>
            <td class="px-5 py-3"><div class="flex items-center gap-2"><div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width:{{ $p->progress }}%"></div></div><span class="text-[10px] text-gray-500">{{ $p->progress }}%</span></div></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $p->due_date?->format('d M Y') ?? '—' }}</td>
            <td class="px-5 py-3">@php $c=['planning'=>'sky','in_progress'=>'amber','completed'=>'emerald','on_hold'=>'gray','cancelled'=>'red']; @endphp<span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$p->status] ?? 'gray' }}-50 text-{{ $c[$p->status] ?? 'gray' }}-700">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span></td>
            <td class="px-5 py-3 flex items-center gap-2"><a href="{{ route('admin.projects.show', $p) }}" class="text-sky-600 hover:text-sky-700 text-xs">View</a><button onclick="downloadPdf('{{ route('admin.projects.pdf', $p) }}', '{{ $p->project_number }}')" class="text-emerald-600 hover:text-emerald-700 text-xs">PDF</button><form id="del-prj-{{ $p->id }}" method="POST" action="{{ route('admin.projects.destroy', $p) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-prj-{{ $p->id }}')" class="text-red-500 hover:text-red-700 text-xs">Delete</button></td>
        
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No projects found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $projects->links() }}</div>
</div>
<div id="createModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create New Project</h3>
        <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-4">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Manager</label><select name="manager_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="">Unassigned</option>
        @foreach($managers as $m)
        <option value="{{ $m->id }}">{{ $m->name }}</option>
        @endforeach
        </select></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="planning">Planning</option><option value="in_progress">In Progress</option><option value="completed">Completed</option><option value="on_hold">On Hold</option><option value="cancelled">Cancelled</option></select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Priority</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Progress (%)</label><input name="progress" type="number" min="0" max="100" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Budget</label><input name="budget" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div></div>
            {{-- Invoicing Type --}}
            <div class="pt-3 border-t">
                <h4 class="text-sm font-bold text-gray-900 mb-2">Invoicing</h4>
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <label class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 border-gray-100 hover:border-emerald-200 cursor-pointer transition-all invoicing-option" data-type="recurring">
                        <input type="radio" name="invoicing_type" value="recurring" class="sr-only" onchange="selectInvoicingType('recurring')">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span class="text-xs font-medium text-gray-600">Recurring</span>
                        <span class="text-[10px] text-gray-400">Monthly auto</span>
                    </label>
                    <label class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 border-gray-100 hover:border-emerald-200 cursor-pointer transition-all invoicing-option" data-type="one_time">
                        <input type="radio" name="invoicing_type" value="one_time" class="sr-only" onchange="selectInvoicingType('one_time')">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-xs font-medium text-gray-600">One-Time</span>
                        <span class="text-[10px] text-gray-400">Single invoice</span>
                    </label>
                    <label class="flex flex-col items-center gap-1 p-3 rounded-lg border-2 border-gray-100 hover:border-emerald-200 cursor-pointer transition-all invoicing-option" data-type="none">
                        <input type="radio" name="invoicing_type" value="none" class="sr-only" onchange="selectInvoicingType('none')" checked>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <span class="text-xs font-medium text-gray-600">None</span>
                        <span class="text-[10px] text-gray-400">Manual only</span>
                    </label>
                </div>
                {{-- Recurring Fields --}}
                <div id="recurringFields" class="hidden grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Monthly Amount (TZS)</label><input name="billing_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Billing Day (1-28)</label><input name="billing_day" type="number" min="1" max="28" value="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                    <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Invoicing End Date (optional)</label><input name="invoicing_end_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                </div>
                {{-- One-Time Fields --}}
                <div id="oneTimeFields" class="hidden grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Invoice Amount (TZS)</label><input name="one_time_amount" type="number" step="0.01" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Generate After</label><select name="one_time_when" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="immediately">Immediately (on create)</option><option value="manual">Manual (from project page)</option><option value="completion">When project completes</option></select></div>
                </div>
            </div>
            {{-- Staff Assignment --}}
            <div class="pt-3 border-t">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-bold text-gray-900">Assign Staff to Project</h4>
                    <span class="text-[10px] text-gray-400">Select employees & their roles</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">Wafanyakazi waliokuchwa wataunganishwa na project hii. Salary costs zao zitatrackwa kwenye settlements.</p>
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    @foreach($employees as $emp)
                    <div class="flex items-center gap-3 p-2.5 rounded-lg border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50/30 transition-all">
                        <input type="checkbox" name="project_employee_ids[]" value="{{ $emp->id }}" id="emp-{{ $emp->id }}" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 flex-shrink-0">
                        <label for="emp-{{ $emp->id }}" class="flex items-center gap-3 flex-1 cursor-pointer">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">{{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-800 truncate">{{ $emp->full_name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $emp->department ?? 'N/A' }} · {{ $emp->designation ?? 'N/A' }} · Salary: {{ number_format($emp->salary ?? 0, 0) }} TZS</p>
                            </div>
                        </label>
                        <input type="text" name="project_employee_roles[{{ $emp->id }}]" placeholder="Role" class="w-32 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-200 outline-none flex-shrink-0" value="">
                    </div>
                    @endforeach
                    @if($employees->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">No active employees. Add employees first.</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 pt-3"><button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Create Project</button></div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function downloadPdf(url, title) {
  Swal.fire({
    title: 'Downloading...',
    text: 'Preparing ' + title,
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); },
    timer: 800,
    willClose: () => { window.open(url, '_blank'); }
  });
}
</script>
@endpush
@endsection
