<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }} &mdash; {{ config('app.name') }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');

  @page { margin: 6mm; size: A4; }
  *{box-sizing:border-box;}
  body{margin:0;background:#fff;color:#1C2321;font-family:'Inter',sans-serif;font-size:9px;line-height:1.3;}

  .sheet{
    width:198mm;
    min-height:277mm;
    padding:4mm;
    position:relative;
    overflow:hidden;
  }

  .stamp{
    position:absolute;top:14px;right:-44px;
    font-size:10px;font-weight:700;letter-spacing:.12em;
    padding:4px 52px;
    transform:rotate(35deg);
    box-shadow:0 4px 10px rgba(0,0,0,.15);
    z-index:10;
  }
  .stamp-paid{background:#2F7A3D;color:#fff;}
  .stamp-partial{background:#C9A227;color:#23270F;}
  .stamp-posted{background:#0F3D3E;color:#fff;}
  .stamp-draft{background:#6E7570;color:#fff;}
  .stamp-overdue{background:#B23A2E;color:#fff;}

  .head{
    display:flex;justify-content:space-between;align-items:flex-start;
    padding-bottom:6px;
    border-bottom:1px solid #E3DDCB;
    margin-bottom:8px;
  }
  .co-mark{display:flex;align-items:center;gap:8px;}
  .co-icon img{height:30px;max-width:110px;object-fit:contain;}
  .co-name{font-family:'Fraunces',serif;font-weight:700;font-size:13px;color:#0F3D3E;}
  .co-addr{font-size:8px;color:#6E7570;margin-top:2px;line-height:1.4;}
  .co-tin{font-size:8px;color:#6E7570;margin-top:1px;}

  .doc-title{text-align:right;}
  .doc-title h1{font-family:'Fraunces',serif;font-size:15px;margin:0 0 3px;color:#1C2321;}
  .doc-title .meta{font-size:8px;color:#6E7570;line-height:1.4;}
  .doc-title .meta b{color:#1C2321;}

  .bill-row{display:flex;justify-content:space-between;margin-bottom:8px;}
  .bill-to .lbl{font-size:8px;text-transform:uppercase;letter-spacing:.06em;color:#6E7570;margin-bottom:2px;}
  .bill-to b{display:block;font-size:10px;color:#1C2321;}
  .bill-to .addr{font-size:8px;color:#6E7570;line-height:1.4;margin-top:1px;}

  table.lines{width:100%;border-collapse:collapse;font-size:9px;}
  table.lines th{
    text-align:left;font-size:8px;text-transform:uppercase;letter-spacing:.05em;
    color:#6E7570;border-bottom:1px solid #1C2321;padding:3px 2px;
  }
  table.lines th.r, table.lines td.r{text-align:right;}
  table.lines th.c, table.lines td.c{text-align:center;}
  table.lines td{padding:4px 2px;border-bottom:1px solid #E3DDCB;color:#1C2321;}
  table.lines tr{page-break-inside:avoid;}

  .totals{margin-left:auto;width:170px;margin-top:5px;font-size:9px;}
  .totals div{display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid #E3DDCB;color:#6E7570;}
  .totals div b{color:#1C2321;font-weight:600;}
  .totals .grand{font-weight:700;font-size:11px;border-bottom:none;color:#0F3D3E;padding-top:4px;}
  .totals .grand b{color:#0F3D3E;}

  .balance-bar{
    margin-top:8px;padding:6px 10px;border-radius:5px;
    display:flex;justify-content:space-between;align-items:center;
  }
  .balance-bar.due{background:#FBE7E2;}
  .balance-bar.paid{background:#E2F0E5;}
  .balance-bar span{font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
  .balance-bar.due span{color:#B23A2E;}
  .balance-bar.paid span{color:#2F7A3D;}
  .balance-bar b{font-family:'JetBrains Mono',monospace;font-size:11px;}
  .balance-bar.due b{color:#B23A2E;}
  .balance-bar.paid b{color:#2F7A3D;}

  .notes-box{
    margin-top:6px;padding:5px 7px;background:#FBF9F2;border-radius:4px;border:1px solid #E3DDCB;
  }
  .notes-box .lbl{font-size:8px;text-transform:uppercase;letter-spacing:.06em;color:#6E7570;margin-bottom:2px;}
  .notes-box p{font-size:8px;color:#1C2321;margin:0;line-height:1.3;}

  .terms-box{
    margin-top:8px;padding:6px 8px;background:#F5F7F6;border-radius:5px;border:1px solid #D8E3DE;
  }
  .terms-box .lbl{font-size:8px;text-transform:uppercase;letter-spacing:.06em;color:#0F3D3E;font-weight:700;margin-bottom:3px;}
  .terms-box .body{font-size:8px;color:#1C2321;line-height:1.4;white-space:pre-line;}

  .thank-you{
    margin-top:8px;padding:7px 10px;border-radius:5px;
    background:linear-gradient(135deg,#0F3D3E 0%,#1A5A5B 100%);
    color:#fff;text-align:center;
  }
  .thank-you .title{font-family:'Fraunces',serif;font-size:10px;font-weight:700;letter-spacing:.02em;}
  .thank-you .contact{font-size:8px;opacity:.85;margin-top:1px;}

  .watermark{
    position:absolute;
    bottom:70px;left:50%;
    transform:translateX(-50%) rotate(-30deg);
    opacity:.04;pointer-events:none;z-index:0;
  }
  .watermark img{height:100px;object-fit:contain;filter:grayscale(100%);}

  .foot{
    margin-top:8px;padding-top:5px;border-top:1px solid #E3DDCB;
    font-size:8px;color:#6E7570;text-align:center;
  }
</style>
</head>
<body>
  <div class="sheet">
    @php $s = $invoice->status @endphp
    <div class="stamp stamp-{{ $s }}">{{ strtoupper($s) }}</div>

    <div class="watermark">
      <img src="{{ public_path('asyxgrouplogo.png') }}" alt="">
    </div>

    <div class="head">
      <div class="co-mark">
        <div class="co-icon">
          <img src="{{ public_path('asyxgrouplogo.png') }}" alt="{{ config('app.name') }}">
        </div>
        <div>
          <div class="co-name">{{ $company?->name ?? config('app.name') }}</div>
          <div class="co-addr">{{ $company?->address ?? 'Dar es Salaam, Tanzania' }}</div>
          <div class="co-tin">
            @if($company?->tax_id)TIN: {{ $company->tax_id }}@endif
            @if($company?->tax_id && $company?->vrn) &middot; @endif
            @if($company?->vrn)VRN: {{ $company->vrn }}@endif
          </div>
        </div>
      </div>
      <div class="doc-title">
        <h1>Invoice {{ $invoice->invoice_number }}</h1>
        <div class="meta">
          Invoice Date: <b>{{ $invoice->invoice_date->format('M d, Y') }}</b><br>
          Due Date: <b>{{ $invoice->due_date->format('M d, Y') }}</b>
        </div>
      </div>
    </div>

    <div class="bill-row">
      <div class="bill-to">
        <div class="lbl">Invoiced To</div>
        <b>{{ $invoice->customer?->name ?? 'N/A' }}</b>
        <div class="addr">
          {{ $invoice->customer?->email ?? '' }}<br>
          {{ $invoice->customer?->phone ?? '' }}
        </div>
      </div>
    </div>

    <table class="lines">
      <tr><th>Description</th><th class="c">Qty</th><th class="r">Unit Price</th><th class="r">Total</th></tr>
      @forelse($invoice->items as $item)
      <tr>
        <td>{{ $item->product_name }}</td>
        <td class="c">{{ $item->quantity }}</td>
        <td class="r">{{ number_format($item->unit_price, 2) }} Tsh</td>
        <td class="r">{{ number_format($item->total_amount, 2) }} Tsh</td>
      </tr>
      @empty
      <tr><td colspan="4" style="padding:12px;text-align:center;color:#6E7570;">No items</td></tr>
      @endforelse
    </table>

    <div class="totals">
      <div><span>Sub Total</span><b>{{ number_format($invoice->subtotal, 2) }} Tsh</b></div>
      @if($invoice->tax_amount > 0)
      <div><span>18.00% VAT</span><b>{{ number_format($invoice->tax_amount, 2) }} Tsh</b></div>
      @endif
      @if($invoice->discount_amount > 0)
      <div><span>Discount</span><b style="color:#B23A2E;">&minus;{{ number_format($invoice->discount_amount, 2) }} Tsh</b></div>
      @endif
      <div class="grand"><span>Total</span><b>{{ number_format($invoice->total_amount, 2) }} Tsh</b></div>
    </div>

    @if($invoice->status == 'paid')
    <div class="balance-bar paid">
      <span>Paid in Full</span>
      <b>{{ number_format($invoice->paid_amount, 2) }} Tsh</b>
    </div>
    @else
    <div class="balance-bar due">
      <span>Balance Due</span>
      <b>{{ number_format($invoice->balance_amount, 2) }} Tsh</b>
    </div>
    @endif

    @if($invoice->notes)
    <div class="notes-box">
      <div class="lbl">Notes</div>
      <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="terms-box">
      <div class="lbl">Terms & Conditions</div>
      <div class="body">{{ $invoice->terms_and_conditions ?? "1. Prices Are Quoted in TZS\n2. Prices are subject to change without prior notice\n3. Payment terms must be strictly observed\n4. Goods remain property of " . config('app.name') . " until fully paid\n\nThank You For Your Business." }}</div>
    </div>

    <div class="thank-you">
      <div class="title">Thank You For Your Business</div>
      <div class="contact">For inquiries contact: {{ $company?->email ?? 'billing@asyxgroup.tz' }}</div>
    </div>

    <div class="foot">
      PDF Generated on {{ now()->format('l, F jS, Y') }} &middot; {{ config('app.name') }}
    </div>
  </div>
</body>
</html>
