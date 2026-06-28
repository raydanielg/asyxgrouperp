<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ' . config('app.name', 'Laravel'))</title>
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); }
        .sidebar-link.active { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-submenu { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .sidebar-submenu.open { max-height: 500px; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #01241f; }
        ::-webkit-scrollbar-thumb { background: #024938; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #f9ac00; }
        @keyframes toastIn { from { opacity:0; transform:translateX(100%); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(100%); } }
        .toast-in { animation: toastIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
        .toast-out { animation: toastOut 0.3s ease-in both; }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-gray-50 text-slate-800">

    {{-- Mobile Overlay --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="adminSidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-emerald-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        {{-- Brand --}}
        <div class="h-16 flex items-center px-4 border-b border-emerald-800/50 flex-shrink-0">
            <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group" class="w-9 h-9 object-contain rounded-lg bg-white/10 p-0.5">
            <span class="ml-2 text-white font-bold text-sm tracking-wide">ASYX<span class="text-gold-400">GROUP</span></span>
            <span class="ml-1 text-gold-400 font-bold text-[10px] tracking-wider bg-gold-400/10 px-1.5 py-0.5 rounded">{{ auth()->user()->isAdmin() ? 'ADMIN' : strtoupper(explode(' ', auth()->user()->roles()->first()?->label ?? 'USER')[0]) }}</span>
        </div>

        {{-- Menu --}}
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
            @php
                $navItems = [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z', 'match' => 'admin.dashboard'],
                ];
                $navGroups = [
                    ['title' => 'Multi-Company', 'items' => [
                        ['label' => 'Companies', 'route' => 'admin.companies.index', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'match' => 'admin.companies*'],
                        ['label' => 'Consolidated Report', 'route' => 'admin.companies.consolidated', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'match' => 'admin.companies.consolidated*'],
                        ['label' => 'Intercompany', 'route' => 'admin.intercompany.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'match' => 'admin.intercompany*'],
                    ]],
                    ['title' => 'Business Flow', 'items' => [
                        ['label' => 'Flow Dashboard', 'route' => 'admin.business-flow.dashboard', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'match' => 'admin.business-flow*'],
                        ['label' => 'Tenders', 'route' => 'admin.tenders.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.tenders*'],
                        ['label' => 'Quotations', 'route' => 'admin.quotations.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.quotations*'],
                        ['label' => 'Project Budgets', 'route' => 'admin.budgets.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.budgets*'],
                        ['label' => 'LPOs', 'route' => 'admin.lpos.index', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'match' => 'admin.lpos*'],
                        ['label' => 'GRNs', 'route' => 'admin.grns.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'match' => 'admin.grns*'],
                        ['label' => 'Delivery Notes', 'route' => 'admin.delivery-notes.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'match' => 'admin.delivery-notes*'],
                        ['label' => 'Vendor Invoices', 'route' => 'admin.vendor-invoices.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.vendor-invoices*'],
                        ['label' => 'Office Expenses', 'route' => 'admin.office-expenses.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'match' => 'admin.office-expenses*'],
                        ['label' => 'Client Receipts', 'route' => 'admin.client-receipts.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.client-receipts*'],
                    ]],
                    ['title' => 'User Management', 'items' => [
                        ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'match' => 'admin.users*'],
                        ['label' => 'Roles & Permissions', 'route' => 'admin.roles.index', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'match' => 'admin.roles*'],
                        ['label' => 'Login History', 'route' => 'admin.users.login-history', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.users.login-history*'],
                        ['label' => 'Profile', 'route' => 'admin.profile', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'match' => 'admin.profile*'],
                    ]],
                    ['title' => 'HRM', 'items' => [
                        ['label' => 'Employees', 'route' => 'admin.employees.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'match' => 'admin.employees*'],
                        ['label' => 'Attendance', 'route' => 'admin.attendance.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'match' => 'admin.attendance*'],
                        ['label' => 'Payroll', 'route' => 'admin.payroll.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.payroll*'],
                        ['label' => 'Leaves', 'route' => 'admin.leaves.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'match' => 'admin.leaves*'],
                        ['label' => 'Performance', 'route' => 'admin.performance.index', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'match' => 'admin.performance*'],
                        ['label' => 'Training', 'route' => 'admin.training.index', 'icon' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z', 'match' => 'admin.training*'],
                        ['label' => 'Recruitment', 'route' => 'admin.job-postings.index', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'match' => 'admin.job-postings*'],
                        ['label' => 'Assets', 'route' => 'admin.assets.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'match' => 'admin.assets*'],
                        ['label' => 'Events', 'route' => 'admin.hr-events.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'match' => 'admin.hr-events*'],
                        ['label' => 'Policies', 'route' => 'admin.policies.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.policies*'],
                    ]],
                    ['title' => 'CRM', 'items' => [
                        ['label' => 'Leads', 'route' => 'admin.crm-leads.index', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'match' => 'admin.crm-leads*'],
                        ['label' => 'Deals', 'route' => 'admin.crm-deals.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.crm-deals*'],
                        ['label' => 'Contracts', 'route' => 'admin.crm-contracts.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.crm-contracts*'],
                        ['label' => 'Contacts', 'route' => 'admin.crm-contacts.index', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'match' => 'admin.crm-contacts*'],
                    ]],
                    ['title' => 'Accounting', 'items' => [
                        ['label' => 'Bank Accounts', 'route' => 'admin.bank-accounts.index', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'match' => 'admin.bank-accounts*'],
                        ['label' => 'Transfers', 'route' => 'admin.acc-transfers.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'match' => 'admin.acc-transfers*'],
                        ['label' => 'Expenses', 'route' => 'admin.expenses.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'match' => 'admin.expenses*'],
                        ['label' => 'Revenue', 'route' => 'admin.revenues.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.revenues*'],
                        ['label' => 'Bills', 'route' => 'admin.bills.index', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z', 'match' => 'admin.bills*'],
                        ['label' => 'Estimates', 'route' => 'admin.estimates.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.estimates*'],
                    ]],
                    ['title' => 'Projects', 'items' => [
                        ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'match' => 'admin.projects*'],
                        ['label' => 'Timesheets', 'route' => 'admin.timesheets.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.timesheets*'],
                        ['label' => 'Bugs', 'route' => 'admin.bugs.index', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'match' => 'admin.bugs*'],
                    ]],
                    ['title' => 'Products & Inventory', 'items' => [
                        ['label' => 'Products', 'route' => 'admin.products.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'match' => 'admin.products*'],
                        ['label' => 'Categories', 'route' => 'admin.product-categories.index', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'match' => 'admin.product-categories*'],
                        ['label' => 'Suppliers', 'route' => 'admin.suppliers.index', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'match' => 'admin.suppliers*'],
                        ['label' => 'Stock Movements', 'route' => 'admin.stock-movements.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'match' => 'admin.stock-movements*'],
                    ]],
                    ['title' => 'Inventory', 'items' => [
                        ['label' => 'Warehouses', 'route' => 'admin.warehouses.index', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'match' => 'admin.warehouses*'],
                        ['label' => 'Transfers', 'route' => 'admin.transfers.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'match' => 'admin.transfers*'],
                    ]],
                    ['title' => 'Sales', 'items' => [
                        ['label' => 'Sales Dashboard', 'route' => 'admin.sales-dashboard', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'match' => 'admin.sales-dashboard*'],
                        ['label' => 'Quotations', 'route' => 'admin.sales-proposals.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.sales-proposals*'],
                        ['label' => 'Sales Invoices', 'route' => 'admin.sales-invoices.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.sales-invoices*'],
                        ['label' => 'Sales Returns', 'route' => 'admin.sales-returns.index', 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'match' => 'admin.sales-returns*'],
                    ]],
                    ['title' => 'Purchase', 'items' => [
                        ['label' => 'Purchase Invoices', 'route' => 'admin.purchase-invoices.index', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'match' => 'admin.purchase-invoices*'],
                        ['label' => 'Purchase Returns', 'route' => 'admin.purchase-returns.index', 'icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'match' => 'admin.purchase-returns*'],
                    ]],
                    ['title' => 'POS', 'items' => [
                        ['label' => 'POS Terminal', 'route' => 'admin.pos.index', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'match' => 'admin.pos.index*'],
                        ['label' => 'POS Reports', 'route' => 'admin.pos.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'match' => 'admin.pos.reports*'],
                    ]],
                    ['title' => 'Subscriptions', 'items' => [
                        ['label' => 'Plans', 'route' => 'admin.plans.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.plans*'],
                        ['label' => 'Orders', 'route' => 'admin.orders.index', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'match' => 'admin.orders*'],
                        ['label' => 'Coupons', 'route' => 'admin.coupons.index', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'match' => 'admin.coupons*'],
                        ['label' => 'Bank Transfers', 'route' => 'admin.bank-transfers.index', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'match' => 'admin.bank-transfers*'],
                    ]],
                    ['title' => 'Approvals', 'items' => [
                        ['label' => 'Workflows', 'route' => 'admin.approvals.index', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'match' => 'admin.approvals*'],
                        ['label' => 'Requests', 'route' => 'admin.approvals.requests', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'match' => 'admin.approvals.requests*'],
                    ]],
                    ['title' => 'Fleet & Assets', 'items' => [
                        ['label' => 'Vehicles', 'route' => 'admin.fleet.index', 'icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1', 'match' => 'admin.fleet*'],
                        ['label' => 'Fixed Assets', 'route' => 'admin.fixed-assets.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'match' => 'admin.fixed-assets*'],
                    ]],
                    ['title' => 'Documents', 'items' => [
                        ['label' => 'Document Mgmt', 'route' => 'admin.documents.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.documents*'],
                    ]],
                    ['title' => 'Call Center', 'items' => [
                        ['label' => 'Dashboard', 'route' => 'admin.call-center.index', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'match' => 'admin.call-center*'],
                    ]],
                    ['title' => 'Support', 'items' => [
                        ['label' => 'Tickets', 'route' => 'admin.helpdesk-tickets.index', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.helpdesk*'],
                        ['label' => 'Categories', 'route' => 'admin.helpdesk-categories.index', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'match' => 'admin.helpdesk-categories*'],
                    ]],
                    ['title' => 'System', 'items' => [
                        ['label' => 'Add-ons', 'route' => 'admin.add-ons.index', 'icon' => 'M11 3.055A5.001 5.001 0 005.035 9H11V3.055zM13 3.055V9h5.965A5.001 5.001 0 0013 3.055zM11 11v6.945A5.001 5.001 0 015.035 11H11zM13 11h5.965A5.001 5.001 0 0113 17.945V11z', 'match' => 'admin.add-ons*'],
                        ['label' => 'Email Templates', 'route' => 'admin.email-templates.index', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'match' => 'admin.email-templates*'],
                        ['label' => 'Notification Templates', 'route' => 'admin.notification-templates.index', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'match' => 'admin.notification-templates*'],
                        ['label' => 'Media Library', 'route' => 'admin.media.index', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'match' => 'admin.media*'],
                        ['label' => 'Messenger', 'route' => 'admin.messenger.index', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'match' => 'admin.messenger*'],
                        ['label' => 'Reports', 'route' => 'admin.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'match' => 'admin.reports*'],
                        ['label' => 'Settings', 'route' => 'admin.settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'match' => 'admin.settings*'],
                        ['label' => 'Audit Logs', 'route' => 'admin.audit-logs.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'match' => 'admin.audit-logs*'],
                    ]],
                ];

                // Permission map: route match pattern => required permission
                $permMap = [
                    'admin.companies*' => 'view-dashboard',
                    'admin.companies.consolidated*' => 'view-dashboard',
                    'admin.intercompany*' => 'view-dashboard',
                    'admin.approvals*' => 'view-dashboard',
                    'admin.fleet*' => 'view-dashboard',
                    'admin.fixed-assets*' => 'view-dashboard',
                    'admin.documents*' => 'view-dashboard',
                    'admin.call-center*' => 'view-dashboard',
                    'admin.audit-logs*' => 'view-dashboard',
                    'admin.business-flow*' => 'view-dashboard',
                    'admin.tenders*' => 'view-dashboard',
                    'admin.quotations*' => 'view-dashboard',
                    'admin.budgets*' => 'view-dashboard',
                    'admin.lpos*' => 'view-dashboard',
                    'admin.grns*' => 'view-dashboard',
                    'admin.delivery-notes*' => 'view-dashboard',
                    'admin.vendor-invoices*' => 'view-dashboard',
                    'admin.office-expenses*' => 'view-expenses',
                    'admin.client-receipts*' => 'view-revenues',
                    'admin.users*' => 'view-users',
                    'admin.roles*' => 'view-roles',
                    'admin.users.login-history*' => 'view-login-history',
                    'admin.profile*' => 'view-dashboard',
                    'admin.employees*' => 'view-employees',
                    'admin.attendance*' => 'view-attendance',
                    'admin.payroll*' => 'view-payroll',
                    'admin.leaves*' => 'view-leaves',
                    'admin.performance*' => 'view-performance',
                    'admin.training*' => 'view-training',
                    'admin.job-postings*' => 'view-recruitment',
                    'admin.assets*' => 'view-assets',
                    'admin.hr-events*' => 'view-events',
                    'admin.policies*' => 'view-policies',
                    'admin.crm-leads*' => 'view-crm-leads',
                    'admin.crm-deals*' => 'view-crm-deals',
                    'admin.crm-contracts*' => 'view-crm-contracts',
                    'admin.crm-contacts*' => 'view-crm-contacts',
                    'admin.bank-accounts*' => 'view-bank-accounts',
                    'admin.acc-transfers*' => 'view-acc-transfers',
                    'admin.expenses*' => 'view-expenses',
                    'admin.revenues*' => 'view-revenues',
                    'admin.bills*' => 'view-bills',
                    'admin.estimates*' => 'view-dashboard',
                    'admin.projects*' => 'view-projects',
                    'admin.timesheets*' => 'view-timesheets',
                    'admin.bugs*' => 'view-bugs',
                    'admin.products*' => 'view-products',
                    'admin.product-categories*' => 'view-product-categories',
                    'admin.suppliers*' => 'view-suppliers',
                    'admin.stock-movements*' => 'view-stock-movements',
                    'admin.warehouses*' => 'view-warehouses',
                    'admin.transfers*' => 'view-acc-transfers',
                    'admin.sales-dashboard*' => 'view-dashboard',
                    'admin.sales-proposals*' => 'view-sales-invoices',
                    'admin.sales-invoices*' => 'view-sales-invoices',
                    'admin.sales-returns*' => 'view-sales-invoices',
                    'admin.purchase-invoices*' => 'view-purchase-invoices',
                    'admin.purchase-returns*' => 'view-purchase-invoices',
                    'admin.pos.index*' => 'view-pos',
                    'admin.pos.reports*' => 'view-pos',
                    'admin.plans*' => 'view-dashboard',
                    'admin.orders*' => 'view-dashboard',
                    'admin.coupons*' => 'view-dashboard',
                    'admin.bank-transfers*' => 'view-bank-accounts',
                    'admin.helpdesk*' => 'view-helpdesk-tickets',
                    'admin.helpdesk-categories*' => 'view-helpdesk-tickets',
                    'admin.add-ons*' => 'view-settings',
                    'admin.email-templates*' => 'view-settings',
                    'admin.notification-templates*' => 'view-settings',
                    'admin.media*' => 'view-settings',
                    'admin.messenger*' => 'view-dashboard',
                    'admin.reports*' => 'view-reports',
                    'admin.settings*' => 'view-settings',
                ];

                // Filter nav groups based on user permissions
                $currentUser = auth()->user();
                $isFullAdmin = $currentUser->isAdmin();
                $filteredGroups = [];
                foreach ($navGroups as $group) {
                    $visibleItems = [];
                    foreach ($group['items'] as $item) {
                        if ($isFullAdmin) {
                            $visibleItems[] = $item;
                            continue;
                        }
                        $requiredPerm = $permMap[$item['match']] ?? null;
                        if (!$requiredPerm || $currentUser->hasPermission($requiredPerm)) {
                            $visibleItems[] = $item;
                        }
                    }
                    if (!empty($visibleItems)) {
                        $filteredGroups[] = ['title' => $group['title'], 'items' => $visibleItems];
                    }
                }
                $navGroups = $filteredGroups;
            @endphp

            {{-- Top-level items --}}
            @if(auth()->user()->isAdmin())
            @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
            @else
            @php
                // Role-specific sidebar menus - using role.page route
                $roleName = auth()->user()->roles()->first()?->name ?? auth()->user()->role ?? 'user';
                $roleSlug = str_replace('_', '-', $roleName);
                $iconDashboard = 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z';
                $iconReports = 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z';
                $iconProjects = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2';
                $iconEmployees = 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z';
                $iconSales = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                $iconPurchases = 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z';
                $iconExpenses = 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z';
                $iconRevenues = 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                $iconTickets = 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                $iconProducts = 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4';
                $iconWarehouses = 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4';
                $iconUsers = 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z';
                $iconRoles = 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z';
                $iconSettings = 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z';
                $iconAttendance = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4';
                $iconPayroll = 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                $iconLeaves = 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z';
                $iconLeads = 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6';
                $iconContacts = 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z';
                $iconDeals = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                $iconContracts = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                $iconBills = 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z';
                $iconBank = 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z';
                $iconTransfers = 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4';
                $iconTimesheets = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                $iconBugs = 'M13 10V3L4 14h7v7l9-11h-7z';
                $iconAssets = 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4';
                $iconPolicies = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                $iconStock = 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4';
                $iconSuppliers = 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4';
                $iconPos = 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z';
                $iconPerformance = 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6';
                $iconTraining = 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z';
                $iconRecruitment = 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z';
                $iconSalesDash = 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z';

                $roleMenus = [
                    'director' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Sales Dashboard', 'module' => 'sales-dashboard', 'icon' => $iconSalesDash],
                        ['label' => 'Employees', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'Sales Invoices', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Purchases', 'module' => 'purchase-invoices', 'icon' => $iconPurchases],
                        ['label' => 'Expenses', 'module' => 'expenses', 'icon' => $iconExpenses],
                        ['label' => 'Helpdesk', 'module' => 'tickets', 'icon' => $iconTickets],
                    ],
                    'finance_officer' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Sales Invoices', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Purchase Invoices', 'module' => 'purchase-invoices', 'icon' => $iconPurchases],
                        ['label' => 'Expenses', 'module' => 'expenses', 'icon' => $iconExpenses],
                        ['label' => 'Revenues', 'module' => 'revenues', 'icon' => $iconRevenues],
                        ['label' => 'Bills', 'module' => 'bills', 'icon' => $iconBills],
                        ['label' => 'Bank Accounts', 'module' => 'bank-accounts', 'icon' => $iconBank],
                        ['label' => 'Transfers', 'module' => 'transfers', 'icon' => $iconTransfers],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                    ],
                    'hr_officer' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Employees', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'Attendance', 'module' => 'attendance', 'icon' => $iconAttendance],
                        ['label' => 'Payroll', 'module' => 'payroll', 'icon' => $iconPayroll],
                        ['label' => 'Leaves', 'module' => 'leaves', 'icon' => $iconLeaves],
                        ['label' => 'Performance', 'module' => 'performance', 'icon' => $iconPerformance],
                        ['label' => 'Training', 'module' => 'training', 'icon' => $iconTraining],
                        ['label' => 'Recruitment', 'module' => 'recruitment', 'icon' => $iconRecruitment],
                        ['label' => 'Assets', 'module' => 'assets', 'icon' => $iconAssets],
                        ['label' => 'Policies', 'module' => 'policies', 'icon' => $iconPolicies],
                    ],
                    'auditor' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Sales Invoices', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Purchase Invoices', 'module' => 'purchase-invoices', 'icon' => $iconPurchases],
                        ['label' => 'Expenses', 'module' => 'expenses', 'icon' => $iconExpenses],
                        ['label' => 'Revenues', 'module' => 'revenues', 'icon' => $iconRevenues],
                        ['label' => 'Bills', 'module' => 'bills', 'icon' => $iconBills],
                        ['label' => 'Bank Accounts', 'module' => 'bank-accounts', 'icon' => $iconBank],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                    ],
                    'admin_manager' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Users', 'module' => 'users', 'icon' => $iconUsers],
                        ['label' => 'Roles & Permissions', 'module' => 'roles', 'icon' => $iconRoles],
                        ['label' => 'Employees', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'Attendance', 'module' => 'attendance', 'icon' => $iconAttendance],
                        ['label' => 'Leaves', 'module' => 'leaves', 'icon' => $iconLeaves],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Settings', 'module' => 'settings', 'icon' => $iconSettings],
                    ],
                    'cashier' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'POS Terminal', 'module' => 'pos', 'icon' => $iconPos],
                        ['label' => 'POS Reports', 'module' => 'pos-reports', 'icon' => $iconReports],
                        ['label' => 'Sales Invoices', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Products', 'module' => 'products', 'icon' => $iconProducts],
                        ['label' => 'Revenues', 'module' => 'revenues', 'icon' => $iconRevenues],
                    ],
                    'technical_manager' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Timesheets', 'module' => 'timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'Bugs', 'module' => 'bugs', 'icon' => $iconBugs],
                        ['label' => 'Employees', 'module' => 'employees', 'icon' => $iconEmployees],
                    ],
                    'technician' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'My Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Timesheets', 'module' => 'timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'Bugs', 'module' => 'bugs', 'icon' => $iconBugs],
                    ],
                    'ict_officer' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Bugs', 'module' => 'bugs', 'icon' => $iconBugs],
                        ['label' => 'Assets', 'module' => 'assets', 'icon' => $iconAssets],
                        ['label' => 'Employees', 'module' => 'employees', 'icon' => $iconEmployees],
                    ],
                    'ict_engineer' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Bugs', 'module' => 'bugs', 'icon' => $iconBugs],
                        ['label' => 'Assets', 'module' => 'assets', 'icon' => $iconAssets],
                        ['label' => 'Settings', 'module' => 'settings', 'icon' => $iconSettings],
                    ],
                    'project_manager' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Timesheets', 'module' => 'timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'Bugs', 'module' => 'bugs', 'icon' => $iconBugs],
                        ['label' => 'Deals', 'module' => 'deals', 'icon' => $iconDeals],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                    ],
                    'operations_manager' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Products', 'module' => 'products', 'icon' => $iconProducts],
                        ['label' => 'Warehouses', 'module' => 'warehouses', 'icon' => $iconWarehouses],
                        ['label' => 'Stock Movements', 'module' => 'stock-movements', 'icon' => $iconStock],
                        ['label' => 'Sales', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Purchases', 'module' => 'purchase-invoices', 'icon' => $iconPurchases],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                    ],
                    'logistics_officer' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Products', 'module' => 'products', 'icon' => $iconProducts],
                        ['label' => 'Warehouses', 'module' => 'warehouses', 'icon' => $iconWarehouses],
                        ['label' => 'Stock Movements', 'module' => 'stock-movements', 'icon' => $iconStock],
                        ['label' => 'Suppliers', 'module' => 'suppliers', 'icon' => $iconSuppliers],
                        ['label' => 'Transfers', 'module' => 'inventory-transfers', 'icon' => $iconTransfers],
                        ['label' => 'Purchases', 'module' => 'purchase-invoices', 'icon' => $iconPurchases],
                    ],
                    'receptionist' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Leads', 'module' => 'leads', 'icon' => $iconLeads],
                        ['label' => 'Contacts', 'module' => 'contacts', 'icon' => $iconContacts],
                        ['label' => 'Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                    ],
                    'call_center_agent' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Leads', 'module' => 'leads', 'icon' => $iconLeads],
                        ['label' => 'Contacts', 'module' => 'contacts', 'icon' => $iconContacts],
                        ['label' => 'Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                    ],
                    'legal_officer' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Contracts', 'module' => 'contracts', 'icon' => $iconContracts],
                        ['label' => 'Contacts', 'module' => 'contacts', 'icon' => $iconContacts],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                    ],
                    'supervisor' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Employees', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'Attendance', 'module' => 'attendance', 'icon' => $iconAttendance],
                        ['label' => 'Leaves', 'module' => 'leaves', 'icon' => $iconLeaves],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'POS Terminal', 'module' => 'pos', 'icon' => $iconPos],
                        ['label' => 'Products', 'module' => 'products', 'icon' => $iconProducts],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                    ],
                    'administrator' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Users', 'module' => 'users', 'icon' => $iconUsers],
                        ['label' => 'Roles', 'module' => 'roles', 'icon' => $iconRoles],
                        ['label' => 'Employees', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Products', 'module' => 'products', 'icon' => $iconProducts],
                        ['label' => 'Settings', 'module' => 'settings', 'icon' => $iconSettings],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                    ],
                ];
                $myMenu = $roleMenus[$roleName] ?? $roleMenus['administrator'] ?? [];
            @endphp
            @foreach($myMenu as $item)
            @php
                $itemUrl = isset($item['route']) ? route($item['route']) : route('role.page', ['module' => $item['module']]);
                $itemActive = isset($item['match']) ? request()->routeIs($item['match']) : (request()->routeIs('role.page') && request()->segment(2) === $item['module']);
            @endphp
            <a href="{{ $itemUrl }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ $itemActive ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
            {{-- Hide grouped nav for non-admin users --}}
            @php $navGroups = []; @endphp
            @endif

            {{-- Grouped items --}}
            @foreach($navGroups as $group)
        <div class="pt-3 pb-1">
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-emerald-400/40">{{ $group['title'] }}</p>
            </div>
        @foreach($group['items'] as $item)
        <a href="{{ route($item['route']) }}" class="sidebar-link w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
            @endforeach
        </div>

        {{-- Bottom User --}}
        <div class="p-4 border-t border-emerald-800/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->first_name ? Auth::user()->first_name . ' ' . Auth::user()->last_name : (Auth::user()->name ?? 'Admin') }}</p>
                    <p class="text-xs text-emerald-300/60">{{ auth()->user()->isAdmin() ? 'Administrator' : (auth()->user()->roles()->first()?->label ?? 'User') }}</p>
                </div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout').submit();" class="text-emerald-300/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
                <form id="admin-logout" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">

        {{-- Header --}}
        <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" class="lg:hidden w-7 h-7 object-contain">
                <h1 class="text-lg font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-4">
                @yield('page_actions')
                {{-- Company Context Switcher --}}
                @php
                    $userCompany = auth()->user()->company;
                    $allCompanies = $userCompany && $userCompany->is_group
                        ? \App\Models\Company::orderBy('is_group', 'desc')->orderBy('name')->get()
                        : collect([$userCompany]);
                    $sessionCompanyId = session('switched_company_id', auth()->user()->company_id);
                @endphp
                @if($allCompanies->count() > 1)
                <div class="relative" id="companySwitcher">
                    <button onclick="document.getElementById('companyDropdown').classList.toggle('hidden')" class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors text-sm">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/></svg>
                        <span class="text-gray-700 font-medium">{{ $allCompanies->where('id', $sessionCompanyId)->first()?->short_code ?? 'All' }}</span>
                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="companyDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl border shadow-lg z-50 py-2 max-h-80 overflow-y-auto">
                        <a href="{{ route('admin.companies.switch', ['company' => 'all']) }}" class="flex items-center gap-2 px-4 py-2 text-xs hover:bg-gray-50 {{ $sessionCompanyId === null ? 'text-emerald-600 font-medium' : 'text-gray-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            All Companies (Group View)
                        </a>
                        <div class="border-t my-1"></div>
                        @foreach($allCompanies as $c)
                        <a href="{{ route('admin.companies.switch', ['company' => $c->id]) }}" class="flex items-center gap-2 px-4 py-2 text-xs hover:bg-gray-50 {{ $sessionCompanyId == $c->id ? 'text-emerald-600 font-medium' : 'text-gray-700' }}">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-medium bg-emerald-50 text-emerald-700">{{ $c->short_code }}</span>
                            {{ $c->name }}
                            @if($c->is_group)<span class="text-[9px] text-gold-500">Group</span>@endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                {{-- Global Employee Search --}} 
                <form method="GET" action="{{ route('admin.employees.index') }}" class="hidden md:flex items-center bg-gray-50 rounded-lg px-3 py-1.5 border border-gray-100">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees by name, email or ID..." class="bg-transparent text-sm outline-none w-64 text-gray-600 placeholder-gray-400">
                    <button class="ml-2 px-2 py-1 text-xs bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Search</button>
                </form>
                <a href="{{ route('admin.applications.index') }}" class="hidden md:inline-flex items-center px-3 py-1.5 rounded-lg border border-emerald-200 text-emerald-700 text-xs font-medium hover:bg-emerald-50">Applications</a>

                {{-- Notifications Dropdown --}}
                @php
                    try {
                        $sessionCompanyId = session('switched_company_id', auth()->user()->company_id);
                        $recentLogs = \App\Models\AuditLog::query()
                            ->when($sessionCompanyId, function($q) use ($sessionCompanyId) { $q->where('company_id', $sessionCompanyId); })
                            ->latest()->take(8)->get();
                    } catch (\Throwable $e) {
                        $recentLogs = collect();
                    }
                @endphp
                <div class="relative" id="notifWrap">
                    <button type="button" onclick="document.getElementById('notifMenu').classList.toggle('hidden')" class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($recentLogs->count() > 0)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </button>
                    <div id="notifMenu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl border shadow-lg z-50 py-2">
                        <div class="px-4 py-2 border-b flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-700">Notifications</span>
                            <a href="{{ route('admin.audit-logs.index') }}" class="text-[11px] text-emerald-600 hover:text-emerald-700">View all</a>
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($recentLogs as $log)
                                <div class="px-4 py-2 hover:bg-gray-50 flex items-start gap-2">
                                    <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold">{{ strtoupper(substr($log->action ?? 'A',0,1)) }}</div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-800">{{ $log->action ?? 'Activity' }} @if(!empty($log->entity_type))<span class="text-gray-400">• {{ $log->entity_type }}</span>@endif</p>
                                        <p class="text-[10px] text-gray-400">{{ $log->user?->name ?? 'System' }} • {{ optional($log->created_at)->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-[11px] text-gray-400">No recent notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Impersonation Banner --}}
        @if(session('impersonated_by'))
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white px-6 py-2.5 flex items-center justify-between sticky top-16 z-20">
          <div class="flex items-center gap-3 text-sm font-medium">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <span>You are currently logged in as <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</span>
            <span class="text-amber-100 text-xs">— Impersonated session</span>
          </div>
          <form method="POST" action="{{ route('admin.users.stop-impersonating') }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-1.5 bg-white text-amber-700 text-xs font-bold rounded-lg hover:bg-amber-50 transition-all shadow-sm inline-flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Stop Impersonating
            </button>
          </form>
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-6 animate-fade">
            @yield('content')
        </main>
    </div>

    {{-- Toast Container --}}
    <div id="toastContainer" class="fixed top-5 right-5 z-[60] flex flex-col gap-3 w-full max-w-sm pointer-events-none"></div>

    {{-- Toast System --}}
    <script>
    (function() {
        const container = document.getElementById('toastContainer');
        function showToast(type, title, message) {
            const toast = document.createElement('div');
            toast.className = 'toast-in pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg border backdrop-blur-sm';
            let iconSvg, bgClass, borderClass;
            if (type === 'success') {
                iconSvg = '<svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                bgClass = 'bg-emerald-50/95'; borderClass = 'border-emerald-200';
            } else if (type === 'error') {
                iconSvg = '<svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                bgClass = 'bg-red-50/95'; borderClass = 'border-red-200';
            } else {
                iconSvg = '<svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                bgClass = 'bg-blue-50/95'; borderClass = 'border-blue-200';
            }
            toast.classList.add(...bgClass.split(' '), ...borderClass.split(' '));
            toast.innerHTML = iconSvg +
                '<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-800">' + title + '</p>' +
                (message ? '<p class="text-sm text-gray-500 mt-0.5">' + message + '</p>' : '') + '</div>' +
                '<button onclick="this.parentElement.classList.add(\'toast-out\'); setTimeout(()=>this.parentElement.remove(), 300)" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
            container.appendChild(toast);
            setTimeout(() => { toast.classList.add('toast-out'); setTimeout(() => toast.remove(), 300); }, 5000);
        }
        window.showToast = showToast;
        @if(session('status'))
            showToast('success', 'Success', '{{ session('status') }}');
        @endif
        @if(session('error'))
            showToast('error', 'Error', '{{ session('error') }}');
        @endif
        @if(session('success'))
            showToast('success', 'Success', '{{ session('success') }}');
        @endif
        @if(session('warning'))
            showToast('warning', 'Warning', '{{ session('warning') }}');
        @endif
    })();

    // SweetAlert delete confirmation
    function confirmDelete(formId, title, text) {
        Swal.fire({
            title: title || 'Are you sure?',
            text: text || 'You will not be able to recover this item!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
        return false;
    }

    // SweetAlert success alert
    function sweetAlert(icon, title, text) {
        Swal.fire({ icon: icon, title: title, text: text, confirmButtonColor: '#024938' });
    }
    @if(session('sweet_alert'))
    Swal.fire({ icon: '{{ session('sweet_alert.icon') }}', title: '{{ session('sweet_alert.title') }}', text: '{{ session('sweet_alert.text') ?? '' }}', confirmButtonColor: '#024938' });
    @endif
    </script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        function toggleMenu(id) {
            const menu = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id.replace('menu-', ''));
            menu.classList.toggle('open');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    </script>
    @stack('scripts')
</body>
</html>
