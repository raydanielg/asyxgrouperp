@extends('layouts.admin')
@section('title', 'Upload Document - ' . config('app.name'))
@section('page_title', 'Upload Document')
@section('content')
<div class="mb-4"><a href="{{ route('admin.documents.index') }}" class="text-sm text-gray-500 hover:text-emerald-600">&larr; Back to Documents</a></div>
<div class="bg-white rounded-xl border p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">@csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input type="text" name="title" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Category</label><select name="category" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"><option value="contract">Contract</option><option value="invoice">Invoice</option><option value="tender">Tender</option><option value="hr">HR</option><option value="legal">Legal</option><option value="technical">Technical</option></select></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Reference Type</label><input type="text" name="reference_type" placeholder="project, tender, employee..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></div>
            <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500"></textarea></div>
            <div class="col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">File <span class="text-red-500">*</span></label><input type="file" name="file" required class="w-full text-sm border border-gray-200 rounded-lg"><p class="text-[10px] text-gray-400 mt-1">Max 20MB. PDF, DOCX, XLSX, images accepted.</p></div>
        </div>
        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Upload</button>
            <a href="{{ route('admin.documents.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Cancel</a>
        </div>
    </form>
</div>
@endsection
