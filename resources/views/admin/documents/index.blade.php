@extends('layouts.admin')
@section('title', 'Documents - ' . config('app.name'))
@section('page_title', 'Document Management')
@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Central document repository with e-signatures, versioning & project linking</p>
        <a href="{{ route('admin.documents.create') }}" class="px-4 py-2 bg-bronze text-white text-sm font-semibold rounded-lg hover:bg-bronze-dark transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Upload Document
        </a>
    </div>

    @if(session('success'))
    <div class="px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Signed</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['signed'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Pending Sig.</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Drafts</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Archived</p>
            <p class="text-2xl font-bold text-gray-500 mt-1">{{ $stats['archived'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Expired</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['expired'] }}</p>
        </div>
    </div>

    {{-- Category Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($categories as $slug => $cat)
        <a href="{{ route('admin.documents.index', ['category' => $slug]) }}" class="bg-white rounded-xl border border-gray-100 p-3 hover:shadow-md transition-shadow group">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-{{ $cat['color'] }}-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-{{ $cat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cat['icon'] }}"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-{{ $cat['color'] }}-600">{{ $cat['label'] }}</span>
            </div>
            <p class="text-lg font-bold text-gray-900">{{ $categoryCounts[$slug] ?? 0 }}</p>
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, number, tags..." class="px-3 py-2 border border-gray-200 rounded-lg text-sm md:col-span-2">
            <select name="category" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $slug => $cat)
                    <option value="{{ $slug }}" {{ request('category') === $slug ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                @endforeach
            </select>
            <select name="project_id" class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <select name="status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm flex-1">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending_signature" {{ request('status') === 'pending_signature' ? 'selected' : '' }}>Pending Signature</option>
                    <option value="signed" {{ request('status') === 'signed' ? 'selected' : '' }}>Signed</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-navy text-white text-sm font-semibold rounded-lg hover:bg-opacity-90">Filter</button>
            </div>
        </form>
    </div>

    {{-- Documents Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50 border-b">
                        <th class="px-5 py-3 font-medium">Number</th>
                        <th class="px-5 py-3 font-medium">Title</th>
                        <th class="px-5 py-3 font-medium">Category</th>
                        <th class="px-5 py-3 font-medium">Project</th>
                        <th class="px-5 py-3 font-medium">Version</th>
                        <th class="px-5 py-3 font-medium">Signatures</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-700">{{ $doc->document_number }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.documents.show', $doc) }}" class="text-xs font-medium text-gray-800 hover:text-bronze">{{ $doc->title }}</a>
                            @if($doc->is_confidential)<span class="ml-1 text-[9px] text-red-500 font-semibold">CONF</span>@endif
                            @if($doc->isExpired())<span class="ml-1 text-[9px] text-red-500 font-semibold">EXPIRED</span>@endif
                        </td>
                        <td class="px-5 py-3">
                            @if(isset($categories[$doc->category ?? '']))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $categories[$doc->category]['color'] }}-50 text-{{ $categories[$doc->category]['color'] }}-700">{{ $categories[$doc->category]['label'] }}</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $doc->project?->title ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">v{{ $doc->version }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $doc->signatures->where('status', 'signed')->count() }} / {{ $doc->signatures->count() }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $doc->status === 'signed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}
                                {{ $doc->status === 'pending_signature' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                {{ $doc->status === 'draft' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}
                                {{ $doc->status === 'archived' ? 'bg-gray-50 text-gray-500 border border-gray-100' : '' }}">{{ str_replace('_', ' ', ucfirst($doc->status)) }}</span>
                        </td>
                        <td class="px-5 py-3 flex items-center gap-3">
                            <a href="{{ route('admin.documents.show', $doc) }}" class="text-bronze hover:underline text-xs font-semibold">View</a>
                            <a href="{{ route('admin.documents.download', $doc) }}" class="text-emerald-600 hover:underline text-xs">Download</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No documents found. <a href="{{ route('admin.documents.create') }}" class="text-bronze font-semibold">Upload one now</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t">{{ $documents->links() }}</div>
    </div>
</div>
@endsection
