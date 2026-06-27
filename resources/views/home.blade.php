@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-4xl" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 px-8 py-6 flex items-center gap-4">
            <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-white">Dashboard</h2>
                <p class="text-emerald-100 text-sm">Welcome back, {{ Auth::user()->first_name ?? Auth::user()->name }}!</p>
            </div>
        </div>

        <div class="p-8">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-5 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Your Name</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ Auth::user()->name }}</p>
                </div>
                <div class="p-5 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Email</p>
                    <p class="text-sm font-bold text-gray-800 mt-1 break-all">{{ Auth::user()->email }}</p>
                </div>
                <div class="p-5 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-gold-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Phone</p>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ Auth::user()->phone ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-6 p-5 rounded-xl bg-gradient-to-br from-emerald-50 to-gold-50 border border-emerald-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">You are logged in!</p>
                        <p class="text-xs text-gray-500">Your account is active and ready to go.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
