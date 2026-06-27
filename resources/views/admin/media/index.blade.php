@extends('layouts.admin')
@section('title', 'Media Library - ' . config('app.name'))
@section('page_title', 'Media Library')
@section('content')
<div class="bg-white rounded-xl border p-8">
    <div class="text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Media Library</h3>
        <p class="text-xs text-gray-400 mb-4">Upload and manage your media files</p>
        <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 max-w-md mx-auto hover:border-emerald-400 transition-colors">
            <p class="text-xs text-gray-400">Drag and drop files here, or click to browse</p>
            <button class="mt-3 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Browse Files</button>
        </div>
    </div>
</div>
@endsection
