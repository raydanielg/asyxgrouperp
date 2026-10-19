<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $company->legal_name ?? 'ASYX Group Company Limited' }} — Quotation {{ $quotation->quotation_number }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --plum:#5B2170;--plum-dark:#471957;--gold:#B06F2C;--gold-dark:#8F5721;
    --cream:#F3EEDC;--navy:#14235A;--red:#D91F26;--ink:#1A1D26;
    --line:#2B2E38;--stamp:#1B3FB4;--paper:#FFFFFF;
    --acc:#2F7A3D;--rej:#B23A2E;--sent:#1B3FB4;--drft:#6B7280;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  html{background:#E9EAEE;}
  body{font-family:'Inter',sans-serif;color:#1A1D26;font-size:10pt;line-height:1.4;-webkit-print-color-adjust:exact;print-color-adjust:exact;}
  .sheet{min-height:297mm;margin:10mm auto;background:#FFFFFF;box-shadow:0 4px 30px rgba(70,25,90,.20);display:flex;flex-direction:column;overflow:hidden;padding:10mm 12mm 0;position:relative;}

  .masthead{position:relative;height:32mm;margin-bottom:9mm;}
  .head-plum{position:absolute;inset:0;background:#5B2170;clip-path:polygon(0 0,100% 0,100% 82%,26% 82%,16% 100%,0 100%);}
  .head-cream{position:absolute;top:0;left:0;width:58%;height:9mm;background:#F3EEDC;clip-path:polygon(0 0,100% 0,92% 100%,7% 100%,0 55%);}
  .co-name{position:absolute;top:6.2mm;left:30%;background:#F3EEDC;color:#5B2170;font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:11pt;letter-spacing:1.6px;padding:1.4mm 5mm;}
  .doc-label{position:absolute;top:3.5mm;right:5mm;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:24pt;letter-spacing:2.5px;line-height:1;}
  .head-contact{position:absolute;top:14.5mm;left:30%;right:5mm;color:#EFE3F7;font-size:6.8pt;display:flex;flex-wrap:wrap;gap:1mm 4mm;align-items:center;}
  .head-contact b{color:#F7C98B;}
  .head-addr{position:absolute;top:14.5mm;right:5mm;text-align:right;color:#D9C6E6;font-size:5.6pt;line-height:1.4;max-width:40mm;}
  .head-addr b{color:#fff;font-size:6pt;display:block;}
  .tin-bar{position:absolute;left:26%;right:0;bottom:1.5mm;height:6mm;background:#B06F2C;clip-path:polygon(3.5% 0,100% 0,100% 100%,0 100%);display:flex;align-items:center;justify-content:center;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10.5pt;letter-spacing:.6px;}
  .logo{position:absolute;top:1.5mm;left:6mm;z-index:2;display:flex;flex-direction:column;align-items:center;background:transparent;}
  .logo svg{width:16mm;height:16mm;}
  .logo .name{font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:13pt;letter-spacing:4px;color:#B06F2C;line-height:1.05;text-shadow:0 0 2px rgba(0,0,0,.25);}
  .logo .grp{font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:6pt;letter-spacing:5px;color:#fff;}
  .rule{height:1.1mm;background:#B06F2C;margin:0 0 0;}

  .info{width:100%;border-collapse:collapse;margin-bottom:6mm;} .info>tbody>tr>td{vertical-align:top;padding:0;} .info td.bill{width:56%;padding-right:12mm;} .info td.meta{width:44%;}
  .bill h3, .meta .k{font-weight:800;font-size:10.5pt;letter-spacing:.2px;}
  .bill h3{margin-bottom:1.5mm;}
  .bill .line{border-bottom:1px solid #1A1D26;padding:1mm 2mm;font-weight:500;min-height:6.5mm;}
  .meta td.pb{padding-bottom:4mm;}
  .meta .v{border-bottom:1px solid #1A1D26;text-align:center;padding:0 2mm .6mm;font-weight:600;min-height:5.5mm;}

  table{width:100%;border-collapse:collapse;}
  .items th{background:#B06F2C;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10pt;letter-spacing:.8px;padding:2mm;border:1.3px solid #2B2E38;text-transform:uppercase;}
  .items th:first-child{background:#5B2170;}
  .items td{border:1.3px solid #2B2E38;padding:2.5mm 2mm;vertical-align:middle;font-weight:500;}
  .items tr:nth-child(even) td{background:#FAF7F2;}
  .c{text-align:center;} .r{text-align:right;padding-right:3mm !important;}
  .w-sn{width:10mm;} .w-qty{width:14mm;} .w-price{width:34mm;} .w-amt{width:36mm;}

  .totals td{border:1.3px solid #2B2E38;padding:1.8mm 3mm;}
  .totals .k{font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;color:#14235A;font-size:12pt;}
  .totals .v{font-weight:700;text-align:right;font-size:11pt;}
  .totals .grand .k{background:#14235A;color:#fff;font-size:14pt;letter-spacing:.5px;}
  .totals .grand .v{background:#14235A;color:#fff;font-size:12pt;}
  .no-border{border:none !important;}

  .lower{width:100%;border-collapse:collapse;margin-top:8mm;} .lower>tbody>tr>td{vertical-align:top;} .lower td.tstamp{width:58mm;padding-left:8mm;text-align:center;}
  .terms h3{color:#D91F26;font-weight:800;font-size:13pt;text-decoration:underline;text-underline-offset:2.5px;margin-bottom:2.5mm;letter-spacing:.3px;}
  .terms ol{padding-left:5.5mm;font-weight:500;}
  .terms li{margin-bottom:1.2mm;}
  .notes{font-weight:500;margin-top:3mm;}
  .thanks{font-weight:800;margin-top:9mm;font-size:10.5pt;}
  .sign{margin-top:14mm;font-size:9.5pt;font-weight:600;}
  .sign .line{display:inline-block;border-top:1px solid #1A1D26;padding:1mm 10mm 0 0;margin-right:12mm;}
  .stamp{width:50mm;height:50mm;justify-self:center;transform:rotate(-6deg);opacity:.92;}

  .foot-wrap{margin-top:auto;padding-bottom:10mm;}
  .foot{background:#5B2170;color:#F0E6F6;padding:3mm 8mm;font-size:6.9pt;line-height:1.6;text-align:center;font-weight:600;}
  .foot-bars{display:flex;height:2mm;margin-top:1.2mm;}
  .foot-bars .b1{flex:1.2;background:#D91F26;}
  .foot-bars .b2{flex:1;background:#14235A;}

  .stamp-badge{position:absolute;top:36mm;right:6mm;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-size:11pt;font-weight:700;letter-spacing:.08em;padding:2mm 8mm;transform:rotate(12deg);box-shadow:0 2px 6px rgba(0,0,0,.2);z-index:10;opacity:.9;border:2px solid #fff;}
  .stamp-badge.draft{background:#6B7280;} .stamp-badge.sent{background:#1B3FB4;}
  .stamp-badge.accepted{background:#2F7A3D;} .stamp-badge.rejected,.stamp-badge.expired{background:#B23A2E;}

  @page{size:A4;margin:0;}
  @media print{html{background:none;}.sheet{margin:0;box-shadow:none;width:210mm;height:297mm;}}
</style>
</head>
<body>
<div class="sheet">
  <div class="stamp-badge {{ $quotation->status }}">{{ strtoupper($quotation->status) }}</div>

  <header class="masthead">
    <div class="head-plum"></div>
    <div class="head-cream"></div>
    <div class="co-name">{{ strtoupper($company->legal_name ?? 'ASYX GROUP COMPANY LIMITED') }}</div>
    <div class="doc-label">PROFORMA</div>
    <div class="head-contact">
      @if($company->phone ?? null)<span><b>☎</b> {{ $company->phone }}</span>@else<span><b>☎</b> +255 755 432 071</span>@endif
      <span><b>✉</b> {{ $company->email ?? 'info@asyx.co.tz' }}</span>
      @if($company->website ?? null)<span><b>🌐</b> {{ $company->website }}</span>@endif
    </div>
    <div class="head-addr">
      <b>{{ $company->name ?? 'HEAD OFFICE' }}</b>
      {{ $company->address ?? 'New Bagamoyo Road' }}@if($company->city ?? null) · {{ $company->city }}@endif
      @if($company->country ?? null)<br>{{ $company->country }}@endif
    </div>
    <div class="tin-bar">TIN: {{ $company->tax_id ?? '108-800-186' }}&nbsp; | &nbsp;VRN: {{ $company->vrn ?? '40-009570-M' }}</div>
    <div class="logo">
      <svg viewBox="0 0 100 100" aria-hidden="true">
        <path d="M50 8 A42 42 0 1 0 92 50" fill="none" stroke="#D91F26" stroke-width="12" stroke-linecap="round"/>
        <path d="M50 25 A25 25 0 1 1 25 50" fill="none" stroke="#14235A" stroke-width="12" stroke-linecap="round"/>
        <circle cx="50" cy="50" r="6.5" fill="#B06F2C"/>
      </svg>
      <div class="name">{{ strtoupper(substr($company->short_code ?? 'ASYX', 0, 8)) }}</div>
      <div class="grp">GROUP</div>
    </div>
  </header>
  <div class="rule"></div>

  <table class="info" style="margin-top:7mm;"><tr>
    <td class="bill">
      <h3>QUOTATION TO:</h3>
      <div class="line"><b>{{ $quotation->client_name }}</b></div>
      <div class="line">{{ $quotation->lead?->company ?? '' }}</div>
      <div class="line">{{ $quotation->client_email ?? '' }}</div>
      @if($quotation->lead?->full_name)
      <div class="line">Attn: {{ $quotation->lead->full_name }}</div>
      @endif
    </td>
    <td class="meta">
      <table style="width:100%;border-collapse:collapse;">
        <tr><td class="k pb">QUOTATION NO:</td><td class="v pb">{{ $quotation->quotation_number }}</td></tr>
        <tr><td class="k pb">DATE:</td><td class="v pb">{{ $quotation->quotation_date->format('d/m/Y') }}</td></tr>
        <tr><td class="k pb">VALID UNTIL:</td><td class="v pb">{{ $quotation->valid_until?->format('d/m/Y') ?? 'N/A' }}</td></tr>
        <tr><td class="k pb">CURRENCY:</td><td class="v pb">TZS</td></tr>
      </table>
    </td>
  </tr></table>

  <table class="items">
    <thead>
      <tr>
        <th class="w-sn">S/N</th>
        <th>Description</th>
        <th>Unit</th>
        <th class="w-qty">Qty</th>
        <th class="w-price">Unit Price</th>
        <th class="w-amt">Amount</th>
      </tr>
    </thead>
    <tbody>
      @forelse($quotation->items as $i => $item)
      <tr>
        <td class="c">{{ $i + 1 }}</td>
        <td>{{ $item->description }}</td>
        <td class="c">{{ $item->unit ?? '—' }}</td>
        <td class="c">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
        <td class="r">{{ number_format($item->unit_price, 2) }}</td>
        <td class="r">{{ number_format($item->line_total, 2) }}</td>
      </tr>
      @empty
      <tr><td colspan="6" class="c" style="padding:6mm;color:#9ca3af;">No items</td></tr>
      @endforelse
    </tbody>
  </table>

  <table class="totals">
    <tr>
      <td class="no-border" style="width:52%;"></td>
      <td class="k" style="width:24%;">Sub-Total:</td>
      <td class="v" style="width:24%;">{{ number_format($quotation->subtotal, 2) }}</td>
    </tr>
    @if($quotation->discount_amount > 0)
    <tr>
      <td class="no-border"></td>
      <td class="k">Discount:</td>
      <td class="v" style="color:#B23A2E;">-{{ number_format($quotation->discount_amount, 2) }}</td>
    </tr>
    @endif
    @if($quotation->tax_amount > 0)
    <tr>
      <td class="no-border"></td>
      <td class="k">VAT / Tax:</td>
      <td class="v">+{{ number_format($quotation->tax_amount, 2) }}</td>
    </tr>
    @endif
    <tr class="grand">
      <td class="no-border"></td>
      <td class="k">GRAND TOTAL</td>
      <td class="v">TZS {{ number_format($quotation->total, 2) }}</td>
    </tr>
  </table>

  <table class="lower"><tr>
    <td class="terms">
      <h3>TERMS &amp; CONDITIONS:</h3>
      @if($quotation->terms)
      <p style="font-weight:500;white-space:pre-line;">{{ $quotation->terms }}</p>
      @else
      <ol>
        <li>Prices Are Quoted TSH</li>
        <li>Prices are subject to change without prior notice</li>
        <li>Payment terms must be strictly observed</li>
        <li>Goods remain property of {{ $company->legal_name ?? 'ASYX Group Company Limited' }} until fully paid</li>
      </ol>
      @endif
      @if($quotation->notes)
      <div class="notes"><b>Notes:</b> {{ $quotation->notes }}</div>
      @endif
      <table class="sign"><tr>
        <td><div class="line">Prepared by</div></td>
        <td><div class="line">Client signature</div></td>
        <td><div class="line">Date</div></td>
      </tr></table>
      <div class="thanks">Thank You For Your Business</div>
    </td>

    <td class="tstamp">
      <div style="width:44mm;height:44mm;border:2.5px solid #1B3FB4;border-radius:50%;margin:0 auto;transform:rotate(-8deg);opacity:.85;text-align:center;">
        <div style="margin-top:9mm;display:inline-block;border-top:1.5px solid #1B3FB4;border-bottom:1.5px solid #1B3FB4;padding:2.5mm 3mm;">
          <div style="color:#1B3FB4;font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:10pt;letter-spacing:1px;line-height:1.25;">{{ strtoupper(substr($company->short_code ?? 'ASYX', 0, 10)) }}<br>GROUP</div>
          <div style="color:#1B3FB4;font-size:6pt;letter-spacing:1px;margin-top:1.5mm;">★ {{ strtoupper($company->city ?? 'DAR ES SALAAM') }} ★</div>
        </div>
      </div>
    </td></tr></table>

  <div class="foot-wrap">
    <div class="foot">
      Software and hardware distribution, Customized software and Mobile apps development and re-engineering,
      enterprise software solutions, IT systems Security and Auditing, Computerized Systems Integration,
      Artificial Intelligence (AI) and Machine Learning, Statistics and Big Data Analytics,
      Customer Specific Annual Maintenance Support Contracts (AMCs) and ICT Consultancy and Training.
    </div>
    <div class="foot-bars"><div class="b1"></div><div class="b2"></div></div>
  </div>
</div>
</body>
</html>
