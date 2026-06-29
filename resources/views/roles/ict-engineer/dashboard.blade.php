@extends('layouts.admin')
@section('title', 'ICT Engineer Dashboard')
@section('page_title', 'ICT Engineer Dashboard')
@section('content')
@php $money = fn($n) => 'TZS ' . number_format($n); @endphp
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div><h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2><p class="text-emerald-100 text-sm mt-1">ICT Engineer Dashboard</p></div>
        <div class="text-right"><p class="text-emerald-100 text-xs">{{ now()->format('l, d M Y') }}</p><p class="text-emerald-200 text-[10px] mt-1">{{ now()->format('H:i') }}</p></div>
    </div>
</div>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @foreach($kpiCards as $card)
@php $colors = ['emerald' => 'from-emerald-600 to-emerald-700 border-emerald-500', 'sky' => 'from-sky-500 to-sky-600 border-sky-400', 'amber' => 'from-amber-400 to-amber-500 border-amber-300', 'rose' => 'from-rose-500 to-rose-600 border-rose-400', 'violet' => 'from-violet-500 to-violet-600 border-violet-400']; $cc = $colors[$card['color']] ?? $colors['emerald']; @endphp
    <div class="bg-gradient-to-br {{ $cc }} rounded-xl border p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium text-white/80">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
        </div>
    </div>
        @endforeach
        </div>
@include('roles.shared.ai-insights')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">{{ $chartData['title'] }}</h3>
        <canvas id="roleChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
        @foreach($quickActions as $action)
        <a href="{{ route($action['route']) }}" class="flex items-center gap-3 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
                {{ $action['label'] }}
            </a>
        @endforeach
        </div>
    </div>
