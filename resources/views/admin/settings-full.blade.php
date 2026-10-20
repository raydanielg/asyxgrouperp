@extends('layouts.admin')
@section('title', 'Settings - ' . config('app.name'))
@section('page_title', 'System Settings')
@section('content')
@php
    $s = fn($k, $d = null) => \App\Models\Setting::get($k, $d);
@endphp

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf

            {{-- General --}}
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
                <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">General</h3></div>
                <div class="p-5 space-y-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Site Title</label><input name="titleText" value="{{ old('titleText', $s('titleText', config('app.name'))) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Footer Text</label><input name="footerText" value="{{ old('footerText', $s('footerText', 'All rights reserved.')) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Support Email</label><input name="supportEmail" type="email" value="{{ old('supportEmail', $s('supportEmail')) }}" placeholder="support@company.com" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Support Phone</label><input name="supportPhone" value="{{ old('supportPhone', $s('supportPhone')) }}" placeholder="+255 ..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    </div>
                </div>
            </div>

            {{-- Regional --}}
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
                <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Regional & Format</h3></div>
                <div class="p-5 grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Default Language</label><select name="defaultLanguage" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="en" {{ $s('defaultLanguage') === 'en' ? 'selected' : '' }}>English</option><option value="fr" {{ $s('defaultLanguage') === 'fr' ? 'selected' : '' }}>French</option><option value="sw" {{ $s('defaultLanguage') === 'sw' ? 'selected' : '' }}>Swahili</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Default Currency</label><input name="defaultCurrency" value="{{ old('defaultCurrency', $s('defaultCurrency', 'TZS')) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Date Format</label><select name="dateFormat" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="d M Y" {{ $s('dateFormat', 'd M Y') === 'd M Y' ? 'selected' : '' }}>d M Y (20 Oct 2026)</option><option value="d/m/Y" {{ $s('dateFormat') === 'd/m/Y' ? 'selected' : '' }}>d/m/Y (20/10/2026)</option><option value="Y-m-d" {{ $s('dateFormat') === 'Y-m-d' ? 'selected' : '' }}>Y-m-d (2026-10-20)</option><option value="m/d/Y" {{ $s('dateFormat') === 'm/d/Y' ? 'selected' : '' }}>m/d/Y (10/20/2026)</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Timezone</label><select name="timezone" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="Africa/Dar_es_Salaam" {{ $s('timezone', 'Africa/Dar_es_Salaam') === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar es Salaam (EAT)</option><option value="Africa/Nairobi" {{ $s('timezone') === 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option><option value="UTC" {{ $s('timezone') === 'UTC' ? 'selected' : '' }}>UTC</option><option value="Africa/Lagos" {{ $s('timezone') === 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos (WAT)</option><option value="Africa/Johannesburg" {{ $s('timezone') === 'Africa/Johannesburg' ? 'selected' : '' }}>Africa/Johannesburg (SAST)</option></select></div>
                </div>
            </div>

            {{-- Signup & Security toggles --}}
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
                <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Signup & Security</h3></div>
                <div class="divide-y divide-gray-100">
                    @php
                        $toggles = [
                            ['key' => 'enableRegistration', 'label' => 'Enable Registration', 'desc' => 'Allow new users to sign up from the public page'],
                            ['key' => 'enableEmailVerification', 'label' => 'Email Verification', 'desc' => 'New users must verify their email before logging in'],
                            ['key' => 'enableTwoFactor', 'label' => 'Two-Factor Authentication', 'desc' => 'Require OTP codes on admin logins'],
                            ['key' => 'lockFailedLogins', 'label' => 'Lock After Failed Logins', 'desc' => 'Temporarily lock accounts after 5 failed attempts'],
                        ];
                    @endphp
                    @foreach($toggles as $t)
                    <div class="px-5 py-4 flex items-center justify-between gap-4">
                        <div><p class="text-xs font-medium text-gray-900">{{ $t['label'] }}</p><p class="text-[11px] text-gray-400 mt-0.5">{{ $t['desc'] }}</p></div>
                        <input type="hidden" name="{{ $t['key'] }}" value="off">
                        <label class="relative inline-flex cursor-pointer items-center flex-shrink-0">
                            <input type="checkbox" name="{{ $t['key'] }}" value="on" class="peer sr-only" {{ $s($t['key']) === 'on' ? 'checked' : '' }}>
                            <div class="w-9 h-5 rounded-full bg-gray-200 peer-checked:bg-gray-900 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4 after:bg-white after:rounded-full after:transition-transform after:shadow-sm peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- System toggles --}}
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
                <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">System</h3></div>
                <div class="divide-y divide-gray-100">
                    @php
                        $sysToggles = [
                            ['key' => 'maintenanceMode', 'label' => 'Maintenance Mode', 'desc' => 'Show a maintenance page to non-admin users'],
                            ['key' => 'enableEmailNotifications', 'label' => 'Email Notifications', 'desc' => 'Send emails for invoices, approvals and alerts'],
                            ['key' => 'enableSmsNotifications', 'label' => 'SMS Notifications', 'desc' => 'Send SMS alerts where configured'],
                            ['key' => 'enableAuditLogging', 'label' => 'Audit Logging', 'desc' => 'Record user actions in the audit log'],
                        ];
                    @endphp
                    @foreach($sysToggles as $t)
                    <div class="px-5 py-4 flex items-center justify-between gap-4">
                        <div><p class="text-xs font-medium text-gray-900">{{ $t['label'] }}</p><p class="text-[11px] text-gray-400 mt-0.5">{{ $t['desc'] }}</p></div>
                        <input type="hidden" name="{{ $t['key'] }}" value="off">
                        <label class="relative inline-flex cursor-pointer items-center flex-shrink-0">
                            <input type="checkbox" name="{{ $t['key'] }}" value="on" class="peer sr-only" {{ $s($t['key']) === 'on' ? 'checked' : '' }}>
                            <div class="w-9 h-5 rounded-full bg-gray-200 peer-checked:bg-gray-900 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4 after:bg-white after:rounded-full after:transition-transform after:shadow-sm peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Settings</button>
            </div>
        </form>
    </div>

    {{-- Sidebar info --}}
    <div class="space-y-4">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Environment</h3></div>
            <div class="divide-y divide-gray-50 text-xs">
                <div class="px-5 py-2.5 flex justify-between"><span class="text-gray-500">App Name</span><span class="text-gray-900 font-medium">{{ config('app.name') }}</span></div>
                <div class="px-5 py-2.5 flex justify-between"><span class="text-gray-500">Environment</span><span class="inline-flex items-center gap-1.5 text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ app()->environment('production') ? '#10b981' : '#f59e0b' }}"></span>{{ config('app.env') }}</span></div>
                <div class="px-5 py-2.5 flex justify-between"><span class="text-gray-500">Database</span><span class="text-gray-900 font-mono">{{ config('database.default') }}</span></div>
                <div class="px-5 py-2.5 flex justify-between"><span class="text-gray-500">PHP</span><span class="text-gray-900 font-mono">{{ phpversion() }}</span></div>
                <div class="px-5 py-2.5 flex justify-between"><span class="text-gray-500">Laravel</span><span class="text-gray-900 font-mono">{{ app()->version() }}</span></div>
            </div>
        </div>

        @if($settings->count() > 0)
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Stored Keys</h3></div>
            <div class="divide-y divide-gray-50">
                @foreach($settings as $setting)
                <div class="flex items-center justify-between px-5 py-2">
                    <span class="text-[11px] font-mono text-gray-500">{{ $setting->key }}</span>
                    <span class="text-[11px] text-gray-800 font-medium max-w-[120px] truncate text-right">{{ $setting->value ?? '—' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
