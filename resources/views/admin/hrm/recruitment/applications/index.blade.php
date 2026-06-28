@extends('layouts.admin')
@section('title', 'Applications - ' . config('app.name'))
@section('page_title', 'Job Applications')
@section('content')
<div class="mb-4 flex items-center justify-between">
  <form method="GET" class="flex items-center gap-2 text-xs">
    <input name="search" value="{{ request('search') }}" placeholder="Search name, email, phone" class="px-3 py-2 rounded-lg border border-gray-200">
    <select name="job" class="px-3 py-2 rounded-lg border border-gray-200">
      <option value="">All Jobs</option>
      @foreach($jobs as $j)
      <option value="{{ $j->id }}" @selected(request('job')==$j->id)>{{ $j->title }}</option>
      @endforeach
    </select>
    <select name="status" class="px-3 py-2 rounded-lg border border-gray-200">
      <option value="">All Status</option>
      @foreach(['submitted','under_review','shortlisted','rejected','hired'] as $s)
      <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst(str_replace('_',' ', $s)) }}</option>
      @endforeach
    </select>
    <button class="px-3 py-2 bg-emerald-600 text-white rounded-lg">Filter</button>
  </form>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left text-xs text-gray-500 bg-gray-50/50">
          <th class="px-5 py-3 font-medium">Applicant</th>
          <th class="px-5 py-3 font-medium">Job</th>
          <th class="px-5 py-3 font-medium">Submitted</th>
          <th class="px-5 py-3 font-medium">Status</th>
          <th class="px-5 py-3 font-medium">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($applications as $a)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
          <td class="px-5 py-3 text-xs text-gray-900">{{ $a->full_name }}<div class="text-[11px] text-gray-500">{{ $a->email }} @if($a->phone) • {{ $a->phone }} @endif</div></td>
          <td class="px-5 py-3 text-xs text-gray-600">{{ $a->jobPosting?->title ?? '—' }}</td>
          <td class="px-5 py-3 text-xs text-gray-400">{{ $a->created_at?->format('d M Y H:i') }}</td>
          <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium @class([
            'bg-gray-50 text-gray-700' => $a->status==='submitted',
            'bg-blue-50 text-blue-700' => $a->status==='under_review',
            'bg-emerald-50 text-emerald-700' => $a->status==='shortlisted',
            'bg-red-50 text-red-700' => $a->status==='rejected',
            'bg-purple-50 text-purple-700' => $a->status==='hired',
          ])">{{ ucfirst(str_replace('_',' ', $a->status)) }}</span></td>
          <td class="px-5 py-3 text-xs">
            <a href="{{ route('admin.applications.show', $a) }}" class="text-emerald-600 hover:text-emerald-700">View</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No applications found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-5 py-4 border-t">{{ $applications->links() }}</div>
</div>
@endsection
