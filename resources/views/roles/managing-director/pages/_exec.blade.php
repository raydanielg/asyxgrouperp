@extends('layouts.admin')
@section('title', ucwords(str_replace('-', ' ', $module)) . ' — ' . $roleLabel)
@section('page_title', ucwords(str_replace('-', ' ', $module)))
@section('content')
@php
$rows = collect(method_exists($items ?? null, 'items') ? $items->items() : ($items ?? []));
$title = ucwords(str_replace('-', ' ', $module));
$moneyFmt = fn($n) => 'TZS ' . number_format((float) $n);

// Smart column detection from the first row
$cols = [];
if ($rows->isNotEmpty()) {
    $first = $rows->first();
    $attrs = is_array($first) ? $first : ($first->getAttributes() ?? []);
    $nameKeys = ['invoice_number','sale_number','return_number','transfer_number','tender_number','contract_number','deal_name','lead_name','employee_name','product_name','name','title','subject','description','reference'];
    $amountKeys = ['total_amount','paid_amount','amount','total','value','price','salary','balance','quantity'];
    foreach ($nameKeys as $k) if (isset($attrs[$k])) { $cols[] = ['key' => $k, 'type' => 'name']; break; }
    foreach ($attrs as $k => $v) {
        if (in_array($k, ['status','state','type','category','branch_id','warehouse_id','company_id'])) {
            if (count($cols) < 4) $cols[] = ['key' => $k, 'type' => 'meta'];
        }
    }
    foreach ($amountKeys as $k) if (isset($attrs[$k])) { $cols[] = ['key' => $k, 'type' => 'amount']; break; }
    $cols[] = ['key' => 'created_at', 'type' => 'date'];
    $cols = array_slice($cols, 0, 5);
}

$statusDot = fn($s) => match(strtolower((string) $s)) {
    'active','completed','paid','approved','processed','posted','confirmed','open','in_progress' => '#10b981',
    'pending','draft','awaiting_reconciliation','submitted','partially_transferred' => '#f59e0b',
    'cancelled','rejected','failed','overdue','inactive','closed' => '#ef4444',
    default => '#94a3b8',
};
@endphp

{{-- Header --}}
<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-bold text-gray-900">{{ $title }}</h2>
        <p class="text-xs text-gray-400 mt-0.5">{{ $rows->count() }} {{ Str::plural('record', $rows->count()) }}{{ method_exists($items ?? null, 'total') ? ' of ' . $items->total() : '' }}</p>
    </div>
    <a href="{{ route('dashboard') }}" class="text-[10px] text-slate-600 hover:text-slate-800">← Back to dashboard</a>
</div>

{{-- KPI strip (if there is data worth summarising) --}}
@php
$statusCounts = $rows->groupBy(fn($r) => strtolower((string) (is_array($r) ? ($r['status'] ?? '') : ($r->status ?? ''))))->filter()->map->count()->sortDesc()->take(4);
$moneyFields = ['total_amount','paid_amount','amount','value','total'];
$moneySum = collect($moneyFields)->first(fn($k) => $rows->isNotEmpty() && isset((is_array($rows->first()) ? $rows->first() : $rows->first()->getAttributes())[$k]));
@endphp
@if($statusCounts->isNotEmpty() || $moneySum)
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    @if($moneySum)
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total {{ ucwords(str_replace('_', ' ', $moneySum)) }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $moneyFmt($rows->sum($moneySum)) }}</p>
    </div>
    @endif
    @foreach($statusCounts as $st => $n)
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">{{ ucfirst(str_replace('_',' ',$st)) }}</span>
        <p class="text-lg font-bold text-gray-900 mt-1">{{ $n }}</p>
    </div>
    @endforeach
</div>
@endif

{{-- Records table --}}
<div class="bg-white rounded-md border border-gray-200">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">{{ $title }}</h3>
    </div>
    <table class="w-full">
        <tbody class="divide-y divide-gray-50">
            @forelse($rows as $row)
            <tr>
                @foreach($cols as $col)
                @php
                    $val = is_array($row) ? ($row[$col['key']] ?? null) : ($row->{$col['key']} ?? null);
                @endphp
                @if($col['type'] === 'name')
                <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $val ?? '—' }}</td>
                @elseif($col['type'] === 'amount')
                <td class="px-5 py-3 text-xs text-right font-mono text-gray-800">{{ $moneyFmt($val ?? 0) }}</td>
                @elseif($col['type'] === 'date')
                <td class="px-5 py-3 text-xs text-gray-400">{{ $val ? \Carbon\Carbon::parse($val)->format('d M Y') : '—' }}</td>
                @else
                <td class="px-5 py-3">
                    @if(in_array($col['key'], ['status','state']))
                    <span class="inline-flex items-center gap-1.5 text-xs text-gray-700"><span style="width:6px;height:6px;border-radius:99px;background:{{ $statusDot($val) }}"></span>{{ ucfirst(str_replace('_', ' ', (string) $val)) }}</span>
                    @else
                    <span class="text-xs text-gray-500">{{ is_scalar($val) ? Str::limit((string) $val, 30) : '—' }}</span>
                    @endif
                </td>
                @endif
                @endforeach
            </tr>
            @empty
            <tr><td class="px-5 py-10 text-center text-xs text-gray-400">No {{ strtolower($title) }} records found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if(method_exists($items ?? null, 'links'))
    <div class="px-5 py-3 border-t border-gray-50">{{ $items->links() }}</div>
    @endif
</div>
@endsection
