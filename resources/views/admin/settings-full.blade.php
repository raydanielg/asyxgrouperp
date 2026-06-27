@extends('layouts.admin')
@section('title', 'Settings - ' . config('app.name'))
@section('page_title', 'System Settings')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Application Settings</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Site Title</label><input name="titleText" value="{{ old('titleText', \App\Models\Setting::get('titleText', config('app.name'))) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Footer Text</label><input name="footerText" value="{{ old('footerText', \App\Models\Setting::get('footerText', 'All rights reserved.')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Default Language</label><select name="defaultLanguage" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"><option value="en" {{ \App\Models\Setting::get('defaultLanguage') === 'en' ? 'selected' : '' }}>English</option><option value="fr" {{ \App\Models\Setting::get('defaultLanguage') === 'fr' ? 'selected' : '' }}>French</option><option value="sw" {{ \App\Models\Setting::get('defaultLanguage') === 'sw' ? 'selected' : '' }}>Swahili</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Default Currency</label><input name="defaultCurrency" value="{{ old('defaultCurrency', \App\Models\Setting::get('defaultCurrency', 'TZS')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Date Format</label><input name="dateFormat" value="{{ old('dateFormat', \App\Models\Setting::get('dateFormat', 'd M Y')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Time Format</label><input name="timeFormat" value="{{ old('timeFormat', \App\Models\Setting::get('timeFormat', 'H:i')) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="space-y-2">
                <label class="flex items-center gap-2"><input type="checkbox" name="enableRegistration" {{ \App\Models\Setting::get('enableRegistration') === 'on' ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Enable Registration</span></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="enableEmailVerification" {{ \App\Models\Setting::get('enableEmailVerification') === 'on' ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Enable Email Verification</span></label>
            </div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Settings</button>
        </form>
    </div>
    @if($settings->count() > 0)
    <div class="bg-white rounded-xl border p-6 mt-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">All Settings</h3>
        <div class="space-y-1">@foreach($settings as $setting)<div class="flex justify-between text-xs py-1.5 border-b border-gray-50"><span class="text-gray-500">{{ $setting->key }}</span><span class="text-gray-900 font-medium">{{ $setting->value }}</span></div>@endforeach</div>
    </div>
    @endif
</div>
@endsection
