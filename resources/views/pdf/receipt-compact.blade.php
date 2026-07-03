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

  .page-header{position:fixed;top:0;left:0;right:0;height:14mm;padding:4mm 14mm;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #E3DDCB;background:#fff;z-index:100;}
  .page-header .h-logo{height:10mm;max-width:35mm;object-fit:contain;}
  .page-header .h-text{font-size:9px;color:#6E7570;}
  .page-footer{position:fixed;bottom:0;left:0;right:0;height:10mm;padding:3mm 14mm;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #E3DDCB;background:#FBF9F2;font-size:8px;color:#6E7570;z-index:100;}

  .watermark{position:fixed;bottom:40%;left:50%;transform:translateX(-50%) rotate(-30deg);opacity:.05;pointer-events:none;z-index:0;}
  .watermark img{height:150mm;object-fit:contain;filter:grayscale(100%);}

  .stamp{
    position:absolute;top:22px;right:-44px;
    background:#2F7A3D;color:#fff;
    font-size:11.5px;font-weight:700;letter-spacing:.12em;
    padding:5px 58px;transform:rotate(35deg);
    box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;
  }
  .head{text-align:center;padding:30px 36px 20px;border-bottom:1px dashed #E3DDCB;margin-top:4mm;}
  .co-icon{
    width:48px;height:48px;border-radius:12px;margin:0 auto 12px;
    overflow:hidden;display:flex;align-items:center;justify-content:center;
    background:transparent;
  }
  .co-icon img{width:100%;height:100%;object-fit:contain;}
  .co-name{font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;}
  .co-addr{font-size:10px;color:#6E7570;margin-top:4px;line-height:1.5;}

  .receipt-title{text-align:center;padding:20px 36px 6px;}
  .receipt-title .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.14em;color:#6E7570;}
  .receipt-title .amt{font-family:'Fraunces',serif;font-size:34px;color:#2F7A3D;margin:6px 0 2px;}
  .receipt-title .sub{font-size:12px;color:#6E7570;}

  .body{padding:20px 36px 6px;}
  .kv{display:flex;justify-content:space-between;font-size:12px;padding:8px 0;border-bottom:1px dashed #E3DDCB;}
  .kv span{color:#6E7570;}
  .kv b{color:#1C2321;font-weight:600;text-align:right;}
  .kv b.mono{font-family:'JetBrains Mono',monospace;font-size:11px;}

  .divider{
    height:14px;width:100%;
    background:linear-gradient(135deg,#EDE9DD 25%,transparent 25%) 0 0/10px 10px,linear-gradient(225deg,#EDE9DD 25%,transparent 25%) 0 0/10px 10px,#fff;
    margin-top:6px;
  }

  .foot{text-align:center;background:#FBF9F2;padding:18px 36px 30px;}
  .foot .thanks{font-family:'Fraunces',serif;font-size:14px;color:#0F3D3E;margin-bottom:6px;}
  .foot .note{font-size:11px;color:#6E7570;line-height:1.6;}
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
