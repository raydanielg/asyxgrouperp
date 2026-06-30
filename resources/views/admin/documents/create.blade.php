@extends('layouts.admin')
@section('title', 'Upload Document - ' . config('app.name'))
@section('page_title', 'Upload Document')
@section('content')
<div class="mb-4"><a href="{{ route('admin.documents.index') }}" class="text-sm text-gray-500 hover:text-bronze">&larr; Back to Documents</a></div>

@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-xl border border-gray-100 p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data">@csrf
        <div class="space-y-5">
            {{-- Basic Info --}}
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-3">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-bronze/30 focus:border-bronze" placeholder="e.g. Service Agreement - ABC Corp">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Category <span class="text-red-500">*</span></label>
                        <select name="category" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-bronze/30 focus:border-bronze">
                            @foreach($categories as $slug => $cat)
                                <option value="{{ $slug }}" {{ $preselectedCategory === $slug ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Tags</label>
                        <input type="text" name="tags" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-bronze/30 focus:border-bronze" placeholder="comma, separated, tags">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-bronze/30 focus:border-bronze" placeholder="Brief description of this document..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Project Linking --}}
            <div class="pt-4 border-t">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Project Linking</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Link to Project</label>
                        <select name="project_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-bronze/30 focus:border-bronze">
                            <option value="">No project (general document)</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ (string)$preselectedProject === (string)$project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">Link this document to a specific project (contracts, minutes, action points, etc.)</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Reference Type</label>
                        <input type="text" name="reference_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-bronze/30 focus:border-bronze" placeholder="project, tender, employee...">
                    </div>
                </div>
            </div>

            {{-- File Upload --}}
            <div class="pt-4 border-t">
                <h3 class="text-sm font-bold text-gray-900 mb-3">File</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Upload File <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required class="w-full text-sm border border-gray-200 rounded-lg p-2">
                        <p class="text-[10px] text-gray-400 mt-1">Max 20MB. PDF, DOCX, XLSX, images accepted.</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Expiry Date (optional)</label>
                            <input type="date" name="expiry_date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-bronze/30 focus:border-bronze">
                        </div>
                        <div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-600 mt-6">
                                <input type="checkbox" name="is_confidential" value="1" class="rounded">
                                Mark as Confidential
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Signers --}}
            <div class="pt-4 border-t">
                <h3 class="text-sm font-bold text-gray-900 mb-1">E-Signature Requests (optional)</h3>
                <p class="text-[10px] text-gray-400 mb-3">Select users who need to sign this document. They will be notified.</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                    @foreach($users as $user)
                    <label class="flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="signers[]" value="{{ $user->id }}" class="rounded">
                        <div>
                            <p class="text-gray-700 text-xs font-medium">{{ $user->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $user->email }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-4 border-t">
                <button type="submit" class="px-5 py-2 bg-bronze text-white text-sm font-semibold rounded-lg hover:bg-bronze-dark">Upload Document</button>
                <a href="{{ route('admin.documents.index') }}" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
