@extends('layouts.admin')
@section('title', 'Application - ' . config('app.name'))
@section('page_title', 'Application Details')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 space-y-4">
    <div class="bg-white rounded-xl border p-5">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-sm font-bold text-gray-900">{{ $application->full_name }}</div>
          <div class="text-xs text-gray-500">{{ $application->email }} @if($application->phone) • {{ $application->phone }} @endif</div>
          <div class="mt-1 text-[11px] text-gray-400">Applied: {{ $application->created_at?->format('d M Y H:i') }}</div>
        </div>
        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium @class([
          'bg-gray-50 text-gray-700' => $application->status==='submitted',
          'bg-blue-50 text-blue-700' => $application->status==='under_review',
          'bg-emerald-50 text-emerald-700' => $application->status==='shortlisted',
          'bg-red-50 text-red-700' => $application->status==='rejected',
          'bg-purple-50 text-purple-700' => $application->status==='hired',
        ])">{{ ucfirst(str_replace('_',' ', $application->status)) }}</span>
      </div>
      <div class="mt-4 text-xs text-gray-700">
        <div class="font-medium text-gray-900 mb-1">Job</div>
        <div>{{ $application->jobPosting?->title ?? '—' }}</div>
      </div>
    </div>

    <div class="bg-white rounded-xl border p-5">
      <div class="font-medium text-sm mb-3">Documents</div>
      <div class="space-y-2 text-xs">
        @if($application->resume_path)
        <div><a target="_blank" href="{{ Storage::url($application->resume_path) }}" class="text-emerald-600 hover:text-emerald-700">Resume</a></div>
        @endif
        @if($application->cover_letter_path)
        <div><a target="_blank" href="{{ Storage::url($application->cover_letter_path) }}" class="text-emerald-600 hover:text-emerald-700">Cover Letter</a></div>
        @endif
        @if(is_array($application->extra_docs))
          @foreach($application->extra_docs as $i => $doc)
          <div><a target="_blank" href="{{ Storage::url($doc) }}" class="text-emerald-600 hover:text-emerald-700">Attachment {{ $i+1 }}</a></div>
          @endforeach
        @endif
        @if(!$application->resume_path && !$application->cover_letter_path && empty($application->extra_docs))
        <div class="text-gray-400">No documents</div>
        @endif
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="bg-white rounded-xl border p-5">
      <div class="font-medium text-sm mb-3">Take Action</div>
      <form method="POST" action="{{ route('admin.applications.approve', $application) }}" class="space-y-3">@csrf
        <select name="decision" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm">
          <option value="shortlist">Shortlist</option>
          <option value="reject">Reject</option>
          <option value="hire">Hire</option>
        </select>
        <textarea name="comment" placeholder="Comment (optional)" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm"></textarea>
        <button class="w-full px-3 py-2 bg-emerald-600 text-white rounded-lg">Submit</button>
      </form>
    </div>

    <div class="bg-white rounded-xl border p-5">
      <div class="font-medium text-sm mb-3">Decision Timeline</div>
      <div class="space-y-3 text-xs">
        @forelse($application->approvals as $ap)
        <div>
          <div class="font-medium text-gray-900">{{ ucfirst($ap->decision) }} <span class="text-gray-400">• {{ $ap->created_at?->format('d M Y H:i') }}</span></div>
          <div class="text-gray-600">By: {{ $ap->approver?->name ?? 'System' }}</div>
          @if($ap->comment)
          <div class="text-gray-500">"{{ $ap->comment }}"</div>
          @endif
        </div>
        @empty
        <div class="text-gray-400">No decisions yet</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
