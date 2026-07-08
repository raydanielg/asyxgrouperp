<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Job Card - {{ $jobCard->job_number }}</title>
<style>
    @page { margin: 15mm; size: A4; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 0; }
    .header { text-align: center; border-bottom: 2px solid #4338ca; padding-bottom: 12px; margin-bottom: 20px; }
    .header h1 { font-size: 18px; color: #4338ca; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 1px; }
    .header p { font-size: 10px; color: #666; margin: 0; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: 600; }
    .badge-open { background: #fef3c7; color: #b45309; }
    .badge-in_progress { background: #e0f2fe; color: #0369a1; }
    .badge-resolved { background: #d1fae5; color: #047857; }
    .badge-closed { background: #f3f4f6; color: #4b5563; }
    .badge-low { background: #f3f4f6; color: #4b5563; }
    .badge-medium { background: #fef3c7; color: #b45309; }
    .badge-high { background: #ffe4e6; color: #e11d48; }
    .badge-critical { background: #fecaca; color: #dc2626; }
    .section { margin-bottom: 16px; }
    .section h3 { font-size: 11px; color: #4338ca; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin: 0 0 8px; text-transform: uppercase; letter-spacing: 0.5px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .field { margin-bottom: 6px; }
    .field-label { font-size: 9px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
    .field-value { font-size: 11px; color: #1a1a1a; font-weight: 500; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th, td { padding: 6px 8px; text-align: left; border: 1px solid #e5e7eb; }
    th { background: #f9fafb; font-size: 9px; text-transform: uppercase; color: #6b7280; }
    .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 6px; }
</style>
</head><body>
<div class="header">
    <h1>Job Card</h1>
    <p>{{ $jobCard->job_number }} &middot; {{ now()->format('d M Y') }}</p>
</div>
<div class="section">
    <h3>Job Information</h3>
    <div class="grid-2">
        <div class="field"><div class="field-label">Title</div><div class="field-value">{{ $jobCard->title }}</div></div>
        <div class="field"><div class="field-label">Status</div><div class="field-value"><span class="badge badge-{{ $jobCard->status }}">{{ str_replace('_', ' ', ucfirst($jobCard->status)) }}</span></div></div>
        <div class="field"><div class="field-label">Priority</div><div class="field-value"><span class="badge badge-{{ $jobCard->priority }}">{{ ucfirst($jobCard->priority) }}</span></div></div>
        <div class="field"><div class="field-label">Due Date</div><div class="field-value">{{ $jobCard->due_date?->format('d M Y') ?? 'N/A' }}</div></div>
        <div class="field"><div class="field-label">Project</div><div class="field-value">{{ $jobCard->project?->title ?? 'N/A' }}</div></div>
        <div class="field"><div class="field-label">Assigned To</div><div class="field-value">{{ $jobCard->assignedTo?->name ?? 'Unassigned' }}</div></div>
        <div class="field"><div class="field-label">Created By</div><div class="field-value">{{ $jobCard->creator?->name ?? 'System' }}</div></div>
        <div class="field"><div class="field-label">Created At</div><div class="field-value">{{ $jobCard->created_at->format('d M Y H:i') }}</div></div>
    </div>
</div>
@if($jobCard->description)
<div class="section">
    <h3>Description</h3>
    <p style="font-size: 10px; color: #374151;">{{ $jobCard->description }}</p>
</div>
@endif
@if($jobCard->notes)
<div class="section">
    <h3>Notes</h3>
    <p style="font-size: 10px; color: #374151;">{{ $jobCard->notes }}</p>
</div>
@endif
@if($jobCard->resolution_notes)
<div class="section">
    <h3>Resolution Notes</h3>
    <p style="font-size: 10px; color: #374151;">{{ $jobCard->resolution_notes }}</p>
</div>
@endif
<div class="section">
    <h3>Timeline</h3>
    <table>
        <tr><th>Event</th><th>Date</th></tr>
        <tr><td>Created</td><td>{{ $jobCard->created_at->format('d M Y H:i') }}</td></tr>
        @if($jobCard->resolved_at)<tr><td>Resolved</td><td>{{ $jobCard->resolved_at->format('d M Y H:i') }}</td></tr>@endif
        @if($jobCard->due_date)<tr><td>Due Date</td><td>{{ $jobCard->due_date->format('d M Y') }}</td></tr>@endif
    </table>
</div>
<div class="footer">
    Job Card {{ $jobCard->job_number }} &middot; Generated {{ now()->format('d M Y H:i') }}
</div>
</body></html>
