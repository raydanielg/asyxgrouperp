@extends('layouts.admin')
@section('title', ucfirst(str_replace('_', ' ', $role)) . ' Dashboard')
@section('page_title', ucfirst(str_replace('_', ' ', $role)) . ' Dashboard')
@section('page_actions')
<a href="{{ route('role.dashboard.report-pdf') }}" onclick="event.preventDefault(); Swal.fire({title:'Generating Report...',text:'Please wait while we prepare your PDF report.',allowOutsideClick:false,allowEscapeKey:false,showConfirmButton:false,willOpen:()=>{Swal.showLoading();fetch('{{ route('role.dashboard.report-pdf') }}').then(r=>r.blob()).then(b=>{const u=URL.createObjectURL(b);const a=document.createElement('a');a.href=u;a.download='role-report-{{ $role }}-{{ now()->format('Ymd') }}.pdf';document.body.appendChild(a);a.click();a.remove();URL.revokeObjectURL(u);Swal.close();}).catch(()=>Swal.fire({icon:'error',title:'Error',text:'Failed to generate PDF',confirmButtonColor:'#024938'}))})" class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white text-xs font-bold rounded-lg hover:from-emerald-700 hover:to-emerald-800 transition-all shadow-sm shadow-emerald-200 inline-flex items-center gap-1.5">
  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
  Export PDF
</a>
@endsection
@section('content')
@php
    $roleLabels = [
        'admin' => 'System Admin',
        'superadmin' => 'Super Admin',
        'admin_manager' => 'Admin Manager',
        'administrator' => 'Administrator',
        'finance_officer' => 'Finance Officer',
        'finance_manager' => 'Finance Manager',
        'chief_accountant' => 'Chief Accountant',
        'accountant' => 'Accountant',
        'accounts_receivable_officer' => 'Accounts Receivable Officer',
        'accounts_payable_officer' => 'Accounts Payable Officer',
        'payroll_officer' => 'Payroll Officer',
        'budget_officer' => 'Budget Officer',
        'credit_controller' => 'Credit Controller',
        'auditor' => 'Auditor',
        'hr_officer' => 'HR Officer',
        'hr_manager' => 'HR Manager',
        'recruitment_officer' => 'Recruitment Officer',
        'training_officer' => 'Training Officer',
        'time_and_attendance_officer' => 'Time & Attendance Officer',
        'legal_officer' => 'Legal Officer',
        'receptionist' => 'Receptionist',
        'logistics_officer' => 'Logistics Officer',
        'technical_manager' => 'Technical Manager',
        'technician' => 'Technician',
        'ict_officer' => 'ICT Officer',
        'ict_engineer' => 'ICT Engineer',
        'ict_administrator' => 'ICT Administrator',
        'project_manager' => 'Project Manager',
        'project_director' => 'Project Director',
        'project_coordinator' => 'Project Coordinator',
        'project_engineer' => 'Project Engineer',
        'project_accountant' => 'Project Accountant',
        'technical_projects_manager' => 'Technical Projects Manager',
        'site_supervisor' => 'Site Supervisor',
        'team_leader' => 'Team Leader',
        'operations_manager' => 'Operations Manager',
        'operations_officer' => 'Operations Officer',
        'fleet_manager' => 'Fleet Manager',
        'call_center_agent' => 'Call Center Agent',
        'call_center_supervisor' => 'Call Center Supervisor',
        'helpdesk_officer' => 'Helpdesk Officer',
        'helpdesk_supervisor' => 'Helpdesk Supervisor',
        'service_desk_manager' => 'Service Desk Manager',
        'cashier' => 'Cashier',
        'supervisor' => 'Supervisor',
        'director' => 'Director',
        'managing_director' => 'Managing Director',
        'general_manager' => 'General Manager',
        'procurement_manager' => 'Procurement Manager',
        'procurement_officer' => 'Procurement Officer',
        'tender_officer' => 'Tender Officer',
        'store_manager' => 'Store Manager',
        'storekeeper' => 'Storekeeper',
        'inventory_controller' => 'Inventory Controller',
        'asset_officer' => 'Asset Officer',
        'sales_manager' => 'Sales Manager',
        'business_development_manager' => 'Business Development Manager',
        'sales_executive' => 'Sales Executive',
        'crm_officer' => 'CRM Officer',
        'marketing_officer' => 'Marketing Officer',
        'senior_systems_engineer' => 'Senior Systems Engineer',
        'systems_engineer' => 'Systems Engineer',
        'network_engineer' => 'Network Engineer',
        'software_engineer' => 'Software Engineer',
        'cybersecurity_engineer' => 'Cybersecurity Engineer',
        'support_engineer' => 'Support Engineer',
        'noc_engineer' => 'NOC Engineer',
        'field_technician' => 'Field Technician',
        'employee_self_service' => 'Employee Self-Service',
        'manager_self_service' => 'Manager Self-Service',
        'erp_super_administrator' => 'ERP Super Administrator',
        'erp_administrator' => 'ERP Administrator',
    ];
    $roleLabel = $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role));
    $colorMap = [
        'emerald' => ['from' => 'from-emerald-600', 'to' => 'to-emerald-700', 'border' => 'border-emerald-500', 'text' => 'text-emerald-100', 'sub' => 'text-emerald-200'],
        'sky' => ['from' => 'from-sky-500', 'to' => 'to-sky-600', 'border' => 'border-sky-400', 'text' => 'text-sky-100', 'sub' => 'text-sky-200'],
        'amber' => ['from' => 'from-amber-400', 'to' => 'to-amber-500', 'border' => 'border-amber-300', 'text' => 'text-amber-50', 'sub' => 'text-amber-100'],
        'rose' => ['from' => 'from-rose-500', 'to' => 'to-rose-600', 'border' => 'border-rose-400', 'text' => 'text-rose-100', 'sub' => 'text-rose-200'],
    ];
