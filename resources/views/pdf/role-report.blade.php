<!DOCTYPE html>
<html><head><meta charset="utf-8">
<title>{{ $roleLabel }} Report - {{ config('app.name') }}</title>
<style>
@page { margin: 20mm 15mm; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1f2937; margin:0; padding:0; }
.watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 72pt; color: rgba(1, 73, 56, 0.04); font-weight: 900; pointer-events: none; z-index: -1; letter-spacing: 8px; }
.header { background: linear-gradient(135deg, #0F3D3E 0%, #024938 100%); color: #fff; padding: 20px 25px; border-radius: 6px; margin-bottom: 20px; }
.header .logo { width: 40px; height: 40px; float: left; margin-right: 12px; }
.header h1 { margin:0; font-size: 16pt; font-weight: 800; letter-spacing: 0.5px; }
.header .subtitle { font-size: 8pt; opacity: 0.8; margin-top: 3px; }
.header .badge { background: rgba(201, 162, 39, 0.25); color: #C9A227; font-size: 7pt; font-weight: 700; padding: 2px 8px; border-radius: 10px; float: right; margin-top: 6px; }
.section-title { font-size: 10pt; font-weight: 800; color: #0F3D3E; border-bottom: 2px solid #C9A227; padding-bottom: 4px; margin: 18px 0 10px; text-transform: uppercase; letter-spacing: 0.5px; }
.kpi-grid { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
.kpi-box { flex: 1 0 22%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 10px; text-align: center; }
.kpi-box .kpi-label { font-size: 6.5pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.3px; }
.kpi-box .kpi-value { font-size: 11pt; font-weight: 800; color: #0F3D3E; margin-top: 2px; }
table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 8pt; }
th { background: #0F3D3E; color: #fff; font-weight: 600; padding: 6px 8px; text-align: left; font-size: 7pt; text-transform: uppercase; letter-spacing: 0.3px; }
td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
tr:nth-child(even) td { background: #f8fafc; }
.footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 6.5pt; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; }
.summary-row { background: #f0fdf4 !important; font-weight: 700; }
.summary-row td { border-top: 2px solid #0F3D3E; }
.text-right { text-align: right; }
.text-muted { color: #64748b; font-size: 7pt; }
.mb-2 { margin-bottom: 8px; }
</style>
</head><body>
<div class="watermark">{{ config('app.name', 'ASYX GROUP') }}</div>

<div class="header">
  <img class="logo" src="{{ public_path('asyxgrouplogo.png') }}" alt="Logo">
  <div class="badge">{{ $roleLabel }}</div>
  <h1>{{ config('app.name', 'ASYX Group') }}</h1>
  <div class="subtitle">{{ $roleLabel }} Report &mdash; Generated {{ now()->format('d F Y') }}</div>
</div>

{{-- KPI Cards --}}
<div class="kpi-grid">
  @foreach($kpiCards as $kpi)
  <div class="kpi-box">
    <div class="kpi-label">{{ $kpi['label'] }}</div>
    <div class="kpi-value">{{ $kpi['value'] }}</div>
  </div>
  @endforeach
</div>

{{-- Role-Specific Sections --}}
@if(in_array($role, ['admin','administrator','admin_manager','director']))
  <div class="section-title">Recent Sales Invoices</div>
  <table>
    <tr><th>#</th><th>Customer</th><th>Date</th><th class="text-right">Amount</th><th class="text-right">Balance</th><th>Status</th></tr>
    @forelse(($recentSales ?? []) as $i)
    <tr>
      <td>{{ $i->invoice_number ?? $i->id }}</td>
      <td>{{ $i->customer?->name ?? $i->customer_name ?? 'N/A' }}</td>
      <td>{{ ($i->invoice_date ?? $i->created_at)->format('d M Y') }}</td>
      <td class="text-right">{{ number_format($i->total_amount ?? 0, 2) }}</td>
      <td class="text-right">{{ number_format($i->balance_amount ?? 0, 2) }}</td>
      <td><span>{{ ucfirst($i->status ?? 'draft') }}</span></td>
    </tr>
    @empty <tr><td colspan="6" class="text-muted">No recent invoices</td></tr>
    @endforelse
  </table>
@endif

@if(in_array($role, ['admin','administrator','admin_manager','director','technical_manager','ict_officer','ict_engineer']))
  <div class="section-title">Open Support Tickets</div>
  <table>
    <tr><th>#</th><th>Subject</th><th>Priority</th><th>Status</th><th>Created</th></tr>
    @forelse(($recentTickets ?? []) as $t)
    <tr>
      <td>{{ $t->id }}</td>
      <td>{{ $t->subject ?? $t->title }}</td>
      <td><span>{{ ucfirst($t->priority ?? 'normal') }}</span></td>
      <td><span>{{ ucfirst($t->status ?? 'open') }}</span></td>
      <td>{{ $t->created_at->format('d M Y') }}</td>
    </tr>
    @empty <tr><td colspan="5" class="text-muted">No open tickets</td></tr>
    @endforelse
  </table>
@endif

@if(in_array($role, ['finance_officer','auditor']))
  <div class="section-title">Recent Expenses</div>
  <table>
    <tr><th>#</th><th>Category</th><th>Date</th><th class="text-right">Amount</th></tr>
    @forelse(($recentExpenses ?? []) as $e)
    <tr>
      <td>{{ $e->id }}</td>
      <td>{{ $e->category ?? $e->expense_category ?? 'N/A' }}</td>
      <td>{{ ($e->expense_date ?? $e->created_at)->format('d M Y') }}</td>
      <td class="text-right">{{ number_format($e->amount ?? 0, 2) }}</td>
    </tr>
    @empty <tr><td colspan="4" class="text-muted">No recent expenses</td></tr>
    @endforelse
  </table>

  <div class="section-title">Recent Revenues</div>
  <table>
    <tr><th>#</th><th>Source</th><th>Date</th><th class="text-right">Amount</th></tr>
    @forelse(($recentRevenues ?? []) as $r)
    <tr>
      <td>{{ $r->id }}</td>
      <td>{{ $r->source ?? $r->revenue_source ?? 'N/A' }}</td>
      <td>{{ ($r->revenue_date ?? $r->created_at)->format('d M Y') }}</td>
      <td class="text-right">{{ number_format($r->amount ?? 0, 2) }}</td>
    </tr>
    @empty <tr><td colspan="4" class="text-muted">No recent revenues</td></tr>
    @endforelse
  </table>
@endif

@if(in_array($role, ['hr_officer','supervisor']))
  <div class="section-title">Recent Employees</div>
  <table>
    <tr><th>#</th><th>Name</th><th>Department</th><th>Status</th><th>Phone</th></tr>
    @forelse(($recentEmployees ?? []) as $e)
    <tr>
      <td>{{ $e->id }}</td>
      <td>{{ $e->first_name }} {{ $e->last_name }}</td>
      <td>{{ $e->department ?? 'N/A' }}</td>
      <td><span>{{ ucfirst($e->status ?? 'active') }}</span></td>
      <td>{{ $e->phone ?? '—' }}</td>
    </tr>
    @empty <tr><td colspan="5" class="text-muted">No employees found</td></tr>
    @endforelse
  </table>
@endif

@if(in_array($role, ['receptionist','call_center_agent']))
  <div class="section-title">Recent Leads</div>
  <table>
    <tr><th>#</th><th>Name</th><th>Company</th><th>Status</th><th>Assigned To</th></tr>
    @forelse(($recentLeads ?? []) as $l)
    <tr>
      <td>{{ $l->id }}</td>
      <td>{{ $l->first_name }} {{ $l->last_name }}</td>
      <td>{{ $l->company ?? '—' }}</td>
      <td><span>{{ ucfirst($l->status ?? 'new') }}</span></td>
      <td>{{ $l->assignedTo?->name ?? '—' }}</td>
    </tr>
    @empty <tr><td colspan="5" class="text-muted">No recent leads</td></tr>
    @endforelse
  </table>
@endif

@if(in_array($role, ['logistics_officer','operations_manager']))
  <div class="section-title">Low Stock Products</div>
  <table>
    <tr><th>#</th><th>Name</th><th>SKU</th><th class="text-right">Stock</th><th class="text-right">Reorder Level</th></tr>
    @forelse(($lowStockProducts ?? []) as $p)
    <tr>
      <td>{{ $p->id }}</td>
      <td>{{ $p->name }}</td>
      <td>{{ $p->sku ?? '—' }}</td>
      <td class="text-right">{{ $p->stock_quantity ?? 0 }}</td>
      <td class="text-right">{{ $p->reorder_level ?? 0 }}</td>
    </tr>
    @empty <tr><td colspan="5" class="text-muted">All products sufficiently stocked</td></tr>
    @endforelse
  </table>
@endif

@if(in_array($role, ['project_manager']))
  <div class="section-title">Active Projects</div>
  <table>
    <tr><th>#</th><th>Name</th><th>Status</th><th>Budget</th><th>Start Date</th></tr>
    @forelse(($activeProjects ?? []) as $p)
    <tr>
      <td>{{ $p->id }}</td>
      <td>{{ $p->name }}</td>
      <td><span>{{ ucfirst(str_replace('_', ' ', $p->status ?? 'planning')) }}</span></td>
      <td class="text-right">{{ number_format($p->budget ?? 0, 2) }}</td>
      <td>{{ $p->start_date?->format('d M Y') ?? '—' }}</td>
    </tr>
    @empty <tr><td colspan="5" class="text-muted">No active projects</td></tr>
    @endforelse
  </table>
@endif

@if(in_array($role, ['cashier']))
  <div class="section-title">Recent POS Sales</div>
  <table>
    <tr><th>#</th><th>Reference</th><th class="text-right">Amount</th><th>Date</th></tr>
    @forelse(($recentSales ?? []) as $s)
    <tr>
      <td>{{ $s->id }}</td>
      <td>{{ $s->reference ?? $s->pos_number ?? '—' }}</td>
      <td class="text-right">{{ number_format($s->total_amount ?? 0, 2) }}</td>
      <td>{{ $s->created_at->format('d M Y H:i') }}</td>
    </tr>
    @empty <tr><td colspan="4" class="text-muted">No recent POS sales</td></tr>
    @endforelse
  </table>
@endif

<div class="footer">
  {{ config('app.name') }} — {{ $roleLabel }} Report — Page 1/1 — Generated {{ now()->format('d M Y H:i') }}
</div>
</body></html>