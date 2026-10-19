@extends('layouts.auth')

@section('title', 'Activation Code - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-sm" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 overflow-hidden">
        {{-- Header --}}
        <div class="px-7 pt-8 pb-2 text-center">
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Enter Activation Code</h2>
            <p class="text-gray-500 text-[13px] mt-1">We sent a 6-digit code to your email</p>
        </div>

        {{-- Form --}}
        <div class="px-7 pb-7 pt-4">
            @if (session('status'))
                <div class="mb-4 p-3.5 rounded bg-gray-50 border border-gray-200 text-[13px] text-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('status') }}
                </div>
        @endif

            @if (session('error'))
                <div class="mb-4 p-3.5 rounded bg-red-50 border border-red-200 text-[13px] text-red-700 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
        @endif

            <div class="mb-5 p-3.5 rounded bg-amber-50 border border-amber-200 text-[13px] text-amber-700 flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Code expires in <strong>15 minutes</strong>. Check your inbox and spam folder.</span>
            </div>

            <form method="POST" action="{{ route('password.code.verify') }}" class="space-y-5" id="otpForm">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="code" id="code">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2 text-center">6-Digit Activation Code</label>
                    <div class="flex items-center justify-center gap-2" id="otpBoxes">
                        @for($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code"
                            class="otp-box w-11 h-12 text-center text-xl font-bold rounded border @error('code') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror bg-gray-50 text-gray-800 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white outline-none transition-all">
                        @endfor
                    </div>
                    @error('code')
                        <p class="mt-2 text-xs text-red-600 flex items-center justify-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Verify Code
                </button>
            </form>

            <form method="POST" action="{{ route('password.resend') }}" class="mt-3">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="w-full py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-800 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Didn't receive code? Resend
                </button>
            </form>

            <div class="relative my-5">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                <div class="relative flex justify-center text-xs"><span class="px-3 bg-white text-gray-400">or</span></div>
            </div>

            <p class="text-center text-[13px] text-gray-500">
                Remember your password?
                <a href="{{ route('login') }}" class="font-semibold text-gray-700 hover:text-gray-900 transition-colors">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
(function() {
    const boxes = document.querySelectorAll('.otp-box');
    const hidden = document.getElementById('code');
    const form = document.getElementById('otpForm');

    function sync() {
        hidden.value = Array.from(boxes).map(b => b.value).join('');
    }

    boxes.forEach((box, i) => {
        box.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '').substring(0, 1);
            if (this.value && i < boxes.length - 1) boxes[i + 1].focus();
            sync();
            if (hidden.value.length === 6) form.requestSubmit();
        });
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && i > 0) boxes[i - 1].focus();
        });
        box.addEventListener('paste', function(e) {
            e.preventDefault();
            const digits = (e.clipboardData.getData('text') || '').replace(/\D/g, '').substring(0, 6);
            digits.split('').forEach((d, j) => { if (boxes[j]) boxes[j].value = d; });
            sync();
            if (hidden.value.length === 6) form.requestSubmit();
            else boxes[Math.min(digits.length, 5)].focus();
        });
    });

    form.addEventListener('submit', sync);
    if (boxes.length) boxes[0].focus();
})();
</script>
@endsection
