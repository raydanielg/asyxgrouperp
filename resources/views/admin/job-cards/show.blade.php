@extends('layouts.admin')
@section('title', 'Job Card - ' . $jobCard->job_number)
@section('page_title', 'Job Card: ' . $jobCard->job_number)
@section('content')
@php
    $money = fn($n) => 'TZS ' . number_format($n);
    $statusColors = [
        'open' => 'bg-amber-50 text-amber-700 border-amber-200',
        'in_progress' => 'bg-sky-50 text-sky-700 border-sky-200',
        'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'closed' => 'bg-gray-50 text-gray-700 border-gray-200',
    ];
    $paymentColors = [
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];
    $callLabels = [
        'corrective' => 'Corrective Maintenance',
        'corrective_preventive' => 'Corrective & Preventive Maintenance',
        'preventive' => 'Preventive Maintenance',
        'installation' => 'Installation',
    ];
@endphp

<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">View and manage service call report details</p>
    <div class="flex gap-2">
        <a href="{{ route('admin.job-cards.print', $jobCard) }}" target="_blank" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Report
        </a>
        <a href="{{ route('admin.job-cards.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">Back</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Main info --}}
        <div class="bg-white rounded-xl border p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $jobCard->title }}</h3>
                    <p class="text-xs text-gray-400 mt-1">{{ $jobCard->job_number }} · Created {{ $jobCard->created_at->format('d M Y H:i') }} by {{ $jobCard->creator?->name ?? 'System' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$jobCard->status] ?? $statusColors['open'] }}">{{ str_replace('_', ' ', ucfirst($jobCard->status)) }}</span>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium border {{ $paymentColors[$jobCard->payment_status] ?? $paymentColors['pending'] }}">{{ ucfirst($jobCard->payment_status) }}</span>
                </div>
            </div>
        </div>

        {{-- Service Call Report form --}}
        <div class="bg-white rounded-xl border p-6">
            <h4 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Service Call Report Details
            </h4>
            <form method="POST" action="{{ route('admin.job-cards.update', $jobCard) }}" class="space-y-4" id="jobCardForm">@csrf @method('PATCH')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" value="{{ $jobCard->title }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">CSR No</label><input name="csr_no" value="{{ $jobCard->csr_no }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Report Date</label><input name="report_date" type="date" value="{{ $jobCard->report_date?->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Name</label><input name="customer_name" value="{{ $jobCard->customer_name }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Customer Address</label><input name="customer_address" value="{{ $jobCard->customer_address }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Branch Name</label><input name="branch_name" value="{{ $jobCard->branch_name }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Department</label><input name="department" value="{{ $jobCard->department }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Equipment Type</label><input name="equipment_type" value="{{ $jobCard->equipment_type }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Make / Brand</label><input name="make_brand" value="{{ $jobCard->make_brand }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Model</label><input name="model" value="{{ $jobCard->model }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Serial Number</label><input name="serial_number" value="{{ $jobCard->serial_number }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Call Type</label><select name="call_type" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">Select</option><option value="corrective" {{ $jobCard->call_type === 'corrective' ? 'selected' : '' }}>Corrective Maintenance</option><option value="corrective_preventive" {{ $jobCard->call_type === 'corrective_preventive' ? 'selected' : '' }}>Corrective &amp; Preventive Maintenance</option><option value="preventive" {{ $jobCard->call_type === 'preventive' ? 'selected' : '' }}>Preventive Maintenance</option><option value="installation" {{ $jobCard->call_type === 'installation' ? 'selected' : '' }}>Installation</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Project</label><select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">None</option>@foreach($projects as $p)<option value="{{ $p->id }}" {{ $jobCard->project_id == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>@endforeach</select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label><select name="assigned_to" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="">Unassigned</option>@foreach($technicians as $t)<option value="{{ $t->id }}" {{ $jobCard->assigned_to == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Priority *</label><select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="low" {{ $jobCard->priority === 'low' ? 'selected' : '' }}>Low</option><option value="medium" {{ $jobCard->priority === 'medium' ? 'selected' : '' }}>Medium</option><option value="high" {{ $jobCard->priority === 'high' ? 'selected' : '' }}>High</option><option value="critical" {{ $jobCard->priority === 'critical' ? 'selected' : '' }}>Critical</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Status *</label><select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"><option value="open" {{ $jobCard->status === 'open' ? 'selected' : '' }}>Open</option><option value="in_progress" {{ $jobCard->status === 'in_progress' ? 'selected' : '' }}>In Progress</option><option value="resolved" {{ $jobCard->status === 'resolved' ? 'selected' : '' }}>Resolved</option><option value="closed" {{ $jobCard->status === 'closed' ? 'selected' : '' }}>Closed</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date</label><input name="due_date" type="date" value="{{ $jobCard->due_date?->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none"></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">{{ $jobCard->description }}</textarea></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Problem Reported</label><textarea name="problem_reported" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">{{ $jobCard->problem_reported }}</textarea></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Defects Found</label><textarea name="defects_found" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">{{ $jobCard->defects_found }}</textarea></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Action Taken</label><textarea name="action_taken" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">{{ $jobCard->action_taken }}</textarea></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Resolution Notes</label><textarea name="resolution_notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">{{ $jobCard->resolution_notes }}</textarea></div>
                    <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Notes</label><textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none">{{ $jobCard->notes }}</textarea></div>
                </div>

                <div class="border rounded-lg p-4 bg-gray-50/50">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Parts Required / Replaced</label>
                    <div id="partsContainer" class="space-y-2">
                        @forelse($jobCard->parts as $index => $part)
                        <div class="grid grid-cols-12 gap-2 part-row">
                            <input type="hidden" name="parts[{{ $index }}][id]" value="{{ $part->id }}">
                            <div class="col-span-4"><input name="parts[{{ $index }}][part_name]" value="{{ $part->part_name }}" placeholder="Part name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                            <div class="col-span-2"><input name="parts[{{ $index }}][quantity]" type="number" step="0.01" value="{{ $part->quantity }}" placeholder="Qty" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                            <div class="col-span-3"><input name="parts[{{ $index }}][model]" value="{{ $part->model }}" placeholder="Model" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                            <div class="col-span-3 flex gap-1"><input name="parts[{{ $index }}][part_number]" value="{{ $part->part_number }}" placeholder="Part number" class="flex-1 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"><button type="button" onclick="this.closest('.part-row').remove()" class="text-rose-500 hover:text-rose-700 px-1">×</button></div>
                        </div>
                        @empty
                        <div class="grid grid-cols-12 gap-2 part-row">
                            <div class="col-span-4"><input name="parts[0][part_name]" placeholder="Part name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                            <div class="col-span-2"><input name="parts[0][quantity]" type="number" step="0.01" value="1" placeholder="Qty" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                            <div class="col-span-3"><input name="parts[0][model]" placeholder="Model" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                            <div class="col-span-3"><input name="parts[0][part_number]" placeholder="Part number" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
                        </div>
                        @endforelse
                    </div>
                    <button type="button" onclick="addPartRow()" class="mt-2 text-xs font-medium text-indigo-600 hover:text-indigo-800">+ Add part</button>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        {{-- Approval workflow --}}
        <div class="bg-white rounded-xl border p-5">
            <h4 class="text-xs font-bold text-gray-900 mb-3">Sign-off & Payment Approval</h4>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-lg border {{ $jobCard->end_user_signed_at ? 'bg-emerald-50/50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 {{ $jobCard->end_user_signed_at ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-xs font-medium {{ $jobCard->end_user_signed_at ? 'text-emerald-800' : 'text-gray-600' }}">End-user sign-off</span>
                    </div>
                    <span class="text-[10px] {{ $jobCard->end_user_signed_at ? 'text-emerald-700' : 'text-gray-400' }}">{{ $jobCard->end_user_signed_at ? 'Signed' : 'Pending' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg border {{ $jobCard->technician_signed_at ? 'bg-emerald-50/50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 {{ $jobCard->technician_signed_at ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-medium {{ $jobCard->technician_signed_at ? 'text-emerald-800' : 'text-gray-600' }}">Technician sign-off</span>
                    </div>
                    <span class="text-[10px] {{ $jobCard->technician_signed_at ? 'text-emerald-700' : 'text-gray-400' }}">{{ $jobCard->technician_signed_at ? 'Signed' : 'Pending' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg border {{ $jobCard->payment_status === 'approved' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-gray-50 border-gray-200' }}">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 {{ $jobCard->payment_status === 'approved' ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-xs font-medium {{ $jobCard->payment_status === 'approved' ? 'text-emerald-800' : 'text-gray-600' }}">Payment approval</span>
                    </div>
                    <span class="text-[10px] {{ $jobCard->payment_status === 'approved' ? 'text-emerald-700' : 'text-gray-400' }}">{{ $jobCard->payment_status === 'approved' ? 'Approved' : 'Pending' }}</span>
                </div>

                @if($jobCard->payment_status !== 'approved')
                <form method="POST" action="{{ route('admin.job-cards.approve-payment', $jobCard) }}" onsubmit="return confirm('Approve payment for this job card?')">@csrf
                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">Approve Payment</button>
                </form>
                @else
                <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-800">
                    Approved by {{ $jobCard->approver?->name ?? '—' }} on {{ $jobCard->approved_at?->format('d M Y H:i') ?? '—' }}
                </div>
                @endif
            </div>
        </div>

        {{-- Signature pads --}}
        <div class="bg-white rounded-xl border p-5">
            <h4 class="text-xs font-bold text-gray-900 mb-3">Capture Signatures</h4>

            <form method="POST" action="{{ route('admin.job-cards.sign-off', $jobCard) }}" class="space-y-4">@csrf
                <input type="hidden" name="type" value="end_user">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">End-user Name</label>
                    <input name="name" value="{{ $jobCard->end_user_name }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none mb-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">End-user Signature</label>
                    <canvas id="endUserCanvas" class="w-full h-24 border border-gray-200 rounded-lg bg-white cursor-crosshair"></canvas>
                    <input type="hidden" name="signature" id="endUserSignature">
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="clearCanvas('endUserCanvas','endUserSignature')" class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs rounded hover:bg-gray-50">Clear</button>
                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700" {{ $jobCard->end_user_signed_at ? 'disabled' : '' }}>{{ $jobCard->end_user_signed_at ? 'Signed' : 'Save Sign-off' }}</button>
                    </div>
                </div>
            </form>

            <hr class="my-4 border-gray-100">

            <form method="POST" action="{{ route('admin.job-cards.sign-off', $jobCard) }}" class="space-y-4">@csrf
                <input type="hidden" name="type" value="technician">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Technician Name</label>
                    <input name="name" value="{{ $jobCard->technician_name }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none mb-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Technician Signature</label>
                    <canvas id="technicianCanvas" class="w-full h-24 border border-gray-200 rounded-lg bg-white cursor-crosshair"></canvas>
                    <input type="hidden" name="signature" id="technicianSignature">
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="clearCanvas('technicianCanvas','technicianSignature')" class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs rounded hover:bg-gray-50">Clear</button>
                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700" {{ $jobCard->technician_signed_at ? 'disabled' : '' }}>{{ $jobCard->technician_signed_at ? 'Signed' : 'Save Sign-off' }}</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-xl border p-5">
            <h4 class="text-xs font-bold text-gray-900 mb-3">Details</h4>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-xs text-gray-400">Project</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->project?->title ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-xs text-gray-400">Assigned To</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->assignedTo?->name ?? '—' }}</span></div>
                <div class="flex justify-between"><span class="text-xs text-gray-400">Created By</span><span class="text-xs font-medium text-gray-900">{{ $jobCard->creator?->name ?? 'System' }}</span></div>
                <div class="flex justify-between"><span class="text-xs text-gray-400">Job Number</span><span class="text-xs font-mono font-medium text-gray-900">{{ $jobCard->job_number }}</span></div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.job-cards.destroy', $jobCard) }}" onsubmit="return confirm('Delete this job card?')">@csrf @method('DELETE')
            <button type="submit" class="w-full px-4 py-2 border border-red-200 text-red-600 text-sm rounded-lg hover:bg-red-50 transition-colors">Delete Job Card</button>
        </form>
    </div>
</div>

<script>
let partIndex = {{ max($jobCard->parts->count(), 1) }};
function addPartRow() {
    const container = document.getElementById('partsContainer');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 part-row';
    row.innerHTML = `
        <div class="col-span-4"><input name="parts[${partIndex}][part_name]" placeholder="Part name" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
        <div class="col-span-2"><input name="parts[${partIndex}][quantity]" type="number" step="0.01" value="1" placeholder="Qty" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
        <div class="col-span-3"><input name="parts[${partIndex}][model]" placeholder="Model" class="w-full px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"></div>
        <div class="col-span-3 flex gap-1"><input name="parts[${partIndex}][part_number]" placeholder="Part number" class="flex-1 px-2 py-1.5 rounded border border-gray-200 text-xs outline-none"><button type="button" onclick="this.closest('.part-row').remove()" class="text-rose-500 hover:text-rose-700 px-1">×</button></div>
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
    ctx.strokeStyle = '#1D3FBF';

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
@endsection