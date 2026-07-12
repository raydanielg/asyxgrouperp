<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASYX Group — Proforma Invoice {{ $quotation->quotation_number }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root{
    --plum:#5B2170;
    --plum-dark:#471957;
    --gold:#B06F2C;
    --gold-dark:#8F5721;
    --cream:#F3EEDC;
    --navy:#14235A;
    --red:#D91F26;
    --ink:#1A1D26;
    --line:#2B2E38;
    --stamp:#1B3FB4;
    --paper:#FFFFFF;
  }
  *{margin:0;padding:0;box-sizing:border-box;}
  html{background:#E9EAEE;}
  body{
    font-family:'Inter',sans-serif;color:var(--ink);
    font-size:10pt;line-height:1.4;
    -webkit-print-color-adjust:exact;print-color-adjust:exact;
  }
  .sheet{
    width:210mm;min-height:297mm;margin:10mm auto;
    background:var(--paper);
    box-shadow:0 4px 30px rgba(70,25,90,.20);
    display:flex;flex-direction:column;overflow:hidden;
    padding:10mm 12mm 0;
  }

  .masthead{position:relative;height:32mm;margin-bottom:12mm;}
  .head-plum{position:absolute;inset:0;background:var(--plum);clip-path:polygon(0 0,100% 0,100% 82%,26% 82%,16% 100%,0 100%);}
  .head-cream{position:absolute;top:0;left:0;width:58%;height:9mm;background:var(--cream);clip-path:polygon(0 0,100% 0,92% 100%,7% 100%,0 55%);}
  .co-name{position:absolute;top:6.2mm;left:30%;background:var(--cream);color:var(--plum);font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:11pt;letter-spacing:1.6px;padding:1.4mm 5mm;}
  .proforma{position:absolute;top:3.5mm;right:5mm;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:26pt;letter-spacing:3px;line-height:1;}
  .head-contact{position:absolute;top:14.5mm;left:30%;right:5mm;color:#EFE3F7;font-size:6.8pt;display:flex;flex-wrap:wrap;gap:1mm 4mm;align-items:center;}
  .head-contact b{color:#F7C98B;}
  .head-addr{position:absolute;top:14.5mm;right:5mm;text-align:right;color:#D9C6E6;font-size:5.6pt;line-height:1.4;max-width:40mm;}
  .head-addr b{color:#fff;font-size:6pt;display:block;}
  .tin-bar{position:absolute;left:26%;right:0;bottom:1.5mm;height:6mm;background:var(--gold);clip-path:polygon(3.5% 0,100% 0,100% 100%,0 100%);display:flex;align-items:center;justify-content:center;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10.5pt;letter-spacing:.6px;}
  .logo{position:absolute;top:1.5mm;left:6mm;z-index:2;display:flex;flex-direction:column;align-items:center;background:transparent;}
  .logo svg{width:16mm;height:16mm;}
  .logo .name{font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:13pt;letter-spacing:4px;color:var(--gold);line-height:1.05;text-shadow:0 0 2px rgba(0,0,0,.25);}
  .logo .grp{font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:6pt;letter-spacing:5px;color:#fff;}
  .rule{height:1.1mm;background:var(--gold);margin:0 0 0;}

  .info{display:grid;grid-template-columns:1.15fr .85fr;gap:14mm;margin-bottom:9mm;}
  .bill h3, .meta .k{font-weight:800;font-size:10.5pt;letter-spacing:.2px;}
  .bill h3{margin-bottom:1.5mm;}
  .bill .line{border-bottom:1px solid var(--ink);padding:1mm 2mm;font-weight:500;min-height:6.5mm;}
  .meta{display:flex;flex-direction:column;gap:1.6mm;}
  .meta .row{display:grid;grid-template-columns:auto 1fr;gap:3mm;align-items:end;}
  .meta .v{border-bottom:1px solid var(--ink);text-align:center;padding:0 2mm .6mm;font-weight:600;min-height:5.5mm;}

  table{width:100%;border-collapse:collapse;}
  .items th{background:var(--gold);color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10pt;letter-spacing:.8px;padding:2mm;border:1.3px solid var(--line);text-transform:uppercase;}
  .items th:first-child{background:var(--plum);}
  .items td{border:1.3px solid var(--line);padding:2.5mm 2mm;vertical-align:middle;font-weight:500;}
  .c{text-align:center;} .r{text-align:right;padding-right:3mm !important;}
  .w-sn{width:10mm;} .w-qty{width:12mm;} .w-price{width:32mm;} .w-amt{width:34mm;}

  .totals td{border:1.3px solid var(--line);padding:1.8mm 3mm;}
  .totals .k{font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;color:var(--navy);font-size:12pt;}
  .totals .v{font-weight:700;text-align:right;font-size:11pt;}
  .totals .grand .k{background:var(--navy);color:#fff;font-size:14pt;letter-spacing:.5px;}
  .totals .grand .v{background:var(--navy);color:#fff;font-size:12pt;}
  .no-border{border:none !important;}

  .lower{display:grid;grid-template-columns:1fr 58mm;gap:8mm;margin-top:12mm;align-items:start;}
  .terms h3{color:var(--red);font-weight:800;font-size:13pt;text-decoration:underline;text-underline-offset:2.5px;margin-bottom:2.5mm;letter-spacing:.3px;}
  .terms ol{padding-left:5.5mm;font-weight:500;}
  .terms li{margin-bottom:1.2mm;}
  .thanks{font-weight:800;margin-top:14mm;font-size:10.5pt;}
  .stamp{width:50mm;height:50mm;justify-self:center;transform:rotate(-6deg);opacity:.92;}

  .foot-wrap{margin-top:auto;padding-bottom:10mm;}
  .foot{background:var(--plum);color:#F0E6F6;padding:3mm 8mm;font-size:6.9pt;line-height:1.6;text-align:center;font-weight:600;}
  .foot-bars{display:flex;height:2mm;margin-top:1.2mm;}
  .foot-bars .b1{flex:1.2;background:var(--red);}
  .foot-bars .b2{flex:1;background:var(--navy);}

  .stamp-badge{position:absolute;top:18mm;right:6mm;background:#0F3D3E;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-size:11pt;font-weight:700;letter-spacing:.08em;padding:2mm 8mm;transform:rotate(12deg);box-shadow:0 2px 6px rgba(0,0,0,.2);z-index:10;opacity:.9;border:2px solid #fff;}

  @page{size:A4;margin:0;}
  @media print{
    html{background:none;}
    .sheet{margin:0;box-shadow:none;width:210mm;height:297mm;}
  }
</style>
</head>
<body>
<div class="sheet" style="position:relative;">
  @if($quotation->status === 'accepted')
  <div class="stamp-badge">ACCEPTED</div>
  @endif

  <header class="masthead">
    <div class="head-plum"></div>
    <div class="head-cream"></div>
    <div class="co-name">ASYX GROUP COMPANY LIMITED</div>
    <div class="proforma">PROFORMA</div>
    <div class="head-contact">
      <span><b>☎</b> +255 755 432 071</span>
      <span><b>✆</b> +255 625 001 100</span>
      <span><b>✉</b> info@asyx.co.tz</span>
      <span><b>◎</b> asyxgroupcompany</span>
      <span><b>🌐</b> www.asyx.co.tz</span>
    </div>
    <div class="head-addr">
      <b>TROPICAL CENTER, 3RD FLOOR</b>
      New Bagamoyo Road · Plot No. 30/00, House No. 301 · P.O. Box 31587 · Dar es Salaam
    </div>
    <div class="tin-bar">TIN: 108-800-186&nbsp; | &nbsp;VRN: 40-009570-M</div>
    <div class="logo">
      <svg viewBox="0 0 100 100" aria-hidden="true">
        <path d="M50 8 A42 42 0 1 0 92 50" fill="none" stroke="#D91F26" stroke-width="12" stroke-linecap="round"/>
        <path d="M50 25 A25 25 0 1 1 25 50" fill="none" stroke="#14235A" stroke-width="12" stroke-linecap="round"/>
        <circle cx="50" cy="50" r="6.5" fill="#B06F2C"/>
      </svg>
      <div class="name">ASYX</div>
      <div class="grp">GROUP</div>
    </div>
  </header>
  <div class="rule"></div>

  <section class="info" style="margin-top:10mm;">
    <div class="bill">
      <h3>PROFORMA INVOICE TO:</h3>
      <div class="line">{{ $quotation->client_name ?? '—' }}</div>
      <div class="line">{{ $quotation->client_email ?? '' }}</div>
      <div class="line">{{ $quotation->lead?->full_name ?? '' }}</div>
      <div class="line">{{ $quotation->lead?->company ?? '' }}</div>
    </div>
    <div class="meta">
      <div class="row"><span class="k">INVOICE NO:</span><span class="v">{{ $quotation->quotation_number }}</span></div>
      <div class="row"><span class="k">INVOICE DATE:</span><span class="v">{{ $quotation->quotation_date->format('d/m/Y') }}</span></div>
      <div class="row"><span class="k">VALID UNTIL:</span><span class="v">{{ $quotation->valid_until?->format('d/m/Y') ?? 'N/A' }}</span></div>
      <div class="row"><span class="k">STATUS:</span><span class="v">{{ strtoupper($quotation->status) }}</span></div>
    </div>
  </section>

  <table class="items">
    <thead>
      <tr>
        <th class="w-sn">S/N</th>
        <th>Item</th>
        <th>Spare Part</th>
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
        <td>{{ $item->unit ?? '' }}</td>
        <td class="c">{{ $item->quantity }}</td>
        <td class="r">{{ number_format($item->unit_price, 2) }}</td>
        <td class="r">{{ number_format($item->line_total, 2) }}</td>
      </tr>
      @empty
      <tr><td class="c">1</td><td>—</td><td>—</td><td class="c">—</td><td class="r">—</td><td class="r">—</td></tr>
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
      <td class="v">{{ number_format($quotation->discount_amount, 2) }}</td>
    </tr>
    @endif
    @if($quotation->tax_amount > 0)
    <tr>
      <td class="no-border"></td>
      <td class="k">Tax:</td>
      <td class="v">{{ number_format($quotation->tax_amount, 2) }}</td>
    </tr>
    @endif
    <tr class="grand">
      <td class="no-border"></td>
      <td class="k">GRAND TOTAL</td>
      <td class="v">{{ number_format($quotation->total, 2) }}</td>
    </tr>
  </table>

  <section class="lower">
    <div class="terms">
      <h3>TERMS &amp; CONDITIONS:</h3>
      <ol>
        <li>Prices Are Quoted TSH</li>
        <li>Prices are subject to change without prior notice</li>
        <li>Payment terms must be strictly observed</li>
        <li>Goods remain property of ASYX Group Company Limited until fully paid</li>
      </ol>
      @if($quotation->notes)
      <div style="margin-top:4mm;font-weight:500;"><b>Notes:</b> {{ $quotation->notes }}</div>
      @endif
      <div class="thanks">Thank You For Your Business</div>
    </div>

    <svg class="stamp" viewBox="0 0 200 200" aria-label="Company stamp">
      <defs>
        <path id="arcTop" d="M 100,100 m -72,0 a 72,72 0 1,1 144,0"/>
        <path id="arcBot" d="M 100,100 m -72,0 a 72,72 0 1,0 144,0"/>
      </defs>
      <circle cx="100" cy="100" r="94" fill="none" stroke="#1B3FB4" stroke-width="4"/>
      <circle cx="100" cy="100" r="86" fill="none" stroke="#1B3FB4" stroke-width="2"/>
      <circle cx="100" cy="100" r="52" fill="none" stroke="#1B3FB4" stroke-width="2.5"/>
      <text fill="#1B3FB4" font-family="Barlow Semi Condensed, sans-serif" font-weight="700" font-size="17" letter-spacing="2.5">
        <textPath href="#arcTop" startOffset="50%" text-anchor="middle">ASYX GROUP COMPANY LIMITED</textPath>
      </text>
      <text fill="#1B3FB4" font-family="Barlow Semi Condensed, sans-serif" font-weight="700" font-size="15" letter-spacing="2">
        <textPath href="#arcBot" startOffset="50%" text-anchor="middle">P. O. Box 4816, DAR ES SALAAM</textPath>
      </text>
      <text x="35" y="106" fill="#1B3FB4" font-size="16">★</text>
      <text x="151" y="106" fill="#1B3FB4" font-size="16">★</text>
      <g stroke="#1B3FB4" fill="#1B3FB4">
        <path d="M100 62 A38 38 0 0 1 138 100 L118 100 A18 18 0 0 0 100 82 Z" opacity=".95"/>
        <path d="M138 100 A38 38 0 0 1 100 138 L100 118 A18 18 0 0 0 118 100 Z" opacity=".8"/>
        <path d="M100 138 A38 38 0 0 1 62 100 L82 100 A18 18 0 0 0 100 118 Z" opacity=".95"/>
        <path d="M62 100 A38 38 0 0 1 100 62 L100 82 A18 18 0 0 0 82 100 Z" opacity=".8"/>
      </g>
    </svg>
  </section>

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