</div>
        @if(!empty($secondaryKpis))
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
        @foreach($secondaryKpis as $kpi)
        <a href="{{ route($kpi['route']) }}" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <span class="text-[10px] font-medium text-gray-500">{{ $kpi['label'] }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $kpi['value'] }}</p>
    </a>
        @endforeach
        </div>
        @endif
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(!empty($recentItems['recentSales']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Sales</h3>
            <a href="{{ route('admin.sales-invoices.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentSales']->take(5) as $invoice)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $invoice->invoice_number }}</p><p class="text-[10px] text-gray-400">{{ $invoice->customer?->name ?? 'N/A' }}</p></div>
                <div class="text-right"><p class="text-xs font-semibold text-gray-900">TZS {{ number_format($invoice->total_amount) }}</p><p class="text-[10px] text-gray-400">{{ $invoice->invoice_date->format('d M Y') }}</p></div>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentUsers']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Users</h3>
            <a href="{{ route('admin.users.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentUsers']->take(5) as $user)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div><p class="text-xs font-medium text-gray-900">{{ $user->name }}</p><p class="text-[10px] text-gray-400">{{ $user->email }}</p></div>
                </div>
                <span class="text-[10px] text-gray-400">{{ $user->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentTickets']) || !empty($recentItems['openTickets']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Support Tickets</h3>
            <a href="{{ route('admin.helpdesk-tickets.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach(($recentItems['recentTickets'] ?? $recentItems['openTickets'] ?? collect())->take(5) as $ticket)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? $ticket->title ?? 'Ticket #' . $ticket->id }}</p><p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $ticket->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentLeads']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Leads</h3>
            <a href="{{ route('admin.crm-leads.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentLeads']->take(5) as $lead)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $lead->name ?? $lead->company_name ?? 'N/A' }}</p><p class="text-[10px] text-gray-400">{{ ucfirst($lead->status ?? '') }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $lead->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentEmployees']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Employees</h3>
            <a href="{{ route('admin.employees.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentEmployees']->take(5) as $emp)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</p><p class="text-[10px] text-gray-400">{{ $emp->position ?? $emp->department ?? '' }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $emp->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['pendingLeaves']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Pending Leave Requests</h3>
            <a href="{{ route('admin.leaves.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['pendingLeaves']->take(5) as $leave)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $leave->employee?->first_name ?? '' }} {{ $leave->employee?->last_name ?? '' }}</p><p class="text-[10px] text-gray-400">{{ $leave->leave_type ?? '' }}</p></div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">Pending</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['activeProjects']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Active Projects</h3>
            <a href="{{ route('admin.projects.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['activeProjects']->take(5) as $project)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $project->name }}</p><p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $project->due_date?->format('d M Y') ?? '' }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['lowStockProducts']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Low Stock Products</h3>
            <a href="{{ route('admin.products.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['lowStockProducts']->take(5) as $product)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $product->name }}</p><p class="text-[10px] text-gray-400">Stock: {{ $product->stock_quantity }}</p></div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700">Low</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentExpenses']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Expenses</h3>
            <a href="{{ route('admin.expenses.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentExpenses']->take(5) as $expense)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $expense->description ?? $expense->category ?? 'Expense' }}</p><p class="text-[10px] text-gray-400">{{ $expense->expense_date?->format('d M Y') ?? '' }}</p></div>
                <p class="text-xs font-semibold text-red-600">TZS {{ number_format($expense->amount) }}</p>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentRevenues']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Revenues</h3>
            <a href="{{ route('admin.revenues.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentRevenues']->take(5) as $revenue)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $revenue->description ?? $revenue->category ?? 'Revenue' }}</p><p class="text-[10px] text-gray-400">{{ $revenue->revenue_date?->format('d M Y') ?? '' }}</p></div>
                <p class="text-xs font-semibold text-emerald-600">TZS {{ number_format($revenue->amount) }}</p>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentPurchases']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Purchases</h3>
            <a href="{{ route('admin.purchase-invoices.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentPurchases']->take(5) as $purchase)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $purchase->invoice_number }}</p><p class="text-[10px] text-gray-400">{{ $purchase->vendor?->name ?? 'N/A' }}</p></div>
                <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($purchase->total_amount) }}</p>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['myTickets']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">My Assigned Tickets</h3>
            <a href="{{ route('admin.helpdesk-tickets.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['myTickets']->take(5) as $ticket)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? $ticket->title ?? 'Ticket #' . $ticket->id }}</p><p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $ticket->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['openDeals']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Open Deals</h3>
            <a href="{{ route('admin.crm-deals.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['openDeals']->take(5) as $deal)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $deal->title ?? 'Deal #' . $deal->id }}</p><p class="text-[10px] text-gray-400">{{ ucfirst($deal->status ?? '') }}</p></div>
                <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($deal->value ?? 0) }}</p>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentTransfers']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Transfers</h3>
            <a href="{{ route('admin.transfers.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentTransfers']->take(5) as $transfer)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">Transfer #{{ $transfer->id }}</p><p class="text-[10px] text-gray-400">{{ ucfirst($transfer->status ?? '') }}</p></div>
                <span class="text-[10px] text-gray-400">{{ $transfer->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
@if(!empty($recentItems['recentAttendance']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Today's Attendance</h3>
            <a href="{{ route('admin.attendance.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentAttendance']->take(5) as $att)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div><p class="text-xs font-medium text-gray-900">{{ $att->employee?->first_name ?? '' }} {{ $att->employee?->last_name ?? '' }}</p><p class="text-[10px] text-gray-400">{{ $att->date?->format('d M Y') ?? '' }}</p></div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($att->status === 'present') ? 'bg-emerald-50 text-emerald-700' : (($att->status === 'absent') ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($att->status) }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('roleChart');
if (ctx) {
    const hasSecondary = @json(!empty($chartData['secondaryValues']));
    const datasets = [{ label: @json($chartData['title'] ?? 'Activity'), data: @json($chartData['values']), backgroundColor: '#024938', borderRadius: 4 }];
    if (hasSecondary) {
        datasets.push({ label: @json($chartData['secondaryTitle'] ?? 'Secondary'), data: @json($chartData['secondaryValues']), backgroundColor: '#f9ac00', borderRadius: 4 });
    }
    new Chart(ctx, {
        type: 'bar',
        data: { labels: @json($chartData['labels']), datasets: datasets },
        options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { font: { size: 10 } } } }, scales: { y: { beginAtZero: true, ticks: { font: { size: 9 } } }, x: { ticks: { font: { size: 8 } } } } }
    });
}
</script>
@endsection