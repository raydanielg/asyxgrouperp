@extends('layouts.admin')
@section('title', 'Recruitment - ' . config('app.name'))
@section('page_title', 'Recruitment & Job Postings')
@section('content')

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between">
        <span class="text-xs text-gray-400">{{ $jobs->total() }} job postings</span>
        <button type="button" onclick="openJobDrawer()" class="px-4 py-2 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">New Job Posting</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-gray-500 uppercase tracking-wide bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-3 font-semibold">Title</th>
                    <th class="px-5 py-3 font-semibold">Department</th>
                    <th class="px-5 py-3 font-semibold">Type</th>
                    <th class="px-5 py-3 font-semibold">Vacancies</th>
                    <th class="px-5 py-3 font-semibold">Deadline</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($jobs as $j)
            @php $jc = ['open' => '#10b981', 'closed' => '#ef4444', 'draft' => '#9ca3af'][$j->status] ?? '#9ca3af'; @endphp
            <tr class="hover:bg-gray-50/60">
                <td class="px-5 py-3.5 text-xs font-medium text-gray-900">{{ $j->title }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ $j->department ?? '—' }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $j->job_type)) }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-700">{{ $j->vacancies }}</td>
                <td class="px-5 py-3.5 text-xs text-gray-400">{{ $j->deadline?->format('d M Y') ?? '—' }}</td>
                <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 text-xs text-gray-600"><span style="width:6px;height:6px;border-radius:99px;background:{{ $jc }}"></span>{{ ucfirst($j->status) }}</span></td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('admin.job-postings.applications', $j) }}" class="text-xs font-medium text-gray-600 hover:text-gray-900">Applications</a>
                        <form method="POST" action="{{ route('admin.job-postings.destroy', $j) }}" class="inline" onsubmit="return confirmAction(this, {title:'Delete posting?', text:'{{ addslashes($j->title) }} will be permanently removed.', confirmText:'Yes, delete'})">@csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-gray-400 hover:text-red-600">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-xs text-gray-400">No job postings</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($jobs->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $jobs->links() }}</div>
    @endif
</div>

<div id="jobDrawer" class="fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeJobDrawer()"></div>
    <div id="jobDrawerPanel" class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col -translate-x-full transition-transform duration-300 ease-out">
        <div class="px-5 py-4 bg-slate-900 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-sm font-bold text-white">New Job Posting</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Open position</p>
            </div>
            <button type="button" onclick="closeJobDrawer()" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.job-postings.store') }}" class="flex-1 overflow-y-auto p-5">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Title <span class="text-red-500">*</span></label><input name="title" required placeholder="e.g. Senior Accountant" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Department</label><input name="department" placeholder="e.g. Finance" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input name="location" placeholder="e.g. Dar es Salaam" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Job Type</label><select name="job_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="full_time">Full Time</option><option value="part_time">Part Time</option><option value="contract">Contract</option><option value="remote">Remote</option></select></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-1">Vacancies</label><input name="vacancies" type="number" value="1" min="1" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                </div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Deadline</label><input name="deadline" type="date" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select name="status" class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400 bg-white"><option value="open">Open</option><option value="closed">Closed</option><option value="draft">Draft</option></select></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" rows="3" placeholder="Role summary..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Requirements</label><textarea name="requirements" rows="3" placeholder="Skills & qualifications..." class="w-full px-3 py-2 text-sm border border-gray-200 rounded outline-none focus:border-gray-400"></textarea></div>
            </div>
            <div class="flex items-center gap-3 pt-5 mt-2 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-slate-900 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">Create</button>
                <button type="button" onclick="closeJobDrawer()" class="px-5 py-2 text-sm text-gray-500 hover:text-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openJobDrawer() {
    document.getElementById('jobDrawer').classList.remove('hidden');
    requestAnimationFrame(() => document.getElementById('jobDrawerPanel').classList.remove('-translate-x-full'));
}
function closeJobDrawer() {
    document.getElementById('jobDrawerPanel').classList.add('-translate-x-full');
    setTimeout(() => document.getElementById('jobDrawer').classList.add('hidden'), 300);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeJobDrawer(); });
</script>
@endpush
@endsection
