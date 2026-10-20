@extends('layouts.admin')
@section('title', 'New Email Template - ' . config('app.name'))
@section('page_title', 'New Email Template')
@section('content')

<a href="{{ route('admin.email-templates.index') }}" class="text-xs text-gray-400 hover:text-gray-600">← Email Templates</a>

<div class="max-w-2xl mt-3">
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Create Template</h3></div>
        <form method="POST" action="{{ route('admin.email-templates.store') }}" class="p-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Name <span class="text-red-500">*</span></label><input name="name" value="{{ old('name') }}" required placeholder="e.g. Invoice Reminder" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">@error('name')<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror</div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">From Email</label><input name="from" type="email" value="{{ old('from') }}" placeholder="no-reply@company.com" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 font-mono"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Subject</label><input name="subject" value="{{ old('subject') }}" placeholder="Invoice {invoice_number} from {company_name}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Content</label><textarea name="content" rows="10" placeholder="Dear {customer_name},&#10;&#10;..." class="w-full px-3 py-2 text-sm font-mono border border-gray-200 rounded outline-none focus:border-gray-400 leading-relaxed">{{ old('content') }}</textarea></div>
            <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer"><input type="checkbox" name="is_active" checked class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">Active</label>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.email-templates.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800">Cancel</a>
                    <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create Template</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
