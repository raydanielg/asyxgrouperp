@extends('layouts.admin')
@section('title', 'Notification Templates - ' . config('app.name'))
@section('page_title', 'Notification Templates')
@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Name</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Subject</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($templates as $template)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $template->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $template->type ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $template->subject ?? 'N/A' }}</td>
            <td class="px-5 py-3">@if($template->is_active)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>@else<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-100">Inactive</span>@endif</td>
            <td class="px-5 py-3"><a href="{{ route('admin.notification-templates.edit', $template) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Edit</a></td>
        </tr>@empty<tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No notification templates found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $templates->links() }}</div>
</div>
@endsection
