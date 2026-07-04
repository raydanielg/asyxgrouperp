@extends('layouts.admin')

@section('title', ($page ? 'Edit' : 'New') . ' Doc Page')
@section('page_title', ($page ? 'Edit' : 'New') . ' Documentation Page')

@section('content')

@if(session('success'))
<div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-sm text-emerald-700 flex items-center gap-2">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
    <ul class="list-disc pl-4 space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">{{ $page ? 'Edit' : 'New' }} Page</h2>
    <p class="text-sm text-gray-500">{{ $page ? 'Update documentation content' : 'Create a new documentation page' }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border p-6">
            <form action="{{ $page ? route('admin.documentation.update', $page->id) : route('admin.documentation.store') }}" method="POST" class="space-y-5">
                @csrf
                @if($page) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Page Title</label>
                        <input type="text" name="title" value="{{ old('title', $page?->title) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="e.g. Getting Started" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $page?->slug) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none font-mono" placeholder="getting-started">
                        <p class="text-[10px] text-gray-400 mt-1">Auto-generated from title if left empty</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none bg-white" required>
                            <option value="general" {{ old('category', $page?->category) === 'general' ? 'selected' : '' }}>General</option>
                            <option value="getting_started" {{ old('category', $page?->category) === 'getting_started' ? 'selected' : '' }}>Getting Started</option>
                            <option value="admin_guide" {{ old('category', $page?->category) === 'admin_guide' ? 'selected' : '' }}>Admin Guide</option>
                            <option value="role_guide" {{ old('category', $page?->category) === 'role_guide' ? 'selected' : '' }}>Role Guide</option>
                            <option value="module_guide" {{ old('category', $page?->category) === 'module_guide' ? 'selected' : '' }}>Module Guide</option>
                            <option value="api_reference" {{ old('category', $page?->category) === 'api_reference' ? 'selected' : '' }}>API Reference</option>
                            <option value="security" {{ old('category', $page?->category) === 'security' ? 'selected' : '' }}>Security</option>
                            <option value="faq" {{ old('category', $page?->category) === 'faq' ? 'selected' : '' }}>FAQ</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $page?->sort_order ?? 0) }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Role Scope (comma-separated role names, or 'all')</label>
                    <input type="text" name="role_scope" value="{{ old('role_scope', $page?->role_scope ?? 'all') }}" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="all or admin, erp_super_administrator, accountant">
                    <p class="text-[10px] text-gray-400 mt-1">Use 'all' to show to everyone. Limits visibility in public docs.</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Content</label>
                    <textarea name="content" id="docContent" rows="20" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none font-mono text-xs resize-none" placeholder="Write your documentation content here... Markdown supported.">{{ old('content', $page?->content) }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" id="isPublished" value="1" {{ old('is_published', $page?->is_published ?? false) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                    <label for="isPublished" class="text-xs font-medium text-gray-700">Publish this page (visible to public docs)</label>
                </div>

                <div class="pt-2 flex items-center gap-3">
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">{{ $page ? 'Update Page' : 'Create Page' }}</button>
                    <a href="{{ route('admin.documentation') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview & Help --}}
    <div class="space-y-4">
        <div class="bg-emerald-900 rounded-xl p-5 text-white">
            <h3 class="text-sm font-semibold mb-2">Markdown Tips</h3>
            <div class="space-y-1.5 text-[11px] text-emerald-200 font-mono">
                <p># Heading 1</p>
                <p>## Heading 2</p>
                <p>**Bold text**</p>
                <p>*Italic text*</p>
                <p>- List item</p>
                <p>`code`</p>
                <p>[Link text](url)</p>
            </div>
        </div>

        @if($page)
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Page Info</h3>
            <div class="space-y-2 text-xs text-gray-600">
                <p class="flex justify-between"><span>Created</span><span>{{ $page->created_at->format('M d, Y H:i') }}</span></p>
                <p class="flex justify-between"><span>Updated</span><span>{{ $page->updated_at->format('M d, Y H:i') }}</span></p>
                <p class="flex justify-between"><span>Status</span><span>{{ $page->is_published ? 'Published' : 'Draft' }}</span></p>
            </div>
            <div class="mt-3 pt-3 border-t space-y-2">
                <a href="{{ route('docs', $page->slug) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 hover:text-emerald-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Public Page
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.documentation.export', $page->id) }}" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export .md
                    </a>
                    <button type="button" onclick="copyFormMd()" class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-gray-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy Markdown
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function copyFormMd() {
    const title = document.querySelector('input[name="title"]').value || 'Untitled';
    const slug = document.querySelector('input[name="slug"]').value || '';
    const category = document.querySelector('select[name="category"]')?.value || 'general';
    const content = document.getElementById('docContent').value;
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
