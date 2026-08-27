@extends('layouts.admin')
@section('title', 'Cost Allocations - ' . config('app.name'))
@section('page_title', 'Cost Allocations')
@section('content')
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Track all cost allocations across cost centers</p>
    <a href="{{ route('admin.cost-centers.index') }}" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        Manage Cost Centers
    </a>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-3 font-medium">Cost Center</th>
                    <th class="px-5 py-3 font-medium">Allocated To</th>
                    <th class="px-5 py-3 font-medium text-right">Amount</th>
                    <th class="px-5 py-3 font-medium text-right">Percentage</th>
                    <th class="px-5 py-3 font-medium">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allocations as $a)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                    <td class="px-5 py-3 text-xs font-medium text-gray-700">{{ $a->costCenter?->name ?? 'N/A' }} <span class="text-gray-400 font-mono">({{ $a->costCenter?->code ?? '—' }})</span></td>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ class_basename($a->costAllocatable_type) ?? '—' }} #{{ $a->costAllocatable_id }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TZS {{ number_format($a->amount) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500 text-right">{{ $a->percentage ? number_format($a->percentage, 2) . '%' : '—' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $a->created_at?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No cost allocations found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t">{{ $allocations->links() }}</div>
</div>
@endsection
