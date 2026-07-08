@extends('layouts.admin')
@section('title', 'Journal Entries - ' . config('app.name'))
@section('page_title', 'Journal Entries')
@section('content')
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <select name="project_id" onchange="this.form.submit()" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
            <option value="">All Projects</option>
            @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->title }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
        <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 outline-none">
        <button type="submit" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200">Filter</button>
    </form>
    <a href="{{ route('admin.journal-entries.create') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Journal Entry
    </a>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Entry #</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Description</th><th class="px-5 py-3 font-medium">Source</th><th class="px-5 py-3 font-medium">Project</th><th class="px-5 py-3 font-medium">Debit</th><th class="px-5 py-3 font-medium">Credit</th><th class="px-5 py-3 font-medium">By</th></tr></thead>
        <tbody>
        @forelse($entries as $e)
        <tr class="border-t border-gray-100 hover:bg-gray-50/50 cursor-pointer" onclick="window.location='{{ route('admin.journal-entries.show', $e) }}'">
            <td class="px-5 py-3 text-xs font-mono text-emerald-700">{{ $e->entry_number }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->entry_date->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ \Illuminate\Support\Str::limit($e->description, 50) }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ str_replace('_', ' ', $e->source_type) }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $e->project?->title ?? '—' }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-700">{{ number_format($e->lines->sum('debit')) }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-700">{{ number_format($e->lines->sum('credit')) }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $e->creator?->name ?? 'System' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400 text-xs">No journal entries posted yet</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $entries->links() }}</div>
</div>
@endsection
