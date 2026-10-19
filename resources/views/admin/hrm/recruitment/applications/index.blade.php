@extends('layouts.admin')
@section('title', 'Applications - ' . config('app.name'))
@section('page_title', 'Job Applications')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
  <form method="GET" class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-2">
    <input name="search" value="{{ request('search') }}" placeholder="Search name, email, phone" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white w-56">
    <select name="job" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
      <option value="">All Jobs</option>
      @foreach($jobs as $j)
      <option value="{{ $j->id }}" @selected(request('job')==$j->id)>{{ $j->title }}</option>
      @endforeach
    </select>
    <select name="status" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
      <option value="">All Status</option>
      @foreach(['submitted','under_review','shortlisted','rejected','hired'] as $s)
      <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
      @endforeach
    </select>
    <button type="submit" class="px-3 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
    @if(request()->hasAny(['search','job','status']))
    <a href="{{ route('admin.applications.index') }}" class="text-xs text-gray-400 hover:text-gray-700">Clear</a>
    @endif
  </form>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
          <th class="px-5 py-3 font-semibold">Applicant</th>
          <th class="px-5 py-3 font-semibold">Job</th>
          <th class="px-5 py-3 font-semibold">Submitted</th>
          <th class="px-5 py-3 font-semibold">Status</th>
          <th class="px-5 py-3 font-semibold text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($applications as $a)
        @php $ac = ['submitted' => '#9ca3af', 'under_review' => '#3b82f6', 'shortlisted' => '#10b981', 'rejected' => '#ef4444', 'hired' => '#8b5cf6'][$a->status] ?? '#9ca3af'; @endphp
        <tr class="hover:bg-gray-50/60">
          <td class="px-5 py-3.5">
            <p class="text-xs font-medium text-gray-900">{{ $a->full_name }}</p>
            <p class="text-[10px] text-gray-400">{{ $a->email }}@if($a->phone) · {{ $a->phone }}@endif</p>
          </td>
          <td class="px-5 py-3.5 text-xs text-gray-600">{{ $a->jobPosting?->title ?? '—' }}</td>
          <td class="px-5 py-3.5 text-xs text-gray-400">{{ $a->created_at?->format('d M Y H:i') }}</td>
          <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $ac }}"></span>{{ ucfirst(str_replace('_',' ', $a->status)) }}</span></td>
          <td class="px-5 py-3.5">
            <div class="flex items-center justify-end">
              <a href="{{ route('admin.applications.show', $a) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">View</a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">No applications found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($applications->hasPages())
  <div class="px-5 py-3 border-t border-gray-100">{{ $applications->links() }}</div>
  @endif
</div>
@endsection
