<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Quotation {{ $quotation->quotation_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  @page { margin: 0; padding: 0; size: A4; }
  *{box-sizing:border-box;}
  body{margin:0;background:#fff;color:#1C2321;font-family:'Inter',sans-serif;font-size:11px;line-height:1.4;}
  .sheet{width:210mm;min-height:297mm;padding:12mm 14mm 16mm;position:relative;overflow:hidden;}

  .page-header{position:fixed;top:0;left:0;right:0;height:14mm;padding:4mm 14mm;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #E5E7EA;background:#fff;z-index:100;}
  .page-header .h-logo{height:10mm;max-width:35mm;object-fit:contain;}
  .page-header .h-text{font-size:9px;color:#6E7570;}
  .page-footer{position:fixed;bottom:0;left:0;right:0;height:10mm;padding:3mm 14mm;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #E5E7EA;background:#FBF9F2;font-size:8px;color:#6E7570;z-index:100;}

  .watermark{position:fixed;bottom:40%;left:50%;transform:translateX(-50%) rotate(-30deg);opacity:.05;pointer-events:none;z-index:0;}
  .watermark img{height:150mm;object-fit:contain;filter:grayscale(100%);}

  .stamp{position:absolute;top:20px;right:-48px;background:#0F3D3E;color:#fff;font-size:11px;font-weight:700;letter-spacing:.12em;padding:5px 60px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.12);z-index:10;opacity:.85;}
  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;margin-top:4mm;}
  .top h1{font-family:'Fraunces',serif;font-size:22px;font-weight:700;margin:0;color:#0F3D3E;letter-spacing:-.02em;}
  .top .co-mark{display:flex;align-items:center;gap:10px;}
  .top .co-icon img{height:40px;max-width:140px;object-fit:contain;}
  .top .co-word{font-family:'Fraunces',serif;font-size:16px;font-weight:700;color:#0F3D3E;}
  .top .co-meta{font-size:9px;color:#6E7570;line-height:1.5;margin-top:3px;}
  .meta{font-size:11px;margin-bottom:18px;}
  .meta-row{display:flex;gap:8px;padding:2px 0;}
  .meta-row .k{width:120px;color:#6E7570;}
  .meta-row .v{color:#1C2321;font-weight:500;}
  .parties{display:flex;gap:40px;margin-bottom:18px;}
  .party b{display:block;font-size:11px;margin-bottom:3px;color:#0F3D3E;}
  .party .lines{font-size:10px;color:#1C2321;line-height:1.6;}
  table.items{width:100%;border-collapse:collapse;font-size:10px;margin-bottom:4px;}
  table.items thead th{text-align:left;font-weight:600;color:#0F3D3E;border-bottom:1.5px solid #0F3D3E;padding:4px 3px;font-size:9px;text-transform:uppercase;letter-spacing:.04em;}
  table.items thead th.r{text-align:right;}
  table.items tbody td{padding:7px 3px;border-bottom:1px solid #EDE9DD;vertical-align:top;color:#1C2321;}
  table.items tbody td.r{text-align:right;font-family:'JetBrains Mono',monospace;font-size:10px;}
  .summary{margin-left:auto;width:220px;margin-top:8px;}
  .summary-row{display:flex;justify-content:space-between;font-size:11px;padding:5px 3px;border-bottom:1px solid #EDE9DD;}
  .summary-row.final{font-weight:700;border-bottom:2px solid #0F3D3E;padding:8px 3px;font-size:12px;color:#0F3D3E;}
  .notes{margin-top:14px;padding:10px 12px;background:#FBF9F2;border-radius:6px;font-size:10px;color:#1C2321;border-left:3px solid #C9A227;}
  .notes b{color:#0F3D3E;}
  .terms{margin-top:12px;padding:10px 12px;background:#F5F7F6;border-radius:8px;border:1px solid #D8E3DE;font-size:10px;line-height:1.5;}
  .terms b{color:#0F3D3E;display:block;margin-bottom:4px;}
  .thank-you{margin-top:12px;padding:10px 14px;border-radius:8px;background:linear-gradient(135deg,#0F3D3E 0%,#1A5A5B 100%);color:#fff;text-align:center;}
  .thank-you .title{font-family:'Fraunces',serif;font-size:13px;font-weight:700;}
  .thank-you .contact{font-size:9px;opacity:.85;margin-top:2px;}
</style>
</head>
<body>
  <div class="sheet">
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
    <div class="foot">Page 1 of 1</div>
  </div>
</body>
</html>
