<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $doc ? $doc->title . ' — ' : '' }}Documentation — {{ config('app.name', 'ERP') }}</title>
    <meta name="description" content="ERP system documentation and guides.">
    @if($doc)
    <meta name="doc-title" content="{{ $doc->title }}">
    <meta name="doc-slug" content="{{ $doc->slug }}">
    <meta name="doc-category" content="{{ $doc->category }}">
    <meta name="doc-updated" content="{{ $doc->updated_at->toIso8601String() }}">
    <link rel="alternate" type="application/json" href="{{ url('/api/docs/' . $doc->slug) }}" title="{{ $doc->title }} — JSON">
    @endif
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
        .docs-sidebar::-webkit-scrollbar { width: 5px; }
        .docs-sidebar::-webkit-scrollbar-track { background: transparent; }
        .docs-sidebar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .docs-content h1 { font-size: 2.25rem; font-weight: 800; color: #111827; margin-bottom: 1.5rem; line-height: 1.2; }
        .docs-content h2 { font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 2.5rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e5e7eb; }
        .docs-content h3 { font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-top: 2rem; margin-bottom: 0.75rem; }
        .docs-content p { color: #4b5563; line-height: 1.75; margin-bottom: 1.25rem; }
        .docs-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #4b5563; }
        .docs-content ul li { margin-bottom: 0.5rem; }
        .docs-content code { background: #f3f4f6; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-family: monospace; font-size: 0.875rem; color: #024938; }
        .docs-content pre { background: #1f2937; padding: 1.25rem; border-radius: 0.75rem; overflow-x: auto; margin-bottom: 1.5rem; }
        .docs-content pre code { background: transparent; color: #e5e7eb; padding: 0; }
        .docs-content blockquote { border-left: 4px solid #f9ac00; padding-left: 1rem; color: #4b5563; font-style: italic; margin-bottom: 1.25rem; }
        .docs-content a { color: #024938; font-weight: 600; text-decoration: underline; }
        .docs-content table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .docs-content th { background: #f9fafb; padding: 0.75rem; text-align: left; font-weight: 600; color: #374151; border-bottom: 2px solid #e5e7eb; }
        .docs-content td { padding: 0.75rem; border-bottom: 1px solid #e5e7eb; color: #4b5563; }
        .docs-search-highlight { background: #fef08a; }
    </style>
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-white text-slate-800">

{{-- Top Bar --}}
<div class="fixed top-0 left-0 right-0 h-16 bg-emerald-900 border-b border-emerald-800 z-40 flex items-center justify-between px-4 lg:px-8">
    <div class="flex items-center gap-3">
        <div class="lg:hidden">
            <button id="docsSidebarToggle" type="button" class="p-2 text-emerald-100 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <a href="{{ url('/') }}" class="text-white font-bold text-lg flex items-center gap-2">
            <span class="bg-emerald-700 px-2 py-1 rounded text-sm">{{ config('app.name', 'ERP') }}</span>
            <span class="hidden sm:inline text-emerald-100 font-medium">Docs</span>
        </a>
        {{-- Working System Badge --}}
        <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-800/60 text-emerald-100 text-xs font-semibold rounded-full border border-emerald-700/50" title="System currently in use">
            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
            Working on {{ config('app.name', 'ERP') }}
        </span>
    </div>
    <div class="flex items-center gap-3">
        @auth
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('role.dashboard') }}" class="text-sm text-emerald-100 hover:text-white">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="text-sm text-emerald-100 hover:text-white">Login</a>
        @endauth
        <a href="{{ url('/') }}" class="text-sm text-emerald-100 hover:text-white">Home</a>
    </div>
</div>

{{-- Main Layout --}}
<div class="pt-16 min-h-screen flex">

    {{-- Sidebar --}}
    <aside id="docsSidebar" class="docs-sidebar fixed lg:sticky top-16 left-0 z-30 w-72 h-[calc(100vh-64px)] bg-gray-50 border-r border-gray-200 overflow-y-auto transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="p-5">
            {{-- Search --}}
            <div class="relative mb-5">
                <input type="text" id="docsSearch" placeholder="Search documentation..." class="w-full px-4 py-2 pl-9 text-sm bg-white border border-gray-200 rounded-lg focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            {{-- Nav --}}
            <nav class="space-y-1" id="docsNav">
                @forelse($allPages as $category => $catPages)
                <div class="mb-4 category-group">
                    <button class="w-full flex items-center justify-between px-3 py-2 text-sm font-semibold text-gray-900 rounded-lg hover:bg-gray-100 transition-colors group" onclick="toggleCategory(this)">
                        {{ ucfirst(str_replace('_', ' ', $category)) }}
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="ml-3 mt-1 space-y-1">
                        @foreach($catPages as $pg)
                        <a href="{{ route('docs', $pg->slug) }}" class="doc-link block px-3 py-1.5 text-sm {{ $currentSlug == $pg->slug ? 'text-emerald-600 font-semibold bg-emerald-50' : 'text-gray-600 hover:text-emerald-600' }} rounded-lg transition-colors" data-title="{{ strtolower($pg->title) }}" data-content="{{ strtolower(strip_tags(Str::markdown($pg->content))) }}">
                            {{ $pg->title }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-xs text-gray-400">No docs yet</p>
                </div>
                @endforelse
            </nav>
        </div>
    </aside>

    {{-- Mobile Sidebar Overlay --}}
    <div id="docsSidebarOverlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden" onclick="closeSidebar()"></div>

    {{-- Content Area --}}
    <main class="flex-1 min-w-0">
        <div class="max-w-4xl mx-auto px-6 py-10 lg:px-12">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-400 mb-8">
                <span>Docs</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-emerald-600 font-medium">{{ $doc?->title ?? 'Documentation' }}</span>
            </div>

            {{-- Content --}}
            @if($doc)
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <button type="button" onclick="copyDocMd()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Copy Markdown
                </button>
                <a href="{{ url('/api/docs/' . $doc->slug) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    JSON
                </a>
                <a href="{{ url('/docs/export.md') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export .md
                </a>
                <a href="{{ url('/llms.txt') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors" title="LLM discovery index">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    llms.txt
                </a>
                <a href="{{ url('/llms-full.txt') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors" title="Full markdown for AI ingestion">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    llms-full.txt
                </a>
            </div>
            @endif

            <div class="docs-content" id="docsContent">
                @if($doc)
                    {!! Illuminate\Support\Str::markdown($doc->content) !!}
                @else
                <div class="text-center py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h2 class="text-lg font-bold text-gray-900 mb-1">No Documentation Yet</h2>
                    <p class="text-sm text-gray-500">Documentation will appear here once published.</p>
                </div>
                @endif
            </div>

            {{-- Footer Nav --}}
            <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-between">
                @php
                    $flatPages = $allPages->flatten();
                    $currentIndex = $flatPages->search(fn($p) => $p->slug === $currentSlug);
                    $prevPage = $currentIndex > 0 ? $flatPages[$currentIndex - 1] : null;
                    $nextPage = $currentIndex !== false && $currentIndex < $flatPages->count() - 1 ? $flatPages[$currentIndex + 1] : null;
                @endphp
                @if($prevPage)
                <a href="{{ route('docs', $prevPage->slug) }}" class="text-sm text-gray-500 hover:text-emerald-600 transition-colors">&larr; {{ $prevPage->title }}</a>
                @else
                <span></span>
                @endif
                @if($nextPage)
                <a href="{{ route('docs', $nextPage->slug) }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">{{ $nextPage->title }} &rarr;</a>
                @else
                <span></span>
                @endif
            </div>
        </div>
    </main>

    {{-- Right Sidebar - On this page (desktop only) --}}
    <aside class="hidden xl:block w-64 sticky top-16 h-[calc(100vh-64px)] border-l border-gray-200 overflow-y-auto p-6">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">On this page</div>
        <nav id="tocNav" class="space-y-2">
            <a href="#" class="block text-sm text-gray-500 hover:text-emerald-600 transition-colors">Overview</a>
        </nav>
    </aside>
</div>

<script>
// Mobile sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('docsSidebar');
    const overlay = document.getElementById('docsSidebarOverlay');
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        closeSidebar();
    }
}
function closeSidebar() {
    document.getElementById('docsSidebar').classList.add('-translate-x-full');
    document.getElementById('docsSidebarOverlay').classList.add('hidden');
}
document.getElementById('docsSidebarToggle').addEventListener('click', toggleSidebar);

function toggleCategory(btn) {
    const content = btn.nextElementSibling;
    const icon = btn.querySelector('svg');
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Search docs
document.getElementById('docsSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase().trim();
    const links = document.querySelectorAll('.doc-link');
    const groups = document.querySelectorAll('.category-group');

    if (!term) {
        links.forEach(l => { l.classList.remove('hidden'); l.innerHTML = l.textContent; });
        groups.forEach(g => g.classList.remove('hidden'));
        return;
    }

    groups.forEach(g => g.classList.add('hidden'));
    links.forEach(link => {
        const title = link.dataset.title;
        const content = link.dataset.content;
        if (title.includes(term) || content.includes(term)) {
            link.classList.remove('hidden');
            link.parentElement.parentElement.classList.remove('hidden');
            link.parentElement.classList.remove('hidden');
            const regex = new RegExp('(' + term + ')', 'gi');
            link.innerHTML = link.textContent.replace(regex, '<span class="docs-search-highlight">$1</span>');
        } else {
            link.classList.add('hidden');
        }
    });
});

// Build TOC from headings
function buildToc() {
    const content = document.getElementById('docsContent');
    const tocNav = document.getElementById('tocNav');
    if (!content || !tocNav) return;
    const headings = content.querySelectorAll('h2, h3');
    if (headings.length === 0) return;
    tocNav.innerHTML = '';
    headings.forEach((h, i) => {
        const id = 'section-' + i;
        h.id = id;
        const a = document.createElement('a');
        a.href = '#' + id;
        a.className = 'block text-sm text-gray-500 hover:text-emerald-600 transition-colors ' + (h.tagName === 'H3' ? 'pl-3' : '');
        a.textContent = h.textContent;
        tocNav.appendChild(a);
    });
}
buildToc();

function copyDocMd() {
    const title = document.querySelector('meta[name="doc-title"]')?.content || 'Documentation';
    const slug = document.querySelector('meta[name="doc-slug"]')?.content || '';
    const category = document.querySelector('meta[name="doc-category"]')?.content || 'general';
    const contentEl = document.querySelector('.docs-content');
    let text = contentEl ? contentEl.innerText : '';
    const md = '# ' + title + '\n\n'
        + '**Category:** ' + category + '\n'
        + '**Slug:** `' + slug + '`\n'
        + '**URL:** ' + window.location.href + '\n\n---\n\n'
        + text + '\n';
    navigator.clipboard.writeText(md).then(() => {
        alert('Markdown copied to clipboard!');
    }).catch(() => {
        const ta = document.createElement('textarea'); ta.value = md; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); alert('Markdown copied!');
    });
}
</script>

</body>
</html>
