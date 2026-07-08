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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .animate-fade { animation: fadeIn 0.3s ease-out both; }
        .sidebar-link { transition: all 0.2s ease; }
        .font-brand { font-family: 'Fraunces','Georgia',serif; }
        .font-menu { font-family: 'Inter','Nunito',sans-serif; }
        .sidebar-link:hover { background: rgba(255,255,255,0.06); }
        .sidebar-link.active { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-submenu { max-height: 0; overflow: hidden; transition: max-height 0.35s cubic-bezier(0.4,0,0.2,1); }
        .sidebar-submenu.open { max-height: 600px; }
        .sidebar-group-btn { transition: all 0.2s ease; }
        .sidebar-group-btn:hover { background: rgba(255,255,255,0.04); }
        .sidebar-group-btn .chevron { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        .sidebar-group-btn.open .chevron { transform: rotate(90deg); }
        .sidebar-group-btn.open { background: rgba(255,255,255,0.03); }
        .sidebar-submenu-item { opacity: 0; transform: translateX(-8px); transition: opacity 0.25s ease, transform 0.25s ease; }
        .sidebar-submenu.open .sidebar-submenu-item { opacity: 1; transform: translateX(0); }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(1) { transition-delay: 0.02s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(2) { transition-delay: 0.04s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(3) { transition-delay: 0.06s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(4) { transition-delay: 0.08s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(5) { transition-delay: 0.10s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(6) { transition-delay: 0.12s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(7) { transition-delay: 0.14s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(8) { transition-delay: 0.16s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(9) { transition-delay: 0.18s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(10) { transition-delay: 0.20s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(11) { transition-delay: 0.22s; }
        .sidebar-submenu.open .sidebar-submenu-item:nth-child(12) { transition-delay: 0.24s; }
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

    {{-- Loading Screen --}}
    @include('partials.loading-screen', ['variant' => 'admin'])

    {{-- Mobile Overlay --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside id="adminSidebar" class="fixed top-0 left-0 z-50 w-64 h-screen bg-emerald-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
        {{-- Brand --}}
        <div class="h-16 flex items-center justify-center px-4 border-b border-emerald-800/50 flex-shrink-0 bg-gradient-to-r from-emerald-900 to-emerald-800">
            <div class="flex items-center gap-3">
                <img src="{{ asset('asyxgrouplogo.png') }}" alt="{{ config('app.name') }}" class="w-10 h-10 object-contain rounded-lg bg-white/10 p-0.5">
                <div class="font-brand text-white leading-none">
                    <span class="text-lg font-bold tracking-wide">ASYX</span>
                    <span class="text-lg font-bold text-gold-400">GROUP</span>
                </div>
            </div>
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
                        ['label' => 'Applications', 'route' => 'admin.applications.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'match' => 'admin.applications*'],
                        ['label' => 'Assets', 'route' => 'admin.assets.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'match' => 'admin.assets*'],
                        ['label' => 'Events', 'route' => 'admin.hr-events.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'match' => 'admin.hr-events*'],
                        ['label' => 'Policies', 'route' => 'admin.policies.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.policies*'],
                        ['label' => 'Bonuses', 'route' => 'admin.bonuses.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.bonuses*'],
                    ]],
                    ['title' => 'CRM', 'items' => [
                        ['label' => 'Leads', 'route' => 'admin.crm-leads.index', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'match' => 'admin.crm-leads*'],
                        ['label' => 'Deals', 'route' => 'admin.crm-deals.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.crm-deals*'],
                        ['label' => 'Contracts', 'route' => 'admin.crm-contracts.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.crm-contracts*'],
                        ['label' => 'Customers', 'route' => 'admin.crm-contacts.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2a3 3 0 013-3h3m0 0h3M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2a3 3 0 00-3-3H2v0h2m13-4a4 4 0 11-8 0 4 4 0 018 0z', 'match' => 'admin.crm-contacts*'],
                        ['label' => 'Contacts', 'route' => 'admin.crm-contacts.index', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'match' => 'admin.crm-contacts*'],
                    ]],
                    ['title' => 'Accounting', 'items' => [
                        ['label' => 'Bank Accounts', 'route' => 'admin.bank-accounts.index', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'match' => 'admin.bank-accounts*'],
                        ['label' => 'Transfers', 'route' => 'admin.acc-transfers.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'match' => 'admin.acc-transfers*'],
                        ['label' => 'Expenses', 'route' => 'admin.expenses.index', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'match' => 'admin.expenses*'],
                        ['label' => 'Revenue', 'route' => 'admin.revenues.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.revenues*'],
                        ['label' => 'Cost Centers', 'route' => 'admin.cost-centers.index', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'match' => 'admin.cost-centers*'],
                        ['label' => 'Bills', 'route' => 'admin.bills.index', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z', 'match' => 'admin.bills*'],
                        ['label' => 'Estimates', 'route' => 'admin.estimates.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.estimates*'],
                        ['label' => 'Petty Cash', 'route' => 'admin.petty-cash.index', 'icon' => 'M9 7h6m0 10v-3m-3 3v-3m-3 3v-3m9-7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2z', 'match' => 'admin.petty-cash*'],
                        ['label' => 'Chart of Accounts', 'route' => 'admin.chart-of-accounts.index', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'match' => 'admin.chart-of-accounts*'],
                        ['label' => 'Journal Entries', 'route' => 'admin.journal-entries.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.journal-entries*'],
                        ['label' => 'Financial Reports', 'route' => 'admin.financial-reports.index', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'match' => 'admin.financial-reports*'],
                    ]],
                    ['title' => 'Projects', 'items' => [
                        ['label' => 'Projects', 'route' => 'admin.projects.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'match' => 'admin.projects*'],
                        ['label' => 'Timesheets', 'route' => 'admin.timesheets.index', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.timesheets*'],
                        ['label' => 'Bugs', 'route' => 'admin.bugs.index', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'match' => 'admin.bugs*'],
                        ['label' => 'Meetings', 'route' => 'admin.meetings.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM6 3v2m12-2v2M3 8h2m14 0h2', 'match' => 'admin.meetings*'],
                        ['label' => 'Settlements', 'route' => 'admin.settlements.index', 'icon' => 'M9 17v-2m2 2v-4m2 4v-6m2 6V7m2 10V5M3 7l3-3 3 3M3 17l3 3 3-3', 'match' => 'admin.settlements*'],
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
                        ['label' => 'Dashboard', 'route' => 'admin.call-center.index', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'match' => 'admin.call-center.index*'],
                        ['label' => 'Tickets', 'route' => 'admin.call-center.tickets', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'match' => 'admin.call-center.tickets*'],
                        ['label' => 'Call Logs', 'route' => 'admin.call-center.calls', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'match' => 'admin.call-center.calls*'],
                        ['label' => 'Import Action Points', 'route' => 'admin.call-center.action-points.import', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12', 'match' => 'admin.call-center.action-points.import*'],
                        ['label' => 'Action Points Reports', 'route' => 'admin.call-center.action-points.reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'match' => 'admin.call-center.action-points.reports*'],
                        ['label' => 'Download Template', 'route' => 'admin.call-center.download-template', 'icon' => 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'match' => 'admin.call-center.download-template*'],
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
                    'admin.meetings*' => 'view-dashboard',
                    'admin.settlements*' => 'view-projects',
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
                    'admin.applications*' => 'view-recruitment',
                    'admin.assets*' => 'view-assets',
                    'admin.hr-events*' => 'view-events',
                    'admin.policies*' => 'view-policies',
                    'admin.bonuses*' => 'view-employees',
                    'admin.crm-leads*' => 'view-crm-leads',
                    'admin.crm-deals*' => 'view-crm-deals',
                    'admin.crm-contracts*' => 'view-crm-contracts',
                    'admin.crm-contacts*' => 'view-crm-contacts',
                    'admin.bank-accounts*' => 'view-bank-accounts',
                    'admin.acc-transfers*' => 'view-acc-transfers',
                    'admin.expenses*' => 'view-expenses',
                    'admin.cost-centers*' => 'view-dashboard',
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

                // Filter nav groups based on user permissions for ALL users
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
        <a href="{{ route($item['route']) }}" class="sidebar-link font-menu w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ request()->routeIs($item['match']) ? 'active' : '' }}">
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
                $iconVisitors = 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z';
                $iconAppointments = 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z';
                $iconCalls = 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z';
                $iconCorrespondence = 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z';
                $iconParcels = 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4';
                $iconFrontDesk = 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2';
                $iconDepartments = 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4';
                $iconAnnouncements = 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.854M11 5.882A1.76 1.76 0 0111.962 4.2a1.76 1.76 0 012.396.82l3.943 6.58a1.76 1.76 0 010 1.8l-3.943 6.58a1.76 1.76 0 01-2.396.82 1.76 1.76 0 01-.962-1.682V5.882z';
                $iconPower = 'M13 10V3L4 14h7v7l9-11h-7z';
                $iconDatabase = 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4';
                $iconShield = 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z';

                $roleMenus = [
                    'erp_super_administrator' => [
                        ['label' => 'Super Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Companies', 'module' => 'companies', 'icon' => $iconWarehouses],
                        ['label' => 'Users', 'module' => 'users', 'icon' => $iconUsers],
                        ['label' => 'Roles & Permissions', 'module' => 'roles', 'icon' => $iconRoles],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'System Control', 'module' => 'system-control', 'icon' => $iconPower],
                        ['label' => 'Database Backup', 'module' => 'database-backup', 'icon' => $iconDatabase],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
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
                        ['label' => 'Salary Advance', 'module' => 'salary-advance', 'icon' => $iconPayroll],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
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
                        ['label' => 'Applications', 'module' => 'applications', 'icon' => $iconRecruitment],
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
                        ['label' => 'Visitors', 'module' => 'visitors', 'icon' => $iconVisitors],
                        ['label' => 'Appointments', 'module' => 'appointments', 'icon' => $iconAppointments],
                        ['label' => 'Calls', 'module' => 'calls', 'icon' => $iconCalls],
                        ['label' => 'Correspondence', 'module' => 'correspondence', 'icon' => $iconCorrespondence],
                        ['label' => 'Parcels', 'module' => 'parcels', 'icon' => $iconParcels],
                        ['label' => 'Front Desk', 'module' => 'front-desk', 'icon' => $iconFrontDesk],
                        ['label' => 'Departments', 'module' => 'departments', 'icon' => $iconDepartments],
                        ['label' => 'Announcements', 'module' => 'announcements', 'icon' => $iconAnnouncements],
                        ['label' => 'Messages', 'module' => 'messages', 'icon' => $iconContacts],
                        ['label' => 'Salary Advance', 'module' => 'salary-advance', 'icon' => $iconPayroll],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'call_center_agent' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Leads', 'module' => 'leads', 'icon' => $iconLeads],
                        ['label' => 'Contacts', 'module' => 'contacts', 'icon' => $iconContacts],
                        ['label' => 'Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                    ],
                    'sgr_agent' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Upload Action Points', 'module' => 'import-action-points', 'icon' => 'M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3'],
                        ['label' => 'My Reports', 'module' => 'action-points-reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['label' => 'Payslips', 'module' => 'payslips', 'icon' => $iconPayroll],
                        ['label' => 'My Account', 'module' => 'my-account', 'icon' => $iconUsers],
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
                    'managing_director' => [
                        ['label' => 'Group Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'All Companies', 'module' => 'companies', 'icon' => $iconWarehouses],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Final Approvals', 'module' => 'approvals', 'icon' => $iconRoles],
                        ['label' => 'Tenders', 'module' => 'tenders', 'icon' => $iconSales],
                        ['label' => 'Contracts', 'module' => 'contracts', 'icon' => $iconContracts],
                        ['label' => 'HR Overview', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'general_manager' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Department Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Approvals', 'module' => 'approvals', 'icon' => $iconRoles],
                        ['label' => 'Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Sales & CRM', 'module' => 'leads', 'icon' => $iconLeads],
                        ['label' => 'HR Overview', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'finance_manager' => [
                        ['label' => 'Finance Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'General Ledger', 'module' => 'journal-entries', 'icon' => $iconBills],
                        ['label' => 'Accounts Payable', 'module' => 'purchase-invoices', 'icon' => $iconPurchases],
                        ['label' => 'Accounts Receivable', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Bank & Cash', 'module' => 'bank-accounts', 'icon' => $iconBank],
                        ['label' => 'Budgeting', 'module' => 'budgets', 'icon' => $iconPerformance],
                        ['label' => 'Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Approvals', 'module' => 'approvals', 'icon' => $iconRoles],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'chief_accountant' => [
                        ['label' => 'General Ledger', 'module' => 'journal-entries', 'icon' => $iconBills],
                        ['label' => 'Bank Reconciliation', 'module' => 'bank-reconciliation', 'icon' => $iconBank],
                        ['label' => 'Financial Statements', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Tax Management', 'module' => 'tax-management', 'icon' => $iconSettings],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'accountant' => [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Journal Entries', 'module' => 'journal-entries', 'icon' => $iconBills],
                        ['label' => 'Invoices', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Expenses', 'module' => 'expenses', 'icon' => $iconExpenses],
                        ['label' => 'Cost Centres', 'module' => 'cost-centres', 'icon' => $iconPerformance],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'accounts_receivable_officer' => [
                        ['label' => 'Customer Invoices', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Receivables Aging', 'module' => 'receivables-aging', 'icon' => $iconReports],
                        ['label' => 'Payment Receipts', 'module' => 'revenues', 'icon' => $iconRevenues],
                        ['label' => 'Credit Notes', 'module' => 'credit-notes', 'icon' => $iconBills],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'accounts_payable_officer' => [
                        ['label' => 'Vendor Invoices', 'module' => 'purchase-invoices', 'icon' => $iconPurchases],
                        ['label' => 'Payment Requests', 'module' => 'acc-transfers', 'icon' => $iconTransfers],
                        ['label' => 'Payables Aging', 'module' => 'payables-aging', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'payroll_officer' => [
                        ['label' => 'Payroll Processing', 'module' => 'payroll', 'icon' => $iconPayroll],
                        ['label' => 'Salary Records', 'module' => 'salary-records', 'icon' => $iconEmployees],
                        ['label' => 'Statutory Deductions', 'module' => 'deductions', 'icon' => $iconBills],
                        ['label' => 'Payslips', 'module' => 'payslips', 'icon' => $iconPayroll],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'budget_officer' => [
                        ['label' => 'Budget Preparation', 'module' => 'budgets', 'icon' => $iconPerformance],
                        ['label' => 'Budget vs Actual', 'module' => 'budget-vs-actual', 'icon' => $iconReports],
                        ['label' => 'Cost Centre Budgets', 'module' => 'cost-centres', 'icon' => $iconPerformance],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'credit_controller' => [
                        ['label' => 'Credit Limits', 'module' => 'credit-limits', 'icon' => $iconSettings],
                        ['label' => 'Overdue Accounts', 'module' => 'overdue-accounts', 'icon' => $iconReports],
                        ['label' => 'Collection Follow-ups', 'module' => 'collections', 'icon' => $iconContacts],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'procurement_manager' => [
                        ['label' => 'Procurement Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Vendors', 'module' => 'suppliers', 'icon' => $iconSuppliers],
                        ['label' => 'RFQ', 'module' => 'rfqs', 'icon' => $iconSales],
                        ['label' => 'Purchase Approvals', 'module' => 'approvals', 'icon' => $iconRoles],
                        ['label' => 'Purchase Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'procurement_officer' => [
                        ['label' => 'RFQ Creation', 'module' => 'rfqs', 'icon' => $iconSales],
                        ['label' => 'Purchase Requisitions', 'module' => 'purchase-requisitions', 'icon' => $iconPurchases],
                        ['label' => 'LPOs', 'module' => 'lpos', 'icon' => $iconPurchases],
                        ['label' => 'GRNs', 'module' => 'grns', 'icon' => $iconStock],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'tender_officer' => [
                        ['label' => 'Tender Registration', 'module' => 'tenders', 'icon' => $iconSales],
                        ['label' => 'Tender Calendar', 'module' => 'tender-calendar', 'icon' => $iconAppointments],
                        ['label' => 'Bid Documents', 'module' => 'documents', 'icon' => $iconBills],
                        ['label' => 'Tender Costing', 'module' => 'tender-costing', 'icon' => $iconPerformance],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'store_manager' => [
                        ['label' => 'Inventory Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Warehouses', 'module' => 'warehouses', 'icon' => $iconWarehouses],
                        ['label' => 'Stock Transfers', 'module' => 'transfers', 'icon' => $iconTransfers],
                        ['label' => 'Reorder Levels', 'module' => 'reorder-levels', 'icon' => $iconSettings],
                        ['label' => 'Inventory Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'storekeeper' => [
                        ['label' => 'Stock In/Out', 'module' => 'stock-movements', 'icon' => $iconStock],
                        ['label' => 'Goods Received', 'module' => 'grns', 'icon' => $iconStock],
                        ['label' => 'Stock Count', 'module' => 'stock-count', 'icon' => $iconProducts],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'inventory_controller' => [
                        ['label' => 'Product Catalogue', 'module' => 'products', 'icon' => $iconProducts],
                        ['label' => 'Batch/Serial', 'module' => 'batch-tracking', 'icon' => $iconSettings],
                        ['label' => 'Barcode', 'module' => 'barcodes', 'icon' => $iconStock],
                        ['label' => 'Variance Reports', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'asset_officer' => [
                        ['label' => 'Asset Register', 'module' => 'assets', 'icon' => $iconAssets],
                        ['label' => 'Asset Assignment', 'module' => 'asset-assignment', 'icon' => $iconEmployees],
                        ['label' => 'Maintenance', 'module' => 'asset-maintenance', 'icon' => $iconSettings],
                        ['label' => 'Disposal Requests', 'module' => 'asset-disposal', 'icon' => $iconBills],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'sales_manager' => [
                        ['label' => 'Sales Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Deal Pipeline', 'module' => 'deals', 'icon' => $iconDeals],
                        ['label' => 'Sales Forecast', 'module' => 'sales-forecast', 'icon' => $iconReports],
                        ['label' => 'Quotations', 'module' => 'quotations', 'icon' => $iconSales],
                        ['label' => 'Team Performance', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'business_development_manager' => [
                        ['label' => 'Lead Management', 'module' => 'leads', 'icon' => $iconLeads],
                        ['label' => 'Opportunity Pipeline', 'module' => 'deals', 'icon' => $iconDeals],
                        ['label' => 'Market Analysis', 'module' => 'market-analysis', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'sales_executive' => [
                        ['label' => 'My Leads', 'module' => 'leads', 'icon' => $iconLeads],
                        ['label' => 'My Deals', 'module' => 'deals', 'icon' => $iconDeals],
                        ['label' => 'Quotations', 'module' => 'quotations', 'icon' => $iconSales],
                        ['label' => 'Communication Log', 'module' => 'calls', 'icon' => $iconCalls],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'crm_officer' => [
                        ['label' => 'Customer Database', 'module' => 'contacts', 'icon' => $iconContacts],
                        ['label' => 'Activity Tracking', 'module' => 'calls', 'icon' => $iconCalls],
                        ['label' => 'Communication History', 'module' => 'correspondence', 'icon' => $iconCorrespondence],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'marketing_officer' => [
                        ['label' => 'Campaigns', 'module' => 'campaigns', 'icon' => $iconAnnouncements],
                        ['label' => 'Lead Source Reports', 'module' => 'lead-source-reports', 'icon' => $iconReports],
                        ['label' => 'Materials', 'module' => 'documents', 'icon' => $iconBills],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'project_director' => [
                        ['label' => 'Portfolio Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Project Profitability', 'module' => 'project-profitability', 'icon' => $iconReports],
                        ['label' => 'Resource Allocation', 'module' => 'resource-allocation', 'icon' => $iconEmployees],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'technical_projects_manager' => [
                        ['label' => 'Technical Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Engineer Allocation', 'module' => 'resource-allocation', 'icon' => $iconEmployees],
                        ['label' => 'Technical Milestones', 'module' => 'milestones', 'icon' => $iconPerformance],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'project_coordinator' => [
                        ['label' => 'Task Tracker', 'module' => 'tasks', 'icon' => $iconTimesheets],
                        ['label' => 'Documents', 'module' => 'documents', 'icon' => $iconBills],
                        ['label' => 'Meetings', 'module' => 'meetings', 'icon' => $iconAppointments],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'project_engineer' => [
                        ['label' => 'My Tasks', 'module' => 'tasks', 'icon' => $iconTimesheets],
                        ['label' => 'Site Reports', 'module' => 'site-reports', 'icon' => $iconBills],
                        ['label' => 'My Timesheet', 'module' => 'timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'site_supervisor' => [
                        ['label' => 'Site Attendance', 'module' => 'attendance', 'icon' => $iconAttendance],
                        ['label' => 'Site Reports', 'module' => 'site-reports', 'icon' => $iconBills],
                        ['label' => 'Issues', 'module' => 'incidents', 'icon' => $iconTickets],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'project_accountant' => [
                        ['label' => 'Project Budget vs Actual', 'module' => 'budget-vs-actual', 'icon' => $iconReports],
                        ['label' => 'Project Invoicing', 'module' => 'sales-invoices', 'icon' => $iconSales],
                        ['label' => 'Cost Allocation', 'module' => 'cost-centres', 'icon' => $iconPerformance],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'senior_systems_engineer' => [
                        ['label' => 'Tickets/Projects', 'module' => 'projects', 'icon' => $iconProjects],
                        ['label' => 'Documentation', 'module' => 'documents', 'icon' => $iconBills],
                        ['label' => 'Team Review', 'module' => 'team-review', 'icon' => $iconEmployees],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'systems_engineer' => [
                        ['label' => 'My Tickets/Tasks', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Technical Assets', 'module' => 'assets', 'icon' => $iconAssets],
                        ['label' => 'Maintenance Logs', 'module' => 'asset-maintenance', 'icon' => $iconSettings],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'support_engineer' => [
                        ['label' => 'Site Visits', 'module' => 'site-visits', 'icon' => $iconAppointments],
                        ['label' => 'Service Reports', 'module' => 'service-reports', 'icon' => $iconBills],
                        ['label' => 'Customer Assets', 'module' => 'assets', 'icon' => $iconAssets],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'noc_engineer' => [
                        ['label' => 'Monitoring Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Incidents', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Escalations', 'module' => 'escalations', 'icon' => $iconCalls],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'service_desk_manager' => [
                        ['label' => 'Service Desk Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'SLA Performance', 'module' => 'sla-reports', 'icon' => $iconReports],
                        ['label' => 'Team Performance', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Escalations', 'module' => 'escalations', 'icon' => $iconCalls],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'helpdesk_supervisor' => [
                        ['label' => 'Ticket Queue', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Agent Performance', 'module' => 'reports', 'icon' => $iconReports],
                        ['label' => 'Escalations', 'module' => 'escalations', 'icon' => $iconCalls],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'helpdesk_officer' => [
                        ['label' => 'My Tickets', 'module' => 'tickets', 'icon' => $iconTickets],
                        ['label' => 'Knowledge Base', 'module' => 'knowledge-base', 'icon' => $iconBills],
                        ['label' => 'Contact Log', 'module' => 'calls', 'icon' => $iconCalls],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'call_center_supervisor' => [
                        ['label' => 'Call Statistics', 'module' => 'call-statistics', 'icon' => $iconReports],
                        ['label' => 'Shift Scheduling', 'module' => 'shift-schedule', 'icon' => $iconAppointments],
                        ['label' => 'SLA Monitoring', 'module' => 'sla-reports', 'icon' => $iconReports],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'hr_manager' => [
                        ['label' => 'HR Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Employee Records', 'module' => 'employees', 'icon' => $iconEmployees],
                        ['label' => 'Recruitment', 'module' => 'recruitment', 'icon' => $iconRecruitment],
                        ['label' => 'Leave Approvals', 'module' => 'leaves', 'icon' => $iconLeaves],
                        ['label' => 'Payroll Approval', 'module' => 'payroll', 'icon' => $iconPayroll],
                        ['label' => 'Disciplinary', 'module' => 'disciplinary', 'icon' => $iconBills],
                        ['label' => 'Appraisals', 'module' => 'performance', 'icon' => $iconPerformance],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'recruitment_officer' => [
                        ['label' => 'Job Postings', 'module' => 'job-postings', 'icon' => $iconRecruitment],
                        ['label' => 'Candidates', 'module' => 'applications', 'icon' => $iconContacts],
                        ['label' => 'Onboarding', 'module' => 'onboarding', 'icon' => $iconAppointments],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'training_officer' => [
                        ['label' => 'Training Calendar', 'module' => 'training', 'icon' => $iconTraining],
                        ['label' => 'Training Records', 'module' => 'training-records', 'icon' => $iconBills],
                        ['label' => 'Certifications', 'module' => 'certifications', 'icon' => $iconSettings],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'time_and_attendance_officer' => [
                        ['label' => 'Attendance Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                        ['label' => 'Shift Records', 'module' => 'shift-schedule', 'icon' => $iconAppointments],
                        ['label' => 'Overtime', 'module' => 'overtime', 'icon' => $iconTimesheets],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'fleet_manager' => [
                        ['label' => 'Vehicle Register', 'module' => 'vehicles', 'icon' => $iconWarehouses],
                        ['label' => 'Driver Assignment', 'module' => 'driver-assignment', 'icon' => $iconEmployees],
                        ['label' => 'Fuel & Maintenance', 'module' => 'fuel-logs', 'icon' => $iconSettings],
                        ['label' => 'Trip Scheduling', 'module' => 'trip-schedule', 'icon' => $iconAppointments],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'logistics_officer' => [
                        ['label' => 'Delivery Schedules', 'module' => 'deliveries', 'icon' => $iconAppointments],
                        ['label' => 'Shipment Tracking', 'module' => 'shipments', 'icon' => $iconTransfers],
                        ['label' => 'Route Planning', 'module' => 'route-planning', 'icon' => $iconStock],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'operations_officer' => [
                        ['label' => 'Daily Operations Log', 'module' => 'operations-log', 'icon' => $iconBills],
                        ['label' => 'Task Assignments', 'module' => 'operations-tasks', 'icon' => $iconTimesheets],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'team_leader' => [
                        ['label' => 'Team Task Board', 'module' => 'team-tasks', 'icon' => $iconTimesheets],
                        ['label' => 'Team Attendance', 'module' => 'team-attendance', 'icon' => $iconAttendance],
                        ['label' => 'Team Timesheets', 'module' => 'team-timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                    ],
                    'employee_self_service' => [
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                        ['label' => 'My Payslips', 'module' => 'payslips', 'icon' => $iconPayroll],
                        ['label' => 'Apply Leave', 'module' => 'leaves', 'icon' => $iconLeaves],
                        ['label' => 'My Attendance', 'module' => 'attendance', 'icon' => $iconAttendance],
                        ['label' => 'My Timesheets', 'module' => 'timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'Announcements', 'module' => 'announcements', 'icon' => $iconAnnouncements],
                    ],
                    'manager_self_service' => [
                        ['label' => 'My Salary', 'module' => 'salary', 'icon' => $iconPayroll],
                        ['label' => 'My Profile', 'module' => 'my-account', 'icon' => $iconUsers],
                        ['label' => 'My Payslips', 'module' => 'payslips', 'icon' => $iconPayroll],
                        ['label' => 'Apply Leave', 'module' => 'leaves', 'icon' => $iconLeaves],
                        ['label' => 'My Attendance', 'module' => 'attendance', 'icon' => $iconAttendance],
                        ['label' => 'My Timesheets', 'module' => 'timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'Team Overview', 'module' => 'team-overview', 'icon' => $iconEmployees],
                        ['label' => 'Team Leaves', 'module' => 'team-leaves', 'icon' => $iconLeaves],
                        ['label' => 'Team Timesheets', 'module' => 'team-timesheets', 'icon' => $iconTimesheets],
                        ['label' => 'Announcements', 'module' => 'announcements', 'icon' => $iconAnnouncements],
                    ],
                ];

                $modulePermMap = [
                    'companies' => 'view-dashboard',
                    'intercompany' => 'view-dashboard',
                    'consolidated' => 'view-dashboard',
                    'users' => 'view-users',
                    'roles' => 'view-roles',
                    'reports' => 'view-reports',
                    'settings' => 'view-settings',
                    'system-control' => 'view-settings',
                    'database-backup' => 'view-settings',
                    'employees' => 'view-employees',
                    'attendance' => 'view-attendance',
                    'payroll' => 'view-payroll',
                    'leaves' => 'view-leaves',
                    'performance' => 'view-performance',
                    'training' => 'view-training',
                    'recruitment' => 'view-recruitment',
                    'job-postings' => 'view-recruitment',
                    'applications' => 'view-recruitment',
                    'assets' => 'view-assets',
                    'hr-events' => 'view-events',
                    'policies' => 'view-policies',
                    'bonuses' => 'view-employees',
                    'crm-leads' => 'view-crm-leads',
                    'crm-deals' => 'view-crm-deals',
                    'crm-contracts' => 'view-crm-contracts',
                    'crm-contacts' => 'view-crm-contacts',
                    'leads' => 'view-crm-leads',
                    'deals' => 'view-crm-deals',
                    'contracts' => 'view-crm-contracts',
                    'contacts' => 'view-crm-contacts',
                    'bank-accounts' => 'view-bank-accounts',
                    'transfers' => 'view-acc-transfers',
                    'acc-transfers' => 'view-acc-transfers',
                    'expenses' => 'view-expenses',
                    'revenues' => 'view-revenues',
                    'bills' => 'view-bills',
                    'estimates' => 'view-dashboard',
                    'projects' => 'view-projects',
                    'timesheets' => 'view-timesheets',
                    'bugs' => 'view-bugs',
                    'products' => 'view-products',
                    'product-categories' => 'view-product-categories',
                    'suppliers' => 'view-suppliers',
                    'stock-movements' => 'view-stock-movements',
                    'warehouses' => 'view-warehouses',
                    'sales-dashboard' => 'view-dashboard',
                    'sales-proposals' => 'view-sales-invoices',
                    'sales-invoices' => 'view-sales-invoices',
                    'sales-returns' => 'view-sales-invoices',
                    'purchase-invoices' => 'view-purchase-invoices',
                    'purchase-returns' => 'view-purchase-invoices',
                    'pos' => 'view-pos',
                    'pos-reports' => 'view-pos',
                    'plans' => 'view-dashboard',
                    'orders' => 'view-dashboard',
                    'coupons' => 'view-dashboard',
                    'bank-transfers' => 'view-dashboard',
                    'approvals' => 'view-dashboard',
                    'fleet' => 'view-dashboard',
                    'fixed-assets' => 'view-dashboard',
                    'documents' => 'view-dashboard',
                    'call-center' => 'view-dashboard',
                    'helpdesk' => 'view-helpdesk-tickets',
                    'tickets' => 'view-helpdesk-tickets',
                    'add-ons' => 'view-settings',
                    'email-templates' => 'view-settings',
                    'notification-templates' => 'view-settings',
                    'media' => 'view-settings',
                    'messenger' => 'view-dashboard',
                    'audit-logs' => 'view-dashboard',
                    'business-flow' => 'view-dashboard',
                    'tenders' => 'view-dashboard',
                    'quotations' => 'view-dashboard',
                    'budgets' => 'view-dashboard',
                    'lpos' => 'view-dashboard',
                    'grns' => 'view-dashboard',
                    'delivery-notes' => 'view-dashboard',
                    'vendor-invoices' => 'view-dashboard',
                    'office-expenses' => 'view-expenses',
                    'client-receipts' => 'view-revenues',
                    'meetings' => 'view-dashboard',
                    'settlements' => 'view-projects',
                    'visitors' => 'view-dashboard',
                    'appointments' => 'view-dashboard',
                    'calls' => 'view-dashboard',
                    'correspondence' => 'view-dashboard',
                    'parcels' => 'view-dashboard',
                    'front-desk' => 'view-dashboard',
                    'departments' => 'view-dashboard',
                    'announcements' => 'view-dashboard',
                    'messages' => 'view-dashboard',
                    'salary-advance' => 'view-dashboard',
                    'my-account' => 'view-dashboard',
                    'payslips' => 'view-dashboard',
                    'salary' => 'view-dashboard',
                    'site-reports' => 'view-dashboard',
                    'service-reports' => 'view-dashboard',
                    'knowledge-base' => 'view-dashboard',
                    'escalations' => 'view-dashboard',
                    'sla-reports' => 'view-dashboard',
                    'call-statistics' => 'view-dashboard',
                    'shift-schedule' => 'view-dashboard',
                    'disciplinary' => 'view-dashboard',
                    'onboarding' => 'view-dashboard',
                    'training-records' => 'view-training',
                    'certifications' => 'view-dashboard',
                    'overtime' => 'view-attendance',
                    'vehicles' => 'view-dashboard',
                    'driver-assignment' => 'view-dashboard',
                    'fuel-logs' => 'view-dashboard',
                    'trip-schedule' => 'view-dashboard',
                    'deliveries' => 'view-dashboard',
                    'shipments' => 'view-dashboard',
                    'route-planning' => 'view-dashboard',
                    'operations-log' => 'view-dashboard',
                    'operations-tasks' => 'view-dashboard',
                    'team-tasks' => 'view-dashboard',
                    'team-attendance' => 'view-attendance',
                    'team-timesheets' => 'view-timesheets',
                    'team-overview' => 'view-dashboard',
                    'team-leaves' => 'view-leaves',
                    'project-profitability' => 'view-dashboard',
                    'resource-allocation' => 'view-dashboard',
                    'milestones' => 'view-dashboard',
                    'tasks' => 'view-projects',
                    'journal-entries' => 'view-dashboard',
                    'cost-centres' => 'view-dashboard',
                    'receivables-aging' => 'view-dashboard',
                    'payables-aging' => 'view-dashboard',
                    'credit-notes' => 'view-bills',
                    'collections' => 'view-dashboard',
                    'deductions' => 'view-payroll',
                    'salary-records' => 'view-payroll',
                    'budget-vs-actual' => 'view-dashboard',
                    'market-analysis' => 'view-dashboard',
                    'sales-forecast' => 'view-dashboard',
                    'campaigns' => 'view-dashboard',
                    'lead-source-reports' => 'view-dashboard',
                    'reorder-levels' => 'view-dashboard',
                    'inventory-reports' => 'view-dashboard',
                    'stock-count' => 'view-products',
                    'batch-tracking' => 'view-dashboard',
                    'barcodes' => 'view-dashboard',
                    'variance-reports' => 'view-dashboard',
                    'asset-assignment' => 'view-assets',
                    'asset-maintenance' => 'view-assets',
                    'asset-disposal' => 'view-assets',
                    'incidents' => 'view-helpdesk-tickets',
                    'team-review' => 'view-dashboard',
                    'site-visits' => 'view-dashboard',
                    'customer-assets' => 'view-assets',
                    'monitoring-dashboard' => 'view-dashboard',
                    'credit-limits' => 'view-dashboard',
                    'overdue-accounts' => 'view-dashboard',
                    'tax-management' => 'view-dashboard',
                    'bank-reconciliation' => 'view-dashboard',
                    'cost-centre-budgets' => 'view-dashboard',
                ];

                if (isset($roleMenus[$roleName])) {
                    $myMenu = $roleMenus[$roleName];
                } else {
                    // Any role not explicitly curated above (including brand-new roles created
                    // and assigned in the future) gets its OWN menu auto-derived from the single
                    // source of truth in App\Support\RoleModules, instead of silently inheriting
                    // the administrator's menu.
                    $autoModules = \App\Support\RoleModules::allowedModules($roleName, $currentUser);
                    $myMenu = [
                        ['label' => 'Dashboard', 'route' => 'role.dashboard', 'icon' => $iconDashboard, 'match' => 'role.dashboard'],
                    ];
                    foreach ($autoModules as $autoModule) {
                        if ($autoModule === 'dashboard') {
                            continue;
                        }
                        $myMenu[] = [
                            'label' => \App\Support\RoleModules::label($autoModule),
                            'module' => $autoModule,
                            'icon' => \App\Support\RoleModules::icon($autoModule),
                        ];
                    }
                }
                $filteredMenu = [];
                foreach ($myMenu as $item) {
                    if ($isFullAdmin) {
                        $filteredMenu[] = $item;
                        continue;
                    }
                    if (isset($item['route'])) {
                        $requiredPerm = $permMap[$item['match']] ?? null;
                        if (!$requiredPerm || $currentUser->hasPermission($requiredPerm)) {
                            $filteredMenu[] = $item;
                        }
                    } else {
                        $requiredPerm = $modulePermMap[$item['module']] ?? 'view-dashboard';
                        if ($currentUser->hasPermission($requiredPerm)) {
                            $filteredMenu[] = $item;
                        }
                    }
                }
                $myMenu = $filteredMenu;
            @endphp
            @foreach($myMenu as $item)
            @php
                $itemUrl = isset($item['route']) ? route($item['route']) : route('role.page', ['module' => $item['module']]);
                $itemActive = isset($item['match']) ? request()->routeIs($item['match']) : (request()->routeIs('role.page') && request()->segment(2) === $item['module']);
            @endphp
            <a href="{{ $itemUrl }}" class="sidebar-link font-menu w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 text-sm font-medium {{ $itemActive ? 'active' : '' }}">
                <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
            {{-- Hide grouped nav for non-admin users --}}
            @php $navGroups = []; @endphp
            @endif

            {{-- Grouped items (collapsible dropdowns) --}}
            @php
                $currentRoute = request()->route() ? request()->route()->getName() : '';
            @endphp
            @foreach($navGroups as $gi => $group)
                @php
                    $groupHasActive = false;
                    foreach ($group['items'] as $item) {
                        if (request()->routeIs($item['match'])) { $groupHasActive = true; break; }
                    }
                    $groupId = 'grp-' . $gi;
                @endphp
            <div class="pt-2">
                <button onclick="toggleSidebarGroup('{{ $groupId }}')" id="btn-{{ $groupId }}" class="sidebar-group-btn font-menu w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-emerald-200 text-sm font-semibold {{ $groupHasActive ? 'open' : '' }}">
                    <span class="flex items-center gap-2">
                        <span class="font-menu text-[13px] tracking-wide text-emerald-300/80 font-semibold">{{ $group['title'] }}</span>
                    </span>
                    <svg class="chevron w-3.5 h-3.5 text-emerald-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div id="{{ $groupId }}" class="sidebar-submenu {{ $groupHasActive ? 'open' : '' }}">
                    <div class="pt-1 space-y-0.5 pl-1">
                        @foreach($group['items'] as $item)
                        <div class="sidebar-submenu-item">
                            <a href="{{ route($item['route']) }}" class="sidebar-link font-menu w-full flex items-center gap-3 px-3 py-2 rounded-lg text-emerald-100/80 text-sm font-medium {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                                <svg class="w-4 h-4 text-gold-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Bottom User --}}
        <div class="p-4 border-t border-emerald-800/50">
            <div class="flex items-center gap-3">
                @if(Auth::user()->avatar)
                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm">
                @else
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-xs font-menu">
                    {{ strtoupper(substr(Auth::user()->first_name ?? Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                @endif
                <div class="flex-1 min-w-0 font-menu">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->first_name ? Auth::user()->first_name . ' ' . Auth::user()->last_name : (Auth::user()->name ?? 'Admin') }}</p>
                    <p class="text-xs text-emerald-300/60">{{ auth()->user()->isAdmin() ? 'Administrator' : (auth()->user()->roles()->first()?->label ?? 'User') }}</p>
                </div>
                <a href="{{ route('docs') }}" target="_blank" class="text-emerald-300/60 hover:text-white transition-colors" title="Documentation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </a>
                <a href="{{ route('admin.profile') }}" class="text-emerald-300/60 hover:text-white transition-colors" title="My Profile">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
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
                <div class="lg:hidden font-brand text-xl font-bold text-emerald-900 leading-none flex items-center gap-2">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" class="w-7 h-7 object-contain rounded">
                    <span>ASYX</span><span class="text-gold-500">GROUP</span>
                </div>
                <h1 class="text-lg font-bold text-gray-800 font-menu">@yield('page_title', 'Dashboard')</h1>
                <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-200" title="System currently in use">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ config('app.name', 'ERP') }}
                </span>
            </div>
            <div class="flex items-center gap-4 font-menu">
                @yield('page_actions')
                {{-- AI Power Insights Button --}}
                <button onclick="if (typeof openAiInsightsModal === 'function') openAiInsightsModal(); else window.location.href='{{ auth()->user()?->isAdmin() ? route('admin.dashboard') : route('role.dashboard') }}';" class="flex items-center gap-2 px-3 py-1.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm" title="AI Power Insights">
                    <svg class="w-4 h-4 text-yellow-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span class="hidden sm:inline">Power</span>
                </button>
                @if(auth()->user()?->isAdmin() || auth()->user()?->hasRole('erp_super_administrator'))
                <a href="{{ route('admin.documentation') }}" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm" title="Manage Documentation">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span class="hidden sm:inline">Docs</span>
                </a>
                @endif
                {{-- Company Context Switcher --}}
                @php
                    $user = auth()->user();
                    $userCompany = $user->company;
                    $canSwitch = $user->isAdmin() || $user->isSuperAdmin() || ($userCompany && $userCompany->is_group);
                    $allCompanies = $canSwitch
                        ? \App\Models\Company::where('is_active', true)->orderBy('is_group', 'desc')->orderBy('name')->get()
                        : collect([$userCompany])->filter();
                    $sessionCompanyId = session('switched_company_id');
                    $currentCompany = $sessionCompanyId ? $allCompanies->firstWhere('id', $sessionCompanyId) : ($userCompany ?? $allCompanies->first());
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
                @php
                    $user = auth()->user();
                    $searchOptions = [];
                    if ($user->isAdmin() || $user->isSuperAdmin() || $user->hasPermission('view-employees')) {
                        $searchOptions[] = ['route' => 'admin.employees.index', 'param' => 'search', 'placeholder' => 'Search employees by name, email or ID...', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'];
                    }
                    if ($user->hasPermission('view-crm-leads') || $user->hasPermission('view-crm-deals')) {
                        $searchOptions[] = ['route' => 'admin.crm-leads.index', 'param' => 'search', 'placeholder' => 'Search leads...', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'];
                    }
                    if ($user->hasPermission('view-projects')) {
                        $searchOptions[] = ['route' => 'admin.projects.index', 'param' => 'search', 'placeholder' => 'Search projects...', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6'];
                    }
                    if ($user->hasPermission('view-products')) {
                        $searchOptions[] = ['route' => 'admin.products.index', 'param' => 'search', 'placeholder' => 'Search products...', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'];
                    }
                    if ($user->hasPermission('view-helpdesk-tickets')) {
                        $searchOptions[] = ['route' => 'admin.helpdesk-tickets.index', 'param' => 'search', 'placeholder' => 'Search tickets...', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z'];
                    }
                    if ($user->hasPermission('view-sales-invoices')) {
                        $searchOptions[] = ['route' => 'admin.sales-invoices.index', 'param' => 'search', 'placeholder' => 'Search sales invoices...', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'];
                    }
                    if ($user->hasPermission('view-purchase-invoices')) {
                        $searchOptions[] = ['route' => 'admin.purchase-invoices.index', 'param' => 'search', 'placeholder' => 'Search purchase invoices...', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'];
                    }
                    if ($user->hasPermission('view-documents')) {
                        $searchOptions[] = ['route' => 'admin.documents.index', 'param' => 'search', 'placeholder' => 'Search documents...', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
                    }
                    $activeSearch = $searchOptions[0] ?? null;
                @endphp

                @if($activeSearch)
                {{-- Permission-based Global Search --}}
                <form method="GET" action="{{ route($activeSearch['route']) }}" class="hidden md:flex items-center bg-gray-50 rounded-lg px-3 py-1.5 border border-gray-100">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="{{ $activeSearch['param'] }}" value="{{ request($activeSearch['param']) }}" placeholder="{{ $activeSearch['placeholder'] }}" class="bg-transparent text-sm outline-none w-64 text-gray-600 placeholder-gray-400">
                    <button class="ml-2 px-2 py-1 text-xs bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Search</button>
                </form>
                @endif

                {{-- Notifications Dropdown --}}
                @php
                    try {
                        $sessionCompanyId = session('switched_company_id', auth()->user()->company_id);
                        $user = auth()->user();
                        $recentLogs = \App\Models\AuditLog::query()
                            ->when($sessionCompanyId, function($q) use ($sessionCompanyId) { $q->where('company_id', $sessionCompanyId); })
                            ->when(!$user->isAdmin() && !$user->isSuperAdmin(), function($q) use ($user) { $q->where('user_id', $user->id); })
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
                    <div id="notifMenu" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-xl border shadow-lg z-50 py-2">
                        <div class="px-4 py-2 border-b flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-700">Your Notifications</span>
                            @if($user->isAdmin() || $user->hasPermission('view-audit-logs'))
                            <a href="{{ route('admin.audit-logs.index') }}" class="text-[11px] text-emerald-600 hover:text-emerald-700">View all</a>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto">
                            @forelse($recentLogs as $log)
                                <div class="px-4 py-2 hover:bg-gray-50 flex items-start gap-3">
                                    <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-bold">{{ strtoupper(substr($log->action ?? 'A',0,1)) }}</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-800 truncate">{{ $log->action ?? 'Activity' }} @if(!empty($log->module))<span class="text-gray-400">• {{ $log->module }}</span>@endif</p>
                                        <p class="text-[10px] text-gray-400">{{ $log->user?->name ?? 'System' }} • {{ optional($log->created_at)->diffForHumans() }}</p>
                                    </div>
                                    <button type="button" onclick="createFollowUpFromNotif('{{ $log->action ?? 'Activity' }}', '{{ $log->module ?? '' }}', '{{ $log->id }}')" class="text-[11px] text-emerald-600 hover:text-emerald-700 font-medium whitespace-nowrap" title="Create follow-up reminder">Follow up</button>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-[11px] text-gray-400">No recent notifications</div>
                            @endforelse
                        </div>
                        <div class="px-4 py-2 border-t bg-gray-50 flex items-center justify-between">
                            <span class="text-[11px] text-gray-500">Showing your activity</span>
                            <button type="button" onclick="openFollowUpsList()" class="text-[11px] text-emerald-600 hover:text-emerald-700 font-medium">My Follow-ups</button>
                        </div>
                    </div>
                </div>

                {{-- Follow Up List Modal --}}
                <div id="followUpListModal" class="fixed inset-0 bg-black/50 z-[60] hidden items-center justify-center">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-5 max-h-[80vh] flex flex-col">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-bold text-gray-900">My Follow-ups</h3>
                            <button type="button" onclick="document.getElementById('followUpListModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div id="followUpListContent" class="overflow-y-auto flex-1 space-y-2 text-sm">
                            <div class="text-center text-gray-400 py-6">Loading...</div>
                        </div>
                    </div>
                </div>

                {{-- Follow Up Modal --}}
                <div id="followUpModal" class="fixed inset-0 bg-black/50 z-[60] hidden items-center justify-center">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">Create Follow-up</h3>
                        <form id="followUpForm" onsubmit="return submitFollowUp(event)">
                            @csrf
                            <input type="hidden" id="followUpRelatedType" name="related_type">
                            <input type="hidden" id="followUpRelatedId" name="related_id">
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                                <input type="text" id="followUpTitle" name="title" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Note</label>
                                <textarea id="followUpNote" name="note" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"></textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Due Date</label>
                                <input type="datetime-local" id="followUpDue" name="due_at" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="document.getElementById('followUpModal').classList.add('hidden')" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                                <button type="submit" class="px-3 py-1.5 text-xs bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save Follow-up</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function createFollowUpFromNotif(action, module, id) {
                        document.getElementById('followUpTitle').value = 'Follow up: ' + action;
                        document.getElementById('followUpNote').value = module ? 'Related to ' + module : '';
                        document.getElementById('followUpRelatedType').value = module || 'audit_log';
                        document.getElementById('followUpRelatedId').value = id;
                        document.getElementById('followUpModal').classList.remove('hidden');
                        document.getElementById('followUpModal').classList.add('flex');
                    }
                    function submitFollowUp(e) {
                        e.preventDefault();
                        const form = document.getElementById('followUpForm');
                        const data = new FormData(form);
                        fetch('{{ route('follow-ups.store') }}', {
                            method: 'POST',
                            body: data,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        }).then(r => r.json()).then(res => {
                            if (res.success) {
                                alert('Follow-up created');
                                document.getElementById('followUpModal').classList.add('hidden');
                                document.getElementById('followUpModal').classList.remove('flex');
                                form.reset();
                            } else {
                                alert('Failed to create follow-up');
                            }
                        }).catch(() => alert('Failed to create follow-up'));
                        return false;
                    }
                    function openFollowUpsList() {
                        document.getElementById('followUpListModal').classList.remove('hidden');
                        document.getElementById('followUpListModal').classList.add('flex');
                        const container = document.getElementById('followUpListContent');
                        container.innerHTML = '<div class="text-center text-gray-400 py-6">Loading...</div>';
                        fetch('{{ route('follow-ups.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.json())
                            .then(res => {
                                if (!res.follow_ups || res.follow_ups.length === 0) {
                                    container.innerHTML = '<div class="text-center text-gray-400 py-6">No pending follow-ups</div>';
                                    return;
                                }
                                container.innerHTML = res.follow_ups.map(f => `
                                    <div class="border rounded-lg p-3 flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-800 truncate">${escapeHtml(f.title)}</p>
                                            ${f.note ? `<p class="text-xs text-gray-500 truncate">${escapeHtml(f.note)}</p>` : ''}
                                            ${f.due_at ? `<p class="text-[11px] text-amber-600">Due: ${new Date(f.due_at).toLocaleString()}</p>` : ''}
                                        </div>
                                        <button type="button" onclick="completeFollowUp(${f.id}, this)" class="text-xs bg-emerald-600 text-white px-2 py-1 rounded hover:bg-emerald-700">Done</button>
                                    </div>
                                `).join('');
                            })
                            .catch(() => container.innerHTML = '<div class="text-center text-red-500 py-6">Failed to load</div>');
                    }
                    function completeFollowUp(id, btn) {
                        btn.disabled = true;
                        btn.textContent = '...';
                        fetch('{{ url('/follow-ups') }}/' + id + '/complete', {
                            method: 'PATCH',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(r => r.json()).then(res => {
                            if (res.success) {
                                btn.closest('div').classList.add('opacity-50');
                                btn.textContent = 'Done';
                            } else {
                                btn.disabled = false;
                                btn.textContent = 'Done';
                            }
                        }).catch(() => { btn.disabled = false; btn.textContent = 'Done'; });
                    }
                    function escapeHtml(text) {
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    }
                </script>
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
        function toggleSidebarGroup(groupId) {
            const submenu = document.getElementById(groupId);
            const btn = document.getElementById('btn-' + groupId);
            if (!submenu || !btn) return;
            submenu.classList.toggle('open');
            btn.classList.toggle('open');
        }
    </script>
    @stack('scripts')
</body>
</html>
