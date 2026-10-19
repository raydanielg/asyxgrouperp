<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Deal {{ $deal->deal_number }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #111; font-size: 12px; }

    .hdr { overflow: hidden; padding-bottom: 20px; border-bottom: 2px solid #111; }
    .hdr .left { float: left; }
    .hdr .right { float: right; text-align: right; }
    .co-name { font-size: 15px; font-weight: bold; }
    .co-meta { font-size: 10px; color: #555; margin-top: 4px; line-height: 1.7; }
    .doc { font-size: 26px; font-weight: bold; letter-spacing: .12em; }

    .meta { width: 100%; border-collapse: collapse; margin-top: 26px; }
    .meta td { vertical-align: top; padding: 0; }
    .meta .k { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; font-weight: bold; margin-bottom: 5px; }
    .meta .v { font-size: 12px; color: #111; font-weight: bold; }
    .meta .s { font-size: 10px; color: #555; margin-top: 2px; }

    .sec { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #444; font-weight: bold; border-bottom: 1.5px solid #111; padding-bottom: 7px; margin-top: 34px; }

    table.rows { width: 100%; border-collapse: collapse; }
    table.rows td { padding: 10px 0; font-size: 11.5px; border-bottom: 1px solid #e5e5e5; }
    table.rows td.r { text-align: right; }

    .totals { width: 260px; float: right; margin-top: 20px; }
    .totals table { width: 100%; border-collapse: collapse; }
    .totals td { padding: 7px 0; font-size: 11.5px; }
    .totals td.r { text-align: right; }
    .totals tr.grand td { border-top: 1.5px solid #111; padding-top: 10px; font-size: 14px; font-weight: bold; }
    .totals tr.grand td.r { font-size: 16px; }

    .note { clear: both; margin-top: 34px; }
    .note .cap { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; font-weight: bold; margin-bottom: 6px; }
    .note .txt { font-size: 11px; color: #333; line-height: 1.7; }

    .sigs { margin-top: 60px; overflow: hidden; }
    .sig { width: 44%; float: left; }
    .sig:last-child { float: right; }
    .sig .line { border-bottom: 1px solid #888; height: 24px; }
    .sig .cap { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; margin-top: 5px; }

    .foot { clear: both; margin-top: 44px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>

<div class="hdr">
    <div class="left">
        <div class="co-name">{{ $company?->legal_name ?? $company?->name ?? config('app.name') }}</div>
        <div class="co-meta">
            {{ trim(($company?->address ?? 'Dar es Salaam') . ($company?->city ? ', ' . $company->city : '')) }}<br>
            @if($company?->phone)Tel: {{ $company->phone }} &middot; @endif{{ $company?->email ?? '' }}<br>
            @if($company?->tin)TIN: {{ $company->tin }}@endif
        </div>
    </div>
    <div class="right">
        <div class="doc">DEAL</div>
    </div>
</div>

<table class="meta">
    <tr>
        <td style="width:36%;">
            <div class="k">Client</div>
            <div class="v">{{ $deal->lead?->full_name ?? '—' }}</div>
            <div class="s">{{ $deal->lead?->company ?? '' }}</div>
        </td>
        <td style="width:32%;">
            <div class="k">Deal No.</div>
            <div class="v">{{ $deal->deal_number }}</div>
            <div class="s">Status: {{ ucfirst($deal->status) }}</div>
        </td>
        <td style="width:32%;">
            <div class="k">Stage / Close</div>
            <div class="v">{{ ucfirst(str_replace('_', ' ', $deal->stage)) }}</div>
            <div class="s">{{ $deal->expected_close_date?->format('d M Y') ?? 'No close date' }}</div>
        </td>
    </tr>
</table>

<div class="sec">Deal Details</div>
<table class="rows">
    <tr>
        <td><b>{{ $deal->title }}</b></td>
        <td class="r" style="font-weight:bold;">TZS {{ number_format($deal->value, 0) }}</td>
    </tr>
    @if($deal->notes)
    <tr>
        <td colspan="2" style="color:#555;font-size:10.5px;">{{ $deal->notes }}</td>
    </tr>
    @endif
</table>

<div class="totals">
    <table>
        <tr><td style="color:#666;">Deal Value</td><td class="r">TZS {{ number_format($deal->value, 0) }}</td></tr>
        <tr class="grand"><td>Total Value</td><td class="r">TZS {{ number_format($deal->value, 0) }}</td></tr>
    </table>
</div>

@if($deal->contracts->count() > 0)
<div class="note" style="clear:both;">
    <div class="sec" style="margin-top:34px;">Related Contracts</div>
    <table class="rows">
        @foreach($deal->contracts as $contract)
        <tr>
            <td>{{ $contract->title ?? 'Contract' }}</td>
            <td class="r" style="color:#555;">{{ $contract->start_date?->format('d M Y') ?? '—' }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endif

@if($deal->relationLoaded('project') && $deal->project)
<div class="note">
    <div class="sec">Converted Project</div>
    <table class="rows">
        <tr>
            <td><b>{{ $deal->project->title }}</b></td>
            <td class="r" style="font-weight:bold;">TZS {{ number_format($deal->project->budget, 0) }}</td>
        </tr>
    </table>
</div>
@endif

<div class="sigs">
    <div class="sig"><div class="line"></div><div class="cap">Sales representative</div></div>
    <div class="sig"><div class="line"></div><div class="cap">Authorized signature</div></div>
</div>

<div class="foot">{{ $company?->legal_name ?? config('app.name') }} &middot; Generated {{ now()->format('d M Y') }}</div>
</body>
</html>
