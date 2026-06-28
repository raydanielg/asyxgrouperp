@extends('layouts.admin')
@section('title', ucfirst(str_replace('-', ' ', $module)) . ' - ' . $roleLabel)
@section('page_title', ucfirst(str_replace('-', ' ', $module)))
@section('content')
@php
$money = fn($n) => 'TZS ' . number_format($n);
$permMap = [
    'employees' => ['create' => 'create-employees', 'edit' => 'edit-employees', 'delete' => 'delete-employees'],
    'sales-invoices' => ['create' => 'create-sales-invoices', 'edit' => 'edit-sales-invoices', 'delete' => 'delete-sales-invoices'],
    'purchase-invoices' => ['create' => 'create-purchase-invoices', 'edit' => 'edit-purchase-invoices', 'delete' => 'delete-purchase-invoices'],
    'expenses' => ['create' => 'create-expenses', 'delete' => 'delete-expenses'],
    'revenues' => ['create' => 'create-revenues', 'delete' => 'delete-revenues'],
    'tickets' => ['create' => 'create-helpdesk-tickets', 'edit' => 'edit-helpdesk-tickets', 'delete' => 'delete-helpdesk-tickets'],
    'leads' => ['create' => 'create-crm-leads', 'edit' => 'edit-crm-leads', 'delete' => 'delete-crm-leads'],
    'contacts' => ['create' => 'create-crm-contacts', 'delete' => 'delete-crm-contacts'],
    'deals' => ['create' => 'create-crm-deals', 'edit' => 'edit-crm-deals', 'delete' => 'delete-crm-deals'],
    'contracts' => ['create' => 'create-crm-contracts', 'delete' => 'delete-crm-contracts'],
    'products' => ['create' => 'create-products', 'edit' => 'edit-products', 'delete' => 'delete-products'],
    'warehouses' => ['create' => 'create-warehouses', 'edit' => 'edit-warehouses', 'delete' => 'delete-warehouses'],
    'stock-movements' => ['create' => 'create-stock-movements'],
    'suppliers' => ['create' => 'create-suppliers', 'delete' => 'delete-suppliers'],
    'inventory-transfers' => ['create' => 'create-acc-transfers', 'delete' => 'delete-acc-transfers'],
    'attendance' => ['create' => 'create-attendance', 'delete' => 'delete-attendance'],
    'leaves' => ['create' => 'create-leaves', 'delete' => 'delete-leaves', 'approve' => 'approve-leaves'],
    'users' => ['create' => 'create-users', 'edit' => 'edit-users', 'delete' => 'delete-users'],
    'roles' => ['create' => 'create-roles', 'edit' => 'edit-roles', 'delete' => 'delete-roles'],
    'bills' => ['create' => 'create-bills', 'delete' => 'delete-bills'],
    'bank-accounts' => ['create' => 'create-bank-accounts', 'delete' => 'delete-bank-accounts'],
    'transfers' => ['create' => 'create-acc-transfers', 'delete' => 'delete-acc-transfers'],
    'payroll' => ['create' => 'create-payroll', 'delete' => 'delete-payroll'],
    'pos' => ['create' => 'create-pos', 'delete' => 'delete-pos'],
    'assets' => ['create' => 'create-assets', 'delete' => 'delete-assets'],
    'bugs' => ['create' => 'create-bugs', 'delete' => 'delete-bugs'],
    'projects' => ['create' => 'create-projects', 'edit' => 'edit-projects', 'delete' => 'delete-projects'],
    'timesheets' => ['create' => 'create-timesheets', 'delete' => 'delete-timesheets'],
    'policies' => ['create' => 'create-policies', 'delete' => 'delete-policies'],
    'performance' => ['create' => 'create-performance', 'delete' => 'delete-performance'],
    'training' => ['create' => 'create-training', 'delete' => 'delete-training'],
    'recruitment' => ['create' => 'create-recruitment', 'delete' => 'delete-recruitment'],
    'settings' => ['edit' => 'edit-settings'],
];
$routeMap = [
    'employees' => ['create' => 'admin.employees.index', 'edit' => 'admin.employees.edit', 'delete' => 'admin.employees.destroy'],
    'sales-invoices' => ['create' => 'admin.sales-invoices.create', 'edit' => 'admin.sales-invoices.edit', 'delete' => 'admin.sales-invoices.destroy'],
    'purchase-invoices' => ['create' => 'admin.purchase-invoices.create', 'edit' => 'admin.purchase-invoices.edit', 'delete' => 'admin.purchase-invoices.destroy'],
    'expenses' => ['create' => 'admin.expenses.index', 'delete' => 'admin.expenses.destroy'],
    'revenues' => ['create' => 'admin.revenues.index', 'delete' => 'admin.revenues.destroy'],
    'tickets' => ['create' => 'admin.helpdesk-tickets.index', 'delete' => 'admin.helpdesk-tickets.index'],
    'leads' => ['create' => 'admin.crm-leads.index', 'delete' => 'admin.crm-leads.destroy'],
    'contacts' => ['create' => 'admin.crm-contacts.index', 'delete' => 'admin.crm-contacts.destroy'],
    'deals' => ['create' => 'admin.crm-deals.index', 'delete' => 'admin.crm-deals.destroy'],
    'contracts' => ['create' => 'admin.crm-contracts.index', 'delete' => 'admin.crm-contracts.destroy'],
    'products' => ['create' => 'admin.products.index', 'delete' => 'admin.products.destroy'],
    'warehouses' => ['create' => 'admin.warehouses.index', 'edit' => 'admin.warehouses.edit', 'delete' => 'admin.warehouses.destroy'],
    'stock-movements' => ['create' => 'admin.stock-movements.index'],
    'suppliers' => ['create' => 'admin.suppliers.index', 'delete' => 'admin.suppliers.destroy'],
    'inventory-transfers' => ['create' => 'admin.acc-transfers.index', 'delete' => 'admin.acc-transfers.destroy'],
    'attendance' => ['create' => 'admin.attendance.index', 'delete' => 'admin.attendance.destroy'],
    'leaves' => ['create' => 'admin.leaves.index', 'approve' => 'admin.leaves.approve', 'delete' => 'admin.leaves.destroy'],
    'users' => ['create' => 'admin.users.create', 'edit' => 'admin.users.edit', 'delete' => 'admin.users.destroy'],
    'roles' => ['create' => 'admin.roles.create', 'edit' => 'admin.roles.edit', 'delete' => 'admin.roles.destroy'],
    'bills' => ['create' => 'admin.bills.index', 'delete' => 'admin.bills.destroy'],
    'bank-accounts' => ['create' => 'admin.bank-accounts.index', 'delete' => 'admin.bank-accounts.destroy'],
    'transfers' => ['create' => 'admin.acc-transfers.index', 'delete' => 'admin.acc-transfers.destroy'],
    'payroll' => ['create' => 'admin.payroll.generate-form', 'delete' => 'admin.payroll.destroy'],
    'pos' => ['create' => 'admin.pos.index', 'delete' => 'admin.pos.destroy'],
    'assets' => ['create' => 'admin.assets.index', 'delete' => 'admin.assets.destroy'],
    'bugs' => ['create' => 'admin.bugs.index', 'delete' => 'admin.bugs.destroy'],
    'projects' => ['create' => 'admin.projects.index', 'delete' => 'admin.projects.destroy'],
    'timesheets' => ['create' => 'admin.timesheets.index', 'delete' => 'admin.timesheets.destroy'],
    'policies' => ['create' => 'admin.policies.index', 'delete' => 'admin.policies.destroy'],
    'performance' => ['create' => 'admin.performance.index', 'delete' => 'admin.performance.destroy'],
    'training' => ['create' => 'admin.training.index', 'delete' => 'admin.training.destroy'],
    'recruitment' => ['create' => 'admin.job-postings.index', 'delete' => 'admin.job-postings.destroy'],
    'settings' => ['edit' => 'admin.settings'],
];
$canCreate = isset($permMap[$module]['create']) && auth()->user()->hasPermission($permMap[$module]['create']);
$canEdit = isset($permMap[$module]['edit']) && auth()->user()->hasPermission($permMap[$module]['edit']);
$canDelete = isset($permMap[$module]['delete']) && auth()->user()->hasPermission($permMap[$module]['delete']);
$canApprove = isset($permMap[$module]['approve']) && auth()->user()->hasPermission($permMap[$module]['approve']);
$hasActions = $canEdit || $canDelete || $canApprove;
@endphp

