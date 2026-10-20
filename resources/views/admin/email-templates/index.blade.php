@extends('layouts.admin')
@section('title', 'Email Templates - ' . config('app.name'))
@section('page_title', 'Email Templates')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Email templates used across modules</span>
        <span class="text-xs text-gray-400">{{ $templates->total() }} templates</span>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Name</th><th class="px-5 py-3 font-semibold">From</th><th class="px-5 py-3 font-semibold">Subject</th><th class="px-5 py-3 font-semibold">Module</th><th class="px-5 py-3 font-semibold">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($templates as $template)
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $template->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-500">{{ $template->from ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 max-w-xs truncate">{{ $template->subject ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500 capitalize">{{ $template->module_name ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $template->is_active ? '#10b981' : '#d1d5db' }}"></span>{{ $template->is_active ? 'Active' : 'Inactive' }}</span></td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400 text-xs">No templates found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($templates->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $templates->links() }}</div>
    @endif
</div>
@endsection
