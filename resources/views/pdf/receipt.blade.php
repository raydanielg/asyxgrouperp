<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $receipt['receipt_number'] }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  @page { margin: 10mm; size: A4; }
  *{box-sizing:border-box;}
  body{margin:0;background:#fff;color:#1C2321;font-family:'Inter',sans-serif;font-size:16px;line-height:1.4;}
  .sheet{width:190mm;min-height:267mm;padding:6mm;position:relative;overflow:hidden;}

  .watermark{position:absolute;bottom:100px;left:50%;transform:translateX(-50%) rotate(-30deg);opacity:.05;pointer-events:none;z-index:0;}
  .watermark img{height:160px;object-fit:contain;filter:grayscale(100%);}

  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:22px;padding-bottom:14px;border-bottom:1px solid #E5E7EA;}
  .top .co-mark{display:flex;align-items:center;gap:12px;}
  .top .co-icon{width:55px;height:55px;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:transparent;}
  .top .co-icon img{width:100%;height:100%;object-fit:contain;}
  .top .co-name{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#0F3D3E;}
  .top .co-meta{font-size:14px;color:#6B7177;line-height:1.5;margin-top:3px;}
  .top h1{font-family:'Fraunces',serif;font-size:26px;font-weight:700;margin:0;color:#17181A;}

  .head-row{display:flex;justify-content:space-between;gap:40px;margin-bottom:20px;}
  .head-row .box b{display:block;font-size:14px;text-transform:uppercase;letter-spacing:.06em;color:#6B7177;margin-bottom:5px;}
  .head-row .box .lines{font-size:16px;color:#17181A;line-height:1.5;}

  .paid-line{font-size:20px;font-weight:700;margin-bottom:16px;color:#17181A;}

  table.items{width:100%;border-collapse:collapse;font-size:16px;margin-bottom:4px;}
  table.items thead th{text-align:left;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:8px 4px;}
  table.items thead th.r{text-align:right;}
  table.items tbody td{padding:10px 4px;border-bottom:1px solid #E5E7EA;vertical-align:top;color:#17181A;}
  table.items tbody td.r{text-align:right;}
  table.items tr{page-break-inside:avoid;}

  .summary{margin-left:auto;width:260px;margin-top:12px;}
  .summary-row{display:flex;justify-content:space-between;font-size:16px;padding:7px 4px;border-bottom:1px solid #E5E7EA;}
  .summary-row.final{font-weight:700;border-bottom:2px solid #0F3D3E;color:#0F3D3E;}

  .section-title{font-size:18px;font-weight:700;margin:22px 0 10px;color:#17181A;}

  table.history{width:100%;border-collapse:collapse;font-size:16px;}
  table.history thead th{text-align:left;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:8px 4px;}
  table.history thead th.r{text-align:right;}
  table.history tbody td{padding:10px 4px;color:#17181A;}
  table.history tbody td.r{text-align:right;}
  table.history tr{page-break-inside:avoid;}

  .thank-you{margin-top:18px;padding:14px 18px;border-radius:8px;background:linear-gradient(135deg,#0F3D3E 0%,#1A5A5B 100%);color:#fff;text-align:center;}
  .thank-you .title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;}
  .thank-you .contact{font-size:13px;opacity:.85;margin-top:3px;}

  .foot{
    margin-top:18px;padding-top:10px;border-top:1px solid #E5E7EA;
    font-size:13px;color:#6B7177;text-align:center;
  }
</style>
</head>
<body>
  <div class="sheet">
    <div class="watermark">
      <img src="{{ public_path('asyxgrouplogo.png') }}" alt="">
    </div>

    <div class="top">
      <div class="co-mark">
        <div class="co-icon">
          <img src="{{ public_path('asyxgrouplogo.png') }}" alt="{{ config('app.name') }}">
        </div>
        <div>
          <div class="co-name">{{ $company?->name ?? config('app.name') }}</div>
          <div class="co-meta">
            {{ $company?->address ?? 'Dar es Salaam, Tanzania' }}<br>
            @if($company?->tax_id)TIN: {{ $company->tax_id }}@endif
            @if($company?->tax_id && $company?->vrn) &middot; @endif
            @if($company?->vrn)VRN: {{ $company->vrn }}@endif
          </div>
        </div>
      </div>
      <div style="text-align:right;">
        <h1>Receipt</h1>
        <div style="font-size:10px;color:#6B7177;">No. <b style="color:#17181A;">{{ $receipt['receipt_number'] }}</b></div>
      </div>
    </div>

    <div class="head-row">
      <div class="box">
        <b>Invoice Details</b>
        <div class="lines">
          Invoice: {{ $salesInvoice->invoice_number }}<br>
          Date Paid: {{ $receipt['payment_date'] }}<br>
          Paid By: {{ $salesInvoice->customer?->name ?? 'N/A' }}
        </div>
      </div>
      <div class="box">
        <b>Customer</b>
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
        <tr><td colspan="4" style="padding:18px;text-align:center;color:#6B7177;">No items</td></tr>
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
        <tr><td colspan="4" style="padding:18px;text-align:center;color:#6B7177;">No payment records</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="thank-you">
      <div class="title">Thank You For Your Payment</div>
      <div class="contact">This receipt is an official proof of payment. Questions? {{ $company?->email ?? 'billing@asyxgroup.tz' }}</div>
    </div>

    <div class="foot">PDF Generated on {{ now()->format('l, F jS, Y') }} &middot; {{ config('app.name') }}</div>
  </div>
</body>
</html>
