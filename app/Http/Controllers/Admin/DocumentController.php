<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentSignature;
use App\Models\DocumentAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with(['uploadedBy', 'signatures'])->latest()->paginate(20);
        return view('admin.documents.index', compact('documents'));
    }

    public function create()
    {
        return view('admin.documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string',
            'file' => 'required|file|max:20480',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'signers' => 'nullable|array',
            'signers.*' => 'exists:users,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $doc = Document::create([
            'document_number' => 'DOC-' . date('Ym') . '-' . str_pad(Document::count() + 1, 4, '0', STR_PAD_LEFT),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'version' => '1.0',
            'status' => !empty($validated['signers']) ? 'pending_signature' : 'draft',
            'reference_type' => $validated['reference_type'] ?? null,
            'reference_id' => $validated['reference_id'] ?? null,
            'uploaded_by' => auth()->id(),
            'company_id' => auth()->user()?->company_id,
        ]);

        if (!empty($validated['signers'])) {
            foreach ($validated['signers'] as $index => $signerId) {
                $signer = \App\Models\User::find($signerId);
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

        return redirect()->route('admin.documents.index')->with('success', 'Document uploaded.');
    }

    public function show(Document $document)
    {
        $document->load(['uploadedBy', 'signatures.signer', 'accessLogs.user']);
        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'view',
            'ip_address' => request()->ip(),
        ]);
        return view('admin.documents.show', compact('document'));
    }

    public function download(Document $document)
    {
        DocumentAccessLog::create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'action' => 'download',
            'ip_address' => request()->ip(),
        ]);
        return Storage::disk('public')->download($document->file_path, $document->title . '.' . $document->file_type);
    }

    public function sign(Request $request, Document $document)
    {
        $signature = $document->signatures()->where('signer_id', auth()->id())->where('status', 'pending')->first();
        if (!$signature) {
            return back()->with('error', 'You are not authorized to sign this document.');
        }

        $signature->update([
            'status' => 'signed',
            'signed_at' => now(),
            'signature_hash' => hash('sha256', $document->id . auth()->id() . now()),
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

        $signature = $document->signatures()->where('signer_id', auth()->id())->where('status', 'pending')->first();
        if (!$signature) {
            return back()->with('error', 'You are not authorized to sign this document.');
        }

        $signature->update([
            'status' => 'declined',
            'decline_reason' => $validated['decline_reason'],
        ]);

        return back()->with('success', 'Signature declined.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('admin.documents.index')->with('success', 'Document deleted.');
    }
}
