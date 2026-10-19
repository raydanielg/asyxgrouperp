@extends('layouts.auth')

@section('title', 'Forgot Password - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-sm" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 overflow-hidden">
        {{-- Header --}}
        <div class="px-7 pt-8 pb-2 text-center">
            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Forgot Password</h2>
            <p class="text-gray-500 text-[13px] mt-1">Enter your email to receive an activation code</p>
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

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-600 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-[18px] h-[18px] text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            class="w-full pl-10 pr-4 py-2.5 rounded border @error('email') border-red-300 ring-2 ring-red-100 @else border-gray-200 @enderror bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 focus:bg-white hover:bg-white outline-none transition-all"
                            placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-3 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Send Reset Code
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
@endsection
