@extends('layouts.auth')

@section('title', 'Register - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="relative bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 overflow-hidden">

        {{-- Header --}}
        <div class="px-7 pt-8 pb-2 text-center">
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Create Account</h2>
            <p class="text-xs text-gray-400 mt-1">Sign up to get started</p>
        </div>

        {{-- Form --}}
        <div class="px-7 pb-7 pt-4">
            <form method="POST" action="{{ route('register') }}" class="space-y-4" id="registerForm">
                @csrf

                {{-- Name --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="first_name" class="block text-xs font-semibold text-gray-600 mb-1.5">First Name</label>
                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" autofocus
                            class="w-full px-3 py-2.5 rounded border @error('first_name') border-rose-300 ring-2 ring-rose-100 @else border-gray-200 @enderror bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="John">
                        @error('first_name')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-semibold text-gray-600 mb-1.5">Last Name</label>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name"
                            class="w-full px-3 py-2.5 rounded border @error('last_name') border-rose-300 ring-2 ring-rose-100 @else border-gray-200 @enderror bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="Doe">
                        @error('last_name')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            class="w-full pl-10 pr-4 py-2.5 rounded border @error('email') border-rose-300 ring-2 ring-rose-100 @else border-gray-200 @enderror bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="you@example.com">
                    </div>
                    @error('email')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-600 mb-1.5">Phone Number</label>
                    <input type="hidden" name="phone" id="phone-hidden" value="{{ old('phone') }}">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center">
                            <div class="flex items-center gap-1.5 bg-gray-50 border-r border-gray-200 px-3 rounded-l h-full">
                                <img src="https://flagcdn.com/w40/tz.png" alt="TZ" class="w-5 h-3.5 object-cover rounded-sm">
                                <span class="text-xs font-bold text-gray-700 select-none">+255</span>
                            </div>
                        </div>
                        <input id="phone-display" type="tel" inputmode="numeric" autocomplete="tel"
                            class="w-full pl-[92px] pr-4 py-2.5 rounded border @error('phone') border-rose-300 ring-2 ring-rose-100 @else border-gray-200 @enderror bg-gray-50 text-sm font-mono text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="7XX XXX XXX" maxlength="9"
                            value="{{ old('phone') ? preg_replace('/^255/', '', old('phone')) : '' }}">
                    </div>
                    @error('phone')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@else<p class="mt-1.5 text-[11px] text-gray-400">9 digits starting with 7 or 6</p>@enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="new-password" minlength="8"
                            class="w-full pl-10 pr-4 py-2.5 rounded border @error('password') border-rose-300 ring-2 ring-rose-100 @else border-gray-200 @enderror bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="Min. 8 characters">
                    </div>
                    @error('password')<p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Confirm --}}
                <div>
                    <label for="password-confirm" class="block text-xs font-semibold text-gray-600 mb-1.5">Confirm Password</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-3 py-2.5 rounded border border-gray-200 bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                        placeholder="Re-enter your password">
                </div>

                {{-- Submit --}}
                <button type="submit" class="w-full py-3 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create Account
                </button>
            </form>

            {{-- Login link --}}
            <p class="mt-5 text-center text-xs text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-gray-700 hover:text-gray-900">Sign in</a>
            </p>
        </div>
    </div>

    <p class="mt-6 text-center text-[11px] text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>

<script>
(function() {
    const phoneDisplay = document.getElementById('phone-display');
    const phoneHidden  = document.getElementById('phone-hidden');
    const form         = document.getElementById('registerForm');
    if (phoneDisplay && phoneHidden) {
        function syncPhone() {
            let raw = phoneDisplay.value.replace(/\D/g, '');
            if (raw.length > 0 && !/^[67]/.test(raw)) raw = raw.substring(1);
            if (raw.length > 9) raw = raw.substring(0, 9);
            phoneDisplay.value = raw;
            phoneHidden.value = (/^[67]/.test(raw) && raw.length === 9) ? '255' + raw : '';
        }
        phoneDisplay.addEventListener('input', syncPhone);
        phoneDisplay.addEventListener('paste', () => setTimeout(syncPhone, 0));
        phoneDisplay.addEventListener('blur', syncPhone);
        form && form.addEventListener('submit', function(e) {
            syncPhone();
            if (!phoneHidden.value || phoneHidden.value.length !== 12) {
                e.preventDefault();
                phoneDisplay.focus();
                phoneDisplay.classList.add('border-rose-300');
            }
        });
        syncPhone();
    }
})();
</script>
@endsection
