@extends('layouts.admin')
@section('title', 'Documents - ' . config('app.name'))
@section('page_title', 'Document Management')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xs text-gray-400">Central document repository — e-signatures, versioning & project linking</span>
        <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Upload Document</a>
    </div>
    <div class="grid grid-cols-3 md:grid-cols-6 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $stats['total'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Signed</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $stats['signed'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Pending</p><p class="text-lg font-bold text-amber-600 mt-0.5">{{ $stats['pending'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Drafts</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $stats['draft'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Archived</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $stats['archived'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Expired</p><p class="text-lg font-bold text-red-500 mt-0.5">{{ $stats['expired'] }}</p></div>
    </div>
</div>

{{-- Category chips --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100"><h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Categories</h3></div>
    <div class="px-5 py-4 flex flex-wrap gap-2">
        <a href="{{ route('admin.documents.index') }}" class="px-3 py-1.5 text-xs font-medium rounded border transition-colors {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'border-gray-200 text-gray-600 hover:border-gray-400' }}">All</a>
        @foreach($categories as $slug => $cat)
        <a href="{{ route('admin.documents.index', ['category' => $slug]) }}" class="px-3 py-1.5 text-xs font-medium rounded border transition-colors {{ request('category') === $slug ? 'bg-gray-900 text-white border-gray-900' : 'border-gray-200 text-gray-600 hover:border-gray-400' }}">
            {{ $cat['label'] }} <span class="{{ request('category') === $slug ? 'text-gray-400' : 'text-gray-400' }}">· {{ $categoryCounts[$slug] ?? 0 }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- Documents table --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, number, tags..." class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 w-56">
            <select name="category" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Categories</option>
                @foreach($categories as $slug => $cat)
                    <option value="{{ $slug }}" {{ request('category') === $slug ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                @endforeach
            </select>
            <select name="project_id" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending_signature" {{ request('status') === 'pending_signature' ? 'selected' : '' }}>Pending Signature</option>
                <option value="signed" {{ request('status') === 'signed' ? 'selected' : '' }}>Signed</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
            @if(request()->hasAny(['search','category','project_id','status']))
            <a href="{{ route('admin.documents.index') }}" class="text-xs text-gray-400 hover:text-gray-600 ml-1">Clear</a>
            @endif
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Number</th>
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Category</th>
                    <th class="px-5 py-3 font-semibold">Project</th>
                    <th class="px-5 py-3 font-semibold">Version</th>
                    <th class="px-5 py-3 font-semibold">Signatures</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                @php $dsc = ['signed' => '#10b981', 'pending_signature' => '#f59e0b', 'draft' => '#3b82f6', 'archived' => '#9ca3af'][$doc->status] ?? '#9ca3af'; @endphp
                <tr class="hover:bg-gray-50/60">
                    <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $doc->document_number }}</td>
                    <td class="px-5 py-3.5">
                        <a href="{{ route('admin.documents.show', $doc) }}" class="text-xs font-medium text-gray-900 hover:text-gray-600">{{ $doc->title }}</a>
                        @if($doc->is_confidential)<span class="ml-1.5 text-[9px] text-red-500 font-semibold">CONF</span>@endif
                        @if($doc->isExpired())<span class="ml-1.5 text-[9px] text-red-500 font-semibold">EXPIRED</span>@endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-600">
                        @if(isset($categories[$doc->category ?? '']))
                            {{ $categories[$doc->category]['label'] }}
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $doc->project?->title ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">v{{ $doc->version }}</td>
                    <td class="px-5 py-3.5 text-xs text-gray-500">{{ $doc->signatures->where('status', 'signed')->count() }} / {{ $doc->signatures->count() }}</td>
                    <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $dsc }}"></span>{{ str_replace('_', ' ', ucfirst($doc->status)) }}</span></td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.documents.show', $doc) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
                            <a href="{{ route('admin.documents.download', $doc) }}" class="text-xs font-medium text-gray-400 hover:text-gray-600">Download</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400 text-xs">No documents found. <a href="{{ route('admin.documents.create') }}" class="text-gray-600 font-medium hover:text-gray-900">Upload one now</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $documents->links() }}</div>
    @endif
</div>
@endsection
