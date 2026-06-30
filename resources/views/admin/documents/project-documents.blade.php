@extends('layouts.admin')

@section('page_title', 'Project Documents - ' . $project->title)

@section('page_actions')
    <a href="{{ route('admin.projects.show', $project) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">← Back to Project</a>
    <a href="{{ route('admin.documents.create', ['project_id' => $project->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-bronze text-white text-sm font-semibold rounded-lg hover:bg-bronze-dark transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Upload Document
    </a>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Project Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">{{ $project->title }}</h2>
        <p class="text-sm text-gray-500 mt-1">Project #: {{ $project->project_number }} - All documents linked to this project</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Total Documents</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Signed</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['signed'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Pending Signature</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
    </div>

    {{-- Category Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($categories as $slug => $cat)
        <div class="bg-white rounded-xl border border-gray-100 p-3">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-{{ $cat['color'] }}-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-{{ $cat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cat['icon'] }}"/></svg>
                </div>
                <span class="text-xs font-semibold text-gray-700">{{ $cat['label'] }}</span>
            </div>
            <p class="text-lg font-bold text-gray-900">{{ $categoryCounts[$slug] ?? 0 }}</p>
        </div>
        @endforeach
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
                        <th class="px-5 py-3 font-medium">Version</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Uploaded</th>
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
                        </td>
                        <td class="px-5 py-3">
                            @if(isset($categories[$doc->category ?? '']))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $categories[$doc->category]['color'] }}-50 text-{{ $categories[$doc->category]['color'] }}-700">{{ $categories[$doc->category]['label'] }}</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-600">v{{ $doc->version }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $doc->status === 'signed' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $doc->status === 'pending_signature' ? 'bg-amber-50 text-amber-700' : '' }}
                                {{ $doc->status === 'draft' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $doc->status === 'archived' ? 'bg-gray-50 text-gray-500' : '' }}">{{ str_replace('_', ' ', ucfirst($doc->status)) }}</span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 flex items-center gap-3">
                            <a href="{{ route('admin.documents.show', $doc) }}" class="text-bronze hover:underline text-xs font-semibold">View</a>
                            <a href="{{ route('admin.documents.download', $doc) }}" class="text-emerald-600 hover:underline text-xs">Download</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No documents for this project yet. <a href="{{ route('admin.documents.create', ['project_id' => $project->id]) }}" class="text-bronze font-semibold">Upload one now</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $documents->links() }}
    </div>
</div>
@endsection
