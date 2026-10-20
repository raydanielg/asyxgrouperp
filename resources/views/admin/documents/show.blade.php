@extends('layouts.admin')
@section('title', $document->title . ' - ' . config('app.name'))
@section('page_title', $document->title)
@section('content')
@php
    $dsc = ['signed' => '#10b981', 'pending_signature' => '#f59e0b', 'draft' => '#3b82f6', 'archived' => '#9ca3af'][$document->status] ?? '#9ca3af';
    $ft = strtolower($document->file_type ?? '');
    $previewable = str_starts_with($ft, 'image/') || in_array($ft, ['application/pdf','text/plain','pdf','png','jpg','jpeg','gif','webp','svg','txt']);
@endphp

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.documents.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Documents</a>
    <div class="flex items-center gap-2">
        @if($document->signatures()->where('signer_id', auth()->id())->where('status', 'pending')->exists())
        <form method="POST" action="{{ route('admin.documents.sign', $document) }}" class="inline">@csrf
            <button class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Sign Document</button>
        </form>
        @endif
        @if($document->status !== 'archived')
        <form method="POST" action="{{ route('admin.documents.archive', $document) }}" class="inline" onsubmit="return confirmAction(this, {title: 'Archive this document?'})">@csrf
            <button class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Archive</button>
        </form>
        @endif
        <a href="{{ route('admin.documents.download', $document) }}" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Download</a>
        <form id="del-doc-{{ $document->id }}" method="POST" action="{{ route('admin.documents.destroy', $document) }}" class="inline">@csrf @method('DELETE')</form>
        <button onclick="confirmDelete('del-doc-{{ $document->id }}')" class="px-3 py-1.5 border border-gray-200 text-red-600 text-xs font-medium rounded hover:bg-red-50 hover:border-red-200">Delete</button>
    </div>
</div>

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border border-red-200 rounded-md px-5 py-3 mb-4 text-xs text-red-700">{{ session('error') }}</div>
@endif

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-bold text-gray-900">{{ $document->document_number }}</span>
                <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dsc }}"></span>{{ str_replace('_', ' ', ucfirst($document->status)) }}</span>
                @if($document->is_confidential)<span class="text-[9px] font-semibold text-red-500 border border-red-200 rounded px-1.5 py-0.5">CONFIDENTIAL</span>@endif
                @if($document->isExpired())<span class="text-[9px] font-semibold text-red-500">EXPIRED</span>@endif
            </div>
            <h1 class="text-base font-bold text-gray-900 mt-1.5">{{ $document->title }}</h1>
            @if($document->description)
            <p class="text-xs text-gray-400 mt-1">{{ $document->description }}</p>
            @endif
        </div>
        <div class="text-right flex-shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Version</p>
            <p class="text-lg font-bold text-gray-900 mt-0.5">v{{ $document->version }}</p>
        </div>
    </div>
    <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-xs">
        <div><p class="text-gray-400">Category</p><p class="text-gray-800 mt-0.5 capitalize">{{ str_replace('_', ' ', $document->category ?? '—') }}</p></div>
        <div><p class="text-gray-400">File Type</p><p class="text-gray-800 mt-0.5 uppercase">{{ str_contains($document->file_type, '/') ? last(explode('/', $document->file_type)) : $document->file_type }}</p></div>
        <div><p class="text-gray-400">Size</p><p class="text-gray-800 mt-0.5">{{ number_format($document->file_size / 1024, 0) }} KB</p></div>
        <div><p class="text-gray-400">Uploaded</p><p class="text-gray-800 mt-0.5">{{ $document->created_at->format('d M Y') }} · {{ $document->uploadedBy?->name }}</p></div>
        @if($document->project)
        <div><p class="text-gray-400">Project</p><a href="{{ route('admin.projects.show', $document->project) }}" class="text-gray-800 mt-0.5 block font-medium hover:text-gray-600">{{ $document->project->title }}</a></div>
        @endif
        @if($document->expiry_date)
        <div><p class="text-gray-400">Expires</p><p class="mt-0.5 {{ $document->isExpired() ? 'text-red-600 font-medium' : 'text-gray-800' }}">{{ $document->expiry_date->format('d M Y') }}</p></div>
        @endif
        @if($document->tags)
        <div><p class="text-gray-400">Tags</p><p class="text-gray-500 mt-0.5">{{ $document->tags }}</p></div>
        @endif
    </div>
