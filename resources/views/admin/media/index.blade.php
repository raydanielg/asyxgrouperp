@extends('layouts.admin')
@section('title', 'Media Library - ' . config('app.name'))
@section('page_title', 'Media Library')
@section('content')
@php
    function mediaSize($bytes) {
        if (!$bytes) return '0 KB';
        return $bytes > 1048576 ? number_format($bytes / 1048576, 1) . ' MB' : number_format($bytes / 1024) . ' KB';
    }
@endphp

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="bg-red-50 border border-red-200 rounded-md px-5 py-3 mb-4 text-xs text-red-600">{{ $errors->first() }}</div>
@endif

<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $files->total() }} files · {{ $counts['images'] }} images · {{ $counts['docs'] }} documents · {{ mediaSize($totalSize) }}</span>
    </div>

    {{-- Upload zone --}}
    <div class="p-5 border-b border-gray-100">
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <label for="fileInput" id="dropZone" class="border-2 border-dashed border-gray-200 rounded-md p-8 text-center hover:border-gray-400 transition-colors cursor-pointer block">
                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <p class="text-xs font-medium text-gray-700" id="dropLabel">Drop files here or click to browse</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Images, PDFs and documents up to 10MB</p>
            </label>
            <input type="file" name="file" id="fileInput" class="hidden" required>
        </form>
    </div>

    {{-- Grid --}}
    @if($files->count())
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 p-5">
        @foreach($files as $file)
        @php $isImage = str_starts_with($file->mime_type ?? '', 'image/'); @endphp
        <div class="group border border-gray-200 rounded-md overflow-hidden hover:border-gray-400 transition-colors">
            <div class="h-28 bg-gray-50 flex items-center justify-center overflow-hidden">
                @if($isImage)
                <img src="{{ $file->url }}" alt="{{ $file->name }}" class="w-full h-full object-cover">
                @else
                <div class="text-center px-2">
                    <svg class="w-7 h-7 text-gray-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <p class="text-[9px] font-bold text-gray-400 uppercase mt-1">{{ strtoupper(pathinfo($file->file_name, PATHINFO_EXTENSION) ?: 'FILE') }}</p>
                </div>
                @endif
            </div>
            <div class="px-3 py-2.5">
                <p class="text-[11px] font-medium text-gray-900 truncate" title="{{ $file->file_name }}">{{ $file->name }}</p>
                <p class="text-[9px] text-gray-400 mt-0.5">{{ mediaSize($file->size) }} · {{ $file->created_at->format('d M') }}</p>
            </div>
            <div class="px-3 py-2 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ $file->url }}" target="_blank" class="text-[10px] font-medium text-gray-500 hover:text-gray-900">Open</a>
                <form method="POST" action="{{ route('admin.media.destroy', $file) }}" onsubmit="return confirmAction(this, {title: 'Delete this file?'})">@csrf @method('DELETE')
                    <button class="text-[10px] font-medium text-gray-400 hover:text-red-600">Delete</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @if($files->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $files->links() }}</div>
    @endif
    @else
    <div class="p-10 text-center"><p class="text-xs text-gray-300">No files uploaded yet</p></div>
    @endif
</div>

@push('scripts')
<script>
(function() {
    const input = document.getElementById('fileInput');
    const form = document.getElementById('uploadForm');
    const label = document.getElementById('dropLabel');
    const zone = document.getElementById('dropZone');
    input.addEventListener('change', () => {
        if (input.files.length) { label.textContent = 'Uploading ' + input.files[0].name + '...'; form.submit(); }
    });
    ['dragover','dragenter'].forEach(e => zone.addEventListener(e, ev => { ev.preventDefault(); zone.classList.add('border-gray-900','bg-gray-50'); }));
    ['dragleave','drop'].forEach(e => zone.addEventListener(e, ev => { ev.preventDefault(); zone.classList.remove('border-gray-900','bg-gray-50'); }));
    zone.addEventListener('drop', ev => {
        if (ev.dataTransfer.files.length) { input.files = ev.dataTransfer.files; input.dispatchEvent(new Event('change')); }
    });
})();
</script>
@endpush
@endsection
