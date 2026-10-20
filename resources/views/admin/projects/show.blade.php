@extends('layouts.admin')
@section('title', 'Project Details - ' . config('app.name'))
@section('page_title', 'Project Details')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.projects.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Projects</a>
    <div class="flex items-center gap-2 flex-wrap">
    @if(in_array($project->status, ['completed','in_progress','planning']) && !$project->recurring_invoicing)
        <form method="POST" action="{{ route('admin.projects.generate-invoice', $project) }}" class="inline">@csrf
            <button type="submit" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Generate Tax Invoice</button>
        </form>
    @endif
    @if($project->recurring_invoicing)
        <form method="POST" action="{{ route('admin.projects.generate-recurring-invoice', $project) }}" class="inline">@csrf
            <button type="submit" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Generate Monthly Invoice</button>
        </form>
    @endif
    <a href="{{ route('admin.projects.settlements', $project) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Settlements</a>
    <a href="{{ route('admin.projects.account', $project) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Account Card</a>
    <a href="{{ route('admin.projects.financing', $project) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Financing</a>
    <a href="{{ route('admin.projects.expense-requests', $project) }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Expense Requests</a>
    <a href="{{ route('admin.projects.pdf', $project) }}" target="_blank" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">PDF</a>
    </div>
</div>

@php
    $statusColor = ['planning'=>'#3b82f6','in_progress'=>'#f59e0b','completed'=>'#10b981','on_hold'=>'#9ca3af','cancelled'=>'#ef4444'][$project->status] ?? '#9ca3af';
    $priorityColor = ['low'=>'#9ca3af','medium'=>'#3b82f6','high'=>'#f59e0b','critical'=>'#ef4444'][$project->priority] ?? '#9ca3af';
    $isTerminal = in_array($project->status, ['on_hold', 'cancelled']);
    $pipeline = [
        ['key' => 'planning', 'label' => 'Planning'],
        ['key' => 'in_progress', 'label' => 'In Progress'],
        ['key' => 'completed', 'label' => 'Completed'],
        ['key' => 'on_hold', 'label' => 'On Hold'],
        ['key' => 'cancelled', 'label' => 'Cancelled'],
    ];
    $pipelineKeys = array_column($pipeline, 'key');
    $currentIdx = array_search($project->status, $pipelineKeys);
    $currentIdx = $currentIdx === false ? -1 : $currentIdx;
@endphp

{{-- Pipeline tracker --}}
@if(!$isTerminal)
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-center">
        @foreach(array_slice($pipeline, 0, 3) as $idx => $stage)
        @php $reached = $idx <= $currentIdx; $isCurrent = $idx === $currentIdx; @endphp
        @if($idx > 0)
        <div class="flex-1 h-px {{ $reached ? 'bg-gray-800' : 'bg-gray-200' }}"></div>
        @endif
        <div class="flex items-center gap-2 px-1">
            <span class="w-5 h-5 rounded-full border flex items-center justify-center flex-shrink-0 {{ $reached ? 'bg-gray-900 border-gray-900' : 'border-gray-300 bg-white' }}">
                @if($reached && !$isCurrent)
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                @elseif($isCurrent)
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                @endif
            </span>
            <span class="text-xs {{ $isCurrent ? 'font-bold text-gray-900' : ($reached ? 'text-gray-600' : 'text-gray-400') }}">{{ $stage['label'] }}</span>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3.5 mb-4 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span style="width:8px;height:8px;border-radius:99px;background:{{ $statusColor }}"></span>
        <p class="text-xs font-semibold text-gray-800">Project is {{ str_replace('_', ' ', $project->status) }}</p>
    </div>
    <p class="text-[11px] text-gray-400">{{ $project->progress }}% complete</p>
</div>
@endif

