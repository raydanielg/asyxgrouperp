@extends('layouts.admin')
@section('title', 'Journal Entries - ' . config('app.name'))
@section('page_title', 'Journal Entries')
@section('content')
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex flex-wrap items-center gap-2">
        <form method="GET" class="flex items-center gap-2 flex-wrap">
            <select name="project_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->title }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
            <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 border border-gray-200 rounded text-xs outline-none focus:border-gray-400">
            <button type="submit" class="px-3 py-2 border border-gray-200 text-gray-600 text-xs font-medium rounded hover:bg-gray-50">Filter</button>
        </form>
        <a href="{{ route('admin.journal-entries.create') }}" class="ml-auto px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Journal Entry</a>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Entry #</th><th class="px-5 py-3 font-semibold">Date</th><th class="px-5 py-3 font-semibold">Description</th><th class="px-5 py-3 font-semibold">Source</th><th class="px-5 py-3 font-semibold">Project</th><th class="px-5 py-3 font-semibold text-right">Debit</th><th class="px-5 py-3 font-semibold text-right">Credit</th><th class="px-5 py-3 font-semibold">By</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($entries as $e)
        <tr class="hover:bg-gray-50/60 cursor-pointer" onclick="window.location='{{ route('admin.journal-entries.show', $e) }}'">
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700">{{ $e->entry_number }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->entry_date->format('d M Y') }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-700">{{ \Illuminate\Support\Str::limit($e->description, 50) }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ str_replace('_', ' ', $e->source_type) }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $e->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700 text-right">{{ number_format($e->lines->sum('debit')) }}</td>
            <td class="px-5 py-3.5 text-xs font-mono text-gray-700 text-right">{{ number_format($e->lines->sum('credit')) }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-400">{{ $e->creator?->name ?? 'System' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-xs text-gray-400">No journal entries posted yet</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($entries->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $entries->links() }}</div>
    @endif
</div>
@endsection
