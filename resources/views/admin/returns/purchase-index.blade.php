@extends('layouts.admin')
@section('title', 'Purchase Returns - ' . config('app.name'))
@section('page_title', 'Purchase Returns')
@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Return #</th><th class="px-5 py-3 font-medium">Vendor</th><th class="px-5 py-3 font-medium">Reason</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Actions</th></tr></thead>
        <tbody>@forelse($returns as $return)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-mono text-gray-700"><a href="{{ route('admin.purchase-returns.show', $return) }}" class="hover:text-emerald-600">{{ $return->return_number }}</a></td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $return->vendor?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $return->reason)) }}</td>
            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($return->total_amount) }}</td>
            <td class="px-5 py-3">@php $c=['draft'=>'gray','approved'=>'sky','completed'=>'emerald','cancelled'=>'red']; @endphp<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-{{ $c[$return->status] ?? 'gray' }}-50 text-{{ $c[$return->status] ?? 'gray' }}-700 border border-{{ $c[$return->status] ?? 'gray' }}-100">{{ ucfirst($return->status) }}</span></td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $return->return_date->format('d M Y') }}</td>
            <td class="px-5 py-3"><form method="POST" action="{{ route('admin.purchase-returns.destroy', $return) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-red-500 hover:text-red-700 text-xs">Delete</button></form></td>
        </tr>@empty<tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No returns found</td></tr>@endforelse</tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $returns->links() }}</div>
</div>
@endsection