@endphp

{{-- Welcome Banner --}}
<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 right-0 w-24 h-24 bg-white/5 rounded-full -mr-8 -mb-8"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
            <p class="text-emerald-100 text-sm mt-1">{{ $roleLabel }} Dashboard</p>
        </div>
        <div class="text-right">
            <p class="text-emerald-100 text-xs">{{ now()->format('l, d M Y') }}</p>
            <p class="text-emerald-200 text-[10px] mt-1">{{ now()->format('H:i') }}</p>
        </div>
    </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
        @foreach($kpiCards as $card)
    @php $c = $colorMap[$card['color']] ?? $colorMap['emerald']; @endphp
    <div class="bg-gradient-to-br {{ $c['from'] }} {{ $c['to'] }} rounded-xl border border-{{ $c['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
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

{{-- Quick Actions --}}
@if(!empty($quickActions))
<div class="bg-white rounded-xl border p-5 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Quick Actions
    </h3>
    <div class="flex flex-wrap gap-3">
        @foreach($quickActions as $action)
        <a href="{{ route($action['route'], $action['params'] ?? []) }}" class="flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors border border-emerald-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"/></svg>
            {{ $action['label'] }}
        </a>
        @endforeach
        </div>
</div>
        @endif

{{-- Recent Items --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if(!empty($recentItems['recentSales']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Sales Invoices</h3>
            <a href="{{ route('role.page', ['module' => 'sales-invoices']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentSales'] as $invoice)
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
    </div>
        @endif

    @if(!empty($recentItems['recentUsers']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Users</h3>
            <a href="{{ route('role.page', ['module' => 'users']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentUsers'] as $user)
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
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Support Tickets</h3>
            <a href="{{ route('role.page', ['module' => 'tickets']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach(($recentItems['recentTickets'] ?? $recentItems['openTickets'] ?? collect())->take(5) as $ticket)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? $ticket->title ?? 'Ticket #' . $ticket->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst($ticket->status ?? '') }}</p>
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
    </div>
        @endif

    @if(!empty($recentItems['recentLeads']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Leads</h3>
            <a href="{{ route('role.page', ['module' => 'leads']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentLeads'] as $lead)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $lead->name ?? $lead->company_name ?? 'N/A' }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst($lead->status ?? '') }}</p>
                </div>
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
            <a href="{{ route('role.page', ['module' => 'employees']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentEmployees'] as $emp)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $emp->position ?? $emp->department ?? '' }}</p>
                </div>
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
            <a href="{{ route('role.page', ['module' => 'leaves']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['pendingLeaves'] as $leave)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $leave->employee?->first_name ?? '' }} {{ $leave->employee?->last_name ?? '' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $leave->leave_type ?? '' }} - {{ $leave->start_date?->format('d M') ?? '' }}</p>
                </div>
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
            <a href="{{ route('role.page', ['module' => 'projects']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['activeProjects'] as $project)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $project->name }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</p>
                </div>
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
            <a href="{{ route('role.page', ['module' => 'products']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['lowStockProducts'] as $product)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $product->name }}</p>
                    <p class="text-[10px] text-gray-400">Stock: {{ $product->stock_quantity }}</p>
                </div>
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
            <a href="{{ route('role.page', ['module' => 'expenses']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentExpenses'] as $expense)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $expense->description ?? $expense->category ?? 'Expense' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $expense->expense_date?->format('d M Y') ?? '' }}</p>
                </div>
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
            <a href="{{ route('role.page', ['module' => 'revenues']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentRevenues'] as $revenue)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $revenue->description ?? $revenue->category ?? 'Revenue' }}</p>
                    <p class="text-[10px] text-gray-400">{{ $revenue->revenue_date?->format('d M Y') ?? '' }}</p>
                </div>
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
            <a href="{{ route('role.page', ['module' => 'purchase-invoices']) }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentPurchases'] as $purchase)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $purchase->invoice_number }}</p>
                    <p class="text-[10px] text-gray-400">{{ $purchase->vendor?->name ?? 'N/A' }}</p>
                </div>
                <p class="text-xs font-semibold text-gray-900">TZS {{ number_format($purchase->total_amount) }}</p>
            </div>
        @endforeach
        </div>
    </div>
        @endif

    @if(!empty($recentItems['recentSales']) && $role === 'cashier')
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent POS Sales</h3>
            <a href="{{ route('admin.pos.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['recentSales'] as $sale)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">Sale #{{ $sale->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ $sale->created_at->format('d M Y H:i') }}</p>
                </div>
                <p class="text-xs font-semibold text-emerald-600">TZS {{ number_format($sale->total_amount) }}</p>
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
        @foreach($recentItems['recentAttendance'] as $att)
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
    </div>
        @endif

    @if(!empty($recentItems['myTickets']))
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">My Assigned Tickets</h3>
            <a href="{{ route('admin.helpdesk-tickets.index') }}" class="text-[10px] text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="divide-y divide-gray-100">
        @foreach($recentItems['myTickets'] as $ticket)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $ticket->subject ?? $ticket->title ?? 'Ticket #' . $ticket->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</p>
                </div>
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
        @foreach($recentItems['openDeals'] as $deal)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">{{ $deal->title ?? 'Deal #' . $deal->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst($deal->status ?? '') }}</p>
                </div>
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
        @foreach($recentItems['recentTransfers'] as $transfer)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50/50">
                <div>
                    <p class="text-xs font-medium text-gray-900">Transfer #{{ $transfer->id }}</p>
                    <p class="text-[10px] text-gray-400">{{ ucfirst($transfer->status ?? '') }}</p>
                </div>
                <span class="text-[10px] text-gray-400">{{ $transfer->created_at->format('d M Y') }}</span>
            </div>
        @endforeach
        </div>
    </div>
        @endif
</div>
@endsection
