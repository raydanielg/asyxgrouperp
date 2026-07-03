<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $receipt['receipt_number'] }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  @page { margin: 0; padding: 0; size: A4; }
  *{box-sizing:border-box;}
  body{margin:0;background:#fff;color:#1C2321;font-family:'Inter',sans-serif;font-size:10px;line-height:1.4;}
  .sheet{width:210mm;min-height:297mm;padding:12mm 14mm 16mm;position:relative;overflow:hidden;}

  .page-header{position:fixed;top:0;left:0;right:0;height:14mm;padding:4mm 14mm;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #E5E7EA;background:#fff;z-index:100;}
  .page-header .h-logo{height:10mm;max-width:35mm;object-fit:contain;}
  .page-header .h-text{font-size:9px;color:#6B7177;}
  .page-footer{position:fixed;bottom:0;left:0;right:0;height:10mm;padding:3mm 14mm;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #E5E7EA;background:#FBF9F2;font-size:8px;color:#6B7177;z-index:100;}

  .watermark{position:fixed;bottom:40%;left:50%;transform:translateX(-50%) rotate(-30deg);opacity:.05;pointer-events:none;z-index:0;}
  .watermark img{height:150mm;object-fit:contain;filter:grayscale(100%);}

  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;margin-top:4mm;padding-bottom:12px;border-bottom:1px solid #E5E7EA;}
  .top .co-mark{display:flex;align-items:center;gap:10px;}
  .top .co-icon{width:40px;height:40px;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:transparent;}
  .top .co-icon img{width:100%;height:100%;object-fit:contain;}
  .top .co-name{font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:#0F3D3E;}
  .top .co-meta{font-size:9px;color:#6B7177;line-height:1.5;margin-top:2px;}
  .top h1{font-family:'Fraunces',serif;font-size:22px;font-weight:700;margin:0;color:#17181A;}

  .head-row{display:flex;justify-content:space-between;gap:40px;margin-bottom:14px;}
  .head-row .box b{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:#6B7177;margin-bottom:3px;}
  .head-row .box .lines{font-size:11px;color:#17181A;line-height:1.5;}

  .paid-line{font-size:15px;font-weight:700;margin-bottom:12px;color:#17181A;}

  table.items{width:100%;border-collapse:collapse;font-size:10px;margin-bottom:4px;}
  table.items thead th{text-align:left;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:3px 3px;}
  table.items thead th.r{text-align:right;}
  table.items tbody td{padding:7px 3px;border-bottom:1px solid #E5E7EA;vertical-align:top;color:#17181A;}
  table.items tbody td.r{text-align:right;}

  .summary{margin-left:auto;width:220px;margin-top:6px;}
  .summary-row{display:flex;justify-content:space-between;font-size:10px;padding:5px 3px;border-bottom:1px solid #E5E7EA;}
  .summary-row.final{font-weight:700;border-bottom:2px solid #0F3D3E;color:#0F3D3E;}

  .section-title{font-size:12px;font-weight:700;margin:18px 0 8px;color:#17181A;}

  table.history{width:100%;border-collapse:collapse;font-size:10px;}
  table.history thead th{text-align:left;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:3px 3px;}
  table.history thead th.r{text-align:right;}
  table.history tbody td{padding:7px 3px;color:#17181A;}
  table.history tbody td.r{text-align:right;}

  .thank-you{margin-top:14px;padding:10px 14px;border-radius:8px;background:linear-gradient(135deg,#0F3D3E 0%,#1A5A5B 100%);color:#fff;text-align:center;}
  .thank-you .title{font-family:'Fraunces',serif;font-size:13px;font-weight:700;}
  .thank-you .contact{font-size:9px;opacity:.85;margin-top:2px;}

  .foot{
    margin-top:20px;padding-top:10px;border-top:1px solid #E5E7EA;
    font-size:9px;color:#6B7177;text-align:center;
  }
</style>
</head>
<body>
  <div class="page-header">
    <img src="{{ public_path('asyxgrouplogo.png') }}" class="h-logo" alt="{{ config('app.name') }}">
    <div class="h-text">{{ config('app.name') }} &middot; {{ $company?->address ?? 'Dar es Salaam, Tanzania' }}</div>
  </div>
  <div class="page-footer">
    <span>{{ config('app.name') }}</span>
    <span>Receipt {{ $receipt['receipt_number'] }}</span>
  </div>
  <div class="watermark">
    <img src="{{ public_path('asyxgrouplogo.png') }}" alt="">
  </div>

  <div class="sheet">
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
