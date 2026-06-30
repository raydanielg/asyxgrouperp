@extends('layouts.admin')

@section('title', 'Employee Bonuses')
@section('page_title', 'Employee Bonuses')

@section('page_actions')
    <button onclick="document.getElementById('bonusModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-bronze text-white text-sm font-semibold rounded-lg hover:bg-bronze-dark transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Bonus
    </button>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    @php
        $totalPending = $bonuses->where('status', 'pending')->sum('amount');
        $totalApproved = $bonuses->where('status', 'approved')->sum('amount');
        $totalPaid = $bonuses->where('status', 'paid')->sum('amount');
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Pending</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ number_format($totalPending, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Approved</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ number_format($totalApproved, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <p class="text-[10px] text-gray-400 uppercase font-semibold">Paid</p>
            <p class="text-xl font-bold text-emerald-600 mt-1">{{ number_format($totalPaid, 0) }}</p>
            <p class="text-[10px] text-gray-400">TZS</p>
        </div>
    </div>

    {{-- Bonuses Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 bg-gray-50 border-b">
                        <th class="px-4 py-3 font-medium">Bonus #</th>
                        <th class="px-4 py-3 font-medium">Employee</th>
                        <th class="px-4 py-3 font-medium">Project</th>
                        <th class="px-4 py-3 font-medium">Type</th>
                        <th class="px-4 py-3 font-medium">Title</th>
                        <th class="px-4 py-3 font-medium text-right">Amount</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bonuses as $bonus)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ $bonus->bonus_number }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.employees.show', $bonus->employee_id) }}" class="text-xs font-medium text-gray-800 hover:text-bronze">{{ $bonus->employee?->full_name ?? '—' }}</a>
                            <p class="text-[10px] text-gray-400">{{ $bonus->employee?->department ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($bonus->project)
                                <a href="{{ route('admin.projects.show', $bonus->project_id) }}" class="text-xs text-blue-600 hover:underline">{{ $bonus->project->title }}</a>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-600">{{ str_replace('_', ' ', ucfirst($bonus->type)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700">{{ $bonus->title }}</td>
                        <td class="px-4 py-3 text-xs text-right font-semibold text-gray-900">{{ number_format($bonus->amount, 0) }} TZS</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $bonus->bonus_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium
                                {{ $bonus->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                {{ $bonus->status === 'approved' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $bonus->status === 'pending' ? 'bg-amber-50 text-amber-700' : '' }}
                                {{ $bonus->status === 'rejected' ? 'bg-red-50 text-red-700' : '' }}">{{ ucfirst($bonus->status) }}</span>
                        </td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            @if($bonus->status === 'pending')
                            <form method="POST" action="{{ route('admin.bonuses.approve', $bonus) }}">@csrf
                                <button type="submit" class="text-xs text-emerald-600 hover:underline font-semibold">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.bonuses.reject', $bonus) }}">@csrf
                                <button type="submit" class="text-xs text-red-600 hover:underline">Reject</button>
                            </form>
                            @elseif($bonus->status === 'approved')
                            <form method="POST" action="{{ route('admin.bonuses.paid', $bonus) }}">@csrf
                                <button type="submit" class="text-xs text-emerald-600 hover:underline font-semibold">Mark Paid</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.bonuses.destroy', $bonus) }}">@csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline" onclick="return confirm('Delete this bonus?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-4 py-12 text-center text-gray-400 text-sm">No bonuses yet. Click "Add Bonus" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $bonuses->links() }}
    </div>
</div>

{{-- Add Bonus Modal --}}
<div id="bonusModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Employee Bonus</h3>
        <form method="POST" action="{{ route('admin.bonuses.store') }}" class="space-y-3">@csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Employee *</label>
                <select name="employee_id" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">Select Employee...</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->department ?? 'N/A' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Project (optional)</label>
                <select name="project_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    <option value="">No specific project</option>
                    @foreach($projects as $proj)
                    <option value="{{ $proj->id }}">{{ $proj->title }} ({{ $proj->project_number }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bonus Type *</label>
                <select name="type" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                    @foreach(['performance' => 'Performance', 'project_completion' => 'Project Completion', 'milestone' => 'Milestone', 'special' => 'Special'] as $k => $v)
                    <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Title *</label><input name="title" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" placeholder="e.g. Q1 Performance Bonus"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea name="description" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none" rows="2"></textarea></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Amount (TZS) *</label><input name="amount" type="number" step="0.01" required class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Bonus Date *</label><input name="bonus_date" type="date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('bonusModal').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-bronze text-white text-sm font-medium rounded-lg hover:bg-bronze-dark">Create Bonus</button>
            </div>
        </form>
    </div>
</div>
@endsection
