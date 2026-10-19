<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Proforma Invoice {{ $quotation->quotation_number }}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  @page{size:A4;margin:14mm 16mm;}
  body{font-family:'Helvetica','DejaVu Sans',sans-serif;color:#1f2937;font-size:9.5pt;line-height:1.45;}

  /* ── Header ── */
  .head{width:100%;border-bottom:2.5px solid #0f172a;padding-bottom:12px;margin-bottom:18px;}
  .head td{vertical-align:bottom;}
  .co-name{font-size:15pt;font-weight:700;color:#0f172a;letter-spacing:.5px;}
  .co-sub{font-size:7.5pt;color:#6b7280;letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
  .co-contact{font-size:7.5pt;color:#6b7280;margin-top:8px;line-height:1.6;}
  .doc-title{text-align:right;padding-right:2mm;}
  .doc-title .t{font-size:17pt;font-weight:700;color:#0f172a;letter-spacing:2px;}
  .doc-title .n{font-size:9pt;color:#6b7280;margin-top:4px;}
  .doc-title .st{display:inline-block;margin-top:6px;font-size:7.5pt;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#fff;padding:3px 10px;
    background:{{ $quotation->status === 'accepted' ? '#059669' : ($quotation->status === 'rejected' ? '#dc2626' : ($quotation->status === 'sent' ? '#2563eb' : '#6b7280')) }};}

  /* ── Bill-to + meta ── */
  .top{width:100%;margin-bottom:16px;border-collapse:collapse;}
  .top td{vertical-align:top;padding:0;}
  .lbl{font-size:7pt;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;}
  .billto{width:55%;}
  .billto .name{font-size:11pt;font-weight:700;color:#111827;}
  .billto .line{font-size:8.5pt;color:#4b5563;margin-top:2px;}
  .meta td{padding:3px 0;font-size:8.5pt;}
  .meta .k{color:#9ca3af;text-align:right;padding-right:10px;width:90px;}
  .meta .v{font-weight:600;color:#111827;}

  /* ── Items ── */
  table.items{width:100%;border-collapse:collapse;margin-top:6px;}
  .items th{background:#0f172a;color:#fff;font-size:7.5pt;font-weight:700;text-transform:uppercase;letter-spacing:1px;padding:8px 10px;text-align:left;}
  .items td{padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:9pt;vertical-align:top;}
  .items tr.alt td{background:#f9fafb;}
  .c{text-align:center;} .r{text-align:right;}

  /* ── Totals ── */
  .totals{width:100%;margin-top:0;border-collapse:collapse;}
  .totals td{padding:7px 10px;font-size:9pt;}
  .totals .k{color:#6b7280;text-align:right;width:75%;}
  .totals .v{text-align:right;font-weight:600;width:25%;border-bottom:1px solid #e5e7eb;}
  .totals .grand td{padding:10px;background:#0f172a;color:#fff;}
  .totals .grand .k{color:#fff;font-weight:700;letter-spacing:1px;}
  .totals .grand .v{font-size:11pt;font-weight:700;}

  /* ── Footer ── */
  .foot{margin-top:28px;}
  .terms h4{font-size:7.5pt;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
  .terms p{font-size:8pt;color:#4b5563;line-height:1.6;}
  .sign{margin-top:40px;width:100%;border-collapse:collapse;}
  .sign td{width:33%;font-size:8pt;color:#6b7280;padding-top:5px;vertical-align:bottom;}
  .sign .line{border-top:1px solid #9ca3af;padding-top:5px;}
  .thanks{font-size:8pt;color:#6b7280;margin-top:26px;text-align:center;}
  .regbar{margin-top:20px;padding-top:10px;border-top:1px solid #e5e7eb;font-size:7pt;color:#9ca3af;text-align:center;line-height:1.7;}
</style>
</head>
<body>

{{-- Header --}}
<table class="head"><tr>
  <td>
    <div class="co-name">{{ $company->legal_name ?? 'ASYX GROUP COMPANY LIMITED' }}</div>
    <div class="co-sub">Enterprise Solutions</div>
    <div class="co-contact">
      {{ $company->address ?? 'Tropical Center, 3rd Floor, New Bagamoyo Road' }}@if($company->city), {{ $company->city }}@endif
      @if($company->phone ?? true) &nbsp;·&nbsp; {{ $company->phone ?? '+255 755 432 071' }}@endif
      @if($company->email ?? true) &nbsp;·&nbsp; {{ $company->email ?? 'info@asyx.co.tz' }}@endif<br>
      @if($company->tax_id ?? true)TIN: {{ $company->tax_id ?? '108-800-186' }}@endif @if($company->vrn ?? true)&nbsp;·&nbsp;VRN: {{ $company->vrn ?? '40-009570-M' }}@endif
    </div>
  </td>
  <td class="doc-title">
    <div class="t">PROFORMA</div>
    <div class="n">{{ $quotation->quotation_number }}</div>
    <div><span class="st">{{ $quotation->status }}</span></div>
  </td>
</tr></table>

{{-- Bill To + Meta --}}
<table class="top"><tr>
  <td class="billto">
    <div class="lbl">Bill To</div>
    <div class="name">{{ $quotation->client_name }}</div>
    @if($quotation->lead?->company)<div class="line">{{ $quotation->lead->company }}</div>@endif
    @if($quotation->client_email)<div class="line">{{ $quotation->client_email }}</div>@endif
    @if($quotation->lead?->full_name)<div class="line">Attn: {{ $quotation->lead->full_name }}</div>@endif
  </td>
  <td>
    <table class="meta">
      <tr><td class="k">Invoice No.</td><td class="v">{{ $quotation->quotation_number }}</td></tr>
      <tr><td class="k">Date</td><td class="v">{{ $quotation->quotation_date->format('d M Y') }}</td></tr>
      <tr><td class="k">Valid Until</td><td class="v">{{ $quotation->valid_until?->format('d M Y') ?? '—' }}</td></tr>
      <tr><td class="k">Currency</td><td class="v">TZS</td></tr>
    </table>
  </td>
</tr></table>

{{-- Items --}}
<table class="items">
  <thead>
    <tr>
      <th style="width:30px;">#</th>
      <th>Description</th>
      <th class="c" style="width:55px;">Qty</th>
      <th class="r" style="width:90px;">Unit Price</th>
      @if($quotation->discount_amount > 0)<th class="r" style="width:75px;">Discount</th>@endif
      <th class="r" style="width:45px;">Tax %</th>
      <th class="r" style="width:95px;">Amount</th>
    </tr>
  </thead>
  <tbody>
    @forelse($quotation->items as $i => $item)
    <tr class="{{ $i % 2 ? 'alt' : '' }}">
      <td class="c">{{ $i + 1 }}</td>
      <td>{{ $item->description }}@if($item->unit) <span style="color:#9ca3af;font-size:8pt;">({{ $item->unit }})</span>@endif</td>
      <td class="c">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
      <td class="r">{{ number_format($item->unit_price, 2) }}</td>
      @if($quotation->discount_amount > 0)<td class="r">{{ number_format($item->discount_amount, 2) }}</td>@endif
      <td class="r">{{ rtrim(rtrim(number_format($item->tax_percentage, 2), '0'), '.') }}</td>
      <td class="r"><b>{{ number_format($item->line_total, 2) }}</b></td>
    </tr>
    @empty
    <tr><td colspan="7" class="c" style="color:#9ca3af;padding:14px;">No items</td></tr>
    @endforelse
  </tbody>
</table>

{{-- Totals --}}
<table class="totals">
  <tr><td class="k">Subtotal</td><td class="v">TZS {{ number_format($quotation->subtotal, 2) }}</td></tr>
  @if($quotation->discount_amount > 0)
  <tr><td class="k">Discount</td><td class="v" style="color:#dc2626;">-{{ number_format($quotation->discount_amount, 2) }}</td></tr>
  @endif
  @if($quotation->tax_amount > 0)
  <tr><td class="k">Tax</td><td class="v">+{{ number_format($quotation->tax_amount, 2) }}</td></tr>
  @endif
  <tr class="grand"><td class="k">GRAND TOTAL</td><td class="v">TZS {{ number_format($quotation->total, 2) }}</td></tr>
</table>

{{-- Terms + signatures --}}
<div class="foot">
  <div class="terms">
    <h4>Terms &amp; Conditions</h4>
    @if($quotation->terms)
    <p>{{ $quotation->terms }}</p>
    @else
    <p>1. Prices are quoted in Tanzanian Shillings (TZS).&nbsp; 2. Prices subject to change without prior notice.&nbsp; 3. Payment terms must be strictly observed.&nbsp; 4. Goods remain property of {{ $company->legal_name ?? 'ASYX Group Company Limited' }} until fully paid.</p>
    @endif
    @if($quotation->notes)<p style="margin-top:6px;"><b>Notes:</b> {{ $quotation->notes }}</p>@endif
  </div>

  <table class="sign"><tr>
    <td><div class="line">Prepared by</div></td>
    <td style="padding-left:20px;"><div class="line">Client signature</div></td>
    <td style="padding-left:20px;"><div class="line">Date</div></td>
  </tr></table>

  <p class="thanks">Thank you for your business.</p>
  <div class="regbar">
    {{ $company->legal_name ?? 'ASYX GROUP COMPANY LIMITED' }}
    @if($company->phone ?? true) · {{ $company->phone ?? '+255 755 432 071' }}@endif
    @if($company->email ?? true) · {{ $company->email ?? 'info@asyx.co.tz' }}@endif
    @if($company->website ?? false) · {{ $company->website }}@endif
  </div>
</div>

</body>
</html>
