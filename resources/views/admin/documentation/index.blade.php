@extends('layouts.admin')

@section('title', 'Documentation')
@section('page_title', 'Documentation')

@section('content')
@include('admin.partials.alert')

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Documentation Pages</h2>
    <p class="text-sm text-gray-500">Manage help guides, role manuals, and system documentation.</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
    @foreach([
        ['label'=>'Total Pages','value'=>number_format($stats['total']),'color'=>'gray','icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['label'=>'Published','value'=>number_format($stats['published']),'color'=>'emerald','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['label'=>'Draft','value'=>number_format($stats['draft']),'color'=>'amber','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z']
    ] as $card)
    <div class="bg-white rounded-xl border p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-500">{{ $card['label'] }}</span>
            <div class="w-7 h-7 rounded-md bg-{{ $card['color'] }}-50 flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
        </div>
        <p class="text-lg font-bold text-gray-900">{{ $card['value'] }}</p>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-sm font-semibold text-gray-900">All Pages</h3>
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search pages..." class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg outline-none focus:border-emerald-500 w-40">
                <select name="category" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg outline-none focus:border-emerald-500 bg-white" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Filter</button>
            </form>
            <a href="{{ route('admin.documentation.export-all') }}" class="px-3 py-1.5 text-xs font-medium bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export
            </a>
            <a href="{{ route('admin.documentation.create') }}" class="px-3 py-1.5 text-xs font-bold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">New Page</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-3 font-medium">Title</th>
                    <th class="px-5 py-3 font-medium">Slug</th>
                    <th class="px-5 py-3 font-medium">Category</th>
                    <th class="px-5 py-3 font-medium">Role Scope</th>
                    <th class="px-5 py-3 font-medium">Order</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Updated</th>
                    <th class="px-5 py-3 font-medium text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $pg)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $pg->title }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <code class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $pg->slug }}</code>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $pg->category)) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ $pg->role_scope ?: 'all' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ $pg->sort_order }}</td>
                    <td class="px-5 py-3">
                        @if($pg->is_published)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Published</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200">Draft</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $pg->updated_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.documentation.edit', $pg->id) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium mr-2">Edit</a>
                        <a href="{{ route('admin.documentation.export', $pg->id) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium mr-2">Export</a>
                        <button type="button" onclick="copyDocMd('{{ addslashes($pg->title) }}', `{{ addslashes($pg->content) }}`, '{{ $pg->slug }}', '{{ $pg->category }}')" class="text-xs text-gray-500 hover:text-gray-700 font-medium mr-2">Copy</button>
                        <form action="{{ route('admin.documentation.destroy', $pg->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this page?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-medium">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-medium">No documentation pages</p>
                        <p class="text-xs mt-1">Create your first page to get started</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pages->hasPages())
    <div class="px-5 py-3 border-t">{{ $pages->links() }}</div>
    @endif
</div>

<script>
function copyDocMd(title, content, slug, category) {
    const md = '# ' + title + '\n\n'
        + '**Category:** ' + category + '\n'
        + '**Slug:** `' + slug + '`\n'
        + '**Updated:** ' + new Date().toISOString() + '\n\n---\n\n'
        + content + '\n';
    navigator.clipboard.writeText(md).then(() => {
        alert('Markdown copied to clipboard!');
    }).catch(() => {
        const ta = document.createElement('textarea'); ta.value = md; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); alert('Markdown copied!');
    });
}
</script>
@endsection
