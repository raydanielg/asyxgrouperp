<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Deal {{ $deal->deal_number }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #111; font-size: 12px; }
    .hdr { overflow: hidden; padding-bottom: 16px; border-bottom: 2px solid #111; margin-bottom: 26px; }
    .hdr .left { float: left; }
    .hdr .right { float: right; text-align: right; }
    .co-name { font-size: 14px; font-weight: bold; }
    .co-meta { font-size: 10px; color: #555; margin-top: 3px; }
    .doc { font-size: 18px; font-weight: bold; letter-spacing: .14em; }
    .doc-no { font-size: 10px; color: #555; margin-top: 3px; }

    .title { font-size: 15px; font-weight: bold; }
    .sub { font-size: 10.5px; color: #555; margin-top: 4px; }

    table.info { width: 100%; border-collapse: collapse; margin-top: 18px; }
    table.info td { vertical-align: top; padding: 9px 0; font-size: 11px; border-bottom: 1px solid #eee; }
    table.info .k { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: #666; font-weight: bold; padding-bottom: 3px; }

    .valbar { margin-top: 28px; border: 1.5px solid #111; padding: 14px 16px; overflow: hidden; }
    .valbar .l { float: left; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .1em; padding-top: 4px; }
    .valbar .r { float: right; font-size: 18px; font-weight: bold; }
    .valbar .r small { font-size: 10px; font-weight: normal; color: #777; }

    .sec { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #444; font-weight: bold; border-bottom: 1.5px solid #111; padding-bottom: 6px; margin-top: 30px; margin-bottom: 4px; }

    table.rows { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.rows td { padding: 9px 0; font-size: 11px; border-bottom: 1px solid #ddd; }
    table.rows td.r { text-align: right; }

    .foot { margin-top: 40px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="hdr">
    <div class="left">
        <div class="co-name">{{ $company?->legal_name ?? $company?->name ?? config('app.name') }}</div>
        <div class="co-meta">
            {{ trim(($company?->address ?? 'Dar es Salaam') . ($company?->city ? ', ' . $company->city : '')) }}
            @if($company?->tin) &middot; TIN: {{ $company->tin }}@endif
        </div>
    </div>
    <div class="right">
        <div class="doc">DEAL</div>
        <div class="doc-no">{{ $deal->deal_number }}</div>
    </div>
</div>

<div class="title">{{ $deal->title }}<span style="font-size:10px;color:#555;margin-left:8px;text-transform:uppercase;letter-spacing:.08em;">— {{ ucfirst($deal->status) }}</span></div>
<div class="sub">{{ ucfirst(str_replace('_', ' ', $deal->stage)) }}@if($deal->expected_close_date) &middot; Expected close {{ $deal->expected_close_date->format('d M Y') }}@endif</div>

<table class="info">
    <tr>
        <td style="width:33%;">
            <div class="k">Lead</div>
            <div>{{ $deal->lead?->full_name ?? '—' }}</div>
        </td>
        <td style="width:33%;">
            <div class="k">Assigned To</div>
            <div>{{ $deal->assignedTo?->name ?? 'Unassigned' }}</div>
        </td>
        <td style="width:33%;">
            <div class="k">Created</div>
            <div>{{ $deal->created_at?->format('d M Y') ?? '—' }}</div>
        </td>
    </tr>
</table>

<div class="valbar">
    <span class="l">Deal Value</span>
    <span class="r">TZS {{ number_format($deal->value, 0) }} <small>incl. all taxes</small></span>
</div>

@if($deal->notes)
<div class="sec">Notes</div>
<div style="font-size:11px;color:#333;line-height:1.7;margin-top:8px;">{{ $deal->notes }}</div>
@endif

@if($deal->contracts->count() > 0)
<div class="sec">Contracts ({{ $deal->contracts->count() }})</div>
<table class="rows">
    @foreach($deal->contracts as $contract)
    <tr>
        <td>{{ $contract->title ?? 'Contract' }}</td>
        <td class="r" style="color:#555;">{{ $contract->start_date?->format('d M Y') ?? '—' }}</td>
    </tr>
    @endforeach
</table>
@endif

@if($deal->relationLoaded('project') && $deal->project)
<div class="sec">Converted Project</div>
<table class="rows">
    <tr>
        <td><b>{{ $deal->project->title }}</b></td>
        <td class="r" style="font-weight:bold;">TZS {{ number_format($deal->project->budget, 0) }}</td>
    </tr>
</table>
@endif

<div class="foot">{{ $company?->legal_name ?? config('app.name') }} &middot; Generated {{ now()->format('d M Y') }}</div>
</body>
</html>
