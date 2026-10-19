<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Lead {{ $lead->lead_number }}</title>
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

    .name { font-size: 15px; font-weight: bold; }
    .status { font-size: 10px; color: #555; margin-left: 8px; }
    .sub { font-size: 10px; color: #555; margin-top: 3px; }

    table.info { width: 100%; border-collapse: collapse; margin-top: 16px; }
    table.info td { vertical-align: top; padding: 7px 0; font-size: 11px; }
    table.info .k { font-size: 9px; text-transform: uppercase; letter-spacing: .08em; color: #666; font-weight: bold; padding-bottom: 3px; }

    .sec { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #444; font-weight: bold; border-bottom: 1.5px solid #111; padding-bottom: 6px; margin-top: 26px; margin-bottom: 4px; }

    table.deals { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.deals td { padding: 9px 0; font-size: 11px; border-bottom: 1px solid #ddd; }
    table.deals td.r { text-align: right; font-weight: bold; }

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
        <div class="doc">LEAD</div>
        <div class="doc-no">{{ $lead->lead_number }}</div>
    </div>
</div>

<div class="name">{{ $lead->full_name }}<span class="status">— {{ ucfirst($lead->status) }}</span></div>
<div class="sub">{{ $lead->company ?? '—' }}@if($lead->source) &middot; {{ ucfirst(str_replace('_', ' ', $lead->source)) }}@endif</div>

<table class="info">
    <tr>
        <td style="width:33%;">
            <div class="k">Email</div>
            <div>{{ $lead->email ?? '—' }}</div>
        </td>
        <td style="width:33%;">
            <div class="k">Phone</div>
            <div>{{ $lead->phone ?? '—' }}</div>
        </td>
        <td style="width:33%;">
            <div class="k">Assigned To</div>
            <div>{{ $lead->assignedTo?->name ?? 'Unassigned' }}</div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="k">Created</div>
            <div>{{ $lead->created_at?->format('d M Y') ?? '—' }}</div>
        </td>
        <td>
            <div class="k">Updated</div>
            <div>{{ $lead->updated_at?->format('d M Y') ?? '—' }}</div>
        </td>
        <td></td>
    </tr>
</table>

@if($lead->notes)
<div class="sec">Notes</div>
<div style="font-size:11px;color:#333;line-height:1.7;margin-top:8px;">{{ $lead->notes }}</div>
@endif

@if($lead->deals->count() > 0)
<div class="sec">Related Deals ({{ $lead->deals->count() }})</div>
<table class="deals">
    @foreach($lead->deals as $deal)
    <tr>
        <td>
            <b>{{ $deal->title }}</b><br>
            <span style="color:#555;font-size:10px;">{{ $deal->deal_number }} &middot; {{ $deal->stage }} &middot; {{ $deal->expected_close_date?->format('d M Y') ?? 'No close date' }}</span>
        </td>
        <td class="r">TZS {{ number_format($deal->value, 0) }}</td>
    </tr>
    @endforeach
</table>
@endif

<div class="foot">{{ $company?->legal_name ?? config('app.name') }} &middot; Generated {{ now()->format('d M Y') }}</div>
</body>
</html>