</div>

{{-- File Preview --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">File Preview</h3>
        <a href="{{ route('admin.documents.preview', $document) }}" target="_blank" class="text-xs font-medium text-gray-600 hover:text-gray-900">Open full size →</a>
    </div>
    <div class="bg-gray-100 p-4 md:p-8">
        @if($previewable)
        <iframe src="{{ route('admin.documents.preview', $document) }}" class="w-full bg-white rounded border border-gray-300 shadow-sm" style="height:700px"></iframe>
        @else
        <div class="bg-white rounded border border-gray-200 px-5 py-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <p class="text-xs text-gray-500 mb-1">{{ strtoupper(str_contains($document->file_type, '/') ? last(explode('/', $document->file_type)) : $document->file_type) }} files can't be previewed</p>
            <a href="{{ route('admin.documents.download', $document) }}" class="text-xs font-medium text-gray-700 hover:text-gray-900 underline">Download to view</a>
        </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- E-Signatures --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">E-Signatures</h3></div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-5 py-2.5 font-semibold">Signer</th><th class="px-5 py-2.5 font-semibold">Status</th><th class="px-5 py-2.5 font-semibold text-right">Signed At</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($document->signatures as $sig)
            @php $ssc = ['signed' => '#10b981', 'declined' => '#ef4444', 'pending' => '#f59e0b'][$sig->status] ?? '#9ca3af'; @endphp
            <tr>
                <td class="px-5 py-3"><p class="text-xs font-medium text-gray-900">{{ $sig->signer_name }}</p><p class="text-[10px] text-gray-400">{{ $sig->signer_email }}</p></td>
                <td class="px-5 py-3"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ssc }}"></span>{{ $sig->status }}</span></td>
                <td class="px-5 py-3 text-xs text-right text-gray-400">{{ $sig->signed_at?->format('d M Y H:i') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No signature requests</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>

    <div class="space-y-4">
        {{-- Upload New Version --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <details class="group">
                <summary class="px-5 py-3.5 border-b border-gray-100 cursor-pointer flex items-center justify-between list-none">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Upload New Version</h3>
                    <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <form method="POST" action="{{ route('admin.documents.upload-version', $document) }}" enctype="multipart/form-data" class="px-5 py-4 space-y-3">@csrf
                    <div><input type="file" name="file" required class="w-full text-xs text-gray-500 file:mr-3 file:px-3 file:py-2 file:border-0 file:rounded file:bg-gray-900 file:text-white file:text-xs file:font-medium hover:file:bg-gray-700 file:cursor-pointer border border-gray-200 rounded"></div>
                    <div><textarea name="version_notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400" placeholder="What changed in this version?"></textarea></div>
                    <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-medium rounded hover:bg-slate-700">Upload Version</button>
                </form>
            </details>
        </div>

        {{-- Version History --}}
        @if($document->versions->count() > 0)
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Version History</h3></div>
            <div class="divide-y divide-gray-100">
                @foreach($document->versions as $version)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-900">v{{ $version->version }}</p>
                        <p class="text-[10px] text-gray-400">{{ $version->uploadedBy?->name }} · {{ $version->created_at->format('d M Y') }}</p>
                        @if($version->description)<p class="text-[10px] text-gray-500 mt-0.5">{{ $version->description }}</p>@endif
                    </div>
                    <a href="{{ route('admin.documents.show', $version) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Access History --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mt-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Access History</h3></div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-100"><th class="px-5 py-2.5 font-semibold">User</th><th class="px-5 py-2.5 font-semibold">Action</th><th class="px-5 py-2.5 font-semibold">IP</th><th class="px-5 py-2.5 font-semibold text-right">Time</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($document->accessLogs->take(15) as $log)
        <tr><td class="px-5 py-3 text-xs text-gray-700">{{ $log->user?->name ?? 'Unknown' }}</td><td class="px-5 py-3 text-xs capitalize text-gray-600">{{ $log->action }}</td><td class="px-5 py-3 text-xs text-gray-400 font-mono">{{ $log->ip_address }}</td><td class="px-5 py-3 text-xs text-right text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</td></tr>
        @empty
        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No access logs</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div>
@endsection
