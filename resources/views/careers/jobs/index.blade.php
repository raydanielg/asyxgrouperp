@extends('layouts.app')
@section('title', 'Careers - ' . config('app.name'))
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
  <h1 class="text-2xl font-bold text-gray-900 mb-6">Open Positions</h1>
  @if(session('success'))
  <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm">{{ session('success') }}</div>
  @endif
  <div class="grid gap-4">
    @forelse($jobs as $job)
      <div class="bg-white rounded-xl border p-5">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-lg font-semibold text-gray-900">{{ $job->title }}</div>
            <div class="text-xs text-gray-500">{{ $job->department ?? '—' }} @if($job->location) • {{ $job->location }} @endif</div>
          </div>
          <a href="{{ route('careers.apply', $job) }}" class="px-3 py-2 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700">Apply</a>
        </div>
        @if($job->description)
        <div class="mt-3 text-sm text-gray-700">{!! nl2br(e(Str::limit($job->description, 300))) !!}</div>
        @endif
        <div class="mt-2 text-[11px] text-gray-400">Deadline: {{ $job->deadline?->format('d M Y') ?? '—' }}</div>
      </div>
    @empty
      <div class="text-gray-500">No open positions at the moment.</div>
    @endforelse
  </div>
  <div class="mt-6">{{ $jobs->links() }}</div>
</div>
@endsection
