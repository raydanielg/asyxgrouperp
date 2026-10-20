<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Project Report — {{ $project->project_number }}</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{background:#fff;color:#111827;font-family:Helvetica,Arial,sans-serif;font-size:11px;line-height:1.45;}
  .page{padding:0;}
  .border-b{border-bottom:1px solid #111827;}
  table{width:100%;border-collapse:collapse;}
  .small{font-size:9.5px;color:#6b7280;}
</style>
</head>
<body>
<div class="page">
@php
  $taskTotal = $project->tasks->count();
  $taskDone = $project->tasks->whereIn('status', ['done','completed'])->count();
  $bugTotal = $project->bugs->count();
  $bugOpen = $project->bugs->where('status','open')->count();
  $totalHours = $project->timesheets->sum('hours');
  $invoiceTotal = $project->invoices->sum('total_amount');
  $coName = $company?->name ?? 'ASYX Group';
@endphp

{{-- ── Header ── --}}
<table style="margin-bottom:14px;">
  <tr>
    <td style="vertical-align:top;">
      <div style="font-size:16px;font-weight:bold;">{{ $coName }}</div>
      <div class="small" style="margin-top:3px;">
        @if($company?->address){{ $company->address }}@endif
        @if($company?->tin) &nbsp;·&nbsp; TIN: {{ $company->tin }}@endif
      </div>
    </td>
    <td style="text-align:right;vertical-align:top;">
      <div style="font-size:22px;font-weight:bold;letter-spacing:1px;">PROJECT REPORT</div>
      <div class="small" style="margin-top:4px;">{{ $project->project_number }} · {{ now()->format('d M Y') }}</div>
    </td>
  </tr>
</table>

<table style="margin-bottom:16px;">
  <tr><td class="border-b" style="height:2px;"></td></tr>
</table>

{{-- ── Project heading ── --}}
<table style="margin-bottom:16px;">
  <tr>
    <td style="vertical-align:top;">
      <div style="font-size:15px;font-weight:bold;">{{ $project->title }}</div>
      <div class="small" style="margin-top:2px;">Status: <b>{{ strtoupper(str_replace('_', ' ', $project->status)) }}</b> · Priority: {{ ucfirst($project->priority) }}</div>
    </td>
    <td style="text-align:right;vertical-align:top;">
      <div class="small">PROGRESS</div>
      <div style="font-size:18px;font-weight:bold;">{{ $project->progress }}%</div>
    </td>
  </tr>
</table>

{{-- ── Details grid ── --}}
<table style="margin-bottom:16px;border-top:1px solid #e5e7eb;">
  <tr>
    <td style="padding:8px 12px 8px 0;vertical-align:top;width:33%;border-bottom:1px solid #e5e7eb;">
      <div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Manager</div>
      <div style="font-weight:bold;margin-top:2px;">{{ $project->manager?->name ?? 'N/A' }}</div>
    </td>
    <td style="padding:8px 12px;vertical-align:top;width:33%;border-bottom:1px solid #e5e7eb;border-left:1px solid #e5e7eb;">
      <div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Start Date</div>
      <div style="font-weight:bold;margin-top:2px;">{{ $project->start_date?->format('d M Y') ?? 'N/A' }}</div>
    </td>
    <td style="padding:8px 12px;vertical-align:top;border-bottom:1px solid #e5e7eb;border-left:1px solid #e5e7eb;">
      <div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Due Date</div>
      <div style="font-weight:bold;margin-top:2px;">{{ $project->due_date?->format('d M Y') ?? 'N/A' }}</div>
    </td>
  </tr>
  <tr>
    <td style="padding:8px 12px 8px 0;vertical-align:top;border-bottom:1px solid #e5e7eb;">
      <div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Budget</div>
      <div style="font-weight:bold;margin-top:2px;">TZS {{ number_format($project->budget, 0) }}</div>
    </td>
    <td style="padding:8px 12px;vertical-align:top;border-bottom:1px solid #e5e7eb;border-left:1px solid #e5e7eb;">
      <div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Invoicing</div>
      <div style="font-weight:bold;margin-top:2px;">{{ $project->recurring_invoicing ? 'Recurring (Monthly)' : 'One-Time / Manual' }}</div>
    </td>
    <td style="padding:8px 12px;vertical-align:top;border-bottom:1px solid #e5e7eb;border-left:1px solid #e5e7eb;">
      <div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Total Invoiced</div>
      <div style="font-weight:bold;margin-top:2px;">TZS {{ number_format($invoiceTotal, 0) }}</div>
    </td>
  </tr>
  @if($project->description)
  <tr>
    <td colspan="3" style="padding:8px 12px 8px 0;vertical-align:top;">
      <div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Description</div>
      <div style="margin-top:2px;color:#374151;">{{ $project->description }}</div>
    </td>
  </tr>
  @endif
</table>

{{-- ── Summary ── --}}
<table style="margin-bottom:18px;">
  <tr>
    <td style="width:25%;text-align:center;padding:10px;border:1px solid #e5e7eb;">
      <div style="font-size:16px;font-weight:bold;">{{ $taskTotal }}</div><div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Tasks</div>
    </td>
    <td style="width:25%;text-align:center;padding:10px;border:1px solid #e5e7eb;border-left:none;">
      <div style="font-size:16px;font-weight:bold;">{{ $taskDone }}</div><div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Tasks Done</div>
    </td>
    <td style="width:25%;text-align:center;padding:10px;border:1px solid #e5e7eb;border-left:none;">
      <div style="font-size:16px;font-weight:bold;">{{ number_format($totalHours, 0) }}</div><div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Hours Logged</div>
    </td>
    <td style="width:25%;text-align:center;padding:10px;border:1px solid #e5e7eb;border-left:none;">
      <div style="font-size:16px;font-weight:bold;">{{ $project->employees->count() }}</div><div class="small" style="text-transform:uppercase;letter-spacing:.5px;">Staff</div>
    </td>
  </tr>
</table>

{{-- ── Staff ── --}}
@if($project->employees->count() > 0)
<div style="margin-bottom:18px;">
  <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.5px;padding-bottom:5px;border-bottom:1px solid #111827;margin-bottom:0;">Assigned Staff</div>
  <table>
    <tr class="small" style="text-transform:uppercase;letter-spacing:.5px;">
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;width:26px;">#</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Name</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Role</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Department</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;text-align:right;">Status</td>
    </tr>
    @foreach($project->employees as $idx => $emp)
    <tr>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $idx + 1 }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-weight:bold;">{{ $emp->first_name }} {{ $emp->last_name }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;">{{ $emp->pivot->role ?? '—' }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $emp->department?->name ?? '—' }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;">{{ $emp->pivot->is_active ? 'Active' : 'Inactive' }}</td>
    </tr>
    @endforeach
  </table>
</div>
@endif

{{-- ── Tasks ── --}}
@if($taskTotal > 0)
<div style="margin-bottom:18px;">
  <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.5px;padding-bottom:5px;border-bottom:1px solid #111827;">Tasks ({{ $taskDone }}/{{ $taskTotal }} done)</div>
  <table>
    <tr class="small" style="text-transform:uppercase;letter-spacing:.5px;">
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;width:26px;">#</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Task</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Assigned To</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Priority</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Status</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;text-align:right;">Due</td>
    </tr>
    @foreach($project->tasks->take(15) as $idx => $task)
    <tr>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $idx + 1 }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;">{{ $task->title }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ ucfirst($task->priority) }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;" class="small">{{ $task->due_date?->format('d M Y') ?? '—' }}</td>
    </tr>
    @endforeach
    @if($taskTotal > 15)
    <tr><td colspan="6" style="padding:6px 0;text-align:center;" class="small">+ {{ $taskTotal - 15 }} more tasks</td></tr>
    @endif
  </table>
</div>
@endif

{{-- ── Timesheets ── --}}
@if($totalHours > 0)
<div style="margin-bottom:18px;">
  <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.5px;padding-bottom:5px;border-bottom:1px solid #111827;">Timesheets ({{ number_format($totalHours, 1) }} hrs)</div>
  <table>
    <tr class="small" style="text-transform:uppercase;letter-spacing:.5px;">
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;width:26px;">#</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Employee</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Description</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Date</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;text-align:right;">Hours</td>
    </tr>
    @foreach($project->timesheets->take(10) as $idx => $ts)
    <tr>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $idx + 1 }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;">{{ $ts->employee?->first_name }} {{ $ts->employee?->last_name }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $ts->description ?? 'Work entry' }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $ts->date?->format('d M Y') ?? '—' }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:bold;">{{ $ts->hours }}h</td>
    </tr>
    @endforeach
    @if($project->timesheets->count() > 10)
    <tr><td colspan="5" style="padding:6px 0;text-align:center;" class="small">+ {{ $project->timesheets->count() - 10 }} more entries</td></tr>
    @endif
  </table>
