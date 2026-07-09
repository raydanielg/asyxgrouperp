<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentationPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'route-permission']);
    }

    public function index(Request $request)
    {
        $query = DocumentationPage::orderBy('sort_order')->orderBy('title');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('slug', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('role_scope')) {
            $query->where('role_scope', 'like', '%' . $request->role_scope . '%');
        }

        $pages = $query->paginate(20);
        $categories = DocumentationPage::distinct()->pluck('category');

        $stats = [
            'total' => DocumentationPage::count(),
            'published' => DocumentationPage::where('is_published', true)->count(),
            'draft' => DocumentationPage::where('is_published', false)->count(),
        ];

        return view('admin.documentation.index', compact('pages', 'categories', 'stats'));
    }

    public function create()
    {
        return view('admin.documentation.form', ['page' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:documentation_pages,slug',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'sort_order' => 'required|integer|min:0',
            'is_published' => 'boolean',
            'role_scope' => 'nullable|string|max:255',
        ]);

        DocumentationPage::create([
            'title' => $request->title,
            'slug' => $request->slug ?: Str::slug($request->title),
            'content' => $request->content,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_published' => $request->boolean('is_published', false),
            'role_scope' => $request->role_scope ?: 'all',
        ]);

        return back()->with('success', 'Documentation page created');
    }

    public function edit($id)
    {
        $page = DocumentationPage::findOrFail($id);
        return view('admin.documentation.form', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $page = DocumentationPage::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:documentation_pages,slug,' . $page->id,
            'content' => 'required|string',
            'category' => 'required|string|max:100',
            'sort_order' => 'required|integer|min:0',
            'is_published' => 'boolean',
            'role_scope' => 'nullable|string|max:255',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_published' => $request->boolean('is_published', false),
            'role_scope' => $request->role_scope ?: 'all',
        ]);

        return back()->with('success', 'Documentation page updated');
    }

    public function destroy($id)
    {
        DocumentationPage::findOrFail($id)->delete();
        return back()->with('success', 'Documentation page deleted');
    }

    public function exportMarkdown($id)
    {
        $page = DocumentationPage::findOrFail($id);
        $markdown = "# {$page->title}\n\n"
            . "**Category:** " . ucfirst(str_replace('_', ' ', $page->category)) . "\n"
            . "**Slug:** `{$page->slug}`\n"
            . "**Updated:** " . $page->updated_at->toDateTimeString() . "\n\n"
            . "---\n\n"
            . $page->content . "\n";

        return response($markdown)
            ->header('Content-Type', 'text/markdown; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $page->slug . '.md"');
    }

    public function exportAllMarkdown()
    {
        $pages = DocumentationPage::orderBy('category')->orderBy('sort_order')->orderBy('title')->get();

        $markdown = "# ERP Documentation\n\n"
            . "> Complete system documentation export.\n"
            . "> Generated: " . now()->toDateTimeString() . "\n"
            . "> Total Pages: " . $pages->count() . "\n\n---\n\n";

        foreach ($pages as $page) {
            $markdown .= "# {$page->title}\n\n"
                . "**Category:** " . ucfirst(str_replace('_', ' ', $page->category)) . " | "
                . "**Slug:** `{$page->slug}` | "
                . "**Status:** " . ($page->is_published ? 'Published' : 'Draft') . " | "
                . "**Updated:** " . $page->updated_at->toDateTimeString() . "\n\n"
                . $page->content . "\n\n---\n\n";
        }

        return response($markdown)
            ->header('Content-Type', 'text/markdown; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="erp-docs.md"');
    }
}
