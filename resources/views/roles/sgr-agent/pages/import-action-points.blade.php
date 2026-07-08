@extends('layouts.admin')
@section('title', 'Upload Action Points - SGR')
@section('page_title', 'Upload Action Points')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Upload SGR weekly action points from Excel</p>
    <a href="{{ route('role.dashboard') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Dashboard
    </a>
</div>
@if(session('success'))
<div class="mb-4 px-5 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-5 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Upload Excel File</h3>
        <div id="uploadForm" class="space-y-4">
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-amber-300 transition-colors cursor-pointer" id="dropZone">
                <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <p class="text-sm text-gray-500 mb-1"><span class="font-medium text-amber-600">Click to upload</span> or drag and drop</p>
                <p class="text-xs text-gray-400">Excel files (.xlsx, .xls, .csv)</p>
                <input type="file" id="fileInput" accept=".xlsx,.xls,.csv" class="hidden">
            </div>
            <div class="flex items-center gap-3">
                <div class="flex-1"><label class="block text-xs font-medium text-gray-600 mb-1">Sheet Name (optional)</label><input type="text" id="sheetName" placeholder="Leave blank for first sheet" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none"></div>
                <div class="w-24"><label class="block text-xs font-medium text-gray-600 mb-1">Header Row</label><input type="number" id="headerRow" value="1" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none"></div>
            </div>
            <div id="uploadProgress" class="hidden">
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1"><span>Uploading...</span><span id="progressPercent">0%</span></div>
                <div class="w-full bg-gray-100 rounded-full h-2"><div id="progressBar" class="bg-amber-500 h-2 rounded-full transition-all" style="width:0%"></div></div>
            </div>
        </div>
        <div id="previewArea" class="hidden space-y-4 mt-4">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-bold text-gray-900">Column Mapping</h4>
                <span id="rowCount" class="text-xs text-gray-400"></span>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Activity Column *</label><select id="activityColumn" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none"></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Responsible Person *</label><select id="responsibleColumn" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none"></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Due Date Column</label><select id="dueDateColumn" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none"><option value="">- None -</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status Column</label><select id="statusColumn" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none"><option value="">- None -</option></select></div>
            </div>
            <div class="overflow-x-auto border rounded-xl">
                <table class="w-full text-xs"><thead><tr class="bg-gray-50 text-left text-gray-500"><th class="px-3 py-2 font-medium">#</th><th id="previewActivity" class="px-3 py-2 font-medium">Activity</th><th id="previewResponsible" class="px-3 py-2 font-medium">Responsible</th><th id="previewDueDate" class="px-3 py-2 font-medium">Due Date</th><th id="previewStatus" class="px-3 py-2 font-medium">Status</th></tr></thead><tbody id="previewBody"></tbody></table>
            </div>
            <button id="submitBtn" class="px-6 py-2.5 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Submit & Save
            </button>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">My Recent Uploads</h3>
        @if($recentBatches->count())
        <div class="space-y-3">
            @foreach($recentBatches as $batch)
            <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg">
                <div><p class="text-xs font-medium text-gray-900">{{ Str::limit($batch->source_filename, 20) }}</p><p class="text-[10px] text-gray-400">{{ $batch->created_at?->format('d M Y H:i') }}</p></div>
                <span class="text-[10px] text-gray-400">Batch #{{ $batch->import_batch }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-xs text-gray-400 text-center py-6">No uploads yet</p>
        @endif
        <div class="mt-4 pt-4 border-t">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-500">Total Uploaded</span>
                <span class="text-sm font-bold text-gray-900">{{ $totalUploaded }}</span>
            </div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-500">Pending Approval</span>
                <span class="text-sm font-bold text-amber-600">{{ $pendingApproval }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Approved</span>
                <span class="text-sm font-bold text-emerald-600">{{ $approvedCount }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
let uploadedPath = null;
let uploadedBatch = null;
let uploadedSheet = null;
let uploadedFilename = null;
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-amber-400', 'bg-amber-50'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-amber-400', 'bg-amber-50'));
dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-amber-400', 'bg-amber-50'); const f = e.dataTransfer.files[0]; if(f) handleFile(f); });
fileInput.addEventListener('change', () => { if(fileInput.files[0]) handleFile(fileInput.files[0]); });

