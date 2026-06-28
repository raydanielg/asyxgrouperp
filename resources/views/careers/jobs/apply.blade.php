@extends('layouts.app')
@section('title', 'Apply - ' . config('app.name'))
@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">Apply for {{ $jobPosting->title }}</h1>
  <div class="bg-white rounded-xl border p-6">
    <form method="POST" action="{{ route('careers.apply.submit', $jobPosting) }}" enctype="multipart/form-data" class="space-y-4">@csrf
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Full Name *</label>
          <input name="full_name" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"/>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
          <input name="email" type="email" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"/>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Phone</label>
          <input name="phone" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"/>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Resume (PDF/DOC up to 5MB)</label>
          <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full text-xs"/>
        </div>
        <div>
          <label class="block text-xs font-medium text-gray-600 mb-1">Cover Letter (PDF/DOC up to 5MB)</label>
          <input type="file" name="cover_letter" accept=".pdf,.doc,.docx" class="w-full text-xs"/>
        </div>
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Attachments (multiple)</label>
        <input type="file" name="attachments[]" multiple class="w-full text-xs"/>
        <p class="text-[11px] text-gray-400 mt-1">You can attach any additional supporting documents.</p>
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
        <textarea name="notes" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"></textarea>
      </div>

      <div class="pt-2 flex gap-3">
        <a href="{{ route('careers.jobs') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</a>
        <button class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Submit Application</button>
      </div>
    </form>
  </div>
</div>
@endsection
