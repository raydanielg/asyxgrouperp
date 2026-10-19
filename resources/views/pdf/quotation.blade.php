<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  @page{size:A4;margin:22mm 20mm;}
  body{font-family:'Helvetica','DejaVu Sans',sans-serif;color:#111827;font-size:10pt;line-height:1.5;padding:22mm 20mm;margin:0;}

  .head{width:100%;border-collapse:collapse;margin-bottom:22mm;}
  .head td{vertical-align:top;}
  .co-name{font-size:11.5pt;font-weight:700;}
  .co-info{font-size:8.5pt;color:#6b7280;margin-top:2px;line-height:1.6;}
  .doc{font-size:20pt;font-weight:700;text-align:right;letter-spacing:1px;color:#111827;}

  .top{width:100%;border-collapse:collapse;margin-bottom:14mm;}
  .top td{vertical-align:top;}
  .lbl{font-size:7.5pt;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:1px;margin-bottom:3px;}
  .val{font-size:10pt;line-height:1.6;}

  table.items{width:100%;border-collapse:collapse;margin-bottom:8mm;}
  .items th{font-size:8pt;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;text-align:left;padding:6px 4px;border-bottom:1.5px solid #111827;}
  .items td{padding:7px 4px;border-bottom:1px solid #d1d5db;font-size:9.5pt;}
  .c{text-align:center;} .r{text-align:right;}

  table.totals{width:100%;border-collapse:collapse;margin-bottom:16mm;}
  .totals td{padding:4px;font-size:9.5pt;}
  .totals .k{text-align:right;color:#111827;width:74%;}
  .totals .v{text-align:right;width:26%;}
  .totals .grand td{border-top:1.5px solid #111827;font-weight:700;padding-top:7px;}

  .terms{margin-bottom:26mm;}
  .terms p{font-size:9.5pt;line-height:1.7;}

  .signs{width:100%;border-collapse:collapse;}
  .signs td{width:50%;vertical-align:bottom;}
  .signs .line{border-top:1px solid #111827;padding-top:4px;font-size:8pt;color:#4b5563;width:70%;}
  .signs td:last-child .line{margin-left:auto;}
</style>
</head>
<body>

<table class="head"><tr>
  <td>
    <div class="co-name">{{ $company->legal_name ?? 'ASYX Group Company Limited' }}</div>
    <div class="co-info">
      {{ $company->address ?? 'New Bagamoyo Road' }}@if($company->city ?? null), {{ $company->city }}@endif<br>
      {{ $company->email ?? 'info@asyx.co.tz' }} | {{ $company->phone ?? '+255 755 432 071' }}
    </div>
  </td>
  <td class="doc">QUOTATION</td>
</tr></table>

<table class="top"><tr>
  <td style="width:40%;">
    <div class="lbl">Bill To</div>
    <div class="val">
      <b>{{ $quotation->client_name }}</b><br>
      @if($quotation->lead?->company){{ $quotation->lead->company }}<br>@endif
      @if($quotation->client_email){{ $quotation->client_email }}<br>@endif
      @if($quotation->lead?->full_name)Attn: {{ $quotation->lead->full_name }}@endif
    </div>
  </td>
  <td style="width:30%;">
    <div class="lbl">Quotation No.</div>
    <div class="val">{{ $quotation->quotation_number }}</div>
    <div class="lbl" style="margin-top:6px;">Date</div>
    <div class="val">{{ $quotation->quotation_date->format('d M Y') }}</div>
  </td>
  <td style="width:30%;">
    <div class="lbl">Valid Until</div>
    <div class="val">{{ $quotation->valid_until?->format('d M Y') ?? '—' }}</div>
    <div class="lbl" style="margin-top:6px;">Status</div>
    <div class="val" style="text-transform:capitalize;">{{ $quotation->status }}</div>
  </td>
</tr></table>

<table class="items">
  <thead>
    <tr>
      <th style="width:55%;">Description</th>
      <th class="c" style="width:10%;">Qty</th>
      <th class="r" style="width:17%;">Price</th>
      <th class="r" style="width:18%;">Total</th>
    </tr>
  </thead>
  <tbody>
    @forelse($quotation->items as $item)
    <tr>
      <td>{{ $item->description }}@if($item->unit) <span style="color:#9ca3af;">({{ $item->unit }})</span>@endif</td>
      <td class="c">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
      <td class="r">{{ number_format($item->unit_price) }}</td>
      <td class="r">{{ number_format($item->line_total) }}</td>
    </tr>
    @empty
    <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:12px;">No items</td></tr>
    @endforelse
  </tbody>
</table>

<table class="totals">
  <tr><td class="k">Subtotal</td><td class="v">{{ number_format($quotation->subtotal) }}</td></tr>
  @if($quotation->discount_amount > 0)
  <tr><td class="k">Discount</td><td class="v">-{{ number_format($quotation->discount_amount) }}</td></tr>
  @endif
  @if($quotation->tax_amount > 0)
  <tr><td class="k">VAT</td><td class="v">{{ number_format($quotation->tax_amount) }}</td></tr>
  @endif
  <tr class="grand"><td class="k">Total (TZS)</td><td class="v">{{ number_format($quotation->total) }}</td></tr>
</table>

<div class="terms">
  <div class="lbl">Terms</div>
  @if($quotation->terms)
  <p style="white-space:pre-line;">{{ $quotation->terms }}</p>
  @else
  <p>Prices are quoted in Tanzanian Shillings (TZS).<br>This quotation is valid until the date shown above.<br>Goods remain property of {{ $company->legal_name ?? 'ASYX Group Company Limited' }} until fully paid.</p>
  @endif
  @if($quotation->notes)<p style="margin-top:6px;"><b>Notes:</b> {{ $quotation->notes }}</p>@endif
</div>

<table class="signs"><tr>
  <td><div class="line">Client signature</div></td>
  <td><div class="line">Company signature</div></td>
</tr></table>

</body>
</html>
