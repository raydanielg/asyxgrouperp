<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Deal {{ $deal->deal_number }}</title>
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
  .badge-open{background:#D1FAE5;color:#065F46;}
  .badge-won{background:#D1FAE5;color:#065F46;}
  .badge-lost{background:#FEE2E2;color:#991B1B;}
  .badge-cancelled{background:#F3F4F6;color:#374151;}
  .value-box{background:linear-gradient(135deg,#0F3D3E,#013028);color:#fff;border-radius:10px;padding:20px 26px;margin-bottom:22px;display:flex;justify-content:space-between;align-items:center;}
  .value-box .label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;opacity:.7;}
  .value-box .amount{font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:#C9A227;letter-spacing:-.02em;}
  .card{background:#FBF9F2;border-radius:8px;padding:22px 26px;margin-bottom:20px;}
  .card-title{font-family:'Fraunces',serif;font-size:15px;font-weight:700;color:#0F3D3E;margin-bottom:14px;letter-spacing:-.01em;}
  .grid{display:flex;flex-wrap:wrap;gap:0;}
  .grid-item{width:50%;padding:7px 0;}
  .grid-item .k{font-size:11px;color:#6E7570;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px;}
  .grid-item .v{font-size:14px;color:#1C2321;font-weight:500;}
  .grid-item .v.mono{font-family:'JetBrains Mono',monospace;font-size:13px;}
  .stage-bar{display:flex;align-items:center;gap:0;margin:14px 0 8px;}
  .stage-step{flex:1;text-align:center;padding:8px 4px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;position:relative;}
  .stage-step.past{background:#D1FAE5;color:#065F46;}
  .stage-step.current{background:#0F3D3E;color:#fff;}
  .stage-step.future{background:#F3F4F6;color:#9CA3AF;}
  .stage-step:not(:last-child)::after{content:'';position:absolute;right:-6px;top:50%;transform:translateY(-50%);border:6px solid transparent;border-left-color:inherit;}
  .foot{margin-top:32px;padding-top:14px;border-top:1px solid #EDE9DD;font-size:11px;color:#6E7570;text-align:center;}
</style>
</head>
<body>
  <div class="sheet">
    @php
      $stages = ['prospecting','qualification','negotiation','proposal','closed_won','closed_lost'];
      $currentIdx = array_search($deal->stage, $stages);
      $badgeMap = ['open'=>'badge-open','won'=>'badge-won','lost'=>'badge-lost','cancelled'=>'badge-cancelled'];
    @endphp
    <div class="top">
      <div><h1>Deal</h1></div>
      <div class="co-mark">
        <div class="co-icon"><img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX"></div>
        <div class="co-word">Asyx</div>
      </div>
    </div>
    <div class="value-box">
      <div><div class="label">Deal Value</div><div class="amount">TZS {{ number_format($deal->value, 0) }}</div></div>
      <div style="text-align:right;">
        <div class="label">Status</div>
        <span class="badge {{ $badgeMap[$deal->status] ?? 'badge-open' }}" style="font-size:12px;padding:4px 16px;">{{ ucfirst($deal->status) }}</span>
      </div>
    </div>
    <div class="card">
      <div class="card-title">{{ $deal->title }}</div>
      <div class="grid">
        <div class="grid-item"><div class="k">Deal Number</div><div class="v mono">{{ $deal->deal_number }}</div></div>
        <div class="grid-item"><div class="k">Stage</div><div class="v">{{ ucfirst(str_replace('_', ' ', $deal->stage)) }}</div></div>
        <div class="grid-item"><div class="k">Lead</div><div class="v">{{ $deal->lead?->full_name ?? 'N/A' }}</div></div>
        <div class="grid-item"><div class="k">Expected Close</div><div class="v">{{ $deal->expected_close_date?->format('d M Y') ?? 'N/A' }}</div></div>
        <div class="grid-item" style="display:none;"></div>
        <div class="grid-item"><div class="k">Created</div><div class="v">{{ $deal->created_at->format('d M Y') }}</div></div>
      </div>
      <div class="stage-bar">
        @foreach($stages as $i => $s)
        <div class="stage-step {{ $i < $currentIdx ? 'past' : ($i == $currentIdx ? 'current' : 'future') }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</div>
        @endforeach
      </div>
    </div>
    @if($deal->notes)
    <div class="card" style="padding:16px 22px;">
      <div style="font-size:11px;color:#6E7570;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Notes</div>
      <div style="font-size:13px;color:#1C2321;line-height:1.7;">{{ $deal->notes }}</div>
    </div>
    @endif
    @if($deal->contracts->count() > 0)
    <div class="card" style="padding:18px 22px;">
      <div class="card-title">Contracts ({{ $deal->contracts->count() }})</div>
      @foreach($deal->contracts as $contract)
      <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #EDE9DD;font-size:13px;">
        <span>{{ $contract->title ?? 'Contract' }}</span>
        <span style="color:#6E7570;">{{ $contract->start_date?->format('d M Y') ?? '' }}</span>
      </div>
      @endforeach
    </div>
    @endif
    @if($deal->relationLoaded('project') && $deal->project)
    <div class="card" style="padding:16px 22px;background:#0F3D3E;color:#fff;">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <div><div style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;opacity:.7;">Converted to Project</div>
        <div style="font-size:16px;font-weight:700;margin-top:4px;">{{ $deal->project->title }}</div></div>
        <div style="text-align:right;"><div style="font-size:11px;opacity:.7;">Budget</div>
        <div style="font-family:'Fraunces',serif;font-size:18px;color:#C9A227;">TZS {{ number_format($deal->project->budget, 0) }}</div></div>
      </div>
    </div>
    @endif
    <div class="foot">Page 1 of 1</div>
  </div>
</body>
</html>
