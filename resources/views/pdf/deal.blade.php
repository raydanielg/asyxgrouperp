<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Deal {{ $deal->deal_number }}</title>
<style>
    @page { margin: 40px 42px; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #111; font-size: 12px; }

    table.layout { width: 100%; border-collapse: collapse; }
    table.layout td { vertical-align: top; }

    .hdr { border-bottom: 2px solid #111; padding-bottom: 20px; }
    .co-name { font-size: 15px; font-weight: bold; }
    .co-meta { font-size: 10px; color: #555; margin-top: 4px; line-height: 1.7; }
    .doc { font-size: 26px; font-weight: bold; letter-spacing: .12em; text-align: right; }

    .meta-wrap { margin-top: 26px; }
    .k { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; font-weight: bold; margin-bottom: 5px; }
    .v { font-size: 12px; color: #111; font-weight: bold; }
    .s { font-size: 10px; color: #555; margin-top: 2px; }

    .sec { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #444; font-weight: bold; border-bottom: 1.5px solid #111; padding-bottom: 7px; margin-top: 34px; }

    table.rows { width: 100%; border-collapse: collapse; }
    table.rows td { padding: 10px 0; font-size: 11.5px; border-bottom: 1px solid #e5e5e5; }
    table.rows td.r { text-align: right; }

    table.totals { border-collapse: collapse; width: 100%; }
    table.totals td { padding: 7px 0; font-size: 11.5px; }
    table.totals td.r { text-align: right; }
    table.totals tr.grand td { border-top: 1.5px solid #111; padding-top: 10px; font-size: 14px; font-weight: bold; }
    table.totals tr.grand td.r { font-size: 16px; }

    .note .cap { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; font-weight: bold; margin-bottom: 6px; }
    .note .txt { font-size: 11px; color: #333; line-height: 1.7; }

    .sig .line { border-bottom: 1px solid #888; height: 26px; }
    .sig .cap { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #777; margin-top: 5px; }

    .foot { margin-top: 44px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>

<table class="layout hdr">
    <tr>
        <td style="width:60%;">
            <div class="co-name">{{ $company?->legal_name ?? $company?->name ?? config('app.name') }}</div>
            <div class="co-meta">
                {{ trim(($company?->address ?? 'Dar es Salaam') . ($company?->city ? ', ' . $company->city : '')) }}<br>
                @if($company?->phone)Tel: {{ $company->phone }} &middot; @endif{{ $company?->email ?? '' }}<br>
                @if($company?->tin)TIN: {{ $company->tin }}@endif
            </div>
        </td>
        <td style="width:40%;">
            <div class="doc">DEAL</div>
        </td>
    </tr>
</table>

<table class="layout meta-wrap">
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
        <td class="r" style="font-weight:bold;white-space:nowrap;">TZS {{ number_format($deal->value, 0) }}</td>
    </tr>
    @if($deal->notes)
    <tr>
        <td colspan="2" style="color:#555;font-size:10.5px;line-height:1.7;">{{ $deal->notes }}</td>
    </tr>
    @endif
</table>

<table class="layout" style="margin-top:20px;">
    <tr>
        <td style="width:55%;"></td>
        <td style="width:45%;">
            <table class="totals">
                <tr><td style="color:#666;">Deal Value</td><td class="r">TZS {{ number_format($deal->value, 0) }}</td></tr>
                <tr class="grand"><td>Total Value</td><td class="r" style="white-space:nowrap;">TZS {{ number_format($deal->value, 0) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

@if($deal->contracts->count() > 0)
<div class="sec">Related Contracts</div>
<table class="rows">
    @foreach($deal->contracts as $contract)
    <tr>
        <td>{{ $contract->title ?? 'Contract' }}</td>
        <td class="r" style="color:#555;white-space:nowrap;">{{ $contract->start_date?->format('d M Y') ?? '—' }}</td>
    </tr>
    @endforeach
</table>
@endif

@if($deal->relationLoaded('project') && $deal->project)
<div class="sec">Converted Project</div>
<table class="rows">
    <tr>
        <td><b>{{ $deal->project->title }}</b></td>
        <td class="r" style="font-weight:bold;white-space:nowrap;">TZS {{ number_format($deal->project->budget, 0) }}</td>
    </tr>
</table>
@endif

<table class="layout" style="margin-top:64px;">
    <tr>
        <td class="sig" style="width:44%;"><div class="line"></div><div class="cap">Sales representative</div></td>
        <td style="width:12%;"></td>
        <td class="sig" style="width:44%;"><div class="line"></div><div class="cap">Authorized signature</div></td>
    </tr>
</table>

<div class="foot">{{ $company?->legal_name ?? config('app.name') }} &middot; Generated {{ now()->format('d M Y') }}</div>
</body>
</html>