@if($project->status === 'completed' && !$project->recurring_invoicing && !$project->invoices->where('type', 'service')->isNotEmpty())
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3.5 mb-4 flex items-center justify-between flex-wrap gap-3">
    <div>
        <p class="text-xs font-semibold text-gray-800">Project completed — no invoice yet</p>
        <p class="text-[11px] text-gray-400">Generate a tax invoice to bill the client.</p>
    </div>
    <form method="POST" action="{{ route('admin.projects.generate-invoice', $project) }}" class="inline">@csrf
        <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Generate Invoice Now</button>
    </form>
</div>
@endif

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $project->project_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $statusColor }}"></span>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-500"><span style="width:6px;height:6px;border-radius:99px;background:{{ $priorityColor }}"></span>{{ ucfirst($project->priority) }} priority</span>
            </div>
            <h3 class="text-base font-bold text-gray-900 mt-1.5">{{ $project->title }}</h3>
            <p class="text-xs text-gray-400 mt-1">{{ $project->description ?? 'No description' }}</p>
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Budget</p>
            <p class="text-xl font-bold text-gray-900 mt-0.5">TZS {{ number_format($project->budget) }}</p>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-xs">
        <div><p class="text-gray-400">Manager</p><p class="text-gray-800 mt-0.5">{{ $project->manager?->name ?? 'N/A' }}</p></div>
        <div><p class="text-gray-400">Start Date</p><p class="text-gray-800 mt-0.5">{{ $project->start_date?->format('d M Y') ?? '—' }}</p></div>
        <div><p class="text-gray-400">Due Date</p><p class="mt-0.5 {{ $project->due_date && $project->due_date->isPast() && $project->status !== 'completed' ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $project->due_date?->format('d M Y') ?? '—' }}</p></div>
        <div><p class="text-gray-400">Staff Assigned</p><p class="text-gray-800 mt-0.5">{{ $project->employees->count() }}</p></div>
        @if($project->recurring_invoicing)
        <div><p class="text-gray-400">Invoicing</p><p class="text-gray-800 mt-0.5">Recurring (Monthly)</p></div>
        <div><p class="text-gray-400">Billing</p><p class="text-gray-800 mt-0.5">TZS {{ number_format($project->billing_amount) }} on day {{ $project->billing_day }}</p></div>
        <div><p class="text-gray-400">Last Invoiced</p><p class="text-gray-800 mt-0.5">{{ $project->last_invoiced_at?->format('d M Y') ?? 'Never' }}</p></div>
        <div><p class="text-gray-400">Next Invoice</p><p class="text-gray-800 mt-0.5">{{ $project->nextInvoiceDate()?->format('d M Y') ?? '—' }}</p></div>
        @else
        <div><p class="text-gray-400">Invoicing</p><p class="text-gray-800 mt-0.5">One-Time / Manual</p></div>
        <div><p class="text-gray-400">Invoices</p><p class="text-gray-800 mt-0.5">{{ $project->invoices->count() }} generated</p></div>
        <div><p class="text-gray-400">Total Invoiced</p><p class="text-gray-800 mt-0.5 font-mono">TZS {{ number_format($project->invoices->sum('total_amount')) }}</p></div>
        @endif
    </div>
    <div class="mt-4 flex items-center gap-3">
        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-gray-800 rounded-full" style="width:{{ $project->progress }}%"></div></div>
        <span class="text-xs font-bold text-gray-900">{{ $project->progress }}%</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    {{-- Tasks --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Tasks</h3>
            <button type="button" onclick="openDrawer('taskDrawer','taskDrawerPanel')" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Add Task</button>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($project->tasks as $t)
        @php $tc = ['done'=>'#10b981','completed'=>'#10b981','in_progress'=>'#f59e0b','review'=>'#3b82f6'][$t->status] ?? '#9ca3af'; @endphp
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex-1 min-w-0"><p class="text-xs font-medium text-gray-900 truncate">{{ $t->title }}</p><p class="text-[10px] text-gray-400">{{ $t->assignee?->name ?? 'Unassigned' }}{{ $t->due_date ? ' · Due ' . $t->due_date->format('d M') : '' }}</p></div>
            <div class="flex items-center gap-3 flex-shrink-0"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $tc }}"></span>{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span><form id="del-task-{{ $t->id }}" method="POST" action="{{ route('admin.projects.tasks.destroy', $t) }}" class="inline">@csrf @method('DELETE')</form><button onclick="confirmDelete('del-task-{{ $t->id }}')" class="text-xs text-gray-400 hover:text-red-600">Delete</button></div>
        </div>
        @empty
        <p class="px-5 py-6 text-xs text-gray-400 text-center">No tasks yet</p>
        @endforelse
        </div>
    </div>

    {{-- Assigned Staff --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Assigned Staff <span class="text-gray-400 normal-case font-normal">· {{ $project->employees->count() }}</span></h3>
            @if($availableEmployees->count() > 0)
            <button type="button" onclick="openDrawer('staffDrawer','staffDrawerPanel')" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Assign</button>
            @endif
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($project->employees as $emp)
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-[10px] flex-shrink-0">{{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}</div>
                <div class="min-w-0">
                    <a href="{{ route('admin.employees.show', $emp->id) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600 truncate block">{{ $emp->full_name }}</a>
                    <p class="text-[10px] text-gray-400">{{ $emp->department ?? 'N/A' }} · {{ $emp->pivot->role ?? 'No role' }} · {{ number_format($emp->salary ?? 0, 0) }} TZS</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $emp->pivot->is_active ? '#10b981' : '#9ca3af' }}"></span>{{ $emp->pivot->is_active ? 'Active' : 'Inactive' }}</span>
                <form id="remove-emp-{{ $emp->id }}" method="POST" action="{{ route('admin.projects.employees.remove', [$project, $emp]) }}" class="inline">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('remove-emp-{{ $emp->id }}')" class="text-xs text-gray-400 hover:text-red-600">Remove</button>
            </div>
        </div>
        @empty
        <div class="px-5 py-6 text-center">
            <p class="text-xs text-gray-400 mb-3">No staff assigned to this project</p>
            @if($availableEmployees->count() > 0)
            <button type="button" onclick="openDrawer('staffDrawer','staffDrawerPanel')" class="px-4 py-1.5 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50">Assign Staff</button>
            @endif
        </div>
        @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    {{-- Bugs --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Bugs</h3></div>
        <div class="divide-y divide-gray-100">
        @forelse($project->bugs as $b)
        @php $bc = ['open'=>'#ef4444','fixed'=>'#10b981','in_progress'=>'#f59e0b'][$b->status] ?? '#9ca3af'; @endphp
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex-1 min-w-0"><p class="text-xs font-medium text-gray-900 truncate">{{ $b->title }}</p><p class="text-[10px] text-gray-400">Severity: {{ ucfirst($b->severity) }}</p></div>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 flex-shrink-0"><span style="width:6px;height:6px;border-radius:99px;background:{{ $bc }}"></span>{{ ucfirst($b->status) }}</span>
        </div>
        @empty
        <p class="px-5 py-6 text-xs text-gray-400 text-center">No bugs reported</p>
        @endforelse
        </div>
    </div>

    {{-- Timesheets --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Timesheets</h3></div>
        <div class="divide-y divide-gray-100">
        @forelse($project->timesheets as $ts)
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex-1 min-w-0"><p class="text-xs font-medium text-gray-900 truncate">{{ $ts->description ?? 'Timesheet entry' }}</p><p class="text-[10px] text-gray-400">{{ $ts->date->format('d M Y') }}</p></div>
            <span class="text-xs font-bold font-mono text-gray-900 flex-shrink-0">{{ $ts->hours }}h</span>
        </div>
        @empty
        <p class="px-5 py-6 text-xs text-gray-400 text-center">No timesheet entries</p>
        @endforelse
        </div>
    </div>
</div>

{{-- Job Cards --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Job Cards</h3>
        <a href="{{ route('admin.job-cards.index') }}?project_id={{ $project->id }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100">
                <th class="px-5 py-2.5 font-semibold">Job #</th><th class="px-5 py-2.5 font-semibold">Title</th><th class="px-5 py-2.5 font-semibold">Assigned To</th><th class="px-5 py-2.5 font-semibold">Priority</th><th class="px-5 py-2.5 font-semibold">Status</th><th class="px-5 py-2.5 font-semibold">Due Date</th><th class="px-5 py-2.5 font-semibold text-right">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($project->jobCards ?? [] as $jc)
            @php $jpc = ['low'=>'#9ca3af','medium'=>'#f59e0b','high'=>'#ef4444','critical'=>'#dc2626'][$jc->priority] ?? '#9ca3af'; $jsc = ['open'=>'#f59e0b','in_progress'=>'#3b82f6','resolved'=>'#10b981','closed'=>'#9ca3af'][$jc->status] ?? '#9ca3af'; @endphp
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3 text-xs font-mono text-gray-700">{{ $jc->job_number }}</td>
                    <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ \Illuminate\Support\Str::limit($jc->title, 40) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $jc->assignedTo?->name ?? '—' }}</td>
                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $jpc }}"></span>{{ $jc->priority }}</span></td>
                    <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $jsc }}"></span>{{ str_replace('_', ' ', ucfirst($jc->status)) }}</span></td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $jc->due_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-5 py-3 text-right"><a href="{{ route('admin.job-cards.show', $jc) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900 mr-3">View</a><a href="{{ route('admin.job-cards.print', $jc) }}" target="_blank" class="text-xs font-medium text-gray-400 hover:text-gray-600">Print</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-6 text-center text-xs text-gray-400">No job cards for this project yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    {{-- Meetings --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Meetings</h3>
            <a href="{{ route('admin.meetings.create') }}?type=project&project_id={{ $project->id }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Schedule</a>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($project->meetings ?? [] as $m)
        @php $mc = ['completed'=>'#10b981','scheduled'=>'#3b82f6','cancelled'=>'#ef4444'][$m->status] ?? '#9ca3af'; @endphp
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-900 truncate">{{ $m->title }}</p>
                <p class="text-[10px] text-gray-400">{{ $m->meeting_date->format('d M Y') }} {{ $m->start_time }} · {{ ucfirst($m->mode) }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 flex-shrink-0"><span style="width:6px;height:6px;border-radius:99px;background:{{ $mc }}"></span>{{ ucfirst($m->status) }}</span>
            <a href="{{ route('admin.meetings.show', $m) }}" class="text-xs text-gray-500 hover:text-gray-900 ml-3">View</a>
        </div>
        @empty
        <p class="px-5 py-6 text-xs text-gray-400 text-center">No meetings scheduled</p>
        @endforelse
        </div>
    </div>

    {{-- Bonuses --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Bonuses</h3>
            <a href="{{ route('admin.bonuses.index') }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Add</a>
        </div>
        <div class="divide-y divide-gray-100">
        @forelse($project->bonuses ?? [] as $bonus)
        @php $bc = ['paid'=>'#10b981','approved'=>'#3b82f6','pending'=>'#f59e0b','rejected'=>'#ef4444'][$bonus->status] ?? '#9ca3af'; @endphp
        <div class="flex items-center justify-between px-5 py-3">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-900 truncate">{{ $bonus->title }}</p>
                <p class="text-[10px] text-gray-400">{{ $bonus->employee?->full_name ?? 'N/A' }} · {{ str_replace('_', ' ', ucfirst($bonus->type)) }} · {{ $bonus->bonus_date->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="text-xs font-mono font-medium text-gray-900">{{ number_format($bonus->amount, 0) }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $bc }}"></span>{{ ucfirst($bonus->status) }}</span>
            </div>
        </div>
        @empty
        <p class="px-5 py-6 text-xs text-gray-400 text-center">No bonuses for this project</p>
        @endforelse
        </div>
    </div>
</div>

{{-- Documents --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Documents</h3>
        <a href="{{ route('admin.documents.create', ['project_id' => $project->id]) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Upload</a>
    </div>
    <div class="divide-y divide-gray-100">
    @forelse($project->documents ?? [] as $doc)
    @php $dc = ['signed'=>'#10b981','pending_signature'=>'#f59e0b'][$doc->status] ?? '#9ca3af'; @endphp
    <div class="flex items-center justify-between px-5 py-3">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-gray-900 truncate">{{ $doc->title }}</p>
            <p class="text-[10px] text-gray-400">{{ $doc->document_number }} · v{{ $doc->version }} · {{ strtoupper($doc->file_type) }} · {{ number_format($doc->file_size / 1024, 0) }}KB</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dc }}"></span>{{ str_replace('_', ' ', ucfirst($doc->status)) }}</span>
            <a href="{{ route('admin.documents.show', $doc) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
            <a href="{{ route('admin.documents.download', $doc) }}" class="text-xs font-medium text-gray-400 hover:text-gray-600">Download</a>
        </div>
    </div>
    @empty
    <p class="px-5 py-6 text-xs text-gray-400 text-center">No documents uploaded</p>
    @endforelse
    </div>
</div>

{{-- PDF Preview --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Project Report PDF</h3>
        <a href="{{ route('admin.projects.pdf', $project) }}" target="_blank" class="text-xs font-medium text-gray-600 hover:text-gray-900">Open full size →</a>
    </div>
    <div class="bg-gray-100 p-4 md:p-8">
        <iframe src="{{ route('admin.projects.pdf', $project) }}" class="w-full bg-white rounded border border-gray-300 shadow-sm" style="height:900px"></iframe>
    </div>
</div>

{{-- Add Task Drawer --}}
<div id="taskDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('taskDrawer','taskDrawerPanel')"></div>
    <div id="taskDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Add Task</h3><p class="text-[11px] text-slate-400 mt-0.5">{{ $project->title }}</p></div>
            <button type="button" onclick="closeDrawer('taskDrawer','taskDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.projects.tasks.store', $project) }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Assigned To</label><select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">— Unassigned —</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="todo">To Do</option><option value="in_progress">In Progress</option><option value="review">Review</option><option value="done">Done</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority</label><select name="priority" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option></select></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Progress (%)</label><input name="progress" type="number" min="0" max="100" value="0" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Add Task</button>
                <button type="button" onclick="closeDrawer('taskDrawer','taskDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Assign Staff Drawer --}}
<div id="staffDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer('staffDrawer','staffDrawerPanel')"></div>
    <div id="staffDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Assign Staff</h3><p class="text-[11px] text-slate-400 mt-0.5">Add employee to this project</p></div>
            <button type="button" onclick="closeDrawer('staffDrawer','staffDrawerPanel')" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
        @if($availableEmployees->count() > 0)
        <form method="POST" action="{{ route('admin.projects.employees.assign', $project) }}" class="space-y-4">@csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                    <option value="">Select employee...</option>
                    @foreach($availableEmployees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->designation ?? 'No designation' }} — {{ $emp->department ?? 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Role on Project</label>
                <input name="role" placeholder="e.g. Project Engineer, Supervisor" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Assign</button>
                <button type="button" onclick="closeDrawer('staffDrawer','staffDrawerPanel')" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
        @else
        <p class="text-sm text-gray-500">All active employees are already assigned to this project.</p>
        @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
function openDrawer(drawerId, panelId) {
    document.getElementById(drawerId).classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById(panelId).classList.remove('-translate-x-full'));
}
function closeDrawer(drawerId, panelId) {
    document.getElementById(panelId).classList.add('-translate-x-full');
    setTimeout(() => document.getElementById(drawerId).classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDrawer('taskDrawer','taskDrawerPanel'); closeDrawer('staffDrawer','staffDrawerPanel'); } });
</script>
@endpush
@endsection
