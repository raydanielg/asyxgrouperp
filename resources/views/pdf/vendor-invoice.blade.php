<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Vendor Invoice {{ $invoice->vendor_invoice_number }}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box;}
  @page{size:A4;margin:22mm 20mm;}
  body{font-family:'Helvetica','DejaVu Sans',sans-serif;color:#111827;font-size:10pt;line-height:1.5;padding:22mm 20mm;margin:0;}

  .head{width:100%;border-collapse:collapse;margin-bottom:20mm;}
  .head td{vertical-align:top;}
  .co-name{font-size:11.5pt;font-weight:700;}
  .co-info{font-size:8.5pt;color:#6b7280;margin-top:2px;line-height:1.6;}
  .doc{font-size:20pt;font-weight:700;text-align:right;letter-spacing:1px;color:#111827;}

  .top{width:100%;border-collapse:collapse;margin-bottom:12mm;}
  .top td{vertical-align:top;}
  .lbl{font-size:7.5pt;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:1px;margin-bottom:3px;}
  .val{font-size:10pt;line-height:1.6;}

  table.items{width:100%;border-collapse:collapse;margin-bottom:8mm;}
  .items th{font-size:8pt;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;text-align:left;padding:6px 4px;border-bottom:1.5px solid #111827;}
  .items td{padding:7px 4px;border-bottom:1px solid #d1d5db;font-size:9.5pt;}
  .c{text-align:center;} .r{text-align:right;}

  table.totals{width:100%;border-collapse:collapse;margin-bottom:10mm;}
  .totals td{padding:4px;font-size:9.5pt;}
  .totals .k{text-align:right;color:#111827;width:74%;}
  .totals .v{text-align:right;width:26%;}
  .totals .grand td{border-top:1.5px solid #111827;font-weight:700;padding-top:7px;}

  .pays h4{font-size:8pt;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
  table.pays-table{width:100%;border-collapse:collapse;}
  .pays-table th{font-size:7.5pt;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.8px;text-align:left;padding:5px 4px;border-bottom:1px solid #9ca3af;}
  .pays-table td{padding:5px 4px;border-bottom:1px solid #e5e7eb;font-size:8.5pt;color:#374151;}

  .terms{margin-top:14mm;}
  .terms p{font-size:9.5pt;line-height:1.7;}
  .foot{margin-top:16mm;font-size:7.5pt;color:#9ca3af;text-align:center;border-top:1px solid #e5e7eb;padding-top:8px;}
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
  <td class="doc">INVOICE</td>
</tr></table>

<table class="top"><tr>
  <td style="width:40%;">
    <div class="lbl">Supplier</div>
    <div class="val">
      <b>{{ $invoice->supplier?->name ?? '—' }}</b><br>
      @if($invoice->supplier_invoice_ref)Ref: {{ $invoice->supplier_invoice_ref }}<br>@endif
      @if($invoice->lpo)LPO {{ $invoice->lpo->lpo_number }}<br>@endif
      @if($invoice->project){{ $invoice->project->title }}@endif
    </div>
  </td>
  <td style="width:30%;">
    <div class="lbl">Invoice No.</div>
    <div class="val">{{ $invoice->vendor_invoice_number }}</div>
    <div class="lbl" style="margin-top:6px;">Invoice Date</div>
    <div class="val">{{ $invoice->invoice_date->format('d M Y') }}</div>
  </td>
  <td style="width:30%;">
    <div class="lbl">Due Date</div>
    <div class="val">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</div>
    <div class="lbl" style="margin-top:6px;">Status</div>
    <div class="val" style="text-transform:capitalize;">{{ str_replace('_',' ',$invoice->status) }}</div>
  </td>
</tr></table>

<table class="items">
  <thead>
    <tr>
      <th>Description</th>
      <th class="r" style="width:25%;">Amount</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Vendor invoice {{ $invoice->vendor_invoice_number }}@if($invoice->supplier_invoice_ref) (Ref: {{ $invoice->supplier_invoice_ref }})@endif</td>
      <td class="r">{{ number_format($invoice->subtotal) }}</td>
    </tr>
  </tbody>
</table>

<table class="totals">
  <tr><td class="k">Subtotal</td><td class="v">{{ number_format($invoice->subtotal) }}</td></tr>
  @if($invoice->tax_amount > 0)
  <tr><td class="k">Tax</td><td class="v">{{ number_format($invoice->tax_amount) }}</td></tr>
  @endif
  <tr class="grand"><td class="k">Total (TZS)</td><td class="v">{{ number_format($invoice->total) }}</td></tr>
  <tr><td class="k" style="color:#059669;">Paid</td><td class="v" style="color:#059669;">-{{ number_format($invoice->amount_paid) }}</td></tr>
  <tr><td class="k" style="font-weight:700;">Balance Due</td><td class="v" style="font-weight:700;">{{ number_format($invoice->balance) }}</td></tr>
</table>

@if($invoice->payments->count())
<div class="pays">
  <h4>Payments Received</h4>
  <table class="pays-table">
    <thead><tr><th>Payment No.</th><th>Date</th><th class="r">Amount</th><th>Method</th><th>Reference</th></tr></thead>
    <tbody>
    @foreach($invoice->payments as $p)
    <tr>
      <td>{{ $p->payment_number }}</td>
      <td>{{ $p->payment_date->format('d M Y') }}</td>
      <td class="r">{{ number_format($p->amount) }}</td>
      <td>{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</td>
      <td>{{ $p->reference_number ?? '—' }}</td>
    </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endif

@if($invoice->notes)
<div class="terms">
  <div class="lbl">Notes</div>
  <p style="white-space:pre-line;">{{ $invoice->notes }}</p>
</div>
@endif

<div class="foot">
  {{ $company->legal_name ?? 'ASYX Group Company Limited' }} · {{ $company->phone ?? '+255 755 432 071' }} · {{ $company->email ?? 'info@asyx.co.tz' }}
</div>

</body>
</html>
