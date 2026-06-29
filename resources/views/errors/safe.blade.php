@extends('layouts.app')

@section('title', 'Page Issue')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="max-w-md w-full text-center">
        <div class="mb-6">
            <div class="w-20 h-20 mx-auto bg-emerald-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Oops! Something went wrong</h1>
        <p class="text-gray-600 mb-6">{{ $message ?? 'We encountered an issue while loading this page. Our team has been notified.' }}</p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg text-sm font-medium transition-colors">Go Back</a>
            <a href="{{ auth()->check() ? (auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')) : route('login') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-colors">Dashboard</a>
        </div>
        @if(isset($code) && app()->environment('local'))
        <p class="mt-6 text-xs text-gray-400">Error code: {{ $code }}</p>
        @endif
    </div>
</div>
@endsection
