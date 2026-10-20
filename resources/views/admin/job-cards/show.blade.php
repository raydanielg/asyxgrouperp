@extends('layouts.admin')
@section('title', 'Job Card - ' . $jobCard->job_number)
@section('page_title', 'Job Card: ' . $jobCard->job_number)
@section('content')
@php
    $sc = ['open'=>'#f59e0b','in_progress'=>'#3b82f6','resolved'=>'#10b981','closed'=>'#9ca3af'][$jobCard->status] ?? '#9ca3af';
    $pyc = ['pending'=>'#f59e0b','approved'=>'#10b981'][$jobCard->payment_status] ?? '#9ca3af';
    $pipeline = ['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'];
    $pipelineKeys = array_keys($pipeline);
    $currentIdx = array_search($jobCard->status, $pipelineKeys);
    $currentIdx = $currentIdx === false ? 0 : $currentIdx;
@endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.job-cards.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Job Cards</a>
    <a href="{{ route('admin.job-cards.print', $jobCard) }}" target="_blank" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Print Report</a>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $jobCard->job_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sc }}"></span>{{ str_replace('_', ' ', ucfirst($jobCard->status)) }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $pyc }}"></span>Payment {{ $jobCard->payment_status }}</span>
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">{{ $jobCard->title }}</h1>
            <p class="text-[11px] text-gray-400 mt-1">Created {{ $jobCard->created_at->format('d M Y, H:i') }} by {{ $jobCard->creator?->name ?? 'System' }}</p>
        </div>
        @if($jobCard->due_date)
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Due Date</p>
            <p class="text-sm font-bold {{ $jobCard->due_date->isPast() && $jobCard->status !== 'resolved' ? 'text-red-600' : 'text-gray-900' }} mt-0.5">{{ $jobCard->due_date->format('d M Y') }}</p>
        </div>
        @endif
    </div>
    {{-- Status pipeline --}}
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center">
            @foreach($pipeline as $key => $label)
            @php $idx = array_search($key, $pipelineKeys); $reached = $idx <= $currentIdx; $isCurrent = $idx === $currentIdx; @endphp
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
                <span class="text-xs {{ $isCurrent ? 'font-bold text-gray-900' : ($reached ? 'text-gray-600' : 'text-gray-400') }}">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        {{-- Service Call Report form --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Service Call Report</h3></div>
            <form method="POST" action="{{ route('admin.job-cards.update', $jobCard) }}" id="jobCardForm">
                @csrf @method('PATCH')

                <div class="px-5 py-4 border-b border-gray-100">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">General</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" value="{{ $jobCard->title }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">CSR No</label><input name="csr_no" value="{{ $jobCard->csr_no }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Report Date</label><input name="report_date" type="date" value="{{ $jobCard->report_date?->format('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    </div>
                </div>

                <div class="px-5 py-4 border-b border-gray-100">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Customer</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Name</label><input name="customer_name" value="{{ $jobCard->customer_name }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Address</label><input name="customer_address" value="{{ $jobCard->customer_address }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Branch Name</label><input name="branch_name" value="{{ $jobCard->branch_name }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Department</label><input name="department" value="{{ $jobCard->department }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    </div>
                </div>

                <div class="px-5 py-4 border-b border-gray-100">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Equipment</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Equipment Type</label><input name="equipment_type" value="{{ $jobCard->equipment_type }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Make / Brand</label><input name="make_brand" value="{{ $jobCard->make_brand }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Model</label><input name="model" value="{{ $jobCard->model }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Serial Number</label><input name="serial_number" value="{{ $jobCard->serial_number }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Call Type</label><select name="call_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">Select</option><option value="corrective" {{ $jobCard->call_type === 'corrective' ? 'selected' : '' }}>Corrective Maintenance</option><option value="corrective_preventive" {{ $jobCard->call_type === 'corrective_preventive' ? 'selected' : '' }}>Corrective &amp; Preventive Maintenance</option><option value="preventive" {{ $jobCard->call_type === 'preventive' ? 'selected' : '' }}>Preventive Maintenance</option><option value="installation" {{ $jobCard->call_type === 'installation' ? 'selected' : '' }}>Installation</option></select></div>
                    </div>
                </div>

                <div class="px-5 py-4 border-b border-gray-100">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Assignment</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">— None —</option>@foreach($projects as $p)<option value="{{ $p->id }}" {{ $jobCard->project_id == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label><select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="">— Unassigned —</option>@foreach($technicians as $t)<option value="{{ $t->id }}" {{ $jobCard->assigned_to == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority <span class="text-red-500">*</span></label><select name="priority" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="low" {{ $jobCard->priority === 'low' ? 'selected' : '' }}>Low</option><option value="medium" {{ $jobCard->priority === 'medium' ? 'selected' : '' }}>Medium</option><option value="high" {{ $jobCard->priority === 'high' ? 'selected' : '' }}>High</option><option value="critical" {{ $jobCard->priority === 'critical' ? 'selected' : '' }}>Critical</option></select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Status <span class="text-red-500">*</span></label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="open" {{ $jobCard->status === 'open' ? 'selected' : '' }}>Open</option><option value="in_progress" {{ $jobCard->status === 'in_progress' ? 'selected' : '' }}>In Progress</option><option value="resolved" {{ $jobCard->status === 'resolved' ? 'selected' : '' }}>Resolved</option><option value="closed" {{ $jobCard->status === 'closed' ? 'selected' : '' }}>Closed</option></select></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" value="{{ $jobCard->due_date?->format('Y-m-d') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    </div>
                </div>

                <div class="px-5 py-4 border-b border-gray-100">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Findings</h4>
                    <div class="space-y-4">
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $jobCard->description }}</textarea></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Problem Reported</label><textarea name="problem_reported" rows="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $jobCard->problem_reported }}</textarea></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Defects Found</label><textarea name="defects_found" rows="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $jobCard->defects_found }}</textarea></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Action Taken</label><textarea name="action_taken" rows="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $jobCard->action_taken }}</textarea></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Resolution Notes</label><textarea name="resolution_notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $jobCard->resolution_notes }}</textarea></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">{{ $jobCard->notes }}</textarea></div>
                    </div>
                </div>

                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Parts Required / Replaced</h4>
                        <button type="button" onclick="addPartRow()" class="text-xs font-medium text-gray-600 hover:text-gray-900">+ Add part</button>
                    </div>
                    <div id="partsContainer" class="space-y-2">
                        @forelse($jobCard->parts as $index => $part)
                        <div class="grid grid-cols-12 gap-2 part-row">
                            <input type="hidden" name="parts[{{ $index }}][id]" value="{{ $part->id }}">
                            <div class="col-span-4"><input name="parts[{{ $index }}][part_name]" value="{{ $part->part_name }}" placeholder="Part name" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                            <div class="col-span-2"><input name="parts[{{ $index }}][quantity]" type="number" step="0.01" value="{{ $part->quantity }}" placeholder="Qty" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                            <div class="col-span-3"><input name="parts[{{ $index }}][model]" value="{{ $part->model }}" placeholder="Model" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                            <div class="col-span-3 flex gap-1"><input name="parts[{{ $index }}][part_number]" value="{{ $part->part_number }}" placeholder="Part number" class="flex-1 px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"><button type="button" onclick="this.closest('.part-row').remove()" class="text-gray-400 hover:text-red-600 px-1">×</button></div>
                        </div>
                        @empty
                        <div class="grid grid-cols-12 gap-2 part-row">
                            <div class="col-span-4"><input name="parts[0][part_name]" placeholder="Part name" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                            <div class="col-span-2"><input name="parts[0][quantity]" type="number" step="0.01" value="1" placeholder="Qty" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                            <div class="col-span-3"><input name="parts[0][model]" placeholder="Model" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                            <div class="col-span-3"><input name="parts[0][part_number]" placeholder="Part number" class="w-full px-2.5 py-1.5 rounded border border-gray-200 text-xs outline-none focus:border-gray-400"></div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="px-5 py-4 flex items-center gap-3">
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        {{-- Sign-off & payment --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Sign-off &amp; Payment</h3></div>
            <div class="px-5 py-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span style="width:8px;height:8px;border-radius:99px;background:{{ $jobCard->end_user_signed_at ? '#10b981' : '#d1d5db' }}"></span>
                        <span class="text-xs text-gray-700">End-user sign-off</span>
                    </div>
                    <span class="text-[10px] {{ $jobCard->end_user_signed_at ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">{{ $jobCard->end_user_signed_at ? 'Signed' : 'Pending' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span style="width:8px;height:8px;border-radius:99px;background:{{ $jobCard->technician_signed_at ? '#10b981' : '#d1d5db' }}"></span>
                        <span class="text-xs text-gray-700">Technician sign-off</span>
                    </div>
                    <span class="text-[10px] {{ $jobCard->technician_signed_at ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">{{ $jobCard->technician_signed_at ? 'Signed' : 'Pending' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span style="width:8px;height:8px;border-radius:99px;background:{{ $jobCard->payment_status === 'approved' ? '#10b981' : '#d1d5db' }}"></span>
                        <span class="text-xs text-gray-700">Payment approval</span>
                    </div>
                    <span class="text-[10px] {{ $jobCard->payment_status === 'approved' ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">{{ $jobCard->payment_status === 'approved' ? 'Approved' : 'Pending' }}</span>
                </div>
            </div>
            <div class="px-5 pb-4">
                @if($jobCard->payment_status !== 'approved')
                <form method="POST" action="{{ route('admin.job-cards.approve-payment', $jobCard) }}" onsubmit="return confirmAction(this, {title: 'Approve payment for this job card?'})">@csrf
                    <button type="submit" class="w-full px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700">Approve Payment</button>
                </form>
                @else
                <div class="px-4 py-3 rounded bg-gray-50 border border-gray-200 text-[11px] text-gray-500">
                    Approved by {{ $jobCard->approver?->name ?? '—' }} · {{ $jobCard->approved_at?->format('d M Y H:i') ?? '—' }}
                </div>
                @endif
            </div>
        </div>

        {{-- Signature pads --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Capture Signatures</h3></div>
            <div class="px-5 py-4 space-y-5">
                <form method="POST" action="{{ route('admin.job-cards.sign-off', $jobCard) }}">@csrf
                    <input type="hidden" name="type" value="end_user">
                    <label class="block text-xs font-medium text-gray-600 mb-1">End-user Name</label>
                    <input name="name" value="{{ $jobCard->end_user_name }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 mb-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">End-user Signature</label>
                    <canvas id="endUserCanvas" class="w-full h-24 border border-gray-200 rounded bg-white cursor-crosshair"></canvas>
                    <input type="hidden" name="signature" id="endUserSignature">
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="clearCanvas('endUserCanvas','endUserSignature')" class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs rounded hover:bg-gray-50">Clear</button>
                        <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white text-xs rounded hover:bg-slate-700 disabled:opacity-40" {{ $jobCard->end_user_signed_at ? 'disabled' : '' }}>{{ $jobCard->end_user_signed_at ? 'Signed' : 'Save Sign-off' }}</button>
                    </div>
                </form>

                <hr class="border-gray-100">

                <form method="POST" action="{{ route('admin.job-cards.sign-off', $jobCard) }}">@csrf
                    <input type="hidden" name="type" value="technician">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Technician Name</label>
                    <input name="name" value="{{ $jobCard->technician_name }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 mb-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Technician Signature</label>
                    <canvas id="technicianCanvas" class="w-full h-24 border border-gray-200 rounded bg-white cursor-crosshair"></canvas>
                    <input type="hidden" name="signature" id="technicianSignature">
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="clearCanvas('technicianCanvas','technicianSignature')" class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs rounded hover:bg-gray-50">Clear</button>
                        <button type="submit" class="px-3 py-1.5 bg-slate-900 text-white text-xs rounded hover:bg-slate-700 disabled:opacity-40" {{ $jobCard->technician_signed_at ? 'disabled' : '' }}>{{ $jobCard->technician_signed_at ? 'Signed' : 'Save Sign-off' }}</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Details</h3></div>
            <div class="divide-y divide-gray-100">
                <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-400">Project</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->project?->title ?? '—' }}</span></div>
                <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-400">Assigned To</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->assignedTo?->name ?? '—' }}</span></div>
                <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-400">Created By</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->creator?->name ?? 'System' }}</span></div>
                <div class="flex justify-between px-5 py-3"><span class="text-xs text-gray-400">Job Number</span><span class="text-xs font-mono font-medium text-gray-900">{{ $jobCard->job_number }}</span></div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.job-cards.destroy', $jobCard) }}" onsubmit="return confirmAction(this, {title: 'Delete this job card?', text: 'This cannot be undone', color: '#dc2626'})">@csrf @method('DELETE')
            <button type="submit" class="w-full px-4 py-2 border border-gray-200 text-red-600 text-xs font-medium rounded hover:bg-red-50 hover:border-red-200 transition-colors">Delete Job Card</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let partIndex = {{ max($jobCard->parts->count(), 1) }};
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

function initSignature(canvasId, inputId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    const input = document.getElementById(inputId);
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * 2;
    canvas.height = rect.height * 2;
    ctx.scale(2, 2);
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#111827';

    let drawing = false;
    function pos(e) {
        const r = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: clientX - r.left, y: clientY - r.top };
    }
    canvas.addEventListener('mousedown', e => { drawing = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove', e => { if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
    canvas.addEventListener('mouseup', () => { drawing = false; input.value = canvas.toDataURL(); });
    canvas.addEventListener('mouseleave', () => drawing = false);
    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = pos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; const p = pos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
    canvas.addEventListener('touchend', () => { drawing = false; input.value = canvas.toDataURL(); });
}

function clearCanvas(canvasId, inputId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById(inputId).value = '';
}

initSignature('endUserCanvas', 'endUserSignature');
initSignature('technicianCanvas', 'technicianSignature');
</script>
@endpush
