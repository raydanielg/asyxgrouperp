@extends('layouts.admin')
@section('title', 'Upload Document - ' . config('app.name'))
@section('page_title', 'Upload Document')
@section('content')
<div class="mb-4"><a href="{{ route('admin.documents.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Documents</a></div>

@if(session('error'))
<div class="bg-red-50 border border-red-200 rounded-md px-5 py-3 mb-4 text-xs text-red-700">{{ session('error') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-start">
    {{-- Form --}}
    <div class="lg:col-span-3 bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Upload Document</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">Attach a file, link to a project, and request signatures</p>
        </div>
        <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="px-5 py-4 border-b border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Basic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="docTitle" name="title" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400" placeholder="e.g. Service Agreement - ABC Corp" oninput="updatePreviewTitle()">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            @foreach($categories as $slug => $cat)
                                <option value="{{ $slug }}" {{ $preselectedCategory === $slug ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tags</label>
                        <input type="text" name="tags" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400" placeholder="comma, separated, tags">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400" placeholder="Brief description of this document..."></textarea>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-b border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">Project Linking</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Link to Project</label>
                        <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                            <option value="">— No project (general document) —</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ (string)$preselectedProject === (string)$project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Reference Type</label>
                        <input type="text" name="reference_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400" placeholder="project, tender, employee...">
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-b border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-3">File</h4>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Upload File <span class="text-red-500">*</span></label>
                    <input type="file" id="docFile" name="file" required class="w-full text-xs text-gray-500 file:mr-3 file:px-3 file:py-2 file:border-0 file:rounded file:bg-gray-900 file:text-white file:text-xs file:font-medium hover:file:bg-gray-700 file:cursor-pointer border border-gray-200 rounded" onchange="previewFile()">
                    <p class="text-[10px] text-gray-400 mt-1.5">Max 20MB — PDF, DOCX, XLSX, images accepted.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Date <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="date" name="expiry_date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                            <input type="checkbox" name="is_confidential" value="1" class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400">
                            Mark as Confidential
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">E-Signature Requests</h4>
                    <span class="text-[10px] text-gray-400">optional — selected users will be notified</span>
                </div>
                <div class="divide-y divide-gray-100 border border-gray-200 rounded max-h-48 overflow-y-auto">
                    @foreach($users as $user)
                    <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="signers[]" value="{{ $user->id }}" class="w-4 h-4 rounded border-gray-300 text-gray-800 focus:ring-gray-400 flex-shrink-0">
                        <div class="min-w-0">
                            <p class="text-gray-800 text-xs font-medium truncate">{{ $user->name }}</p>
                            <p class="text-[10px] text-gray-400 truncate">{{ $user->email }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="px-5 py-4 flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-sm font-medium rounded hover:bg-gray-700">Upload Document</button>
                <a href="{{ route('admin.documents.index') }}" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Live Preview --}}
    <div class="lg:col-span-2 lg:sticky lg:top-4">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Preview</h4>
                <span id="previewBadge" class="text-[10px] text-gray-400">No file</span>
            </div>
            <div class="bg-gray-100 p-4">
                <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
                    {{-- File content preview --}}
                    <div id="previewEmpty" class="px-5 py-12 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <p class="text-xs text-gray-400">Select a file to preview</p>
                    </div>
                    <iframe id="previewFrame" class="hidden w-full border-0" style="height:340px"></iframe>
                    <img id="previewImg" class="hidden w-full max-h-[340px] object-contain bg-gray-50">
                    {{-- Meta --}}
                    <div id="previewMeta" class="hidden border-t border-gray-100 px-4 py-3">
                        <p id="previewName" class="text-xs font-medium text-gray-900 truncate"></p>
                        <p class="text-[10px] text-gray-400 mt-0.5"><span id="previewSize"></span> · <span id="previewType"></span></p>
                    </div>
                </div>
            </div>
            <div class="px-5 py-3.5 border-t border-gray-100">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Document Title</p>
                <p id="previewTitleText" class="text-xs text-gray-500 italic">(will show as typed)</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function fmtSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(0) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}
function previewFile() {
    const input = document.getElementById('docFile');
    const file = input.files && input.files[0];
    const empty = document.getElementById('previewEmpty');
    const frame = document.getElementById('previewFrame');
    const img = document.getElementById('previewImg');
    const meta = document.getElementById('previewMeta');
    const badge = document.getElementById('previewBadge');
    if (!file) {
        empty.classList.remove('hidden'); frame.classList.add('hidden'); img.classList.add('hidden'); meta.classList.add('hidden');
        badge.textContent = 'No file'; return;
    }
    const url = URL.createObjectURL(file);
    const ext = (file.name.split('.').pop() || '').toUpperCase();
    const isImg = file.type.startsWith('image/');
    const isPdf = file.type === 'application/pdf';
    empty.classList.add('hidden');
    if (isPdf) { frame.src = url; frame.classList.remove('hidden'); img.classList.add('hidden'); }
    else if (isImg) { img.src = url; img.classList.remove('hidden'); frame.classList.add('hidden'); }
    else { frame.classList.add('hidden'); img.classList.add('hidden'); empty.classList.remove('hidden'); }
    document.getElementById('previewName').textContent = file.name;
    document.getElementById('previewSize').textContent = fmtSize(file.size);
    document.getElementById('previewType').textContent = ext + ' file';
    meta.classList.remove('hidden');
    badge.textContent = ext;
}
function updatePreviewTitle() {
    const v = document.getElementById('docTitle').value.trim();
    const el = document.getElementById('previewTitleText');
    el.textContent = v || '(will show as typed)';
    el.classList.toggle('italic', !v);
}
</script>
@endpush
