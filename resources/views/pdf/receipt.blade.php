<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $receipt['receipt_number'] }}</title>
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
    border-radius:4px;
    box-shadow:0 18px 40px -10px rgba(15,61,62,.18),0 0 0 1px #E5E7EA;
    padding:48px 52px 40px;
  }
  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:34px;}
  .top h1{font-size:26px;font-weight:800;margin:0;letter-spacing:-.01em;color:#17181A;}
  .top .co-mark{display:flex;align-items:center;gap:9px;}
  .top .co-icon{
    width:26px;height:26px;border-radius:7px;
    background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);
    overflow:hidden;display:flex;align-items:center;justify-content:center;
  }
  .top .co-icon img{width:20px;height:20px;object-fit:contain;border-radius:4px;}
  .top .co-word{font-size:18px;font-weight:700;letter-spacing:-.01em;color:#0F3D3E;}

  .meta{font-size:13px;margin-bottom:30px;}
  .meta-row{display:flex;gap:8px;padding:2px 0;}
  .meta-row .k{width:140px;color:#17181A;}
  .meta-row .v{color:#17181A;}

  .parties{display:flex;gap:60px;margin-bottom:34px;}
  .party b{display:block;font-size:13px;margin-bottom:6px;color:#17181A;}
  .party .lines{font-size:13px;color:#17181A;line-height:1.6;}

  .paid-line{font-size:17px;font-weight:700;margin-bottom:18px;color:#17181A;}

  table.items{width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px;}
  table.items thead th{
    text-align:left;font-weight:600;color:#17181A;
    border-bottom:1.5px solid #17181A;
    padding:0 4px 10px;
  }
  table.items thead th.r{text-align:right;}
  table.items tbody td{
    padding:14px 4px;border-bottom:1px solid #E5E7EA;
    vertical-align:top;color:#17181A;
  }
  table.items tbody td.r{text-align:right;}

  .summary{margin-left:auto;width:260px;margin-top:8px;}
  .summary-row{display:flex;justify-content:space-between;font-size:13px;padding:8px 4px;border-bottom:1px solid #E5E7EA;}
  .summary-row.final{font-weight:700;border-bottom:none;}

  .section-title{font-size:15px;font-weight:700;margin:36px 0 14px;color:#17181A;}

  table.history{width:100%;border-collapse:collapse;font-size:13px;}
  table.history thead th{
    text-align:left;font-weight:600;color:#17181A;
    border-bottom:1.5px solid #17181A;
    padding:0 4px 10px;
  }
  table.history thead th.r{text-align:right;}
  table.history tbody td{padding:13px 4px;color:#17181A;}
  table.history tbody td.r{text-align:right;}

  .foot{
    margin-top:40px;padding-top:14px;border-top:1px solid #E5E7EA;
    font-size:11.5px;color:#6B7177;text-align:right;
  }
</style>
</head>
<body>
  <div class="sheet">
    <div class="top">
      <h1>Receipt</h1>
      <div class="co-mark">
        <div class="co-icon">
          <img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX">
        </div>
        <div class="co-word">Asyx</div>
      </div>
    </div>

    <div class="meta">
      <div class="meta-row"><div class="k">Invoice number</div><div class="v">{{ $salesInvoice->invoice_number }}</div></div>
      <div class="meta-row"><div class="k">Receipt number</div><div class="v">{{ $receipt['receipt_number'] }}</div></div>
      <div class="meta-row"><div class="k">Date paid</div><div class="v">{{ $receipt['payment_date'] }}</div></div>
    </div>

    <div class="parties">
      <div class="party">
        <b>{{ config('app.name') }}</b>
        <div class="lines">
          {{ $company?->name ?? 'ASYX Group' }}<br>
          Dar es Salaam, Tanzania<br>
          billing@asyxgroup.tz
        </div>
      </div>
      <div class="party">
        <b>Bill to</b>
        <div class="lines">
          {{ $salesInvoice->customer?->name ?? 'N/A' }}<br>
          {{ $salesInvoice->customer?->email ?? '' }}<br>
          {{ $salesInvoice->customer?->phone ?? '' }}
        </div>
      </div>
    </div>

    <div class="paid-line">{{ number_format($receipt['paid_amount'], 2) }} Tsh paid on {{ $receipt['payment_date'] }}</div>

    <table class="items">
      <thead>
        <tr><th>Description</th><th class="r">Qty</th><th class="r">Unit price</th><th class="r">Amount</th></tr>
      </thead>
      <tbody>
        @forelse($salesInvoice->items as $item)
        <tr>
          <td>{{ $item->product_name }}</td>
          <td class="r">{{ $item->quantity }}</td>
          <td class="r">{{ number_format($item->unit_price, 2) }}</td>
          <td class="r">{{ number_format($item->total_amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="padding:24px;text-align:center;color:#6B7177;">No items</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="summary">
      <div class="summary-row"><span>Subtotal</span><span>{{ number_format($salesInvoice->subtotal, 2) }} Tsh</span></div>
      @if($salesInvoice->tax_amount > 0)
      <div class="summary-row"><span>VAT (18%)</span><span>{{ number_format($salesInvoice->tax_amount, 2) }} Tsh</span></div>
      @endif
      <div class="summary-row"><span>Total</span><span>{{ number_format($salesInvoice->total_amount, 2) }} Tsh</span></div>
      <div class="summary-row final"><span>Amount paid</span><span>{{ number_format($receipt['paid_amount'], 2) }} Tsh</span></div>
    </div>

    <div class="section-title">Payment history</div>
    <table class="history">
      <thead>
        <tr><th>Payment method</th><th>Date</th><th class="r">Amount paid</th><th class="r">Receipt number</th></tr>
      </thead>
      <tbody>
        @forelse($receipt['payments'] as $pmt)
        <tr>
          <td>{{ $pmt['method'] }}</td>
          <td>{{ $pmt['date'] }}</td>
          <td class="r">{{ number_format($pmt['amount'], 2) }} Tsh</td>
          <td class="r">{{ $pmt['reference'] }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="padding:24px;text-align:center;color:#6B7177;">No payment records</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="foot">Page 1 of 1</div>
  </div>
</body>
</html>
