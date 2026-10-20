@extends('layouts.auth')

@section('title', 'Page Not Found - ' . config('app.name', 'Laravel'))

@section('content')
<div class="w-full max-w-sm text-center" style="animation: simpleFadeIn 0.4s ease-out both;">
    <div class="bg-white/95 backdrop-blur-xl rounded-md shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] border border-white/60 px-8 py-10">
        <p class="text-6xl font-extrabold text-gray-900 tracking-tight">404</p>
        <div class="w-10 h-0.5 bg-gray-300 mx-auto my-4"></div>
        <h2 class="text-sm font-bold text-gray-800">Page Not Found</h2>
        <p class="text-xs text-gray-400 mt-1.5 mb-7 leading-relaxed">The page you're looking for doesn't exist or has been moved.</p>
        <a href="{{ auth()->check() ? url('/admin/dashboard') : url('/') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 text-sm font-semibold text-white bg-gray-900 hover:bg-gray-800 rounded transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ auth()->check() ? 'Back to Dashboard' : 'Back to Login' }}
        </a>
    </div>
    <p class="mt-6 text-[11px] text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
</div>
@endsection
