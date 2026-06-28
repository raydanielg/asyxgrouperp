@extends('layouts.admin')
@section('title', 'Stock Movements - ' . config('app.name'))
@section('page_title', 'Stock Movements')
@section('content')
<div class="mb-4">
    <p class="text-sm text-gray-500">Track all inventory stock movements</p>
</div>
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto"><table class="w-full text-sm">
        <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50"><th class="px-5 py-3 font-medium">Product</th><th class="px-5 py-3 font-medium">Warehouse</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Quantity</th><th class="px-5 py-3 font-medium">Balance After</th><th class="px-5 py-3 font-medium">Reference</th><th class="px-5 py-3 font-medium">Date</th></tr></thead>
        <tbody>@forelse($movements as $m)<tr class="border-t border-gray-100 hover:bg-gray-50/50">
            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $m->product?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ $m->warehouse?->name ?? 'N/A' }}</td>
            <td class="px-5 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium @if($m->type=='in')bg-emerald-50 text-emerald-700 @elseif($m->type=='out')bg-red-50 text-red-700 @else bg-amber-50 text-amber-700 @endif">{{ ucfirst($m->type) }}</span></td>
            <td class="px-5 py-3 text-xs font-semibold @if($m->type=='in')text-emerald-700@elseif($m->type=='out')text-red-600@else text-amber-700 @endif">{{ $m->type == 'in' ? '+' : ($m->type == 'out' ? '-' : '') }}{{ $m->quantity }}</td>
            <td class="px-5 py-3 text-xs text-gray-700">{{ $m->balance_after }}</td>
            <td class="px-5 py-3 text-xs text-gray-400 max-w-xs truncate">{{ $m->reference ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-400">{{ $m->created_at->format('d M Y H:i') }}</td>
        
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No stock movements recorded</td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="px-5 py-4 border-t">{{ $movements->links() }}</div>
</div>
@endsection
