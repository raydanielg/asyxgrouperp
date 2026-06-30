<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentSignature;
use App\Models\DocumentAccessLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private const CATEGORIES = [
        'policy' => ['label' => 'Company Policy', 'color' => 'blue', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'contract' => ['label' => 'Contract', 'color' => 'purple', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'minutes' => ['label' => 'Meeting Minutes', 'color' => 'amber', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM6 3v2m12-2v2M3 8h2m14 0h2'],
        'action_point' => ['label' => 'Action Points', 'color' => 'red', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        'project_doc' => ['label' => 'Project Document', 'color' => 'emerald', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        'tender' => ['label' => 'Tender', 'color' => 'indigo', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        'hr' => ['label' => 'HR Document', 'color' => 'pink', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        'legal' => ['label' => 'Legal', 'color' => 'gray', 'icon' => 'M3 6l3 1m0 0l-3 9 5-5m5-5l3 1m-3-1l-3 9 5-5M6 7l3 9m0 0l3-9m3 1l3 9m-3-9l-3 9'],
        'financial' => ['label' => 'Financial', 'color' => 'green', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'technical' => ['label' => 'Technical', 'color' => 'cyan', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
        'other' => ['label' => 'Other', 'color' => 'slate', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
    ];

    public function index(Request $request)
    {
        $query = Document::with(['uploadedBy', 'signatures', 'project']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Document::count(),
            'signed' => Document::signed()->count(),
            'pending' => Document::pendingSignature()->count(),
            'draft' => Document::where('status', 'draft')->count(),
            'archived' => Document::where('status', 'archived')->count(),
            'expired' => Document::whereNotNull('expiry_date')->where('expiry_date', '<', now())->count(),
        ];

        $categoryCounts = [];
        foreach (array_keys(self::CATEGORIES) as $cat) {
            $categoryCounts[$cat] = Document::where('category', $cat)->count();
        }

        $projects = Project::orderBy('title')->get(['id', 'title']);
        $categories = self::CATEGORIES;

        return view('admin.documents.index', compact('documents', 'stats', 'categoryCounts', 'categories', 'projects'));
    }

    public function create(Request $request)
    {
        $projects = Project::orderBy('title')->get(['id', 'title']);
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $categories = self::CATEGORIES;
        $preselectedProject = $request->get('project_id');
        $preselectedCategory = $request->get('category');

        return view('admin.documents.create', compact('projects', 'users', 'categories', 'preselectedProject', 'preselectedCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'tags' => 'nullable|string|max:255',
            'file' => 'required|file|max:20480',
            'project_id' => 'nullable|exists:projects,id',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'is_confidential' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',
            'signers' => 'nullable|array',
            'signers.*' => 'exists:users,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $docNumber = 'DOC-' . date('Ym') . '-' . str_pad(Document::count() + 1, 4, '0', STR_PAD_LEFT);

        $doc = Document::create([
            'document_number' => $docNumber,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'tags' => $validated['tags'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'version' => '1.0',
            'status' => !empty($validated['signers']) ? 'pending_signature' : 'draft',
            'is_confidential' => $validated['is_confidential'] ?? false,
            'project_id' => $validated['project_id'] ?? null,
            'reference_type' => $validated['reference_type'] ?? ($validated['project_id'] ? 'project' : null),
            'reference_id' => $validated['reference_id'] ?? $validated['project_id'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'uploaded_by' => auth()->id(),
            'company_id' => session('current_company_id') ?? auth()->user()?->company_id,
        ]);

        if (!empty($validated['signers'])) {
            foreach ($validated['signers'] as $index => $signerId) {
                $signer = User::find($signerId);
                DocumentSignature::create([
                    'document_id' => $doc->id,
                    'signer_id' => $signerId,
                    'signer_name' => $signer->name,
                    'signer_email' => $signer->email,
                    'status' => 'pending',
                    'order' => $index,
                ]);
            }
        }

        DocumentAccessLog::create([
            'document_id' => $doc->id,
            'user_id' => auth()->id(),
            'action' => 'upload',
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.documents.show', $doc)
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $document->load(['uploadedBy', 'signatures.signer', 'accessLogs.user', 'project', 'versions.uploadedBy']);

        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'view',
            'ip_address' => request()->ip(),
        ]);

        $categories = self::CATEGORIES;

        return view('admin.documents.show', compact('document', 'categories'));
    }

    public function download(Document $document)
    {
        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'download',
            'ip_address' => request()->ip(),
        ]);

        return Storage::disk('public')->download(
            $document->file_path,
            $document->title . '.' . $document->file_type
        );
    }

    public function sign(Request $request, Document $document)
    {
        $signature = $document->signatures()
            ->where('signer_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$signature) {
            return back()->with('error', 'You are not authorized to sign this document.');
        }

        $signature->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_hash' => hash('sha256', $document->id . auth()->id() . now()),
            'ip_address' => request()->ip(),
        ]);

        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'sign',
            'ip_address' => request()->ip(),
        ]);

        $pendingCount = $document->signatures()->pending()->count();
        if ($pendingCount === 0) {
            $document->update(['status' => 'signed', 'signed_at' => now()]);
        }

        return back()->with('success', 'Document signed successfully.');
    }

    public function decline(Request $request, Document $document)
    {
        $validated = $request->validate(['decline_reason' => 'required|string']);

        $signature = $document->signatures()
            ->where('signer_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$signature) {
            return back()->with('error', 'You are not authorized to sign this document.');
        }

        $signature->update([
            'status' => 'declined',
            'decline_reason' => $validated['decline_reason'],
        ]);

        return back()->with('success', 'Signature declined.');
    }

    public function archive(Document $document)
    {
        $document->update(['status' => 'archived']);

        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'archive',
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'Document archived.');
    }

    public function uploadVersion(Request $request, Document $document)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
            'version_notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $versionParts = explode('.', $document->version);
        $major = (int)($versionParts[0] ?? 1);
        $minor = (int)($versionParts[1] ?? 0) + 1;
        if ($minor > 9) {
            $major++;
            $minor = 0;
        }
        $newVersion = $major . '.' . $minor;

        $newDoc = Document::create([
            'document_number' => $document->document_number . '-v' . $newVersion,
            'title' => $document->title,
            'description' => $request->version_notes ?? $document->description,
            'category' => $document->category,
            'tags' => $document->tags,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'version' => $newVersion,
            'status' => 'draft',
            'is_confidential' => $document->is_confidential,
            'project_id' => $document->project_id,
            'reference_type' => $document->reference_type,
            'reference_id' => $document->reference_id,
            'parent_document_id' => $document->id,
            'uploaded_by' => auth()->id(),
            'company_id' => $document->company_id,
        ]);

        return redirect()->route('admin.documents.show', $newDoc)
            ->with('success', 'New version v' . $newVersion . ' uploaded.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('admin.documents.index')
            ->with('success', 'Document deleted.');
    }

    public function projectDocuments(Project $project)
    {
        $documents = Document::where('project_id', $project->id)
            ->with(['uploadedBy', 'signatures'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Document::where('project_id', $project->id)->count(),
            'signed' => Document::where('project_id', $project->id)->signed()->count(),
            'pending' => Document::where('project_id', $project->id)->pendingSignature()->count(),
        ];

        $categories = self::CATEGORIES;
        $categoryCounts = [];
        foreach (array_keys(self::CATEGORIES) as $cat) {
            $categoryCounts[$cat] = Document::where('project_id', $project->id)->where('category', $cat)->count();
        }

        return view('admin.documents.project-documents', compact('documents', 'project', 'stats', 'categories', 'categoryCounts'));
    }
}
