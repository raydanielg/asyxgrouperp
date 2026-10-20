@extends('layouts.admin')
@section('title', 'Media Library - ' . config('app.name'))
@section('page_title', 'Media Library')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Upload and manage media files</span>
    </div>
    <div class="p-6">
        <div class="border-2 border-dashed border-gray-200 rounded-md p-10 text-center hover:border-gray-400 transition-colors max-w-xl mx-auto">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-xs font-medium text-gray-700 mb-1">Drop files here to upload</p>
            <p class="text-[11px] text-gray-400 mb-4">Images, PDFs and documents up to 10MB</p>
            <button class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Browse Files</button>
        </div>
        <div class="mt-6 text-center">
            <p class="text-[11px] text-gray-300">No files uploaded yet</p>
        </div>
    </div>
</div>
@endsection