</div>
@endif

{{-- ── Invoices ── --}}
@if($project->invoices->count() > 0)
<div style="margin-bottom:18px;">
  <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:.5px;padding-bottom:5px;border-bottom:1px solid #111827;">Invoices</div>
  <table>
    <tr class="small" style="text-transform:uppercase;letter-spacing:.5px;">
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;width:26px;">#</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Invoice No.</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;">Date</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;text-align:right;">Amount</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;text-align:right;">Paid</td>
      <td style="padding:6px 0;border-bottom:1px solid #e5e7eb;text-align:right;">Status</td>
    </tr>
    @foreach($project->invoices as $idx => $inv)
    <tr>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $idx + 1 }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;font-family:Courier,monospace;font-size:10px;">{{ $inv->invoice_number }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;" class="small">{{ $inv->invoice_date?->format('d M Y') ?? '—' }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;">{{ number_format($inv->total_amount, 0) }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;">{{ number_format($inv->paid_amount, 0) }}</td>
      <td style="padding:6px 0;border-bottom:1px solid #f3f4f6;text-align:right;">{{ ucfirst($inv->status) }}</td>
    </tr>
    @endforeach
  </table>
</div>
@endif

{{-- ── Signatures ── --}}
<table style="margin-top:34px;">
  <tr>
    <td style="width:45%;"><div style="border-top:1px solid #111827;padding-top:5px;width:75%;" class="small">Project Manager</div></td>
    <td></td>
    <td style="width:45%;text-align:right;"><div style="border-top:1px solid #111827;padding-top:5px;width:75%;margin-left:auto;" class="small">Authorized Signature</div></td>
  </tr>
</table>

<div class="small" style="margin-top:26px;padding-top:8px;border-top:1px solid #e5e7eb;">
  {{ $coName }} · Generated {{ now()->format('d M Y, H:i') }}
</div>
</div>
</body>
</html>
