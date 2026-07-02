<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<title>Project Report — {{ $project->project_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap');
  *{box-sizing:border-box;margin:0;padding:0;}
  body{background:#fff;color:#1C2321;font-family:'Inter',sans-serif;font-size:13px;line-height:1.5;}
  .page{padding:0 40px 30px;}
  /* ── Letterhead ── */
  .letterhead{display:flex;justify-content:space-between;align-items:center;padding:24px 0 16px;border-bottom:3px solid #0F3D3E;margin-bottom:0;}
  .lh-left{display:flex;align-items:center;gap:14px;}
  .lh-logo{width:52px;height:52px;border-radius:10px;overflow:hidden;}
  .lh-logo img{width:52px;height:52px;object-fit:contain;}
  .lh-co-name{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:#0F3D3E;line-height:1.1;}
  .lh-co-sub{font-size:10px;color:#6E7570;letter-spacing:.04em;margin-top:2px;}
  .lh-right{text-align:right;font-size:10px;color:#6E7570;line-height:1.6;}
  .lh-right strong{color:#0F3D3E;}
  /* ── Title bar ── */
  .title-bar{background:#0F3D3E;color:#fff;padding:14px 20px;margin:0 -40px 24px;}
  .title-bar h1{font-family:'Fraunces',serif;font-size:20px;font-weight:700;letter-spacing:-.01em;}
  .title-bar .ref{font-size:11px;opacity:.8;margin-top:2px;}
  /* ── Memo ── */
  .memo{background:#FBF9F2;border:1px solid #EDE9DD;border-radius:6px;padding:16px 20px;margin-bottom:22px;}
  .memo-row{display:flex;gap:8px;padding:3px 0;font-size:12px;}
  .memo-row .lbl{width:90px;color:#6E7570;text-transform:uppercase;letter-spacing:.04em;font-size:10px;font-weight:600;flex-shrink:0;padding-top:2px;}
  .memo-row .val{color:#1C2321;font-weight:500;}
  /* ── Section ── */
  .section{margin-bottom:22px;}
  .section-title{font-family:'Fraunces',serif;font-size:14px;font-weight:700;color:#0F3D3E;margin-bottom:10px;padding-bottom:6px;border-bottom:1.5px solid #C9A227;}
  /* ── Stat cards ── */
  .stat-row{display:flex;gap:12px;margin-bottom:22px;}
  .stat-card{flex:1;background:#FBF9F2;border:1px solid #EDE9DD;border-radius:6px;padding:14px 12px;text-align:center;}
  .stat-card .num{font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:#0F3D3E;}
  .stat-card .lbl{font-size:9px;text-transform:uppercase;letter-spacing:.06em;color:#6E7570;margin-top:3px;}
  /* ── Progress ── */
  .progress-bar{height:10px;background:#EDE9DD;border-radius:10px;overflow:hidden;margin:8px 0 4px;}
  .progress-fill{height:100%;border-radius:10px;background:linear-gradient(90deg,#C9A227,#0F3D3E);}
  .progress-label{font-size:11px;color:#6E7570;text-align:right;}
  /* ── Table ── */
  table{width:100%;border-collapse:collapse;font-size:12px;}
  th{text-align:left;padding:8px 10px;background:#0F3D3E;color:#fff;font-size:10px;text-transform:uppercase;letter-spacing:.04em;font-weight:600;}
  td{padding:8px 10px;border-bottom:1px solid #EDE9DD;}
  tr:nth-child(even) td{background:#FBF9F2;}
  .badge{display:inline-block;padding:2px 10px;border-radius:20px;font-size:10px;font-weight:600;letter-spacing:.03em;}
  .status-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px;}
  /* ── Charts ── */
  .chart-row{display:flex;gap:20px;margin-bottom:22px;}
  .chart-box{flex:1;background:#FBF9F2;border:1px solid #EDE9DD;border-radius:6px;padding:16px 18px;}
  .chart-title{font-size:11px;font-weight:700;color:#0F3D3E;text-transform:uppercase;letter-spacing:.04em;margin-bottom:12px;text-align:center;}
  .chart-legend{display:flex;flex-wrap:wrap;gap:8px 16px;margin-top:10px;justify-content:center;font-size:10px;color:#6E7570;}
  .chart-legend .dot{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:4px;vertical-align:middle;}
  .bar-chart{display:flex;align-items:flex-end;gap:10px;height:120px;padding-top:10px;}
  .bar-col{flex:1;display:flex;flex-direction:column;align-items:center;}
  .bar{width:100%;border-radius:4px 4px 0 0;min-height:2px;transition:height .3s;}
  .bar-label{font-size:9px;color:#6E7570;margin-top:4px;text-align:center;}
  .bar-value{font-size:10px;font-weight:600;color:#0F3D3E;margin-bottom:2px;}
  /* ── Footer ── */
  .footer{margin-top:30px;padding-top:12px;border-top:2px solid #0F3D3E;display:flex;justify-content:space-between;font-size:9px;color:#6E7570;}
  .footer .sig{text-align:center;}
  .footer .sig-line{border-top:1px solid #6E7570;width:180px;margin:24px auto 4px;}
  .footer .sig-name{font-weight:600;color:#1C2321;font-size:11px;}
  .footer .sig-title{font-size:9px;color:#6E7570;}
</style>
</head>
<body>
  <div class="page">
    @php
      $taskTotal = $project->tasks->count();
      $taskDone = $project->tasks->where('status','done')->count() + $project->tasks->where('status','completed')->count();
      $taskPending = $project->tasks->where('status','pending')->count();
      $taskProgress = $project->tasks->where('status','in_progress')->count();
      $bugTotal = $project->bugs->count();
      $bugOpen = $project->bugs->where('status','open')->count();
      $totalHours = $project->timesheets->sum('hours');
      $totalBonus = $project->bonuses->sum('amount');
      $invoiceTotal = $project->invoices->sum('total_amount');
      $invoicePaid = $project->invoices->sum('paid_amount');
      $statusColors = ['planning'=>'#FEF3C7','in_progress'=>'#DBEAFE','on_hold'=>'#F3F4F6','completed'=>'#D1FAE5','cancelled'=>'#FEE2E2'];
      $statusTextColors = ['planning'=>'#92400E','in_progress'=>'#1E40AF','on_hold'=>'#374151','completed'=>'#065F46','cancelled'=>'#991B1B'];
      $coName = $company?->name ?? 'ASYX Group';
      $coAddr = $company?->address ?? '';
      $coCity = $company?->city ?? '';
      $coPhone = $company?->phone ?? '';
      $coEmail = $company?->email ?? '';
      $logoPath = $company && $company->logo ? storage_path('app/public/' . $company->logo) : public_path('asyxgrouplogo.png');
      if (!file_exists($logoPath)) { $logoPath = public_path('asyxgrouplogo.png'); }
    @endphp

    {{-- Letterhead --}}
    <div class="letterhead">
      <div class="lh-left">
        <div class="lh-logo"><img src="{{ $logoPath }}" alt="Logo"></div>
        <div>
          <div class="lh-co-name">{{ $coName }}</div>
          <div class="lh-co-sub">{{ $company?->slogan ?? 'Excellence in Service Delivery' }}</div>
        </div>
      </div>
      <div class="lh-right">
        @if($coAddr)<div><strong>Address:</strong> {{ $coAddr }}</div>@endif
        @if($coCity)<div>{{ $coCity }}{{ $company?->country ? ', ' . $company->country : '' }}</div>@endif
        @if($coPhone)<div><strong>Tel:</strong> {{ $coPhone }}</div>@endif
        @if($coEmail)<div><strong>Email:</strong> {{ $coEmail }}</div>@endif
        @if($company?->website)<div><strong>Web:</strong> {{ $company->website }}</div>@endif
      </div>
    </div>

    {{-- Title Bar --}}
    <div class="title-bar">
      <h1>PROJECT REPORT</h1>
      <div class="ref">Ref: {{ $project->project_number }} | Generated: {{ now()->format('d M Y H:i') }}</div>
    </div>

    {{-- Memo --}}
    <div class="memo">
      <div class="memo-row"><div class="lbl">To</div><div class="val">Management / Project Stakeholders</div></div>
      <div class="memo-row"><div class="lbl">From</div><div class="val">{{ $project->manager?->name ?? 'Project Manager' }}</div></div>
      <div class="memo-row"><div class="lbl">Date</div><div class="val">{{ now()->format('d F Y') }}</div></div>
      <div class="memo-row"><div class="lbl">Subject</div><div class="val">Project Status Report — {{ $project->title }}</div></div>
      <div class="memo-row"><div class="lbl">Status</div><div class="val">
        <span class="badge" style="background:{{ $statusColors[$project->status] ?? '#F3F4F6' }};color:{{ $statusTextColors[$project->status] ?? '#374151' }};">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
        <span class="badge" style="background:#EDE9DD;color:#1C2321;margin-left:6px;">{{ ucfirst($project->priority) }} Priority</span>
      </div></div>
    </div>

    {{-- Summary Stats --}}
    <div class="stat-row">
      <div class="stat-card"><div class="num">{{ $taskTotal }}</div><div class="lbl">Total Tasks</div></div>
      <div class="stat-card"><div class="num">{{ $bugTotal }}</div><div class="lbl">Total Bugs</div></div>
      <div class="stat-card"><div class="num">{{ number_format($totalHours, 0) }}</div><div class="lbl">Hours Logged</div></div>
      <div class="stat-card"><div class="num">{{ $project->employees->count() }}</div><div class="lbl">Staff Assigned</div></div>
      <div class="stat-card"><div class="num">{{ $project->progress }}%</div><div class="lbl">Complete</div></div>
    </div>

    {{-- Project Details --}}
    <div class="section">
      <div class="section-title">1. Project Details</div>
      <table>
        <tr><td style="width:18%;font-weight:600;color:#0F3D3E;">Project Title</td><td>{{ $project->title }}</td>
            <td style="width:18%;font-weight:600;color:#0F3D3E;">Project No.</td><td style="width:20%;font-family:'JetBrains Mono',monospace;font-size:11px;">{{ $project->project_number }}</td></tr>
        <tr><td style="font-weight:600;color:#0F3D3E;">Manager</td><td>{{ $project->manager?->name ?? 'N/A' }}</td>
            <td style="font-weight:600;color:#0F3D3E;">Priority</td><td>{{ ucfirst($project->priority) }}</td></tr>
        <tr><td style="font-weight:600;color:#0F3D3E;">Start Date</td><td>{{ $project->start_date?->format('d M Y') ?? 'N/A' }}</td>
            <td style="font-weight:600;color:#0F3D3E;">Due Date</td><td>{{ $project->due_date?->format('d M Y') ?? 'N/A' }}</td></tr>
        <tr><td style="font-weight:600;color:#0F3D3E;">Budget</td><td>TZS {{ number_format($project->budget, 0) }}</td>
            <td style="font-weight:600;color:#0F3D3E;">Status</td><td>{{ ucfirst(str_replace('_', ' ', $project->status)) }}</td></tr>
        <tr><td style="font-weight:600;color:#0F3D3E;">Invoicing</td><td>{{ $project->recurring_invoicing ? 'Recurring' : 'One-Time / Manual' }}</td>
            <td style="font-weight:600;color:#0F3D3E;">Progress</td><td>{{ $project->progress }}%</td></tr>
        @if($project->description)
        <tr><td style="font-weight:600;color:#0F3D3E;">Description</td><td colspan="3">{{ $project->description }}</td></tr>
        @endif
      </table>
      <div class="progress-bar" style="margin-top:12px;"><div class="progress-fill" style="width:{{ $project->progress }}%"></div></div>
      <div class="progress-label">{{ $project->progress }}% Complete</div>
    </div>

    {{-- Charts --}}
    <div class="section">
      <div class="section-title">2. Visual Analytics</div>
      <div class="chart-row">
        {{-- Task Status Pie Chart --}}
        @if($taskTotal > 0)
        <div class="chart-box">
          <div class="chart-title">Task Status Distribution</div>
          @php
            $segments = [
              ['label'=>'Completed','count'=>$taskDone,'color'=>'#10B981'],
              ['label'=>'In Progress','count'=>$taskProgress,'color'=>'#F59E0B'],
              ['label'=>'Pending','count'=>$taskPending,'color'=>'#9CA3AF'],
            ];
            $total = max($taskTotal, 1);
            $startAngle = 0;
            $radius = 60;
            $cx = 80; $cy = 80;
          @endphp
          <svg width="160" height="160" style="display:block;margin:0 auto;">
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $radius }}" fill="#EDE9DD"/>
            @foreach($segments as $seg)
              @php
                $pct = $seg['count'] / $total;
                $angle = $pct * 360;
                $endAngle = $startAngle + $angle;
                $rad1 = deg2rad($startAngle);
                $rad2 = deg2rad($endAngle);
                $x1 = $cx + $radius * cos($rad1 - M_PI/2);
                $y1 = $cy + $radius * sin($rad1 - M_PI/2);
                $x2 = $cx + $radius * cos($rad2 - M_PI/2);
                $y2 = $cy + $radius * sin($rad2 - M_PI/2);
                $largeArc = $angle > 180 ? 1 : 0;
                $startAngle = $endAngle;
              @endphp
              @if($seg['count'] > 0)
              <path d="M{{ $cx }},{{ $cy }} L{{ $x1 }},{{ $y1 }} A{{ $radius }},{{ $radius }} 0 {{ $largeArc }},1 {{ $x2 }},{{ $y2 }} Z" fill="{{ $seg['color'] }}"/>
              @endif
            @endforeach
            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="28" fill="#fff"/>
            <text x="{{ $cx }}" y="{{ $cy-2 }}" text-anchor="middle" font-size="20" font-weight="700" fill="#0F3D3E" font-family="Fraunces,serif">{{ $taskTotal }}</text>
            <text x="{{ $cx }}" y="{{ $cy+12 }}" text-anchor="middle" font-size="8" fill="#6E7570">TASKS</text>
          </svg>
          <div class="chart-legend">
            @foreach($segments as $seg)
            <span><span class="dot" style="background:{{ $seg['color'] }}"></span>{{ $seg['label'] }} ({{ $seg['count'] }})</span>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Hours Bar Chart --}}
        @if($totalHours > 0)
        <div class="chart-box">
          <div class="chart-title">Hours by Employee</div>
          @php
            $empHours = $project->timesheets->groupBy('employee_id')->map(function($g) {
              return ['name' => ($g->first()->employee?->first_name . ' ' . substr($g->first()->employee?->last_name ?? '', 0, 1)) ?? 'Unknown',
                      'hours' => $g->sum('hours')];
            })->sortByDesc('hours')->take(6);
            $maxHours = $empHours->max('hours') ?: 1;
            $barColors = ['#0F3D3E','#C9A227','#1E40AF','#065F46','#8C5E2A','#374151'];
          @endphp
          <div class="bar-chart">
            @foreach($empHours as $idx => $eh)
            <div class="bar-col">
              <div class="bar-value">{{ $eh['hours'] }}h</div>
              <div class="bar" style="height:{{ max(($eh['hours']/$maxHours)*100, 3) }}%;background:{{ $barColors[$idx % count($barColors)] }};"></div>
              <div class="bar-label">{{ substr($eh['name'], 0, 8) }}</div>
            </div>
            @endforeach
          </div>
        </div>
        @endif
      </div>

      {{-- Budget vs Cost Chart --}}
      @if($project->budget > 0 || $totalBonus > 0 || $invoiceTotal > 0)
      <div class="chart-box" style="margin-top:12px;">
        <div class="chart-title">Financial Overview (TZS)</div>
        @php
          $finData = [
            ['label'=>'Budget','value'=>$project->budget ?? 0,'color'=>'#0F3D3E'],
            ['label'=>'Invoiced','value'=>$invoiceTotal,'color'=>'#C9A227'],
            ['label'=>'Collected','value'=>$invoicePaid,'color'=>'#10B981'],
            ['label'=>'Bonuses','value'=>$totalBonus,'color'=>'#8C5E2A'],
          ];
          $maxFin = max(array_column($finData, 'value'), 1);
        @endphp
        <div class="bar-chart" style="height:100px;">
          @foreach($finData as $fd)
          <div class="bar-col">
            <div class="bar-value">{{ number_format($fd['value']/1000000, 1) }}M</div>
            <div class="bar" style="height:{{ max(($fd['value']/$maxFin)*80, 3) }}%;background:{{ $fd['color'] }};"></div>
            <div class="bar-label">{{ $fd['label'] }}</div>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    {{-- Staff Assignments --}}
    @if($project->employees->count() > 0)
    <div class="section">
      <div class="section-title">3. Assigned Staff</div>
      <table>
        <thead><tr><th>#</th><th>Name</th><th>Role</th><th>Department</th><th>Status</th></tr></thead>
        <tbody>
          @foreach($project->employees as $idx => $emp)
          <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
            <td>{{ $emp->pivot->role ?? 'N/A' }}</td>
            <td>{{ $emp->department?->name ?? 'N/A' }}</td>
            <td><span class="badge" style="background:{{ $emp->pivot->is_active ? '#D1FAE5' : '#FEE2E2' }};color:{{ $emp->pivot->is_active ? '#065F46' : '#991B1B' }};">{{ $emp->pivot->is_active ? 'Active' : 'Inactive' }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Tasks --}}
    @if($taskTotal > 0)
    <div class="section">
      <div class="section-title">4. Task Breakdown</div>
      <table>
        <thead><tr><th>#</th><th>Task</th><th>Assigned To</th><th>Priority</th><th>Status</th><th>Due Date</th></tr></thead>
        <tbody>
          @foreach($project->tasks as $idx => $task)
          <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $task->title }}</td>
            <td>{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
            <td>{{ ucfirst($task->priority) }}</td>
            <td><span class="status-dot" style="background:{{ $task->status=='done'||$task->status=='completed'?'#10B981':($task->status=='in_progress'?'#F59E0B':'#9CA3AF') }}"></span>{{ ucfirst(str_replace('_',' ',$task->status)) }}</td>
            <td>{{ $task->due_date?->format('d M Y') ?? 'N/A' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Timesheets --}}
    @if($totalHours > 0)
    <div class="section">
      <div class="section-title">5. Timesheet Summary ({{ number_format($totalHours, 1) }} total hours)</div>
      <table>
        <thead><tr><th>#</th><th>Employee</th><th>Description</th><th>Date</th><th>Hours</th></tr></thead>
        <tbody>
          @foreach($project->timesheets->take(10) as $idx => $ts)
          <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $ts->employee?->first_name }} {{ $ts->employee?->last_name }}</td>
            <td>{{ $ts->description ?? 'Work entry' }}</td>
            <td>{{ $ts->date?->format('d M Y') ?? 'N/A' }}</td>
            <td style="font-family:'JetBrains Mono',monospace;font-weight:600;color:#0F3D3E;">{{ $ts->hours }}h</td>
          </tr>
          @endforeach
          @if($project->timesheets->count() > 10)
          <tr><td colspan="5" style="text-align:center;color:#6E7570;font-size:11px;">+{{ $project->timesheets->count() - 10 }} more entries</td></tr>
          @endif
        </tbody>
      </table>
    </div>
    @endif

    {{-- Invoices --}}
    @if($project->invoices->count() > 0)
    <div class="section">
      <div class="section-title">6. Invoices</div>
      <table>
        <thead><tr><th>#</th><th>Invoice No.</th><th>Date</th><th>Amount</th><th>Paid</th><th>Status</th></tr></thead>
        <tbody>
          @foreach($project->invoices as $idx => $inv)
          <tr>
            <td>{{ $idx + 1 }}</td>
            <td style="font-family:'JetBrains Mono',monospace;font-size:11px;">{{ $inv->invoice_number }}</td>
            <td>{{ $inv->invoice_date?->format('d M Y') ?? 'N/A' }}</td>
            <td>TZS {{ number_format($inv->total_amount, 0) }}</td>
            <td>TZS {{ number_format($inv->paid_amount, 0) }}</td>
            <td><span class="badge" style="background:{{ $inv->status=='paid'?'#D1FAE5':($inv->status=='overdue'?'#FEE2E2':'#FEF3C7') }};color:{{ $inv->status=='paid'?'#065F46':($inv->status=='overdue'?'#991B1B':'#92400E') }};">{{ ucfirst($inv->status) }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif

    {{-- Deal --}}
    @if($project->deal)
    <div class="section">
      <div class="section-title">7. Originating Deal</div>
      <table>
        <tr><td style="width:18%;font-weight:600;color:#0F3D3E;">Deal Title</td><td>{{ $project->deal->title }}</td>
            <td style="width:18%;font-weight:600;color:#0F3D3E;">Deal No.</td><td>{{ $project->deal->deal_number }}</td></tr>
        <tr><td style="font-weight:600;color:#0F3D3E;">Value</td><td>TZS {{ number_format($project->deal->value, 0) }}</td>
            <td style="font-weight:600;color:#0F3D3E;">Stage</td><td>{{ ucfirst(str_replace('_',' ',$project->deal->stage)) }}</td></tr>
      </table>
    </div>
    @endif

    {{-- Footer with signatures --}}
    <div class="footer">
      <div style="width:33%;"></div>
      <div class="sig">
        <div class="sig-line"></div>
        <div class="sig-name">{{ $project->manager?->name ?? 'Project Manager' }}</div>
        <div class="sig-title">Project Manager</div>
      </div>
      <div style="width:33%;text-align:right;">
        <div>Generated by {{ $coName }} ERP System</div>
        <div>{{ now()->format('d M Y H:i') }}</div>
      </div>
    </div>
  </div>
</body>
</html>