<div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">{{ ucfirst(str_replace('-', ' ', $module)) }}</h2>
            <p class="text-emerald-100 text-sm mt-1">{{ $roleLabel }} - {{ ucfirst(str_replace('-', ' ', $module)) }}</p>
        </div>
        <div class="text-right">
            <p class="text-emerald-100 text-xs">{{ now()->format('l, d M Y') }}</p>
        </div>
    </div>
</div>
        @if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm">
    {{ session('success') }}
</div>
        @endif

<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h3 class="text-sm font-bold text-gray-900">{{ ucfirst(str_replace('-', ' ', $module)) }} List</h3>
        @if($canCreate && isset($routeMap[$module]['create']) && \Illuminate\Support\Facades\Route::has($routeMap[$module]['create']))
        <a href="{{ route($routeMap[$module]['create']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Create New
        </a>
        @endif
    </div>

    @switch($module)
    @case('reports')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Total Sales</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $money($totalSales ?? 0) }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-amber-600">Total Expenses</span>
                    <p class="text-xl font-bold text-amber-900 mt-1">{{ $money($totalExpenses ?? 0) }}</p>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-sky-600">Total Revenues</span>
                    <p class="text-xl font-bold text-sky-900 mt-1">{{ $money($totalRevenues ?? 0) }}</p>
                </div>
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-violet-600">Total Purchases</span>
                    <p class="text-xl font-bold text-violet-900 mt-1">{{ $money($totalPurchases ?? 0) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Sales</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentSales ?? collect())->take(5) as $inv)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $inv->invoice_number }}</span><span class="font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</span></div>
        @endforeach
        </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Expenses</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentExpenses ?? collect())->take(5) as $exp)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $exp->description ?? $exp->category ?? 'Expense' }}</span><span class="font-semibold text-red-600">TZS {{ number_format($exp->amount) }}</span></div>
        @endforeach
        </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Revenues</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentRevenues ?? collect())->take(5) as $rev)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $rev->description ?? $rev->category ?? 'Revenue' }}</span><span class="font-semibold text-emerald-600">TZS {{ number_format($rev->amount) }}</span></div>
        @endforeach
        </div>
                </div>
            </div>
        </div>
        @break

    @case('projects')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Project</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Due Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($projects ?? collect())->items() ?? [] as $project)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $project->name }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($project->status === 'in_progress') ? 'bg-sky-50 text-sky-700' : (($project->status === 'completed') ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $project->due_date?->format('d M Y') ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $project) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this project?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($projects ?? null)?->links() ?? '' }}</div>
        @break

    @case('employees')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Position</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Department</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($employees ?? collect())->items() ?? [] as $emp)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $emp->position ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $emp->department ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($emp->status ?? '') === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-600' }}">{{ ucfirst($emp->status ?? 'N/A') }}</span></td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $emp) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $emp) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this employee?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($employees ?? null)?->links() ?? '' }}</div>
        @break

    @case('sales-invoices')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($invoices ?? collect())->items() ?? [] as $inv)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $inv->invoice_number }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->customer?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->invoice_date->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $inv) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $inv) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this invoice?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($invoices ?? null)?->links() ?? '' }}</div>
        @break

    @case('purchase-invoices')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Invoice #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($invoices ?? collect())->items() ?? [] as $inv)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $inv->invoice_number }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->vendor?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $inv->invoice_date->format('d M Y') }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canEdit && isset($routeMap[$module]['edit']))
                                <a href="{{ route($routeMap[$module]['edit'], $inv) }}" class="text-emerald-500 hover:text-emerald-700 p-1 rounded hover:bg-emerald-50 transition-colors" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                @endif
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $inv) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this invoice?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($invoices ?? null)?->links() ?? '' }}</div>
        @break

    @case('expenses')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($expenses ?? collect())->items() ?? [] as $exp)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $exp->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $exp->category ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-red-600">TZS {{ number_format($exp->amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $exp->expense_date?->format('d M Y') ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $exp) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this expense?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($expenses ?? null)?->links() ?? '' }}</div>
        @break

    @case('revenues')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Description</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        @if($hasActions)<th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($revenues ?? collect())->items() ?? [] as $rev)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $rev->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $rev->category ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($rev->amount) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $rev->revenue_date?->format('d M Y') ?? '-' }}</td>
                        @if($hasActions)
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($canDelete && isset($routeMap[$module]['delete']))
                                <form action="{{ route($routeMap[$module]['delete'], $rev) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition-colors" title="Delete" onclick="return confirm('Delete this revenue?')"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($revenues ?? null)?->links() ?? '' }}</div>
        @break

    @case('tickets')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Subject</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($tickets ?? collect())->items() ?? [] as $ticket)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $ticket->subject ?? 'Ticket #' . $ticket->id }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($ticket->status === 'open') ? 'bg-rose-50 text-rose-700' : (($ticket->status === 'resolved') ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst(str_replace('_', ' ', $ticket->status ?? '')) }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($ticket->priority ?? '-') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $ticket->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($tickets ?? null)?->links() ?? '' }}</div>
        @break

    @case('leads')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Company</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($leads ?? collect())->items() ?? [] as $lead)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $lead->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $lead->company_name ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700">{{ ucfirst($lead->status ?? 'New') }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $lead->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($leads ?? null)?->links() ?? '' }}</div>
        @break

    @case('contacts')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Phone</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($contacts ?? collect())->items() ?? [] as $contact)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $contact->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $contact->email ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $contact->phone ?? '-' }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($contacts ?? null)?->links() ?? '' }}</div>
        @break

    @case('deals')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Deal</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Value</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($deals ?? collect())->items() ?? [] as $deal)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $deal->title ?? 'Deal #' . $deal->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($deal->value ?? 0) }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700">{{ ucfirst($deal->status ?? 'Open') }}</span></td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($deals ?? null)?->links() ?? '' }}</div>
        @break

    @case('contracts')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Contract</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Value</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($contracts ?? collect())->items() ?? [] as $contract)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $contract->title ?? 'Contract #' . $contract->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($contract->value ?? 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($contract->status ?? '-') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($contracts ?? null)?->links() ?? '' }}</div>
        @break

    @case('products')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">SKU</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($products ?? collect())->items() ?? [] as $product)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $product->sku ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($product->price ?? 0) }}</td>
                        <td class="px-4 py-3"><span class="text-xs @if(($product->stock_quantity ?? 0)
        < 10) text-rose-600 font-bold @else text-gray-700 @endif">{{ $product->stock_quantity ?? 0 }}</span></td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($products ?? null)?->links() ?? '' }}</div>
        @break

    @case('warehouses')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($warehouses ?? collect())->items() ?? [] as $wh)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $wh->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $wh->location ?? '-' }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($warehouses ?? null)?->links() ?? '' }}</div>
        @break

    @case('stock-movements')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Quantity</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($movements ?? collect())->items() ?? [] as $m)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $m->product?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($m->type ?? '-') }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">{{ $m->quantity ?? 0 }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $m->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($movements ?? null)?->links() ?? '' }}</div>
        @break

    @case('suppliers')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Phone</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Email</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($suppliers ?? collect())->items() ?? [] as $s)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $s->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $s->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $s->email ?? '-' }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($suppliers ?? null)?->links() ?? '' }}</div>
        @break

    @case('inventory-transfers')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Transfer #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($transfers ?? collect())->items() ?? [] as $t)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">Transfer #{{ $t->id }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ ucfirst($t->status ?? '-') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $t->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($transfers ?? null)?->links() ?? '' }}</div>
        @break

    @case('attendance')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($records ?? collect())->items() ?? [] as $r)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $r->employee?->first_name ?? '' }} {{ $r->employee?->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $r->date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($r->status === 'present') ? 'bg-emerald-50 text-emerald-700' : (($r->status === 'absent') ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($r->status) }}</span></td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($records ?? null)?->links() ?? '' }}</div>
        @break

    @case('leaves')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Dates</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($leaves ?? collect())->items() ?? [] as $l)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $l->employee?->first_name ?? '' }} {{ $l->employee?->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $l->leave_type ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ ($l->status === 'approved') ? 'bg-emerald-50 text-emerald-700' : (($l->status === 'rejected') ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">{{ ucfirst($l->status ?? 'Pending') }}</span></td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $l->start_date?->format('d M') ?? '' }} - {{ $l->end_date?->format('d M Y') ?? '' }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($leaves ?? null)?->links() ?? '' }}</div>
        @break

    @case('users')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($users ?? collect())->items() ?? [] as $u)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $u->roles()->first()?->label ?? $u->role ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $u->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($users ?? null)?->links() ?? '' }}</div>
        @break

    @case('roles')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Permissions</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Users</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($roles ?? collect()) as $r)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $r->label ?? ucfirst(str_replace('_', ' ', $r->name)) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $r->permissions()->count() }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $r->users()->count() }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        @break

    @case('bills')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Bill #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Vendor</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Due Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($bills ?? collect())->items() ?? [] as $bill)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $bill->bill_number ?? 'Bill #' . $bill->id }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $bill->vendor?->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($bill->amount ?? 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $bill->due_date?->format('d M Y') ?? '-' }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($bills ?? null)?->links() ?? '' }}</div>
        @break

    @case('bank-accounts')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Bank</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Account #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($accounts ?? collect())->items() ?? [] as $acc)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $acc->bank_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $acc->account_number ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($acc->balance ?? 0) }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($accounts ?? null)?->links() ?? '' }}</div>
        @break

    @case('transfers')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Transfer #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($transfers ?? collect())->items() ?? [] as $t)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">Transfer #{{ $t->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($t->amount ?? 0) }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $t->created_at->format('d M Y') }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($transfers ?? null)?->links() ?? '' }}</div>
        @break

    @case('payroll')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Employee</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Position</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Salary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
        @foreach(($employees ?? collect())->items() ?? [] as $emp)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $emp->first_name ?? '' }} {{ $emp->last_name ?? '' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $emp->position ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($emp->salary ?? 0) }}</td>
                    </tr>
        @endforeach
        </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t">{{ ($employees ?? null)?->links() ?? '' }}</div>
        @break

    @case('pos')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Today's Sales</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $money($todaySales ?? 0) }}</p>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-sky-600">Transactions</span>
                    <p class="text-xl font-bold text-sky-900 mt-1">{{ $todayCount ?? 0 }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach(($products ?? collect())->take(12) as $product)
                <div class="border rounded-lg p-3 hover:shadow-md transition-shadow cursor-pointer">
                    <p class="text-xs font-medium text-gray-900">{{ $product->name }}</p>
                    <p class="text-[10px] text-gray-400 mt-1">Stock: {{ $product->stock_quantity ?? 0 }}</p>
                    <p class="text-sm font-bold text-emerald-600 mt-1">TZS {{ number_format($product->price ?? 0) }}</p>
                </div>
        @endforeach
        </div>
        </div>
        @break

    @case('pos-reports')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Total Sales</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $money($totalSales ?? 0) }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-amber-600">This Month</span>
                    <p class="text-xl font-bold text-amber-900 mt-1">{{ $money($monthSales ?? 0) }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Sale #</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
        @foreach(($sales ?? collect())->items() ?? [] as $sale)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs font-medium text-gray-900">Sale #{{ $sale->id }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-emerald-600">TZS {{ number_format($sale->total_amount) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $sale->created_at->format('d M Y H:i') }}</td>
                        </tr>
        @endforeach
        </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t">{{ ($sales ?? null)?->links() ?? '' }}</div>
        </div>
        @break

    @case('sales-dashboard')
        <div class="p-5">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-emerald-600">Total Proposals</span>
                    <p class="text-xl font-bold text-emerald-900 mt-1">{{ $totalProposals ?? 0 }}</p>
                </div>
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-sky-600">Accepted</span>
                    <p class="text-xl font-bold text-sky-900 mt-1">{{ $acceptedProposals ?? 0 }}</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-amber-600">Total Invoices</span>
                    <p class="text-xl font-bold text-amber-900 mt-1">{{ $totalInvoices ?? 0 }}</p>
                </div>
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4">
                    <span class="text-[10px] font-medium text-violet-600">Total Sales</span>
                    <p class="text-xl font-bold text-violet-900 mt-1">{{ $money($totalSales ?? 0) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Proposals</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentProposals ?? collect())->take(5) as $prop)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $prop->title ?? 'Proposal #' . $prop->id }}</span><span class="text-gray-500">{{ ucfirst($prop->status ?? '') }}</span></div>
        @endforeach
        </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b"><h4 class="text-xs font-bold text-gray-700">Recent Invoices</h4></div>
                    <div class="divide-y divide-gray-100">
        @foreach(($recentInvoices ?? collect())->take(5) as $inv)
                        <div class="px-4 py-2.5 flex justify-between text-xs"><span class="text-gray-700">{{ $inv->invoice_number }}</span><span class="font-semibold text-gray-900">TZS {{ number_format($inv->total_amount) }}</span></div>
        @endforeach
        </div>
                </div>
            </div>
        </div>
        @break

    @case('settings')
        <div class="p-5">
            <p class="text-sm text-gray-500">Settings page for {{ $roleLabel }}.</p>
        </div>
        @break

    @case('timesheets')
    @case('bugs')
    @case('assets')
    @case('policies')
    @case('performance')
    @case('training')
    @case('recruitment')
        <div class="p-5">
            <p class="text-sm text-gray-500">{{ ucfirst(str_replace('-', ' ', $module)) }} module for {{ $roleLabel }}.</p>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Details</th>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
        @foreach(($employees ?? ($projects ?? collect()))->take(10) as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $item->name ?? ($item->first_name ?? '') . ' ' . ($item->last_name ?? '') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $item->position ?? $item->status ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ ($item->created_at ?? now())->format('d M Y') }}</td>
                        </tr>
        @endforeach
        </tbody>
                </table>
            </div>
        </div>
        @break

    @default
        <div class="p-8 text-center text-gray-400 text-sm">Module: {{ $module }}</div>
    @endswitch
</div>
@endsection
