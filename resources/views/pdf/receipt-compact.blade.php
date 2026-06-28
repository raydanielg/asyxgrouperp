<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $receipt['receipt_number'] }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');

  *{box-sizing:border-box;}
  body{
    margin:30px auto;
    background:#EDE9DD;
    color:#1C2321;
    font-family:'Inter',sans-serif;
    max-width:520px;
  }
  .sheet{
    background:#fff;
    border-radius:6px;
    box-shadow:0 18px 40px -10px rgba(15,61,62,.25),0 0 0 1px #E3DDCB;
    position:relative;overflow:hidden;
  }
  .stamp{
    position:absolute;top:22px;right:-44px;
    background:#2F7A3D;color:#fff;
    font-size:11.5px;font-weight:700;letter-spacing:.12em;
    padding:5px 58px;transform:rotate(35deg);
    box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;
  }
  .head{text-align:center;padding:34px 36px 22px;border-bottom:1px dashed #E3DDCB;}
  .co-icon{
    width:40px;height:40px;border-radius:11px;margin:0 auto 12px;
    background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);
    overflow:hidden;display:flex;align-items:center;justify-content:center;
  }
  .co-icon img{width:32px;height:32px;object-fit:contain;border-radius:6px;}
  .co-name{font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;}
  .co-addr{font-size:11px;color:#6E7570;margin-top:4px;line-height:1.5;}

  .receipt-title{text-align:center;padding:22px 36px 6px;}
  .receipt-title .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.14em;color:#6E7570;}
  .receipt-title .amt{font-family:'Fraunces',serif;font-size:34px;color:#2F7A3D;margin:6px 0 2px;}
  .receipt-title .sub{font-size:12px;color:#6E7570;}

  .body{padding:24px 36px 6px;}
  .kv{display:flex;justify-content:space-between;font-size:13px;padding:9px 0;border-bottom:1px dashed #E3DDCB;}
  .kv span{color:#6E7570;}
  .kv b{color:#1C2321;font-weight:600;text-align:right;}
  .kv b.mono{font-family:'JetBrains Mono',monospace;font-size:12px;}

  .divider{
    height:14px;width:100%;
    background:linear-gradient(135deg,#EDE9DD 25%,transparent 25%) 0 0/10px 10px,linear-gradient(225deg,#EDE9DD 25%,transparent 25%) 0 0/10px 10px,#fff;
    margin-top:6px;
  }

  .foot{text-align:center;background:#FBF9F2;padding:20px 36px 30px;}
  .foot .thanks{font-family:'Fraunces',serif;font-size:14px;color:#0F3D3E;margin-bottom:6px;}
  .foot .note{font-size:11px;color:#6E7570;line-height:1.6;}
</style>
</head>
<body>
  <div class="sheet">
    <div class="stamp">PAID</div>

    <div class="head">
      <div class="co-icon">
        <img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX">
      </div>
      <div class="co-name">{{ config('app.name') }}</div>
      <div class="co-addr">{{ $company?->name ?? 'ASYX Group' }}<br>Dar es Salaam, Tanzania</div>
    </div>

    <div class="receipt-title">
      <div class="lbl">Kiasi Kilicholipwa</div>
      <div class="amt">{{ number_format($receipt['paid_amount'], 0) }} Tsh</div>
      <div class="sub">Imelipwa kikamilifu &mdash; {{ $receipt['payment_date'] }}</div>
    </div>

    <div class="body">
      <div class="kv"><span>Receipt No.</span><b class="mono">{{ $receipt['receipt_number'] }}</b></div>
      <div class="kv"><span>Invoice Ref.</span><b class="mono">{{ $salesInvoice->invoice_number }}</b></div>
      <div class="kv"><span>Imelipwa na</span><b>{{ $salesInvoice->customer?->name ?? 'N/A' }}</b></div>
      <div class="kv"><span>Njia ya Malipo</span><b>{{ $receipt['payments'][0]['method'] ?? 'Bank Transfer' }}</b></div>
      <div class="kv"><span>Transaction ID</span><b class="mono">{{ $receipt['payments'][0]['transaction_id'] ?? 'N/A' }}</b></div>
      <div class="kv"><span>Tarehe ya Malipo</span><b>{{ $receipt['payment_date'] }}, {{ $receipt['payment_time'] }}</b></div>
      <div class="kv"><span>Maelezo</span><b style="text-align:right;">{{ $salesInvoice->items->first()?->product_name ?? 'Invoice Payment' }}</b></div>
    </div>

    <div class="divider"></div>

    <div class="foot">
      <div class="thanks">Asante kwa malipo yako</div>
      <div class="note">Risiti hii ni uthibitisho rasmi wa malipo.<br>Iwapo una swali lolote, wasiliana nasi: billing@asyxgroup.tz</div>
    </div>
  </div>
</body>
</html>
