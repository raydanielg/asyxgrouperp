@extends('layouts.admin')
@section('title', 'Notification Templates - ' . config('app.name'))
@section('page_title', 'Notification Templates')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Notification templates sent to users</span>
        <span class="text-xs text-gray-400">{{ $templates->total() }} templates</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold">Type</th><th class="px-5 py-3 font-semibold">Subject</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($templates as $template)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $template->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ $template->type ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 max-w-xs truncate">{{ $template->subject ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $template->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $template->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="px-5 py-3.5 text-right"><a href="{{ route('admin.notification-templates.edit', $template) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Edit</a></td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-xs">No notification templates found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($templates->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $templates->links() }}</div>
    @endif
</div>
@endsection
