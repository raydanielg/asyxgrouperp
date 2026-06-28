<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  *{box-sizing:border-box;}
  body{margin:36px auto;background:#EDE9DD;color:#1C2321;font-family:'Inter',sans-serif;max-width:780px;}
  .sheet{background:#fff;border-radius:4px;box-shadow:0 18px 40px -10px rgba(15,61,62,.18),0 0 0 1px #E5E7EA;padding:48px 52px 40px;position:relative;overflow:hidden;}
  .stamp{position:absolute;top:28px;right:-40px;background:#0F3D3E;color:#fff;font-size:11px;font-weight:700;letter-spacing:.12em;padding:4px 56px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.12);z-index:10;opacity:.85;}
  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:32px;}
  .top h1{font-family:'Fraunces',serif;font-size:28px;font-weight:700;margin:0;color:#0F3D3E;letter-spacing:-.02em;}
  .top .co-mark{display:flex;align-items:center;gap:10px;}
  .top .co-icon{width:28px;height:28px;border-radius:8px;background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);display:flex;align-items:center;justify-content:center;}
  .top .co-icon img{width:22px;height:22px;object-fit:contain;border-radius:5px;}
  .top .co-word{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#0F3D3E;}
  .meta{font-size:13px;margin-bottom:28px;}
  .meta-row{display:flex;gap:8px;padding:3px 0;}
  .meta-row .k{width:130px;color:#6E7570;}
  .meta-row .v{color:#1C2321;font-weight:500;}
  .parties{display:flex;gap:60px;margin-bottom:30px;}
  .party b{display:block;font-size:13px;margin-bottom:5px;color:#0F3D3E;}
  .party .lines{font-size:13px;color:#1C2321;line-height:1.7;}
  table.items{width:100%;border-collapse:collapse;font-size:12.5px;margin-bottom:4px;}
  table.items thead th{text-align:left;font-weight:600;color:#0F3D3E;border-bottom:1.5px solid #0F3D3E;padding:0 4px 10px;font-size:11px;text-transform:uppercase;letter-spacing:.04em;}
  table.items thead th.r{text-align:right;}
  table.items tbody td{padding:13px 4px;border-bottom:1px solid #EDE9DD;vertical-align:top;color:#1C2321;}
  table.items tbody td.r{text-align:right;font-family:'JetBrains Mono',monospace;font-size:12px;}
  .summary{margin-left:auto;width:250px;margin-top:10px;}
  .summary-row{display:flex;justify-content:space-between;font-size:12.5px;padding:8px 4px;border-bottom:1px solid #EDE9DD;}
  .summary-row.final{font-weight:700;border-bottom:2px solid #0F3D3E;padding:10px 4px;font-size:14px;color:#0F3D3E;}
  .notes{margin-top:30px;padding:16px 20px;background:#FBF9F2;border-radius:6px;font-size:12px;color:#1C2321;border-left:3px solid #C9A227;}
  .notes b{color:#0F3D3E;}
  .foot{margin-top:36px;padding-top:14px;border-top:1px solid #EDE9DD;font-size:11px;color:#6E7570;text-align:center;}
</style>
</head>
<body>
  <div class="sheet">
    @if($quotation->status === 'accepted')<div class="stamp">ACCEPTED</div>@endif
    <div class="top">
      <h1>Quotation</h1>
      <div class="co-mark">
        <div class="co-icon"><img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX"></div>
        <div class="co-word">Asyx</div>
      </div>
    </div>
    <div class="meta">
      <div class="meta-row"><div class="k">Quotation No.</div><div class="v">{{ $quotation->quotation_number }}</div></div>
      <div class="meta-row"><div class="k">Date</div><div class="v">{{ $quotation->quotation_date->format('d M Y') }}</div></div>
      <div class="meta-row"><div class="k">Valid Until</div><div class="v">{{ $quotation->valid_until?->format('d M Y') ?? 'N/A' }}</div></div>
      <div class="meta-row"><div class="k">Status</div><div class="v">{{ ucfirst($quotation->status) }}</div></div>
    </div>
    <div class="parties">
      <div class="party">
        <b>{{ config('app.name') }}</b>
        <div class="lines">{{ $company?->name ?? 'ASYX Group' }}<br>Dar es Salaam, Tanzania<br>billing@asyxgroup.tz</div>
      </div>
      <div class="party">
        <b>Client</b>
        <div class="lines">{{ $quotation->client_name }}<br>{{ $quotation->client_email ?? '' }}<br>{{ $quotation->lead?->full_name ?? '' }}</div>
      </div>
    </div>
    @if($quotation->items->count() > 0)
    <table class="items">
      <thead>
        <tr><th style="width:40%;">Description</th><th class="r">Qty</th><th class="r">Unit Price</th><th class="r">Disc.</th><th class="r">Tax</th><th class="r">Total</th></tr>
      </thead>
      <tbody>
        @foreach($quotation->items as $item)
        <tr>
          <td>{{ $item->description }}</td>
          <td class="r">{{ $item->quantity }} {{ $item->unit ?? '' }}</td>
          <td class="r">{{ number_format($item->unit_price, 2) }}</td>
          <td class="r">{{ $item->discount_amount > 0 ? number_format($item->discount_amount, 2) : '-' }}</td>
          <td class="r">{{ $item->tax_percentage > 0 ? $item->tax_percentage.'%' : '-' }}</td>
          <td class="r">{{ number_format($item->line_total, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
    <div class="summary">
      <div class="summary-row"><span>Subtotal</span><span>TZS {{ number_format($quotation->subtotal, 2) }}</span></div>
      @if($quotation->discount_amount > 0)
      <div class="summary-row" style="color:#B91C1C;"><span>Discount</span><span>-TZS {{ number_format($quotation->discount_amount, 2) }}</span></div>
      @endif
      @if($quotation->tax_amount > 0)
      <div class="summary-row"><span>VAT (18%)</span><span>TZS {{ number_format($quotation->tax_amount, 2) }}</span></div>
      @endif
      <div class="summary-row final"><span>Total</span><span>TZS {{ number_format($quotation->total, 2) }}</span></div>
    </div>
    @if($quotation->notes)
    <div class="notes"><b>Notes</b><br>{{ $quotation->notes }}</div>
    @endif
    <div class="foot">Page 1 of 1</div>
  </div>
</body>
</html>
