@extends('layouts.app')

@section('title', 'Registration Successful - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.5s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden text-center">
        {{-- Success Animation Header --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 px-8 py-10 relative overflow-hidden">
            <div class="absolute top-2 left-4 w-2 h-2 rounded-full bg-white/20"></div>
            <div class="absolute top-6 right-8 w-3 h-3 rounded-full bg-white/15"></div>
            <div class="absolute bottom-4 left-10 w-2 h-2 rounded-full bg-white/25"></div>
            <div class="absolute top-10 right-4 w-1.5 h-1.5 rounded-full bg-white/30"></div>

            <div class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-4 shadow-lg" style="animation: scaleIn 0.4s ease-out 0.2s both;">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation: checkDraw 0.5s ease-out 0.4s both;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 class="text-2xl font-extrabold text-white">Account Created!</h2>
            <p class="text-emerald-100 text-sm mt-1">Welcome to {{ config('app.name', 'Laravel') }}</p>
        </div>

        {{-- Body --}}
        <div class="p-8">
            <div class="space-y-4">
                <p class="text-sm text-gray-600 leading-relaxed">
                    Your account has been successfully created. You can now log in and get started.
                </p>
        @if(session('registered_email'))
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-lg border border-gray-100">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="text-xs text-gray-600 font-medium">{{ session('registered_email') }}</span>
                </div>
        @endif

                {{-- Steps indicator --}}
                <div class="flex items-center justify-center gap-3 py-2">
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-[10px] text-gray-400">Register</span>
                    </div>
                    <div class="w-8 h-0.5 bg-emerald-200 rounded-full"></div>
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </div>
                        <span class="text-[10px] text-gray-500 font-medium">Log In</span>
                    </div>
                    <div class="w-8 h-0.5 bg-gray-200 rounded-full"></div>
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-[10px] text-gray-400">Start</span>
                    </div>
                </div>
            </div>

            {{-- Login Button --}}
            <div class="mt-8 space-y-3">
                <a href="{{ route('login') }}" class="w-full py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 rounded-xl shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Go to Login
                </a>

                <a href="{{ url('/') }}" class="w-full py-2.5 text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>
@endsection
