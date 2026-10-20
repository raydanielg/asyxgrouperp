@extends('layouts.auth')

@section('title', 'Login - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-sm relative" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="relative bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 overflow-hidden">

        {{-- Header --}}
        <div class="px-7 pt-8 pb-2 text-center">
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Welcome Back</h2>
        </div>

        {{-- Form --}}
        <div class="px-7 pb-7 pt-4">
            @if (session('status'))
                <div class="mb-4 p-3.5 rounded bg-gray-50 border border-gray-200 text-[13px] text-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-10 pr-4 py-2.5 rounded border @error('email') border-rose-300 ring-2 ring-rose-100 @else border-gray-200 @enderror bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full pl-10 pr-10 py-2.5 rounded border @error('password') border-rose-300 ring-2 ring-rose-100 @else border-gray-200 @enderror bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="Enter your password">
                        <button type="button" onclick="var p=document.getElementById('password');p.type=p.type==='password'?'text':'password'" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-gray-600 focus:ring-gray-400">
                        <span class="text-[13px] text-gray-500">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[13px] font-semibold text-gray-600 hover:text-gray-900 transition-colors">Forgot password?</a>
        @endif
                </div>

                {{-- Submit --}}
                <button type="submit" id="loginBtn" class="w-full py-3 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded transition-colors flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg id="btnIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span id="loginBtnText">Sign In</span>
                </button>
            </form>

            @if(\App\Models\Setting::get('enableRegistration') === 'on')
            <div class="mt-4 text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 rounded border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 hover:border-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create an account
                </a>
            </div>
            @endif

            {{-- Demo Quick Login Toggle --}}
            <div class="mt-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                    <span class="text-xs font-semibold text-gray-600 px-2">Demo Access</span>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                </div>

                <button type="button" id="quickLoginToggle" class="w-full py-2.5 rounded bg-gray-50 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 text-gray-700 text-xs font-bold transition-all flex items-center justify-center gap-2 group">
                    <svg class="w-4 h-4 text-gray-500 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Quick Login as Demo Role
                </button>
            </div>

            {{-- Sliding Demo Roles Sidebar (inside card) --}}
            <div id="demoSidebar" class="absolute inset-0 z-20 bg-white transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col rounded-md">
                {{-- Sidebar header --}}
                <div class="px-5 py-4 flex items-center justify-between shrink-0 border-b border-gray-200 bg-gray-900 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-white/5 rounded-full -mr-8 -mt-8"></div>
                    <div class="relative flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-white font-bold text-sm">Select Demo Role</span>
                    </div>
                    <button type="button" id="closeDemoSidebar" class="relative w-7 h-7 rounded bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Search --}}
                <div class="p-3.5 border-b border-gray-100 shrink-0">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" id="roleSearch" placeholder="Find a role..." class="w-full pl-9 pr-3 py-2 rounded border border-gray-200 text-xs focus:border-gray-400 focus:ring-2 focus:ring-gray-200 outline-none transition-all">
                    </div>
                </div>

                {{-- Roles list --}}
                <div class="flex-1 overflow-y-auto p-3.5">
                    <div id="roleGrid" class="space-y-4">
                        @php
                            $roleGroups = [
                                'Administration' => [
                                    ['superadmin@djanproject.com', 'ERP Super Administrator', 'emerald'],
                                    ['admin@djanproject.com', 'ERP Administrator', 'emerald'],
                                ],
                                'Executive' => [
                                    ['managing.director@djanproject.com', 'Managing Director', 'violet'],
                                    ['general.manager@djanproject.com', 'General Manager', 'violet'],
                                    ['technical.manager@djanproject.com', 'Technical Manager', 'blue'],
                                    ['operations.manager@djanproject.com', 'Operations Manager', 'orange'],
                                ],
                                'Finance' => [
                                    ['finance.manager@djanproject.com', 'Finance Manager', 'amber'],
                                    ['chief.accountant@djanproject.com', 'Chief Accountant', 'amber'],
                                    ['accountant@djanproject.com', 'Accountant', 'orange'],
                                    ['accounts.receivable.officer@djanproject.com', 'AR Officer', 'orange'],
                                    ['accounts.payable.officer@djanproject.com', 'AP Officer', 'orange'],
                                    ['cashier@djanproject.com', 'Cashier', 'amber'],
                                    ['payroll.officer@djanproject.com', 'Payroll Officer', 'amber'],
                                    ['budget.officer@djanproject.com', 'Budget Officer', 'amber'],
                                    ['credit.controller@djanproject.com', 'Credit Controller', 'orange'],
                                ],
                                'Procurement & Inventory' => [
                                    ['procurement.manager@djanproject.com', 'Procurement Manager', 'amber'],
                                    ['procurement.officer@djanproject.com', 'Procurement Officer', 'amber'],
                                    ['tender.officer@djanproject.com', 'Tender Officer', 'indigo'],
                                    ['store.manager@djanproject.com', 'Store Manager', 'sky'],
                                    ['storekeeper@djanproject.com', 'Storekeeper', 'sky'],
                                    ['inventory.controller@djanproject.com', 'Inventory Controller', 'sky'],
                                    ['asset.officer@djanproject.com', 'Asset Officer', 'sky'],
                                ],
                                'Sales & CRM' => [
                                    ['sales.manager@djanproject.com', 'Sales Manager', 'amber'],
                                    ['business.development.manager@djanproject.com', 'BD Manager', 'amber'],
                                    ['sales.executive@djanproject.com', 'Sales Executive', 'orange'],
                                    ['crm.officer@djanproject.com', 'CRM Officer', 'orange'],
                                    ['marketing.officer@djanproject.com', 'Marketing Officer', 'pink'],
                                ],
                                'Projects' => [
                                    ['project.director@djanproject.com', 'Project Director', 'violet'],
                                    ['project.manager@djanproject.com', 'Project Manager', 'indigo'],
                                    ['technical.projects.manager@djanproject.com', 'Technical Projects Manager', 'indigo'],
                                    ['project.coordinator@djanproject.com', 'Project Coordinator', 'indigo'],
                                    ['project.engineer@djanproject.com', 'Project Engineer', 'blue'],
                                    ['site.supervisor@djanproject.com', 'Site Supervisor', 'blue'],
                                    ['team.leader@djanproject.com', 'Team Leader', 'blue'],
                                    ['project.accountant@djanproject.com', 'Project Accountant', 'amber'],
                                ],
                                'Technical' => [
                                    ['senior.systems.engineer@djanproject.com', 'Senior Systems Engineer', 'blue'],
                                    ['systems.engineer@djanproject.com', 'Systems Engineer', 'blue'],
                                    ['network.engineer@djanproject.com', 'Network Engineer', 'cyan'],
                                    ['software.engineer@djanproject.com', 'Software Engineer', 'cyan'],
                                    ['cybersecurity.engineer@djanproject.com', 'Cybersecurity Engineer', 'rose'],
                                    ['support.engineer@djanproject.com', 'Support Engineer', 'cyan'],
                                    ['field.technician@djanproject.com', 'Field Technician', 'cyan'],
                                    ['noc.engineer@djanproject.com', 'NOC Engineer', 'cyan'],
                                ],
                                'Service Desk' => [
                                    ['service.desk.manager@djanproject.com', 'Service Desk Manager', 'emerald'],
                                    ['helpdesk.supervisor@djanproject.com', 'Helpdesk Supervisor', 'emerald'],
                                    ['helpdesk.officer@djanproject.com', 'Helpdesk Officer', 'teal'],
                                    ['call.center.supervisor@djanproject.com', 'Call Center Supervisor', 'teal'],
                                    ['call.center.agent@djanproject.com', 'Call Center Agent', 'teal'],
                                ],
                                'HR' => [
                                    ['hr.manager@djanproject.com', 'HR Manager', 'sky'],
                                    ['hr.officer@djanproject.com', 'HR Officer', 'sky'],
                                    ['recruitment.officer@djanproject.com', 'Recruitment Officer', 'sky'],
                                    ['training.officer@djanproject.com', 'Training Officer', 'sky'],
                                    ['time.attendance.officer@djanproject.com', 'Time & Attendance Officer', 'sky'],
                                ],
                                'Operations' => [
                                    ['operations.officer@djanproject.com', 'Operations Officer', 'orange'],
                                    ['fleet.manager@djanproject.com', 'Fleet Manager', 'orange'],
                                    ['logistics.officer@djanproject.com', 'Logistics Officer', 'orange'],
                                ],
                                'Self-Service' => [
                                    ['employee.self.service@djanproject.com', 'Employee Self-Service', 'gray'],
                                    ['manager.self.service@djanproject.com', 'Manager Self-Service', 'gray'],
                                ],
                            ];
                        @endphp
                        @foreach($roleGroups as $groupName => $items)
                            <div class="role-group" data-group-label="{{ strtolower($groupName) }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2 py-0.5 rounded bg-gray-800 text-white text-[10px] font-bold uppercase tracking-wide">{{ $groupName }}</span>
                                    <span class="text-[10px] text-gray-400">{{ count($items) }}</span>
                                </div>
                                <div class="space-y-1.5">
                                    @foreach($items as $r)
                                        <button type="button" data-role-label="{{ strtolower($r[1]) }} {{ strtolower($groupName) }}" onclick="quickLogin('{{ $r[0] }}')" class="role-chip w-full px-3 py-2.5 rounded border border-gray-200 bg-white hover:border-gray-400 hover:bg-gray-50 transition-all flex items-center gap-3 group">
                                            <span class="w-8 h-8 rounded bg-gray-100 text-gray-600 flex items-center justify-center flex-shrink-0 font-bold text-xs group-hover:scale-110 transition-transform">{{ strtoupper(substr($r[1], 0, 1)) }}</span>
                                            <div class="flex-1 text-left">
                                                <p class="text-[13px] font-bold text-gray-700 group-hover:text-gray-900 leading-tight">{{ $r[1] }}</p>
                                                <p class="text-[10px] text-gray-400 mt-0.5">Demo login</p>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Sidebar footer --}}
                <div class="p-3 border-t border-gray-100 bg-gray-50 shrink-0">
                    <p class="text-center text-[10px] text-gray-400 font-medium">Click any role to auto-fill &amp; login instantly</p>
                </div>
            </div>
            <script>
            function quickLogin(email, password) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = password || 'password123';
                document.getElementById('loginBtn').disabled = true;
                document.getElementById('btnIcon').innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';
                document.getElementById('loginBtnText').textContent = 'Signing In...';
                document.querySelector('#loginBtn').closest('form').submit();
            }

            (function() {
                const toggle = document.getElementById('quickLoginToggle');
                const sidebar = document.getElementById('demoSidebar');
                const close = document.getElementById('closeDemoSidebar');
                const search = document.getElementById('roleSearch');

                function openSidebar() {
                    if (sidebar) sidebar.classList.remove('translate-x-full');
                    if (search) setTimeout(() => search.focus(), 300);
                }

                function closeSidebar() {
                    if (sidebar) sidebar.classList.add('translate-x-full');
                }

                if (toggle) toggle.addEventListener('click', openSidebar);
                if (close) close.addEventListener('click', closeSidebar);

                if (search) {
                    search.addEventListener('input', function(e) {
                        const term = e.target.value.toLowerCase();
                        document.querySelectorAll('.role-group').forEach(function(group) {
                            let visibleCount = 0;
                            group.querySelectorAll('.role-chip').forEach(function(btn) {
                                const label = btn.getAttribute('data-role-label');
                                const visible = label.includes(term);
                                btn.style.display = visible ? 'flex' : 'none';
                                if (visible) visibleCount++;
                            });
                            group.style.display = visibleCount > 0 ? 'block' : 'none';
                        });
                    });
                }
            })();
            </script>
        </div>
    </div>
</div>
@endsection
