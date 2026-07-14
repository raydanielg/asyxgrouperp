@php
    $roleLabels = [
        'superadmin' => 'Super Admin',
        'admin' => 'System Admin',
        'director' => 'Director',
        'accountant' => 'Accountant',
        'finance_manager' => 'Finance Manager',
        'procurement_manager' => 'Procurement Manager',
        'sales_manager' => 'Sales Manager',
        'project_manager' => 'Project Manager',
        'technical_manager' => 'Technical Manager',
        'operations_manager' => 'Operations Manager',
        'hr_manager' => 'HR Manager',
    ];
    $roleLabel = $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    $colorMap = [
        'emerald' => ['from' => 'from-emerald-600', 'to' => 'to-emerald-700', 'border' => 'border-emerald-500', 'text' => 'text-emerald-100', 'sub' => 'text-emerald-200', 'soft' => 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100'],
        'sky' => ['from' => 'from-sky-500', 'to' => 'to-sky-600', 'border' => 'border-sky-400', 'text' => 'text-sky-100', 'sub' => 'text-sky-200', 'soft' => 'bg-sky-50 text-sky-700 border-sky-100 hover:bg-sky-100'],
        'amber' => ['from' => 'from-amber-400', 'to' => 'to-amber-500', 'border' => 'border-amber-300', 'text' => 'text-amber-50', 'sub' => 'text-amber-100', 'soft' => 'bg-amber-50 text-amber-700 border-amber-100 hover:bg-amber-100'],
        'rose' => ['from' => 'from-rose-500', 'to' => 'to-rose-600', 'border' => 'border-rose-400', 'text' => 'text-rose-100', 'sub' => 'text-rose-200', 'soft' => 'bg-rose-50 text-rose-700 border-rose-100 hover:bg-rose-100'],
        'violet' => ['from' => 'from-violet-500', 'to' => 'to-violet-600', 'border' => 'border-violet-400', 'text' => 'text-violet-100', 'sub' => 'text-violet-200', 'soft' => 'bg-violet-50 text-violet-700 border-violet-100 hover:bg-violet-100'],
    ];

    $createRoutes = [
        'sales-invoices' => ['label' => 'New Invoice', 'route' => route('role.page', ['module' => 'sales-invoices']), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'purchase-invoices' => ['label' => 'New Purchase', 'route' => route('role.page', ['module' => 'purchase-invoices']), 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        'expenses' => ['label' => 'New Expense', 'route' => route('role.page', ['module' => 'expenses']), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        'revenues' => ['label' => 'New Revenue', 'route' => route('role.page', ['module' => 'revenues']), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8'],
        'leads' => ['label' => 'New Lead', 'route' => route('role.page', ['module' => 'leads']), 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        'tickets' => ['label' => 'New Ticket', 'route' => route('role.page', ['module' => 'tickets']), 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'projects' => ['label' => 'New Project', 'route' => route('role.page', ['module' => 'projects']), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2'],
        'products' => ['label' => 'New Product', 'route' => route('role.page', ['module' => 'products']), 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        'employees' => ['label' => 'New Employee', 'route' => route('role.page', ['module' => 'employees']), 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        'leaves' => ['label' => 'Request Leave', 'route' => route('role.page', ['module' => 'leaves']), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        'attendance' => ['label' => 'Mark Attendance', 'route' => route('role.page', ['module' => 'attendance']), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
        'deals' => ['label' => 'New Deal', 'route' => route('role.page', ['module' => 'deals']), 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        'pos' => ['label' => 'Open POS', 'route' => route('role.page', ['module' => 'pos']), 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17'],
        'contacts' => ['label' => 'New Contact', 'route' => route('role.page', ['module' => 'contacts']), 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        'transfers' => ['label' => 'New Transfer', 'route' => route('role.page', ['module' => 'transfers']), 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
        'warehouses' => ['label' => 'New Warehouse', 'route' => route('role.page', ['module' => 'warehouses']), 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        'contracts' => ['label' => 'New Contract', 'route' => route('role.page', ['module' => 'contracts']), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];
@endphp

{{-- Welcome Banner --}}
@php $hour = now()->hour; $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening'); @endphp
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-48 h-48 bg-white/5 rounded-full -mr-24 -mt-24"></div>
    <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-12 -mb-12"></div>
    <div class="absolute top-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mt-12"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xl border border-white/20">
                {{ strtoupper(substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $greeting }}, {{ auth()->user()->first_name ?? auth()->user()->name }}</h2>
                <p class="text-emerald-100 text-sm mt-0.5">{{ $roleLabel }} Dashboard</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-emerald-100 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-emerald-200 text-lg font-mono font-bold mt-1 tracking-wider" id="liveClock">{{ now()->format('H:i:s') }}</p>
        </div>
    </div>
</div>
<script>
setInterval(function() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    const el = document.getElementById('liveClock');
    if (el) el.textContent = h + ':' + m + ':' + s;
}, 1000);
</script>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach($kpiCards as $card)
    @php $c = $colorMap[$card['color']] ?? $colorMap['emerald']; @endphp
    <div class="bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }} rounded-xl border {{ $c['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full -mr-10 -mt-10"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $c['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $c['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Chart + Quick Actions --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">{{ $chartData['title'] ?? 'Activity' }}</h3>
        <canvas id="roleChart" height="120"></canvas>
    </div>
    <div class="bg-white rounded-xl border p-5 flex flex-col">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Quick Actions
        </h3>
        <div class="space-y-2 flex-1">
            @forelse($quickActions as $action)
            <a href="{{ route($action['route'], $action['params'] ?? []) }}" class="flex items-center gap-3 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors border border-emerald-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
                {{ $action['label'] }}
            </a>
            @empty
            <p class="text-xs text-gray-400">No quick actions available.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Secondary KPIs --}}
@if(!empty($secondaryKpis))
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-6">
    @foreach($secondaryKpis as $kpi)
    <a href="{{ route($kpi['route'], $kpi['params'] ?? []) }}" class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <span class="text-[10px] font-medium text-gray-500">{{ $kpi['label'] }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $kpi['value'] }}</p>
    </a>
    @endforeach
</div>
@endif

{{-- Finance Salary Advance Widget --}}
@if(in_array($role, ['accountant', 'finance_manager']))
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white">
        <span class="text-[10px] font-medium text-amber-50">Pending Salary Advances</span>
        <p class="text-xl font-bold mt-1">{{ $salaryAdvancePending ?? 0 }}</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl border border-emerald-400 p-4 text-white">
        <span class="text-[10px] font-medium text-emerald-100">Approved Salary Advances</span>
        <p class="text-xl font-bold mt-1">{{ $salaryAdvanceApproved ?? 0 }}</p>
    </div>
    <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl border border-rose-400 p-4 text-white">
        <span class="text-[10px] font-medium text-rose-100">Total Advance Amount</span>
        <p class="text-xl font-bold mt-1">{{ $money($salaryAdvanceTotal ?? 0) }}</p>
    </div>
</div>
@endif

{{-- Recent Items Widgets --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    @if(!empty($recentItems['recentSales']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Sales Invoices</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'sales-invoices']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach(collect($recentItems['recentSales'])->take(5) as $invoice)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $invoice->invoice_number }}</p>
                    <p class="text-[10px] text-gray-400">{{ $invoice->customer?->name ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($invoice->total_amount) }}</p>
                    <p class="text-[10px] text-gray-400">{{ $invoice->invoice_date->format('d M Y') }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['sales-invoices']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['sales-invoices']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['sales-invoices']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentUsers']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Users</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'users']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentUsers']->take(5) as $user)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <div>
                        <p class="text-xs font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
                <span class="text-[10px] text-gray-400">{{ $user->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($recentItems['recentTickets']) || !empty($recentItems['openTickets']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Support Tickets</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'tickets']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach(($recentItems['recentTickets'] ?? $recentItems['openTickets'] ?? collect())->take(5) as $ticket)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? $ticket->title ?? 'Ticket #' . $ticket->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                    @if(($ticket->status ?? '') === 'open') bg-rose-50 text-rose-700
                    @elseif(($ticket->status ?? '') === 'in_progress') bg-amber-50 text-amber-700
                    @elseif(($ticket->status ?? '') === 'resolved') bg-emerald-50 text-emerald-700
                    @else bg-gray-50 text-gray-700 @endif">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status ?? 'unknown')) }}
                </span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['tickets']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['tickets']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['tickets']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentLeads']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Leads</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'leads']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentLeads']->take(5) as $lead)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $lead->name ?? $lead->company_name ?? 'N/A' }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst($lead->status ?? '') }}</p>
                </div>
                <span class="text-[10px] text-gray-400">{{ $lead->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['leads']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['leads']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['leads']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentEmployees']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center text-violet-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Employees</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'employees']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentEmployees']->take(5) as $emp)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $emp->position ?? $emp->department ?? '' }}</p>
                </div>
                <span class="text-[10px] text-gray-400">{{ $emp->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['employees']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['employees']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['employees']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['pendingLeaves']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Pending Leave Requests</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'leaves']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['pendingLeaves']->take(5) as $leave)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $leave->employee?->first_name ?? '' }} {{ $leave->employee?->last_name ?? '' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $leave->leave_type ?? '' }} - {{ $leave->start_date?->format('d M') ?? '' }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">Pending</span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['leaves']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['leaves']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['leaves']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['activeProjects']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Active Projects</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'projects']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['activeProjects']->take(5) as $project)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $project->name }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</p>
                </div>
                <span class="text-[10px] text-gray-400">{{ $project->due_date?->format('d M Y') ?? '' }}</span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['projects']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['projects']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['projects']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['lowStockProducts']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Low Stock Products</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'products']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['lowStockProducts']->take(5) as $product)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $product->name }}</p>
                    <p class="text-[10px] text-gray-400">Stock: {{ $product->stock_quantity }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-50 text-rose-700">Low</span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['products']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['products']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['products']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentExpenses']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Expenses</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'expenses']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentExpenses']->take(5) as $expense)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $expense->description ?? $expense->category ?? 'Expense' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $expense->expense_date?->format('d M Y') ?? '' }}</p>
                </div>
                <p class="text-xs font-semibold text-red-600">TZS {{ number_format($expense->amount) }}</p>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['expenses']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['expenses']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['expenses']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentRevenues']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Revenues</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'revenues']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentRevenues']->take(5) as $revenue)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $revenue->description ?? $revenue->category ?? 'Revenue' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $revenue->revenue_date?->format('d M Y') ?? '' }}</p>
                </div>
                <p class="text-xs font-semibold text-emerald-600">TZS {{ number_format($revenue->amount) }}</p>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['revenues']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['revenues']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['revenues']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentPurchases']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Purchases</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'purchase-invoices']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentPurchases']->take(5) as $purchase)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $purchase->invoice_number }}</p>
                    <p class="text-[10px] text-gray-400">{{ $purchase->vendor?->name ?? 'N/A' }}</p>
                </div>
                <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($purchase->total_amount) }}</p>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['purchase-invoices']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['purchase-invoices']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['purchase-invoices']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentSales']) && in_array($role, ['admin', 'superadmin', 'director']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent POS Sales</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'pos']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentSales']->take(5) as $sale)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">Sale #{{ $sale->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ $sale->created_at->format('d M Y H:i') }}</p>
                </div>
                <p class="text-xs font-semibold text-emerald-600">TZS {{ number_format($sale->total_amount) }}</p>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['pos']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['pos']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['pos']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentAttendance']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center text-violet-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Today's Attendance</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'attendance']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentAttendance']->take(5) as $att)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $att->employee?->first_name ?? '' }} {{ $att->employee?->last_name ?? '' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $att->date?->format('d M Y') ?? '' }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                    @if($att->status === 'present') bg-emerald-50 text-emerald-700
                    @elseif($att->status === 'absent') bg-rose-50 text-rose-700
                    @else bg-amber-50 text-amber-700 @endif">
                    {{ ucfirst($att->status) }}
                </span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['attendance']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['attendance']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['attendance']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['myTickets']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">My Assigned Tickets</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'tickets']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['myTickets']->take(5) as $ticket)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? $ticket->title ?? 'Ticket #' . $ticket->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</p>
                </div>
                <span class="text-[10px] text-gray-400">{{ $ticket->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['tickets']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['tickets']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['tickets']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['openDeals']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Open Deals</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'deals']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['openDeals']->take(5) as $deal)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $deal->title ?? 'Deal #' . $deal->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst($deal->status ?? '') }}</p>
                </div>
                <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($deal->value ?? 0) }}</p>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['deals']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['deals']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['deals']['label'] }}
            </a>
        </div>
        @endif
    </div>
    @endif

    @if(!empty($recentItems['recentTransfers']))
    <div class="bg-white rounded-xl border overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Recent Transfers</h3>
            </div>
            <a href="{{ route('role.page', ['module' => 'transfers']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        <div class="divide-y divide-gray-100 flex-1">
            @foreach($recentItems['recentTransfers']->take(5) as $transfer)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">Transfer #{{ $transfer->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst($transfer->status ?? '') }}</p>
                </div>
                <span class="text-[10px] text-gray-400">{{ $transfer->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
        @if(!empty($createRoutes['transfers']))
        <div class="px-5 py-3 border-t bg-gray-50/50">
            <a href="{{ $createRoutes['transfers']['route'] }}" class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700 hover:text-emerald-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createRoutes['transfers']['label'] }}
            </a>
        </div>
        @endif
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
