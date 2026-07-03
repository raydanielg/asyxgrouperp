@extends('layouts.app')

@section('title', 'Login - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-8 py-8 text-center">
            <div class="w-20 h-20 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-4">
                <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group" class="w-16 h-16 object-contain">
            </div>
            <h2 class="text-2xl font-extrabold text-white">Welcome Back</h2>
            <p class="text-emerald-100 text-sm mt-1">Sign in to your account</p>
        </div>

        {{-- Form --}}
        <div class="p-8">
            @if (session('status'))
                <div class="mb-5 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('status') }}
                </div>
        @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm"
                            placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-11 pr-4 py-2.5 rounded-lg border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm"
                            placeholder="Enter your password">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition-colors">Forgot password?</a>
        @endif
                </div>

                {{-- Submit --}}
                <button type="submit" id="loginBtn" class="w-full py-3 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span id="loginBtnText">Sign In</span>
                </button>
                <script>
                document.querySelector('#loginBtn').closest('form').addEventListener('submit', function(e) {
                    document.getElementById('loginBtn').disabled = true;
                    document.getElementById('loginBtnText').textContent = 'Signing In...';
                    document.getElementById('loginBtn').classList.add('opacity-70', 'cursor-not-allowed');
                });
                </script>
            </form>

            {{-- Demo Quick Login --}}
            <div class="mt-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Demo Quick Login</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                {{-- Admin shortcut --}}
                <button type="button" onclick="quickLogin('admin@djanproject.com', 'password123')" class="w-full mb-3 px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Login as Super Admin
                </button>

                {{-- Role search + grid --}}
                <div class="relative mb-2">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="roleSearch" placeholder="Find a role..." class="w-full pl-8 pr-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>

                <div id="roleGrid" class="grid grid-cols-3 sm:grid-cols-4 gap-1.5 max-h-40 overflow-y-auto p-1 rounded-lg border border-gray-100 bg-gray-50/50">
                    @php
                        $roleGroups = [
                            'System' => [
                                ['erp.super.administrator@djanproject.com', 'ERP Super Admin', 'emerald'],
                                ['erp.administrator@djanproject.com', 'ERP Admin', 'teal'],
                                ['ict.administrator@djanproject.com', 'ICT Admin', 'cyan'],
                            ],
                            'Executive' => [
                                ['managing.director@djanproject.com', 'Managing Director', 'violet'],
                                ['general.manager@djanproject.com', 'General Manager', 'indigo'],
                                ['technical.manager@djanproject.com', 'Technical Manager', 'blue'],
                                ['operations.manager@djanproject.com', 'Operations Manager', 'sky'],
                            ],
                            'Finance' => [
                                ['finance.manager@djanproject.com', 'Finance Mgr', 'amber'],
                                ['chief.accountant@djanproject.com', 'Chief Acct', 'yellow'],
                                ['accountant@djanproject.com', 'Accountant', 'orange'],
                                ['accounts.receivable.officer@djanproject.com', 'AR Officer', 'lime'],
                                ['accounts.payable.officer@djanproject.com', 'AP Officer', 'green'],
                                ['payroll.officer@djanproject.com', 'Payroll', 'emerald'],
                                ['budget.officer@djanproject.com', 'Budget', 'teal'],
                                ['credit.controller@djanproject.com', 'Credit Ctrl', 'rose'],
                                ['cashier@djanproject.com', 'Cashier', 'pink'],
                            ],
                            'Procurement' => [
                                ['procurement.manager@djanproject.com', 'Procurement Mgr', 'amber'],
                                ['procurement.officer@djanproject.com', 'Procurement Off', 'orange'],
                                ['tender.officer@djanproject.com', 'Tender Off', 'yellow'],
                            ],
                            'Inventory' => [
                                ['store.manager@djanproject.com', 'Store Mgr', 'emerald'],
                                ['storekeeper@djanproject.com', 'Storekeeper', 'teal'],
                                ['inventory.controller@djanproject.com', 'Inventory Ctrl', 'cyan'],
                                ['asset.officer@djanproject.com', 'Asset Off', 'sky'],
                            ],
                            'Sales & CRM' => [
                                ['sales.manager@djanproject.com', 'Sales Mgr', 'amber'],
                                ['business.development.manager@djanproject.com', 'BDM', 'orange'],
                                ['sales.executive@djanproject.com', 'Sales Exec', 'yellow'],
                                ['crm.officer@djanproject.com', 'CRM Off', 'pink'],
                                ['marketing.officer@djanproject.com', 'Marketing', 'fuchsia'],
                            ],
                            'Projects' => [
                                ['project.director@djanproject.com', 'Project Director', 'violet'],
                                ['project.manager@djanproject.com', 'Project Mgr', 'indigo'],
                                ['technical.projects.manager@djanproject.com', 'Tech Proj Mgr', 'blue'],
                                ['project.coordinator@djanproject.com', 'Project Coord', 'sky'],
                                ['project.engineer@djanproject.com', 'Project Eng', 'cyan'],
                                ['site.supervisor@djanproject.com', 'Site Supervisor', 'teal'],
                                ['team.leader@djanproject.com', 'Team Leader', 'emerald'],
                                ['project.accountant@djanproject.com', 'Project Acct', 'amber'],
                            ],
                            'Technical' => [
                                ['senior.systems.engineer@djanproject.com', 'Senior Sys Eng', 'blue'],
                                ['systems.engineer@djanproject.com', 'Systems Eng', 'indigo'],
                                ['network.engineer@djanproject.com', 'Network Eng', 'cyan'],
                                ['software.engineer@djanproject.com', 'Software Eng', 'sky'],
                                ['cybersecurity.engineer@djanproject.com', 'Cyber Eng', 'teal'],
                                ['support.engineer@djanproject.com', 'Support Eng', 'emerald'],
                                ['field.technician@djanproject.com', 'Field Tech', 'green'],
                                ['noc.engineer@djanproject.com', 'NOC Eng', 'orange'],
                            ],
                            'Service Desk' => [
                                ['service.desk.manager@djanproject.com', 'Service Desk Mgr', 'rose'],
                                ['helpdesk.supervisor@djanproject.com', 'Helpdesk Sup', 'pink'],
                                ['helpdesk.officer@djanproject.com', 'Helpdesk Off', 'fuchsia'],
                                ['call.center.supervisor@djanproject.com', 'CC Sup', 'red'],
                                ['call.center.agent@djanproject.com', 'CC Agent', 'rose'],
                            ],
                            'HR' => [
                                ['hr.manager@djanproject.com', 'HR Manager', 'sky'],
                                ['hr.officer@djanproject.com', 'HR Officer', 'cyan'],
                                ['recruitment.officer@djanproject.com', 'Recruitment', 'teal'],
                                ['training.officer@djanproject.com', 'Training', 'emerald'],
                                ['time.and.attendance.officer@djanproject.com', 'T&A Officer', 'indigo'],
                            ],
                            'Operations' => [
                                ['operations.officer@djanproject.com', 'Ops Officer', 'orange'],
                                ['fleet.manager@djanproject.com', 'Fleet Mgr', 'amber'],
                                ['logistics.officer@djanproject.com', 'Logistics', 'yellow'],
                            ],
                            'Self Service' => [
                                ['employee.self.service@djanproject.com', 'Employee SS', 'emerald'],
                                ['manager.self.service@djanproject.com', 'Manager SS', 'teal'],
                            ],
                            'Legacy / Shared' => [
                                ['director@djanproject.com', 'Director', 'violet'],
                                ['administrator@djanproject.com', 'Administrator', 'slate'],
                                ['admin.manager@djanproject.com', 'Admin Manager', 'gray'],
                                ['finance.officer@djanproject.com', 'Finance Officer', 'amber'],
                                ['auditor@djanproject.com', 'Auditor', 'teal'],
                                ['legal.officer@djanproject.com', 'Legal Officer', 'lime'],
                                ['receptionist@djanproject.com', 'Receptionist', 'pink'],
                                ['technician@djanproject.com', 'Technician', 'blue'],
                                ['ict.officer@djanproject.com', 'ICT Officer', 'fuchsia'],
                                ['supervisor@djanproject.com', 'Supervisor', 'yellow'],
                            ],
                        ];
                        $allQuickRoles = [];
                        foreach ($roleGroups as $group => $items) {
                            foreach ($items as $item) {
                                $allQuickRoles[] = [$item[0], 'password123', $item[1], $item[2], $group];
                            }
                        }
                    @endphp
                    @foreach($allQuickRoles as $r)
                        <button type="button" data-role-label="{{ strtolower($r[2]) }} {{ strtolower($r[4]) }}" onclick="quickLogin('{{ $r[0] }}', '{{ $r[1] }}')" class="role-chip px-2 py-1.5 rounded-md border border-gray-200 bg-white hover:border-{{ $r[3] }}-400 hover:bg-{{ $r[3] }}-50 text-[10px] font-semibold text-gray-600 hover:text-{{ $r[3] }}-700 transition-all text-center leading-tight">
                            {{ $r[2] }}
                        </button>
                    @endforeach
                </div>
                <p class="mt-2 text-center text-[10px] text-gray-400">Click any role to auto-fill &amp; login instantly</p>
            </div>
            <script>
            function quickLogin(email, password) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
                document.querySelector('#loginBtn').closest('form').submit();
            }

            document.getElementById('roleSearch').addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.role-chip').forEach(function(btn) {
                    const label = btn.getAttribute('data-role-label');
                    btn.style.display = label.includes(term) ? 'block' : 'none';
                });
            });
            </script>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} ASYX Group. All rights reserved.</p>
</div>
@endsection
