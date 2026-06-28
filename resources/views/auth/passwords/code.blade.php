@extends('layouts.app')

@section('title', 'Activation Code - ASYX Group')

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-8 py-8 text-center">
            <div class="w-20 h-20 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-4">
                <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group" class="w-16 h-16 object-contain">
            </div>
            <h2 class="text-2xl font-extrabold text-white">Enter Activation Code</h2>
            <p class="text-emerald-100 text-sm mt-1">We sent a 6-digit code to your email</p>
        </div>

        {{-- Form --}}
        <div class="p-8">
            @if (session('status'))
                <div class="mb-5 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('status') }}
                </div>
        @endif

            @if (session('error'))
                <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
        @endif

            <div class="mb-6 p-4 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Code expires in <strong>15 minutes</strong>. Check your email inbox and spam folder.</span>
            </div>

            <form method="POST" action="{{ route('password.code.verify') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-1.5">6-Digit Activation Code</label>
                    <input id="code" type="text" name="code" required maxlength="6" autocomplete="one-time-code" autofocus
                        inputmode="numeric" pattern="[0-9]{6}"
                        class="w-full text-center text-3xl font-bold tracking-[0.5em] py-4 rounded-lg border @error('code') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"
                        placeholder="000000"
                        style="font-family:'Courier New',monospace;">
                    @error('code')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Verify Code
                </button>
            </form>

            <form method="POST" action="{{ route('password.resend') }}" class="mt-4">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="w-full py-2.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Didn't receive code? Resend
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-sm"><span class="px-3 bg-white text-gray-400">or</span></div>
            </div>

            <p class="text-center text-sm text-gray-500">
                Remember your password?
                <a href="{{ route('login') }}" class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">Sign in</a>
            </p>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} ASYX Group. All rights reserved.</p>
</div>

<script>
document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '').substring(0, 6);
});
</script>
@endsection