function handleFile(file) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '{{ csrf_token() }}');
    const sheet = document.getElementById('sheetName').value;
    if(sheet) formData.append('sheet_name', sheet);
    formData.append('header_row', document.getElementById('headerRow').value);

    document.getElementById('uploadForm').classList.add('hidden');
    document.getElementById('uploadProgress').classList.remove('hidden');

    fetch('{{ route("sgr.action-points.upload") }}', {
        method: 'POST', body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            uploadedPath = data.path;
            uploadedBatch = data.batch;
            uploadedSheet = data.sheet_name;
            uploadedFilename = data.filename;
            showPreview(data.preview);
        } else {
            alert(data.message || 'Upload failed');
            location.reload();
        }
    })
    .catch(() => { alert('Upload error'); location.reload(); });
}

function showPreview(preview) {
    document.getElementById('uploadProgress').classList.add('hidden');
    document.getElementById('previewArea').classList.remove('hidden');
    const headers = preview.headers || [];
    const rows = preview.rows || [];
    document.getElementById('rowCount').textContent = rows.length + ' rows found';

    const selA = document.getElementById('activityColumn');
    const selR = document.getElementById('responsibleColumn');
    const selD = document.getElementById('dueDateColumn');
    const selS = document.getElementById('statusColumn');
    [selA, selR, selD, selS].forEach(s => { s.innerHTML = ''; });

    const chooseActivity = headers.includes('ACTIVITY');
    const chooseResponsible = headers.includes('RESPONSIBLE PERSON');
    const chooseDueDate = headers.includes('DUE DATE');
    const chooseStatus = headers.includes('STATUS');

    headers.forEach((h, i) => {
        const o1 = new Option(h, String(i));
        const o2 = new Option(h, String(i));
        const o3 = new Option(h, String(i));
        const o4 = new Option(h, String(i));
        selA.add(o1); selR.add(o2); selD.add(o3); selS.add(o4);
        if(chooseActivity && h === 'ACTIVITY') selA.value = String(i);
        if(chooseResponsible && h === 'RESPONSIBLE PERSON') selR.value = String(i);
        if(chooseDueDate && h === 'DUE DATE') selD.value = String(i);
        if(chooseStatus && h === 'STATUS') selS.value = String(i);
    });

    if(!chooseActivity) selA.value = '0';
    if(!chooseResponsible) selR.value = headers.length > 1 ? '1' : '0';

    const tbody = document.getElementById('previewBody');
    tbody.innerHTML = '';
    rows.slice(0, 10).forEach((row, ri) => {
        const tr = document.createElement('tr');
        tr.className = ri % 2 === 0 ? 'bg-white' : 'bg-gray-50/50';
        tr.innerHTML = `<td class="px-3 py-2 text-gray-400">${ri + 1}</td>` +
            headers.map(h => `<td class="px-3 py-2 text-gray-700">${row[h] ?? ''}</td>`).join('');
        tbody.appendChild(tr);
    });

    document.getElementById('submitBtn').onclick = submitData;
}

function submitData() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.textContent = 'Saving...';

    fetch('{{ route("sgr.action-points.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            activity_column: document.getElementById('activityColumn').value,
            responsible_column: document.getElementById('responsibleColumn').value,
            due_date_column: document.getElementById('dueDateColumn').value || '',
            status_column: document.getElementById('statusColumn').value || '',
            header_row: parseInt(document.getElementById('headerRow').value) || 1,
        })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            window.location.href = '{{ route("role.page", ["module" => "action-points-reports"]) }}?batch=' + (data.batch || '');
        } else {
            alert(data.message || 'Save failed');
            btn.disabled = false; btn.textContent = 'Submit & Save';
        }
    })
    .catch(() => { alert('Save error'); btn.disabled = false; btn.textContent = 'Submit & Save'; });
}
</script>
@endpush