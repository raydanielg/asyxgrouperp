@extends('layouts.admin')
@section('title', 'Project Details - ' . config('app.name'))
@section('page_title', 'Project Details')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.projects.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Projects</a>
    <div class="flex items-center gap-2">
    @if(in_array($project->status, ['completed','in_progress','planning']) && !$project->recurring_invoicing)
        <form method="POST" action="{{ route('admin.projects.generate-invoice', $project) }}">@csrf
            <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate Tax Invoice
            </button>
        </form>
    @endif
    @if($project->recurring_invoicing)
        <form method="POST" action="{{ route('admin.projects.generate-recurring-invoice', $project) }}">@csrf
            <button type="submit" class="px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Generate Monthly Invoice
            </button>
        </form>
    @endif
    <button onclick="downloadPdf('{{ route('admin.projects.pdf', $project) }}', '{{ $project->project_number }}')" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Download PDF
    </button>
    <a href="{{ route('admin.projects.settlements', $project) }}" class="px-3 py-1.5 bg-bronze text-white text-xs font-medium rounded-lg hover:bg-bronze-dark transition-all flex items-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m2 2v-4m2 4v-6m2 6V7m2 10V5M3 7l3-3 3 3M3 17l3 3 3-3"/></svg>
        Settlements
    </a>
    </div>
</div>
@if($project->status === 'completed' && !$project->recurring_invoicing && !$project->invoices->where('type', 'service')->isNotEmpty())
<div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-indigo-900">Project Completed — Generate Final Invoice</p>
            <p class="text-xs text-indigo-600">This project is completed but has no invoice yet. Generate a tax invoice to bill the client.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.projects.generate-invoice', $project) }}">@csrf
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700">Generate Invoice Now</button>
    </form>
