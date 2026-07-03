<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  @page { margin: 10mm; size: A4; }
  *{box-sizing:border-box;}
  body{margin:0;background:#fff;color:#1C2321;font-family:'Inter',sans-serif;font-size:16px;line-height:1.4;}
  .sheet{width:190mm;min-height:267mm;padding:6mm;position:relative;overflow:hidden;}

  .watermark{position:absolute;bottom:100px;left:50%;transform:translateX(-50%) rotate(-30deg);opacity:.05;pointer-events:none;z-index:0;}
  .watermark img{height:160px;object-fit:contain;filter:grayscale(100%);}

  .stamp{position:absolute;top:20px;right:-48px;background:#0F3D3E;color:#fff;font-size:14px;font-weight:700;letter-spacing:.12em;padding:6px 60px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.12);z-index:10;opacity:.85;}
  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:22px;}
  .top h1{font-family:'Fraunces',serif;font-size:26px;font-weight:700;margin:0;color:#0F3D3E;letter-spacing:-.02em;}
  .top .co-mark{display:flex;align-items:center;gap:12px;}
  .top .co-icon img{height:55px;max-width:180px;object-fit:contain;}
  .top .co-word{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#0F3D3E;}
  .top .co-meta{font-size:14px;color:#6E7570;line-height:1.5;margin-top:3px;}
  .meta{font-size:16px;margin-bottom:20px;}
  .meta-row{display:flex;gap:8px;padding:3px 0;}
  .meta-row .k{width:140px;color:#6E7570;}
  .meta-row .v{color:#1C2321;font-weight:500;}
  .parties{display:flex;gap:50px;margin-bottom:20px;}
  .party b{display:block;font-size:14px;margin-bottom:4px;color:#0F3D3E;}
  .party .lines{font-size:16px;color:#1C2321;line-height:1.6;}
  table.items{width:100%;border-collapse:collapse;font-size:16px;margin-bottom:4px;}
  table.items thead th{text-align:left;font-weight:600;color:#0F3D3E;border-bottom:1.5px solid #0F3D3E;padding:8px 4px;font-size:13px;text-transform:uppercase;letter-spacing:.04em;}
  table.items thead th.r{text-align:right;}
  table.items tbody td{padding:10px 4px;border-bottom:1px solid #EDE9DD;vertical-align:top;color:#1C2321;}
  table.items tbody td.r{text-align:right;font-family:'JetBrains Mono',monospace;font-size:15px;}
  table.items tr{page-break-inside:avoid;}
  .summary{margin-left:auto;width:260px;margin-top:12px;}
  .summary-row{display:flex;justify-content:space-between;font-size:16px;padding:7px 4px;border-bottom:1px solid #EDE9DD;}
  .summary-row.final{font-weight:700;border-bottom:2px solid #0F3D3E;padding:10px 4px;font-size:18px;color:#0F3D3E;}
  .notes{margin-top:18px;padding:12px 14px;background:#FBF9F2;border-radius:6px;font-size:15px;color:#1C2321;border-left:3px solid #C9A227;}
  .notes b{color:#0F3D3E;}
  .terms{margin-top:16px;padding:14px 16px;background:#F5F7F6;border-radius:8px;border:1px solid #D8E3DE;font-size:15px;line-height:1.5;}
  .terms b{color:#0F3D3E;display:block;margin-bottom:6px;}
  .thank-you{margin-top:16px;padding:14px 18px;border-radius:8px;background:linear-gradient(135deg,#0F3D3E 0%,#1A5A5B 100%);color:#fff;text-align:center;}
  .thank-you .title{font-family:'Fraunces',serif;font-size:18px;font-weight:700;}
  .thank-you .contact{font-size:13px;opacity:.85;margin-top:3px;}
</style>
</head>
<body>
  <div class="sheet">
    <div class="watermark">
      <img src="{{ public_path('asyxgrouplogo.png') }}" alt="">
    </div>
    @if($quotation->status === 'accepted')<div class="stamp">ACCEPTED</div>@endif
    <div class="top">
      <h1>Quotation</h1>
      <div class="co-mark">
        <div class="co-icon"><img src="{{ public_path('asyxgrouplogo.png') }}" alt="{{ config('app.name') }}"></div>
        <div>
          <div class="co-word">{{ $company?->name ?? config('app.name') }}</div>
          <div class="co-meta">
            {{ $company?->address ?? 'Dar es Salaam, Tanzania' }}<br>
            @if($company?->tax_id)TIN: {{ $company->tax_id }}@endif
            @if($company?->tax_id && $company?->vrn) &middot; @endif
            @if($company?->vrn)VRN: {{ $company->vrn }}@endif
          </div>
        </div>
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
    <div class="terms">
      <b>Terms & Conditions</b>
      1. Prices Are Quoted in TZS<br>
      2. Prices are subject to change without prior notice<br>
      3. Payment terms must be strictly observed<br>
      4. Goods remain property of {{ config('app.name') }} until fully paid
    </div>
    <div class="thank-you">
      <div class="title">Thank You For Your Business</div>
      <div class="contact">For inquiries contact: {{ $company?->email ?? 'billing@asyxgroup.tz' }}</div>
    </div>
  </div>
</body>
</html>
