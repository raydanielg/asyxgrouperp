@extends('layouts.admin')
@section('title', 'Bugs - ' . config('app.name'))
@section('page_title', 'Bug Management')
@section('content')

@if(session('success'))
<div class="bg-gray-50 border border-gray-200 rounded-md px-5 py-3 mb-4 text-xs text-gray-700">{{ session('success') }}</div>
@endif

@php
    $counts = [
        'total' => \App\Models\ProjectBug::count(),
        'open' => \App\Models\ProjectBug::where('status', 'open')->count(),
        'in_progress' => \App\Models\ProjectBug::where('status', 'in_progress')->count(),
        'fixed' => \App\Models\ProjectBug::where('status', 'fixed')->count(),
        'critical' => \App\Models\ProjectBug::where('severity', 'critical')->whereNotIn('status', ['fixed', 'closed'])->count(),
    ];
@endphp

{{-- Summary strip --}}
<div class="bg-white rounded-md border border-gray-200 overflow-hidden mb-4">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">Track and manage project bugs</span>
        <button type="button" onclick="openDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">Report Bug</button>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 divide-x divide-gray-100">
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Total</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $counts['total'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Open</p><p class="text-lg font-bold text-red-500 mt-0.5">{{ $counts['open'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">In Progress</p><p class="text-lg font-bold text-amber-500 mt-0.5">{{ $counts['in_progress'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Fixed</p><p class="text-lg font-bold text-emerald-600 mt-0.5">{{ $counts['fixed'] }}</p></div>
        <div class="px-4 py-3.5"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Critical</p><p class="text-lg font-bold text-red-600 mt-0.5">{{ $counts['critical'] }}</p></div>
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search bugs..." class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 w-48">
            <select name="project_id" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                @endforeach
            </select>
            <select name="severity" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Severity</option>
                <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            <select name="status" class="px-3 py-1.5 border border-gray-200 rounded text-xs outline-none focus:border-gray-400 bg-white">
                <option value="">All Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="fixed" {{ request('status') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <button type="submit" class="px-4 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Filter</button>
            @if(request()->hasAny(['search','project_id','severity','status']))
            <a href="{{ route('admin.bugs.index') }}" class="text-xs text-gray-400 hover:text-gray-600 ml-1">Clear</a>
            @endif
        </form>
    </div>
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                <th class="px-5 py-3 font-semibold">Bug</th><th class="px-5 py-3 font-semibold">Project</th><th class="px-5 py-3 font-semibold">Reported By</th><th class="px-5 py-3 font-semibold">Assigned To</th><th class="px-5 py-3 font-semibold">Severity</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($bugs as $b)
        @php
            $sev = ['low'=>'#3b82f6','medium'=>'#f59e0b','high'=>'#ef4444','critical'=>'#dc2626'][$b->severity] ?? '#9ca3af';
            $st = ['open'=>'#ef4444','in_progress'=>'#f59e0b','fixed'=>'#10b981','closed'=>'#9ca3af'][$b->status] ?? '#9ca3af';
        @endphp
        <tr class="hover:bg-gray-50/60">
            <td class="px-5 py-3.5 max-w-[260px]">
                <p class="text-xs font-medium text-gray-900 truncate">{{ $b->title }}</p>
                @if($b->description)<p class="text-[10px] text-gray-400 truncate mt-0.5">{{ $b->description }}</p>@endif
            </td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $b->project?->title ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs text-gray-500">{{ $b->reportedBy?->name ?? '—' }}</td>
            <td class="px-5 py-3.5 text-xs {{ $b->assignedTo ? 'text-gray-700' : 'text-gray-300' }}">{{ $b->assignedTo?->name ?? '—' }}</td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600 capitalize"><span style="width:6px;height:6px;border-radius:99px;background:{{ $sev }}"></span>{{ $b->severity }}</span></td>
            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $st }}"></span>{{ ucfirst(str_replace('_', ' ', $b->status)) }}</span></td>
            <td class="px-5 py-3.5 text-right">
                <form id="del-bug-{{ $b->id }}" method="POST" action="{{ route('admin.bugs.destroy', $b) }}" class="inline">@csrf @method('DELETE')</form>
                <button onclick="confirmDelete('del-bug-{{ $b->id }}')" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-xs">No bugs reported</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($bugs->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $bugs->links() }}</div>
    @endif
</div>

{{-- Report Bug Drawer --}}
<div id="createDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeDrawer()"></div>
    <div id="createDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-sm bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div><h3 class="text-sm font-bold text-white">Report Bug</h3><p class="text-[11px] text-slate-400 mt-0.5">File a new bug</p></div>
            <button type="button" onclick="closeDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.bugs.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Project <span class="text-red-500">*</span></label>
                    <select name="project_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">Select project...</option>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="Brief bug title..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="3" placeholder="Steps to reproduce..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Severity</label><select name="severity" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="open">Open</option><option value="in_progress">In Progress</option><option value="fixed">Fixed</option><option value="closed">Closed</option></select></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Assign To</label>
                    <select name="assigned_to" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white">
                        <option value="">— Unassigned —</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Report Bug</button>
                <button type="button" onclick="closeDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDrawer() {
    document.getElementById('createDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('createDrawerPanel').classList.remove('-translate-x-full'));
}
function closeDrawer() {
    document.getElementById('createDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('createDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });
</script>
@endpush
