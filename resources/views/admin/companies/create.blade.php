@extends('layouts.admin')
@section('title', 'Add Company - ' . config('app.name'))
@section('page_title', 'Add Company')
@section('content')
<div class="mb-4">
    <a href="{{ route('admin.companies.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Companies</a>
</div>

<div class="max-w-2xl bg-white rounded-md border border-gray-200 overflow-hidden">
    {{-- Header strip — matches sidebar --}}
    <div class="px-6 py-4 bg-slate-900 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-white">New Company</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">Register a subsidiary under ASYX Group</p>
        </div>
        <svg class="w-5 h-5 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"/></svg>
    </div>

    <form method="POST" action="{{ route('admin.companies.store') }}" class="p-6">
        @csrf

        {{-- Identity --}}
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Identity</p>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Legal Name <span class="text-red-500">*</span></label>
                <input type="text" name="legal_name" value="{{ old('legal_name') }}" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Short Code <span class="text-red-500">*</span></label>
                <input type="text" name="short_code" value="{{ old('short_code') }}" required maxlength="10" placeholder="e.g. PARKTECH" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0 uppercase font-mono">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Currency <span class="text-red-500">*</span></label>
                <input type="text" name="currency" value="{{ old('currency', 'TZS') }}" required maxlength="3" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Registration Number</label>
                <input type="text" name="registration_number" value="{{ old('registration_number') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Parent Company</label>
                <select name="parent_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
                    <option value="">— None —</option>
                    @foreach($parents as $id => $name)
                    <option value="{{ $id }}" @selected(old('parent_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Tax & Contact --}}
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Tax &amp; Contact</p>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tax ID (TIN)</label>
                <input type="text" name="tax_id" value="{{ old('tax_id') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">VRN</label>
                <input type="text" name="vrn" value="{{ old('vrn') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Website</label>
                <input type="text" name="website" value="{{ old('website') }}" placeholder="https://" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
        </div>

        {{-- Location --}}
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Location</p>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">{{ old('address') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">City</label>
                <input type="text" name="city" value="{{ old('city', 'Dar es Salaam') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Country</label>
                <input type="text" name="country" value="{{ old('country', 'Tanzania') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 focus:ring-0">
            </div>
        </div>

        {{-- Settings --}}
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Settings</p>
        <div class="flex items-center gap-8 mb-6">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="is_group" value="1" @checked(old('is_group')) class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                Is Group Company
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                Active
            </label>
        </div>

        @if($errors->any())
        <div class="mb-4 px-3 py-2 rounded bg-red-50 border border-red-100 text-xs text-red-600">{{ $errors->first() }}</div>
        @endif

        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Company</button>
            <a href="{{ route('admin.companies.index') }}" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
