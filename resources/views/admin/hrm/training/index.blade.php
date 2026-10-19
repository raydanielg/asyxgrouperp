@extends('layouts.admin')
@section('title', 'Training - ' . config('app.name'))
@section('page_title', 'Training Plans')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $trainings->total() }} programs</span>
        <button type="button" onclick="openTrDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Training</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Trainer</th>
                    <th class="px-5 py-3 font-semibold">Dates</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($trainings as $t)
            @php $tc = ['completed' => '#10b981', 'scheduled' => '#3b82f6', 'in_progress' => '#f59e0b', 'cancelled' => '#ef4444'][$t->status] ?? '#9ca3af'; @endphp
            <tr class="hover:bg-gray-50/60">
                <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $t->title }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $t->trainer ?? '—' }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $t->start_date?->format('d M Y') ?? '—' }}{{ $t->end_date ? ' – ' . $t->end_date->format('d M Y') : '' }}</td>
                <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $tc }}"></span>{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span></td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end">
                        <form method="POST" action="{{ route('admin.training.destroy', $t) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete training?', text:'{{ addslashes($t->title) }} will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">No training programs</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($trainings->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $trainings->links() }}</div>
    @endif
</div>

<div id="trDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeTrDrawer()"></div>
    <div id="trDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Training Program</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Employee development</p>
            </div>
            <button type="button" onclick="closeTrDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.training.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Safety Induction" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Trainer</label><input name="trainer" placeholder="e.g. HR Team" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input name="start_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input name="end_date" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="scheduled">Scheduled</option><option value="in_progress">In Progress</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="3" placeholder="What will be covered..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
                <button type="button" onclick="closeTrDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openTrDrawer() {
    document.getElementById('trDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('trDrawerPanel').classList.remove('-translate-x-full'));
}
function closeTrDrawer() {
    document.getElementById('trDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('trDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeTrDrawer(); });
</script>
@endpush
@endsection
