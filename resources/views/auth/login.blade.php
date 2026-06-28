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
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="quickLogin('admin@djanproject.com', 'admin12345')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-emerald-400 hover:bg-emerald-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-emerald-700">Admin</span>
                    </button>
                    <button type="button" onclick="quickLogin('director@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-violet-400 hover:bg-violet-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-violet-100 group-hover:bg-violet-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-violet-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-violet-700">Director</span>
                    </button>
                    <button type="button" onclick="quickLogin('hr@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-sky-400 hover:bg-sky-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-sky-100 group-hover:bg-sky-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-sky-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-sky-700">HR</span>
                    </button>
                    <button type="button" onclick="quickLogin('finance@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-amber-400 hover:bg-amber-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-amber-700">Finance</span>
                    </button>
                    <button type="button" onclick="quickLogin('cashier@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-rose-400 hover:bg-rose-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-rose-100 group-hover:bg-rose-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-rose-700">Cashier</span>
                    </button>
                    <button type="button" onclick="quickLogin('tech.manager@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-indigo-100 group-hover:bg-indigo-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-indigo-700">Tech Mgr</span>
                    </button>
                    <button type="button" onclick="quickLogin('auditor@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-teal-400 hover:bg-teal-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-teal-100 group-hover:bg-teal-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-teal-700">Auditor</span>
                    </button>
                    <button type="button" onclick="quickLogin('logistics@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-cyan-400 hover:bg-cyan-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-cyan-100 group-hover:bg-cyan-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-cyan-700">Logistics</span>
                    </button>
                    <button type="button" onclick="quickLogin('receptionist@djanproject.com', 'password123')" class="group flex flex-col items-center gap-1.5 p-2.5 rounded-lg border border-gray-200 hover:border-pink-400 hover:bg-pink-50 transition-all">
                        <span class="w-8 h-8 rounded-full bg-pink-100 group-hover:bg-pink-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </span>
                        <span class="text-[10px] font-semibold text-gray-600 group-hover:text-pink-700">Reception</span>
                    </button>
                </div>
                <p class="mt-2 text-center text-[10px] text-gray-400">Click a role to auto-fill &amp; login instantly</p>
            </div>
            <script>
            function quickLogin(email, password) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
                document.querySelector('#loginBtn').closest('form').submit();
            }
            </script>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} ASYX Group. All rights reserved.</p>
</div>
@endsection
