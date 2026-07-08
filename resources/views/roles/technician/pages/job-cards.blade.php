@extends('roles.shared.page')
@section('title', 'Job Cards - ' . $roleLabel)
@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 mb-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-12 -mt-12"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Job Cards</h2>
                <p class="text-emerald-100 text-sm mt-1">Your assigned job cards and tasks</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Total Assigned</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCards ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-amber-200 p-4">
            <p class="text-[10px] font-medium text-amber-600 uppercase tracking-wider">Open</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $openCards ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-sky-200 p-4">
            <p class="text-[10px] font-medium text-sky-600 uppercase tracking-wider">In Progress</p>
            <p class="text-2xl font-bold text-sky-700 mt-1">{{ $inProgressCards ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-emerald-200 p-4">
            <p class="text-[10px] font-medium text-emerald-600 uppercase tracking-wider">Resolved</p>
            <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $resolvedCards ?? 0 }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Job Cards</h3>
        </div>
        @php $jobCards = $jobCards ?? collect(); @endphp
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Job #</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Title</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Project</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Priority</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-600 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($jobCards as $card)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-900">{{ $card->job_number }}</td>
                        <td class="px-4 py-3 text-gray-900 max-w-[200px] truncate font-medium">{{ $card->title }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $card->project?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{
                                $card->priority === 'critical' ? 'bg-rose-50 text-rose-700' :
                                ($card->priority === 'high' ? 'bg-orange-50 text-orange-700' :
                                ($card->priority === 'medium' ? 'bg-sky-50 text-sky-700' :
                                'bg-gray-50 text-gray-600'))
                            }}">{{ ucfirst($card->priority ?? 'normal') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{
                                $card->status === 'open' ? 'bg-amber-50 text-amber-700' :
                                ($card->status === 'in_progress' ? 'bg-sky-50 text-sky-700' :
                                ($card->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' :
                                'bg-gray-50 text-gray-600'))
                            }}">{{ ucfirst(str_replace('_', ' ', $card->status)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs {{ $card->due_date && $card->due_date->isPast() && $card->status !== 'resolved' ? 'text-rose-600 font-semibold' : 'text-gray-600' }}">
                            {{ $card->due_date ? $card->due_date->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.job-cards.show', $card) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-semibold text-emerald-600 hover:text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-50 transition-colors">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm text-gray-400">No job cards assigned to you yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobCards instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $jobCards->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
