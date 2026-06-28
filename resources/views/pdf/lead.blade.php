<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Lead {{ $lead->lead_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  *{box-sizing:border-box;}
  body{margin:36px auto;background:#EDE9DD;color:#1C2321;font-family:'Inter',sans-serif;max-width:720px;}
  .sheet{background:#fff;border-radius:4px;box-shadow:0 18px 40px -10px rgba(15,61,62,.18),0 0 0 1px #E5E7EA;padding:44px 48px 36px;position:relative;overflow:hidden;}
  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;}
  .top h1{font-family:'Fraunces',serif;font-size:26px;font-weight:700;margin:0;color:#0F3D3E;letter-spacing:-.02em;}
  .top .co-mark{display:flex;align-items:center;gap:10px;}
  .top .co-icon{width:28px;height:28px;border-radius:8px;background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);display:flex;align-items:center;justify-content:center;}
  .top .co-icon img{width:22px;height:22px;object-fit:contain;border-radius:5px;}
  .top .co-word{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#0F3D3E;}
  .badge{display:inline-block;padding:3px 14px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:.04em;}
  .badge-new{background:#E0F2FE;color:#075985;}
  .badge-contacted{background:#FEF3C7;color:#92400E;}
  .badge-qualified{background:#D1FAE5;color:#065F46;}
  .badge-converted{background:#D1FAE5;color:#065F46;}
  .badge-lost{background:#FEE2E2;color:#991B1B;}
  .card{background:#FBF9F2;border-radius:8px;padding:22px 26px;margin-bottom:20px;}
  .card-title{font-family:'Fraunces',serif;font-size:15px;font-weight:700;color:#0F3D3E;margin-bottom:14px;letter-spacing:-.01em;}
  .grid{display:flex;flex-wrap:wrap;gap:0;}
  .grid-item{width:50%;padding:7px 0;}
  .grid-item .k{font-size:11px;color:#6E7570;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;}
  .grid-item .v{font-size:14px;color:#1C2321;font-weight:500;}
  .grid-item .v.mono{font-family:'JetBrains Mono',monospace;font-size:13px;}
  .deal-card{border:1px solid #EDE9DD;border-radius:6px;padding:14px 18px;margin-bottom:10px;}
  .deal-card:last-child{margin-bottom:0;}
  .deal-card .deal-title{font-size:14px;font-weight:600;color:#0F3D3E;}
  .deal-card .deal-meta{font-size:12px;color:#6E7570;margin-top:4px;}
  .deal-card .deal-value{font-family:'JetBrains Mono',monospace;font-size:15px;font-weight:700;color:#C9A227;}
  .foot{margin-top:32px;padding-top:14px;border-top:1px solid #EDE9DD;font-size:11px;color:#6E7570;text-align:center;}
</style>
</head>
<body>
  <div class="sheet">
    <div class="top">
      <div><h1>Lead Profile</h1></div>
      <div class="co-mark">
        <div class="co-icon"><img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX"></div>
        <div class="co-word">Asyx</div>
      </div>
    </div>
    @php
      $badgeMap = ['new'=>'badge-new','contacted'=>'badge-contacted','qualified'=>'badge-qualified','converted'=>'badge-converted','lost'=>'badge-lost'];
      $badgeClass = $badgeMap[$lead->status] ?? 'badge-new';
    @endphp
    <div class="card">
      <div class="card-title">{{ $lead->full_name }} <span class="badge {{ $badgeClass }}">{{ ucfirst($lead->status) }}</span></div>
      <div class="grid">
        <div class="grid-item"><div class="k">Lead Number</div><div class="v mono">{{ $lead->lead_number }}</div></div>
        <div class="grid-item"><div class="k">Source</div><div class="v">{{ $lead->source ?? 'N/A' }}</div></div>
        <div class="grid-item"><div class="k">Email</div><div class="v">{{ $lead->email ?? 'N/A' }}</div></div>
        <div class="grid-item"><div class="k">Phone</div><div class="v">{{ $lead->phone ?? 'N/A' }}</div></div>
        <div class="grid-item"><div class="k">Company</div><div class="v">{{ $lead->company ?? 'N/A' }}</div></div>
        <div class="grid-item"><div class="k">Assigned To</div><div class="v">{{ $lead->assignedTo?->name ?? 'Unassigned' }}</div></div>
        <div class="grid-item"><div class="k">Created</div><div class="v">{{ $lead->created_at->format('d M Y') }}</div></div>
        <div class="grid-item"><div class="k">Updated</div><div class="v">{{ $lead->updated_at->format('d M Y') }}</div></div>
      </div>
    </div>
    @if($lead->notes)
    <div class="card" style="padding:16px 22px;">
      <div style="font-size:11px;color:#6E7570;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Notes</div>
      <div style="font-size:13px;color:#1C2321;line-height:1.7;">{{ $lead->notes }}</div>
    </div>
    @endif
    @if($lead->deals->count() > 0)
    <div class="card" style="padding:18px 22px;">
      <div class="card-title">Related Deals ({{ $lead->deals->count() }})</div>
      @foreach($lead->deals as $deal)
      <div class="deal-card">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <div>
            <div class="deal-title">{{ $deal->title }}</div>
            <div class="deal-meta">{{ $deal->deal_number }} &middot; {{ $deal->stage }} &middot; {{ $deal->expected_close_date?->format('d M Y') ?? 'No close date' }}</div>
          </div>
          <div class="deal-value">TZS {{ number_format($deal->value, 0) }}</div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
    <div class="foot">Page 1 of 1</div>
  </div>
</body>
</html>
