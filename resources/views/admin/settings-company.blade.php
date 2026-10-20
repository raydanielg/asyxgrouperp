@extends('layouts.admin')
@section('title', 'Company Settings - ' . config('app.name'))
@section('page_title', 'Company Settings')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf

            {{-- Company profile --}}
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Company Profile</h3></div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Company Name</label>
                        <input name="name" value="{{ old('name', $company?->name) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                        <input name="email" type="email" value="{{ old('email', $company?->email) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                        <input name="phone" value="{{ old('phone', $company?->phone) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                        <input name="address" value="{{ old('address', $company?->address) }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Notifications</h3></div>
                <div class="p-5">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Email Notifications</p>
                            <p class="text-xs text-gray-400 mt-0.5">Send emails to your company's users — invoices, approvals, tickets, and more</p>
                        </div>
                        <input type="hidden" name="emails_enabled" value="0">
                        <input type="checkbox" name="emails_enabled" value="1" {{ old('emails_enabled', $company?->emails_enabled ?? true) ? 'checked' : '' }} class="toggle-check sr-only">
                        <span class="toggle-track w-11 h-6 rounded-full bg-gray-200 relative transition-colors flex-shrink-0 ml-4">
                            <span class="toggle-dot absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"></span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Save Settings</button>
            </div>
        </form>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Company Info</h3></div>
            <div class="divide-y divide-gray-100">
                <div class="px-5 py-3 flex justify-between"><span class="text-xs text-gray-400">Company</span><span class="text-xs font-medium text-gray-900">{{ $company?->name ?? '—' }}</span></div>
                <div class="px-5 py-3 flex justify-between"><span class="text-xs text-gray-400">Plan</span><span class="text-xs font-medium text-gray-900 capitalize">{{ $company?->subscription_plan ?? '—' }}</span></div>
                <div class="px-5 py-3 flex justify-between"><span class="text-xs text-gray-400">Status</span><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $company?->subscription_status === 'active' ? '#10b981' : '#f59e0b' }}"></span>{{ ucfirst($company?->subscription_status ?? 'pending') }}</span></div>
                <div class="px-5 py-3 flex justify-between"><span class="text-xs text-gray-400">Emails</span><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $company?->emails_enabled ? '#10b981' : '#d1d5db' }}"></span>{{ $company?->emails_enabled ? 'Enabled' : 'Disabled' }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Email Templates</h3>
                <a href="{{ route('admin.email-templates.index') }}" class="text-[10px] font-medium text-gray-600 hover:text-gray-900">Manage →</a>
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-500 leading-relaxed">Your company has <span class="font-bold text-gray-900">{{ $templates->count() }}</span> custom template{{ $templates->count() !== 1 ? 's' : '' }}. Platform defaults are used for everything else.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
.toggle-check:checked + .toggle-track { background: #0f172a; }
.toggle-check:checked + .toggle-track .toggle-dot { transform: translateX(20px); }
</style>
@endpush
@endsection
