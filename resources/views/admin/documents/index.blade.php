@extends('layouts.admin')
@section('title', 'Documents - ' . config('app.name'))
@section('page_title', 'Document Management')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Central document repository with e-signatures</p>
    <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Upload Document
    </a>
</div>
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
@endif
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Number</th><th class="px-5 py-3 font-medium">Title</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium">Version</th><th class="px-5 py-3 font-medium">Signatures</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>
        @forelse($documents as $doc)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium">{{ $doc->document_number }}</td>
            <td class="px-5 py-3 text-xs"><a href="{{ route('admin.documents.show', $doc) }}" class="text-gray-800 hover:text-emerald-600">{{ $doc->title }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ str_replace('_', ' ', $doc->category ?? '—') }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">v{{ $doc->version }}</td>
            <td class="px-5 py-3 text-xs text-gray-600">{{ $doc->signatures->where('status', 'signed')->count() }} / {{ $doc->signatures->count() }}</td>
            <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $doc->status === 'signed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : ($doc->status === 'pending_signature' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-gray-50 text-gray-500 border border-gray-100') }}">{{ $doc->status }}</span></td>
            <td class="px-5 py-3 flex items-center gap-3">
                <a href="{{ route('admin.documents.show', $doc) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">View</a>
                <a href="{{ route('admin.documents.download', $doc) }}" class="text-emerald-600 hover:text-emerald-700 text-xs">Download</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No documents uploaded</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $documents->links() }}</div>
</div>
@endsection
