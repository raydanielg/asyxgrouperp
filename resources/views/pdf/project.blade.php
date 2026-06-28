<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Project {{ $project->project_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  *{box-sizing:border-box;}
  body{margin:36px auto;background:#EDE9DD;color:#1C2321;font-family:'Inter',sans-serif;max-width:780px;}
  .sheet{background:#fff;border-radius:4px;box-shadow:0 18px 40px -10px rgba(15,61,62,.18),0 0 0 1px #E5E7EA;padding:44px 48px 36px;position:relative;overflow:hidden;}
  .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;}
  .top h1{font-family:'Fraunces',serif;font-size:26px;font-weight:700;margin:0;color:#0F3D3E;letter-spacing:-.02em;}
  .top .co-mark{display:flex;align-items:center;gap:10px;}
  .top .co-icon{width:28px;height:28px;border-radius:8px;background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);display:flex;align-items:center;justify-content:center;}
  .top .co-icon img{width:22px;height:22px;object-fit:contain;border-radius:5px;}
  .top .co-word{font-family:'Fraunces',serif;font-size:19px;font-weight:700;color:#0F3D3E;}
  .badge{display:inline-block;padding:3px 12px;border-radius:20px;font-size:10px;font-weight:600;letter-spacing:.04em;}
  .stat-row{display:flex;gap:14px;margin-bottom:22px;}
  .stat-card{flex:1;background:#FBF9F2;border-radius:8px;padding:16px 18px;text-align:center;}
  .stat-card .num{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:#0F3D3E;}
  .stat-card .lbl{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:#6E7570;margin-top:4px;}
  .card{background:#FBF9F2;border-radius:8px;padding:20px 24px;margin-bottom:18px;}
  .card-title{font-family:'Fraunces',serif;font-size:15px;font-weight:700;color:#0F3D3E;margin-bottom:12px;letter-spacing:-.01em;}
  .grid{display:flex;flex-wrap:wrap;}
  .grid-item{width:50%;padding:6px 0;}
  .grid-item .k{font-size:11px;color:#6E7570;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;}
  .grid-item .v{font-size:14px;color:#1C2321;font-weight:500;}
  .grid-item .v.mono{font-family:'JetBrains Mono',monospace;font-size:13px;}
  .progress-bar{height:8px;background:#EDE9DD;border-radius:10px;overflow:hidden;margin:12px 0;}
  .progress-fill{height:100%;border-radius:10px;background:linear-gradient(90deg,#C9A227,#0F3D3E);}
  .item-list{margin:0;padding:0;list-style:none;}
  .item-list li{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #EDE9DD;font-size:13px;}
  .item-list li:last-child{border-bottom:none;}
  .item-list .status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:8px;}
  .foot{margin-top:32px;padding-top:14px;border-top:1px solid #EDE9DD;font-size:11px;color:#6E7570;text-align:center;}
</style>
</head>
<body>
  <div class="sheet">
    @php
      $taskDone = $project->tasks->where('status','done')->count();
      $taskTotal = $project->tasks->count();
      $bugOpen = $project->bugs->where('status','open')->count();
      $bugTotal = $project->bugs->count();
      $totalHours = $project->timesheets->sum('hours');
      $statusColors = ['planning'=>'#FEF3C7','in_progress'=>'#DBEAFE','on_hold'=>'#F3F4F6','completed'=>'#D1FAE5','cancelled'=>'#FEE2E2'];
      $statusTextColors = ['planning'=>'#92400E','in_progress'=>'#1E40AF','on_hold'=>'#374151','completed'=>'#065F46','cancelled'=>'#991B1B'];
    @endphp
    <div class="top">
      <div><h1>Project Report</h1></div>
      <div class="co-mark">
        <div class="co-icon"><img src="{{ public_path('asyxgrouplogo.png') }}" alt="ASYX"></div>
        <div class="co-word">Asyx</div>
      </div>
    </div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <div>
        <div style="font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:#1C2321;">{{ $project->title }}</div>
        <div style="font-size:12px;color:#6E7570;margin-top:4px;">{{ $project->project_number }}</div>
      </div>
      <div>
        <span class="badge" style="background:{{ $statusColors[$project->status] ?? '#F3F4F6' }};color:{{ $statusTextColors[$project->status] ?? '#374151' }};">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
        <span class="badge" style="background:#EDE9DD;color:#1C2321;margin-left:6px;">{{ ucfirst($project->priority) }} Priority</span>
      </div>
    </div>
    <div class="stat-row">
      <div class="stat-card"><div class="num">{{ $taskTotal }}</div><div class="lbl">Tasks</div></div>
      <div class="stat-card"><div class="num">{{ $bugTotal }}</div><div class="lbl">Bugs</div></div>
      <div class="stat-card"><div class="num">{{ number_format($totalHours, 0) }}</div><div class="lbl">Hours</div></div>
      <div class="stat-card"><div class="num">{{ $project->progress }}%</div><div class="lbl">Progress</div></div>
    </div>
    <div class="card">
      <div class="card-title">Details</div>
      <div class="grid">
        <div class="grid-item"><div class="k">Manager</div><div class="v">{{ $project->manager?->name ?? 'N/A' }}</div></div>
        <div class="grid-item"><div class="k">Budget</div><div class="v mono">TZS {{ number_format($project->budget, 0) }}</div></div>
        <div class="grid-item"><div class="k">Start Date</div><div class="v">{{ $project->start_date?->format('d M Y') ?? 'N/A' }}</div></div>
        <div class="grid-item"><div class="k">Due Date</div><div class="v">{{ $project->due_date?->format('d M Y') ?? 'N/A' }}</div></div>
        <div class="grid-item" style="width:100%;"><div class="k">Description</div><div class="v" style="font-size:13px;font-weight:400;">{{ $project->description ?? 'No description' }}</div></div>
      </div>
      <div class="progress-bar"><div class="progress-fill" style="width:{{ $project->progress }}%"></div></div>
    </div>
    <div style="display:flex;gap:16px;">
      <div style="flex:1;">
        <div class="card" style="padding:16px 20px;">
          <div class="card-title">Tasks ({{ $taskTotal }})</div>
          @if($taskTotal > 0)
          <ul class="item-list">
            @foreach($project->tasks as $task)
            <li>
              <span><span class="status-dot" style="background:{{ $task->status=='done'?'#10B981':($task->status=='in_progress'?'#F59E0B':'#9CA3AF') }}"></span>{{ $task->title }}</span>
              <span style="font-size:11px;color:#6E7570;">{{ ucfirst(str_replace('_',' ',$task->status)) }}</span>
            </li>
            @endforeach
          </ul>
          @else
          <div style="font-size:12px;color:#9CA3AF;text-align:center;padding:12px 0;">No tasks</div>
          @endif
        </div>
      </div>
      <div style="flex:1;">
        <div class="card" style="padding:16px 20px;">
          <div class="card-title">Bugs ({{ $bugTotal }})</div>
          @if($bugTotal > 0)
          <ul class="item-list">
            @foreach($project->bugs as $bug)
            <li>
              <span><span class="status-dot" style="background:{{ $bug->status=='open'?'#EF4444':($bug->status=='fixed'?'#10B981':'#9CA3AF') }}"></span>{{ $bug->title }}</span>
              <span style="font-size:11px;color:#6E7570;">{{ ucfirst($bug->severity) }}</span>
            </li>
            @endforeach
          </ul>
          @else
          <div style="font-size:12px;color:#9CA3AF;text-align:center;padding:12px 0;">No bugs</div>
          @endif
        </div>
      </div>
    </div>
    @if($totalHours > 0)
    <div class="card" style="padding:16px 20px;">
      <div class="card-title">Timesheets ({{ number_format($totalHours, 1) }} total hours)</div>
      <ul class="item-list">
        @foreach($project->timesheets->take(8) as $ts)
        <li>
          <span>{{ $ts->description ?? 'Timesheet entry' }}</span>
          <span style="font-family:'JetBrains Mono',monospace;font-size:12px;color:#0F3D3E;">{{ $ts->date->format('d M') }} &middot; {{ $ts->hours }}h</span>
        </li>
        @endforeach
        @if($project->timesheets->count() > 8)
        <li style="justify-content:center;color:#6E7570;font-size:11px;">+{{ $project->timesheets->count() - 8 }} more entries</li>
        @endif
      </ul>
    </div>
    @endif
    @if($project->deal)
    <div class="card" style="padding:14px 20px;background:#0F3D3E;color:#fff;">
      <div style="display:flex;justify-content:space-between;align-items:center;">
        <div><div style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;opacity:.7;">Originating Deal</div>
        <div style="font-size:15px;font-weight:700;margin-top:2px;">{{ $project->deal->title }}</div></div>
        <div style="font-family:'Fraunces',serif;font-size:18px;color:#C9A227;">TZS {{ number_format($project->deal->value, 0) }}</div>
      </div>
    </div>
    @endif
    <div class="foot">Page 1 of 1</div>
  </div>
</body>
</html>
