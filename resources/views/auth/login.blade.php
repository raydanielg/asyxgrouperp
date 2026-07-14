@extends('layouts.auth')

@section('title', 'Login - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden relative">
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
                            class="w-full pl-11 pr-10 py-2.5 rounded-lg border @error('password') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm"
                            placeholder="Enter your password">
                        <button type="button" onclick="var p=document.getElementById('password');p.type=p.type==='password'?'text':'password'" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
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
                <button type="submit" id="loginBtn" class="w-full py-3 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg id="btnIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span id="loginBtnText">Sign In</span>
                </button>
            </form>

            {{-- Demo Quick Login Toggle --}}
            <div class="mt-6">
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Demo Access</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <button type="button" id="quickLoginToggle" class="w-full py-2.5 rounded-lg bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-xs font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 group">
                    <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Quick Login as Demo Role
                </button>
            </div>

            {{-- Sliding Demo Roles Sidebar (inside card) --}}
            <div id="demoSidebar" class="absolute inset-0 z-20 bg-white transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col rounded-2xl">
                {{-- Sidebar header --}}
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-6 py-4 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-white font-bold text-sm">Select Demo Role</span>
                    </div>
                    <button type="button" id="closeDemoSidebar" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Search --}}
                <div class="p-4 border-b border-gray-100 shrink-0">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" id="roleSearch" placeholder="Find a role..." class="w-full pl-9 pr-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                </div>

                {{-- Roles list --}}
                <div class="flex-1 overflow-y-auto p-4">
                    <div id="roleGrid" class="space-y-4">
                        @php
                            $roleGroups = [
                                'Administration' => [
                                    ['admin@djanproject.com', 'Super Admin', 'emerald', 'admin12345'],
                                    ['system.admin@djanproject.com', 'System Admin', 'teal'],
                                ],
                                'Executive' => [
                                    ['director@djanproject.com', 'Director', 'violet'],
                                ],
                                'Finance' => [
                                    ['finance.manager@djanproject.com', 'Finance Manager', 'amber'],
                                    ['accountant@djanproject.com', 'Accountant', 'orange'],
                                ],
                                'Procurement & Operations' => [
                                    ['procurement.manager@djanproject.com', 'Procurement Manager', 'amber'],
                                    ['operations.manager@djanproject.com', 'Operations Manager', 'orange'],
                                ],
                                'Sales & Projects' => [
                                    ['sales.manager@djanproject.com', 'Sales Manager', 'amber'],
                                    ['project.manager@djanproject.com', 'Project Manager', 'indigo'],
                                ],
                                'Technical' => [
                                    ['technical.manager@djanproject.com', 'Technical Manager', 'blue'],
                                ],
                                'HR' => [
                                    ['hr.manager@djanproject.com', 'HR Manager', 'sky'],
                                ],
                            ];
                        @endphp
                        @foreach($roleGroups as $groupName => $items)
                            <div class="role-group" data-group-label="{{ strtolower($groupName) }}">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2.5 py-1 rounded-md bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wide">{{ $groupName }}</span>
                                    <span class="text-[10px] text-gray-400">{{ count($items) }} roles</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($items as $r)
                                        <button type="button" data-role-label="{{ strtolower($r[1]) }} {{ strtolower($groupName) }}" onclick="quickLogin('{{ $r[0] }}', '{{ $r[3] ?? 'password123' }}')" class="role-chip w-full px-4 py-3 rounded-xl border border-gray-200 bg-white hover:border-{{ $r[2] }}-400 hover:bg-{{ $r[2] }}-50 transition-all flex items-center gap-3 group">
                                            <span class="w-10 h-10 rounded-lg bg-{{ $r[2] }}-100 text-{{ $r[2] }}-600 flex items-center justify-center flex-shrink-0 font-bold text-sm">{{ strtoupper(substr($r[1], 0, 1)) }}</span>
                                            <div class="flex-1 text-left">
                                                <p class="text-sm font-bold text-gray-700 group-hover:text-{{ $r[2] }}-700 leading-tight">{{ $r[1] }}</p>
                                                <p class="text-[10px] text-gray-400 mt-0.5">Demo login</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-300 group-hover:text-{{ $r[2] }}-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Sidebar footer --}}
                <div class="p-4 border-t border-gray-100 bg-gray-50 shrink-0">
                    <p class="text-center text-[10px] text-gray-400">Click any role to auto-fill &amp; login instantly</p>
                </div>
            </div>
            <script>
            function quickLogin(email, password) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
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
