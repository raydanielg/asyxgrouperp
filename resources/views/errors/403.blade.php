@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-red-50 px-4">
    <div class="max-w-lg w-full text-center">
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg ring-4 ring-red-50">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Access Denied</h1>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <p class="text-xl text-red-700 font-bold">
                {{ $message ?? 'You are not allowed to access this page.' }}
            </p>
        </div>
        <p class="text-sm text-gray-600 mb-8">
            Your account does not have the required permission to view this section. If you believe this is an error, please contact your system administrator.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}" class="px-5 py-2.5 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                Go Back
            </a>
            <a href="{{ route('role.dashboard') }}" class="px-5 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition-colors">
                Dashboard
            </a>
            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors shadow-md flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Login
            </a>
        </div>
    </div>
</div>
@endsection
