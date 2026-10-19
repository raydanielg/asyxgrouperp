@extends('layouts.admin')
@section('title', 'Create Application - ' . config('app.name'))
@section('page_title', 'New Application')
@section('content')
<div class="max-w-2xl">
  <div class="mb-4">
    <a href="{{ route('admin.applications.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Applications</a>
  </div>

  <form method="POST" action="{{ route('admin.applications.store') }}" enctype="multipart/form-data" class="space-y-5">@csrf

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Applicant</h4></div>
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Job Posting <span class="text-red-500">*</span></label>
          <select name="job_posting_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
            @foreach($jobs as $j)
            <option value="{{ $j->id }}">{{ $j->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input name="full_name" required placeholder="e.g. Jane Doe" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
            <input name="email" type="email" placeholder="name@email.com" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
          <input name="phone" placeholder="+255 700 000 000" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400">
        </div>
      </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Documents</h4></div>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Resume</label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-600 hover:file:bg-gray-200">
            <p class="text-[10px] text-gray-400 mt-1">PDF/DOC up to 5MB</p>
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Cover Letter</label>
            <input type="file" name="cover_letter" accept=".pdf,.doc,.docx" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-600 hover:file:bg-gray-200">
            <p class="text-[10px] text-gray-400 mt-1">PDF/DOC up to 5MB</p>
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Attachments</label>
          <input type="file" name="attachments[]" multiple class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-600 hover:file:bg-gray-200">
          <p class="text-[10px] text-gray-400 mt-1">Any additional supporting documents</p>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
          <textarea name="notes" rows="3" placeholder="Internal notes..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
        </div>
      </div>
    </div>

    <div class="flex items-center justify-between gap-3 bg-white rounded-md border border-gray-200 px-5 py-3.5">
      <a href="{{ route('admin.applications.index') }}" class="text-xs text-gray-500 hover:text-gray-800">Cancel</a>
      <button type="submit" class="px-5 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Create Application</button>
    </div>
  </form>
</div>
@endsection
