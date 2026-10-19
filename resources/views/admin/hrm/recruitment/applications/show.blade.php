@extends('layouts.admin')
@section('title', 'Application - ' . config('app.name'))
@section('page_title', 'Application Details')
@section('content')
@php
  $ac = ['submitted' => '#9ca3af', 'under_review' => '#3b82f6', 'shortlisted' => '#10b981', 'rejected' => '#ef4444', 'hired' => '#8b5cf6'][$application->status] ?? '#9ca3af';
@endphp

<div class="mb-4">
  <a href="{{ route('admin.applications.index') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to Applications</a>
</div>

{{-- Header card --}}
<div class="bg-white rounded-md border border-gray-200 px-5 py-4 mb-4 flex items-center justify-between flex-wrap gap-4">
  <div class="flex items-center gap-4">
    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-white font-bold text-xs uppercase">{{ substr($application->full_name ?? '?', 0, 1) }}</div>
    <div>
      <div class="flex items-center gap-2">
        <span class="text-sm font-bold text-gray-900">{{ $application->full_name }}</span>
        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ac }}"></span>{{ ucfirst(str_replace('_',' ', $application->status)) }}</span>
      </div>
      <p class="text-xs text-gray-400 mt-0.5">{{ $application->email }}@if($application->phone) · {{ $application->phone }}@endif · Applied {{ $application->created_at?->format('d M Y H:i') }}</p>
    </div>
  </div>
  <div class="text-right">
    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Applied For</p>
    <p class="text-sm font-bold text-gray-900">{{ $application->jobPosting?->title ?? '—' }}</p>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    {{-- Documents --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Documents</h4></div>
      <div class="p-5 space-y-2 text-xs">
        @if($application->resume_path)
        <div><a target="_blank" href="{{ Storage::url($application->resume_path) }}" class="text-gray-700 hover:text-gray-900 font-medium">Resume &rarr;</a></div>
        @endif
        @if($application->cover_letter_path)
        <div><a target="_blank" href="{{ Storage::url($application->cover_letter_path) }}" class="text-gray-700 hover:text-gray-900 font-medium">Cover Letter &rarr;</a></div>
        @endif
        @if(is_array($application->extra_docs))
          @foreach($application->extra_docs as $i => $doc)
          <div><a target="_blank" href="{{ Storage::url($doc) }}" class="text-gray-700 hover:text-gray-900 font-medium">Attachment {{ $i+1 }} &rarr;</a></div>
          @endforeach
        @endif
        @if(!$application->resume_path && !$application->cover_letter_path && empty($application->extra_docs))
        <p class="text-gray-400">No documents uploaded</p>
        @endif
      </div>
    </div>
    @if($application->notes)
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Notes</h4></div>
      <div class="p-5 text-xs text-gray-600 whitespace-pre-wrap">{{ $application->notes }}</div>
    </div>
    @endif
  </div>

  <div class="space-y-4">
    {{-- Take Action --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Take Action</h4></div>
      <form method="POST" action="{{ route('admin.applications.approve', $application) }}" class="p-5 space-y-3">@csrf
        <select name="decision" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
          <option value="shortlist">Shortlist</option>
          <option value="reject">Reject</option>
          <option value="hire">Hire</option>
        </select>
        <textarea name="comment" rows="2" placeholder="Comment (optional)" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea>
        <button type="submit" class="w-full px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Submit Decision</button>
      </form>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
      <div class="px-5 py-3.5 border-b border-gray-100"><h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">Decision Timeline</h4></div>
      <div class="p-5 space-y-3 text-xs">
        @forelse($application->approvals as $ap)
        <div>
          <p class="font-medium text-gray-900">{{ ucfirst($ap->decision) }} <span class="font-normal text-gray-400">· {{ $ap->created_at?->format('d M Y H:i') }}</span></p>
          <p class="text-gray-500">By: {{ $ap->approver?->name ?? 'System' }}</p>
          @if($ap->comment)
          <p class="text-gray-500 mt-0.5">"{{ $ap->comment }}"</p>
          @endif
        </div>
        @empty
        <p class="text-gray-400">No decisions yet</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
