@extends('layouts.app')
@section('title', 'Page Not Found')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="max-w-sm w-full text-center">
        <p class="text-5xl font-bold text-gray-300">404</p>
        <div class="w-8 h-0.5 bg-gray-300 mx-auto my-4"></div>
        <h1 class="text-lg font-bold text-gray-900">Page Not Found</h1>
        <p class="text-sm text-gray-500 mt-2 mb-6">The page you're looking for doesn't exist or has been moved.</p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url()->previous() }}" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-200 rounded hover:bg-gray-100">Go Back</a>
            <a href="{{ auth()->check() ? route('role.dashboard') : route('login') }}" class="px-4 py-2 text-sm font-medium text-white bg-slate-800 rounded hover:bg-slate-700">Dashboard</a>
        </div>
    </div>
</div>
@endsection
