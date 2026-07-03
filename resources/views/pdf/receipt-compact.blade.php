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

  .stamp{
    position:absolute;top:22px;right:-44px;
    background:#2F7A3D;color:#fff;
    font-size:14px;font-weight:700;letter-spacing:.12em;
    padding:6px 58px;transform:rotate(35deg);
    box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;
  }
  .head{text-align:center;padding:30px 36px 20px;border-bottom:1px dashed #E3DDCB;}
  .co-icon{
    width:70px;height:70px;border-radius:12px;margin:0 auto 14px;
    overflow:hidden;display:flex;align-items:center;justify-content:center;
    background:transparent;
  }
  .co-icon img{width:100%;height:100%;object-fit:contain;}
  .co-name{font-family:'Fraunces',serif;font-weight:700;font-size:22px;color:#0F3D3E;}
  .co-addr{font-size:14px;color:#6E7570;margin-top:5px;line-height:1.5;}

  .receipt-title{text-align:center;padding:24px 36px 8px;}
  .receipt-title .lbl{font-size:14px;text-transform:uppercase;letter-spacing:.14em;color:#6E7570;}
  .receipt-title .amt{font-family:'Fraunces',serif;font-size:44px;color:#2F7A3D;margin:8px 0 4px;}
  .receipt-title .sub{font-size:16px;color:#6E7570;}

  .body{padding:24px 36px 6px;}
  .kv{display:flex;justify-content:space-between;font-size:16px;padding:10px 0;border-bottom:1px dashed #E3DDCB;}
  .kv span{color:#6E7570;}
  .kv b{color:#1C2321;font-weight:600;text-align:right;}
  .kv b.mono{font-family:'JetBrains Mono',monospace;font-size:15px;}

  .divider{
    height:18px;width:100%;
    background:linear-gradient(135deg,#EDE9DD 25%,transparent 25%) 0 0/10px 10px,linear-gradient(225deg,#EDE9DD 25%,transparent 25%) 0 0/10px 10px,#fff;
    margin-top:8px;
  }

  .foot{text-align:center;background:#FBF9F2;padding:22px 36px 34px;}
  .foot .thanks{font-family:'Fraunces',serif;font-size:18px;color:#0F3D3E;margin-bottom:8px;}
  .foot .note{font-size:15px;color:#6E7570;line-height:1.6;}
</style>
</head>
<body>
  <div class="sheet">
    <div class="watermark">
      <img src="{{ public_path('asyxgrouplogo.png') }}" alt="">
    </div>

    <div class="stamp">PAID</div>

    <div class="head">
      <div class="co-icon">
        <img src="{{ public_path('asyxgrouplogo.png') }}" alt="{{ config('app.name') }}">
      </div>
      <div class="co-name">{{ $company?->name ?? config('app.name') }}</div>
      <div class="co-addr">
        {{ $company?->address ?? 'Dar es Salaam, Tanzania' }}<br>
        @if($company?->tax_id)TIN: {{ $company->tax_id }}@endif
        @if($company?->tax_id && $company?->vrn) &middot; @endif
        @if($company?->vrn)VRN: {{ $company->vrn }}@endif
      </div>
    </div>

    <div class="receipt-title">
      <div class="lbl">Amount Received</div>
      <div class="amt">{{ number_format($receipt['paid_amount'], 0) }} Tsh</div>
      <div class="sub">Paid in full &mdash; {{ $receipt['payment_date'] }}</div>
    </div>

    <div class="body">
      <div class="kv"><span>Receipt No.</span><b class="mono">{{ $receipt['receipt_number'] }}</b></div>
      <div class="kv"><span>Invoice Ref.</span><b class="mono">{{ $salesInvoice->invoice_number }}</b></div>
      <div class="kv"><span>Received From</span><b>{{ $salesInvoice->customer?->name ?? 'N/A' }}</b></div>
      <div class="kv"><span>Payment Method</span><b>{{ $receipt['payments'][0]['method'] ?? 'Bank Transfer' }}</b></div>
      <div class="kv"><span>Transaction ID</span><b class="mono">{{ $receipt['payments'][0]['transaction_id'] ?? 'N/A' }}</b></div>
      <div class="kv"><span>Payment Date</span><b>{{ $receipt['payment_date'] }}, {{ $receipt['payment_time'] }}</b></div>
      <div class="kv"><span>Description</span><b style="text-align:right;">{{ $salesInvoice->items->first()?->product_name ?? 'Invoice Payment' }}</b></div>
    </div>

    <div class="divider"></div>

    <div class="foot">
      <div class="thanks">Thank You For Your Payment</div>
      <div class="note">This receipt is an official proof of payment.<br>If you have any questions, contact us: {{ $company?->email ?? 'billing@asyxgroup.tz' }}</div>
    </div>
  </div>
</body>
</html>
