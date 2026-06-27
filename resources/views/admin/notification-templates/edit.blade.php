@extends('layouts.admin')
@section('title', 'Edit Notification Template - ' . config('app.name'))
@section('page_title', 'Edit Notification Template')
@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Edit Notification Template</h3>
        <form method="POST" action="{{ route('admin.notification-templates.update', $notificationTemplate) }}" class="space-y-4">
            @csrf @method('PATCH')
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Name *</label><input name="name" value="{{ old('name', $notificationTemplate->name) }}" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><input name="type" value="{{ old('type', $notificationTemplate->type) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Subject</label><input name="subject" value="{{ old('subject', $notificationTemplate->subject) }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Content</label><textarea name="content" rows="10" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm font-mono focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">{{ old('content', $notificationTemplate->content) }}</textarea></div>
            <div><label class="flex items-center gap-2"><input type="checkbox" name="is_active" {{ old('is_active', $notificationTemplate->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600"><span class="text-xs text-gray-600">Active</span></label></div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('admin.notification-templates.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
