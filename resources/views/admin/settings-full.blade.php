@extends('layouts.admin')
@section('title', 'Settings - ' . config('app.name'))
@section('page_title', 'System Settings')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="max-w-2xl">
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Application Settings</h3></div>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-5 space-y-4">
            @csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Site Title</label><input name="titleText" value="{{ old('titleText', \App\Models\Setting::get('titleText', config('app.name'))) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Footer Text</label><input name="footerText" value="{{ old('footerText', \App\Models\Setting::get('footerText', 'All rights reserved.')) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Default Language</label><select name="defaultLanguage" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="en" {{ \App\Models\Setting::get('defaultLanguage') === 'en' ? 'selected' : '' }}>English</option><option value="fr" {{ \App\Models\Setting::get('defaultLanguage') === 'fr' ? 'selected' : '' }}>French</option><option value="sw" {{ \App\Models\Setting::get('defaultLanguage') === 'sw' ? 'selected' : '' }}>Swahili</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Default Currency</label><input name="defaultCurrency" value="{{ old('defaultCurrency', \App\Models\Setting::get('defaultCurrency', 'TZS')) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Date Format</label><input name="dateFormat" value="{{ old('dateFormat', \App\Models\Setting::get('dateFormat', 'd M Y')) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Time Format</label><input name="timeFormat" value="{{ old('timeFormat', \App\Models\Setting::get('timeFormat', 'H:i')) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            </div>
            <div class="flex items-center gap-6 pt-1">
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="enableRegistration" {{ \App\Models\Setting::get('enableRegistration') === 'on' ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Enable Registration</label>
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="enableEmailVerification" {{ \App\Models\Setting::get('enableEmailVerification') === 'on' ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Enable Email Verification</label>
            </div>
            <div class="pt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Settings</button>
            </div>
        </form>
    </div>

    @if($settings->count() > 0)
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">All Settings</h3></div>
        <div class="divide-y divide-gray-50">
            @foreach($settings as $setting)
            <div class="flex items-center justify-between px-5 py-2.5">
                <span class="text-xs font-mono text-gray-500">{{ $setting->key }}</span>
                <span class="text-xs text-gray-900 font-medium max-w-xs truncate">{{ $setting->value ?? '—' }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
