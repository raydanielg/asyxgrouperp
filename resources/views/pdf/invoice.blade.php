<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }} &mdash; {{ config('app.name') }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');

  *{box-sizing:border-box;}
  body{
    margin:36px auto;
    background:#EDE9DD;
    color:#1C2321;
    font-family:'Inter',sans-serif;
    max-width:760px;
  }

  .sheet{
    background:#fff;
    border-radius:6px;
    box-shadow:0 18px 40px -10px rgba(15,61,62,.25),0 0 0 1px #E3DDCB;
    position:relative;
    overflow:hidden;
  }
  .stamp{
    position:absolute;top:26px;right:-52px;
    font-size:12px;font-weight:700;letter-spacing:.12em;
    padding:6px 68px;
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
    padding:38px 44px 26px;
    border-bottom:1px solid #E3DDCB;
  }
  .co-mark{display:flex;align-items:center;gap:12px;}
  .co-icon{
    width:38px;height:38px;border-radius:10px;
    background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);
    flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;
  }
  .co-icon img{width:30px;height:30px;object-fit:contain;border-radius:4px;}
  .co-name{font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;}
  .co-addr{font-size:11.5px;color:#6E7570;margin-top:3px;line-height:1.5;}

  .doc-title{text-align:right;}
  .doc-title h1{font-family:'Fraunces',serif;font-size:24px;margin:0 0 8px;color:#1C2321;}
  .doc-title .meta{font-size:12px;color:#6E7570;line-height:1.6;}
  .doc-title .meta b{color:#1C2321;}

  .body{padding:30px 44px;}

  .bill-row{display:flex;justify-content:space-between;margin-bottom:28px;}
  .bill-to .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;}
  .bill-to b{display:block;font-size:14.5px;color:#1C2321;}
  .bill-to .addr{font-size:12.5px;color:#6E7570;line-height:1.6;margin-top:2px;}

  table.lines{width:100%;border-collapse:collapse;font-size:13px;}
  table.lines th{
    text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;
    color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;
  }
  table.lines th.r, table.lines td.r{text-align:right;}
  table.lines th.c, table.lines td.c{text-align:center;}
  table.lines td{padding:14px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;}

  .totals{margin-left:auto;width:280px;margin-top:14px;font-size:13.5px;}
  .totals div{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #E3DDCB;color:#6E7570;}
  .totals div b{color:#1C2321;font-weight:600;}
  .totals .grand{font-weight:700;font-size:17px;border-bottom:none;color:#0F3D3E;padding-top:14px;}
  .totals .grand b{color:#0F3D3E;}

  .balance-bar{
    margin-top:24px;padding:16px 20px;border-radius:10px;
    display:flex;justify-content:space-between;align-items:center;
  }
  .balance-bar.due{background:#FBE7E2;}
  .balance-bar.paid{background:#E2F0E5;}
  .balance-bar span{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
  .balance-bar.due span{color:#B23A2E;}
  .balance-bar.paid span{color:#2F7A3D;}
  .balance-bar b{font-family:'JetBrains Mono',monospace;font-size:17px;}
  .balance-bar.due b{color:#B23A2E;}
  .balance-bar.paid b{color:#2F7A3D;}

  .notes-box{
    margin-top:20px;padding:14px 16px;background:#FBF9F2;border-radius:8px;border:1px solid #E3DDCB;
  }
  .notes-box .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:4px;}
  .notes-box p{font-size:12.5px;color:#1C2321;margin:0;line-height:1.5;}

  .foot{
    padding:18px 44px;border-top:1px solid #E3DDCB;
    background:#FBF9F2;
    font-size:11px;color:#6E7570;text-align:center;
  }
</style>
</head>
<body>
  <div class="sheet">
    @php $s = $invoice->status @endphp
    <div class="stamp stamp-{{ $s }}">{{ strtoupper($s) }}</div>

    <div class="head">
      <div class="co-mark">
        <div class="co-icon">
          <img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX">
        </div>
        <div>
          <div class="co-name">{{ config('app.name') }}</div>
          <div class="co-addr">{{ $company?->name ?? 'ASYX Group' }}<br>Dar es Salaam, Tanzania</div>
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

    <div class="body">
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
        <tr><td colspan="4" style="padding:24px;text-align:center;color:#6E7570;">No items</td></tr>
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
    </div>

    <div class="foot">
      PDF Generated on {{ now()->format('l, F jS, Y') }} &middot; {{ config('app.name') }}
    </div>
  </div>
</body>
</html>
