@extends('layouts.app')

@section('title', 'Registration Disabled - ASYX Group')

@section('content')
<div class="w-full max-w-md" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-8 py-8 text-center">
            <div class="w-20 h-20 mx-auto bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-4">
                <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX Group" class="w-16 h-16 object-contain">
            </div>
            <h2 class="text-2xl font-extrabold text-white">Registration Disabled</h2>
            <p class="text-emerald-100 text-sm mt-1">Account creation is currently not available</p>
        </div>

        {{-- Content --}}
        <div class="p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-5">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>

            <h3 class="text-lg font-bold text-gray-800 mb-2">Account Creation is Disabled</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-6">
                Self-registration is currently disabled for security reasons. If you need an account, please contact the system administrator who will create one for you.
            </p>

            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-left mb-6">
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wide mb-2">Contact Administrator</p>
                <div class="flex items-center gap-2 text-sm text-emerald-600 mb-1">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>admin@djanproject.com</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-emerald-600">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>+255 700 000 000</span>
                </div>
            </div>

            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-gray-900 bg-gradient-to-r from-gold-300 to-gold-400 hover:from-gold-400 hover:to-gold-500 rounded-lg shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Back to Login
            </a>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ date('Y') }} ASYX Group. All rights reserved.</p>
</div>
@endsection