</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-3">{{ $project->title }}</h3>
        <p class="text-xs text-gray-500 mb-4">{{ $project->description ?? 'No description' }}</p>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between"><span class="text-gray-400">Project #</span><span class="font-mono text-gray-700">{{ $project->project_number }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Manager</span><span class="text-gray-700">{{ $project->manager?->name ?? 'N/A' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Start Date</span><span class="text-gray-700">{{ $project->start_date?->format('d M Y') ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Due Date</span><span class="text-gray-700">{{ $project->due_date?->format('d M Y') ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Budget</span><span class="font-semibold text-gray-900">TZS {{ number_format($project->budget) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Priority</span><span class="text-gray-700">{{ ucfirst($project->priority) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span></div>
            @if($project->recurring_invoicing)
            <div class="pt-2 mt-2 border-t">
                <div class="flex justify-between"><span class="text-gray-400">Invoicing Type</span><span class="text-purple-600 font-semibold">Recurring (Monthly)</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Billing Amount</span><span class="font-semibold text-gray-900">TZS {{ number_format($project->billing_amount) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Billing Day</span><span class="text-gray-700">{{ $project->billing_day }} of each month</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Last Invoiced</span><span class="text-gray-700">{{ $project->last_invoiced_at?->format('d M Y') ?? 'Never' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Next Invoice</span><span class="text-gray-700">{{ $project->nextInvoiceDate()?->format('d M Y') ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Invoicing Ends</span><span class="text-gray-700">{{ $project->invoicing_end_date?->format('d M Y') ?? 'No end date' }}</span></div>
            </div>
            @else
            <div class="pt-2 mt-2 border-t">
                <div class="flex justify-between"><span class="text-gray-400">Invoicing Type</span><span class="text-indigo-600 font-semibold">One-Time / Manual</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Invoices Generated</span><span class="text-gray-700">{{ $project->invoices->count() }}</span></div>
                <div class="flex justify-between"><span class="text-gray-400">Total Invoiced</span><span class="font-semibold text-gray-900">TZS {{ number_format($project->invoices->sum('total_amount')) }}</span></div>
            </div>
            @endif
        </div>
        <div class="mt-4"><div class="flex items-center justify-between mb-1"><span class="text-[10px] text-gray-400 uppercase">Progress</span><span class="text-xs font-bold text-emerald-700">{{ $project->progress }}%</span></div><div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width:{{ $project->progress }}%"></div></div></div>
    </div>
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between mb-3"><h3 class="text-sm font-bold text-gray-900">Tasks</h3><button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">+ Add Task</button></div>
        <div class="space-y-2">
        @forelse($project->tasks as $t)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1"><p class="text-xs font-medium text-gray-900">{{ $t->title }}</p><p class="text-[10px] text-gray-400">{{ $t->assignee?->name ?? 'Unassigned' }} {{ $t->due_date ? '- Due ' . $t->due_date->format('d M') : '' }}</p></div>
            <div class="flex items-center gap-2"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($t->status=='done') ? 'bg-emerald-50 text-emerald-700' : (($t->status=='in_progress') ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span><form id="del-task-{{ $t->id }}" method="POST" action="{{ route('admin.projects.tasks.destroy', $t) }}">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-task-{{ $t->id }}')" class="text-red-500 hover:text-red-700 text-[10px]">Delete</button></div>
        
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No tasks yet</p>
        @endforelse
        </div>
    </div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Bugs</h3>
        <div class="space-y-2">
        @forelse($project->bugs as $b)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1"><p class="text-xs font-medium text-gray-900">{{ $b->title }}</p><p class="text-[10px] text-gray-400">Severity: {{ ucfirst($b->severity) }}</p></div>
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($b->status=='open') ? 'bg-red-50 text-red-700' : (($b->status=='fixed') ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600') }}">{{ ucfirst($b->status) }}</span>
        
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No bugs reported</p>
        @endforelse
        </div>
    </div>
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-bold text-gray-900 border-b pb-3 mb-3">Timesheets</h3>
        <div class="space-y-2">
        @forelse($project->timesheets as $ts)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1"><p class="text-xs font-medium text-gray-900">{{ $ts->description ?? 'Timesheet entry' }}</p><p class="text-[10px] text-gray-400">{{ $ts->date->format('d M Y') }}</p></div>
            <span class="text-xs font-semibold text-emerald-700">{{ $ts->hours }}h</span>
        
        </div>
        @empty
        <p class="text-xs text-gray-400 text-center py-4">No timesheet entries</p>
        @endforelse
        </div>
    </div>
</div>
{{-- Project Meetings --}}
<div class="bg-white rounded-xl border p-6 mb-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-bold text-gray-900">Project Meetings</h3>
        <a href="{{ route('admin.meetings.create') }}?type=project&project_id={{ $project->id }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">+ Schedule Meeting</a>
    </div>
    <div class="space-y-2">
    @forelse($project->meetings ?? [] as $m)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-900">{{ $m->title }}</p>
                <p class="text-[10px] text-gray-400">{{ $m->meeting_date->format('d M Y') }} {{ $m->start_time }} {{ ucfirst($m->mode) }}</p>
            </div>
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($m->status=='completed') ? 'bg-emerald-50 text-emerald-700' : (($m->status=='scheduled') ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700') }}">{{ ucfirst($m->status) }}</span>
            <a href="{{ route('admin.meetings.show', $m) }}" class="text-xs text-bronze hover:underline ml-2">View</a>
        </div>
    @empty
        <p class="text-xs text-gray-400 text-center py-4">No meetings scheduled for this project</p>
    @endforelse
    </div>
</div>
{{-- Assigned Staff --}}
<div class="bg-white rounded-xl border p-6 mb-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-bold text-gray-900">Assigned Staff</h3>
        <span class="text-[10px] text-gray-400">{{ $project->employees->count() }} employees</span>
    </div>
    <div class="space-y-2">
    @forelse($project->employees as $emp)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">{{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}</div>
                <div>
                    <a href="{{ route('admin.employees.show', $emp->id) }}" class="text-xs font-medium text-gray-900 hover:text-bronze">{{ $emp->full_name }}</a>
                    <p class="text-[10px] text-gray-400">{{ $emp->department ?? 'N/A' }} | {{ $emp->pivot->role ?? 'No role' }} | Salary: {{ number_format($emp->salary ?? 0, 0) }} TZS</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if($emp->pivot->is_active)
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700">Active</span>
                @else
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500">Inactive</span>
                @endif
            </div>
        </div>
    @empty
        <p class="text-xs text-gray-400 text-center py-4">No staff assigned to this project</p>
    @endforelse
    </div>
</div>
{{-- Project Bonuses --}}
<div class="bg-white rounded-xl border p-6 mb-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-bold text-gray-900">Project Bonuses</h3>
        <a href="{{ route('admin.bonuses.index') }}" class="text-xs text-bronze hover:underline font-medium">+ Add Bonus</a>
    </div>
    <div class="space-y-2">
    @forelse($project->bonuses ?? [] as $bonus)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $bonus->title }}</p>
                    <p class="text-[10px] text-gray-400">{{ $bonus->employee?->full_name ?? 'N/A' }} | {{ str_replace('_', ' ', ucfirst($bonus->type)) }} | {{ $bonus->bonus_date->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-gray-900">{{ number_format($bonus->amount, 0) }} TZS</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium
                    {{ $bonus->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}
                    {{ $bonus->status === 'approved' ? 'bg-blue-50 text-blue-700' : '' }}
                    {{ $bonus->status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                    {{ $bonus->status === 'rejected' ? 'bg-red-50 text-red-700' : '' }}">{{ ucfirst($bonus->status) }}</span>
            </div>
        </div>
    @empty
        <p class="text-xs text-gray-400 text-center py-4">No bonuses for this project</p>
    @endforelse
    </div>
</div>
{{-- Project Documents --}}
<div class="bg-white rounded-xl border p-6 mb-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-bold text-gray-900">Project Documents</h3>
        <a href="{{ route('admin.documents.create', ['project_id' => $project->id]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">+ Upload Document</a>
    </div>
    <div class="space-y-2">
    @forelse($project->documents ?? [] as $doc)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3 flex-1">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-900">{{ $doc->title }}</p>
                    <p class="text-[10px] text-gray-400">{{ $doc->document_number }} - v{{ $doc->version }} - {{ strtoupper($doc->file_type) }} - {{ number_format($doc->file_size / 1024, 0) }}KB</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] text-gray-500 capitalize">{{ str_replace('_', ' ', $doc->category ?? '') }}</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($doc->status=='signed') ? 'bg-emerald-50 text-emerald-700' : (($doc->status=='pending_signature') ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600') }}">{{ str_replace('_', ' ', ucfirst($doc->status)) }}</span>
                <a href="{{ route('admin.documents.show', $doc) }}" class="text-xs text-bronze hover:underline">View</a>
                <a href="{{ route('admin.documents.download', $doc) }}" class="text-xs text-emerald-600 hover:underline">Download</a>
            </div>
        </div>
    @empty
        <p class="text-xs text-gray-400 text-center py-4">No documents uploaded for this project</p>
    @endforelse
    </div>
</div>
<div id="taskModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Task</h3>
        <form method="POST" action="{{ route('admin.projects.tasks.store', $project) }}" class="space-y-3">@csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-3"><div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="todo">To Do</option><option value="in_progress">In Progress</option><option value="review">Review</option><option value="done">Done</option></select></div><div><label class="block text-xs font-medium text-gray-600 mb-1">Priority</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select></div></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Progress (%)</label><input name="progress" type="number" min="0" max="100" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="flex gap-2 pt-2"><button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button><button type="submit" class="flex-1 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Add Task</button></div>
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
