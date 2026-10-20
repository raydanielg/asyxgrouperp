@extends('layouts.admin')
@section('title', 'Inventory Audit')
@section('page_title', 'Inventory Integrity Audit')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Negative Stock Balances</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $negative->count() ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $negative->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($negative as $row)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-800">{{ $row->product?->name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $row->warehouse?->name }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono text-red-600">{{ number_format($row->quantity) }}</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">No negative balances.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Received GRNs Missing Stock Movement</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $missingMovement->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $missingMovement->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($missingMovement as $grn)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $grn->grn_number }}</td>
                    <td class="px-5 py-3 text-xs text-amber-600">Warehouse posting required, no movement found</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">All received GRNs have movement records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Duplicate Movement References</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $duplicates->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $duplicates->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($duplicates as $dup)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-600">{{ $dup->reference }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono">{{ $dup->cnt }} rows</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">No duplicate references.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Completed Transfers — Missing Movement History</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $transferMissing->count() ? 'bg-gray-100 text-gray-500' : 'bg-emerald-50 text-emerald-600' }}">{{ $transferMissing->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($transferMissing as $t)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-600">Transfer #{{ $t->id }} — {{ $t->product_name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">Legacy record — no stock history exists (pre-integration)</td>
                </tr>
                @empty
                <tr><td class="px-5 py-8 text-center text-xs text-gray-400">All product-linked transfers have movements.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">
            <p class="text-[10px] text-gray-400">Missing-history records are distinguished from verified discrepancies — legacy data predates stock integration and cannot be reconciled automatically.</p>
            <p class="text-xs text-gray-600 mt-2">Unmapped LPO/GRN items awaiting reconciliation: <a href="{{ route('admin.inventory.reconciliation') }}" class="font-medium text-gray-900">{{ $unmappedCount }}</a></p>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-800">Stock Snapshot — On-hand / Reserved / Available</h3></div>
        <table class="w-full">
            <thead><tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
                <th class="px-5 py-2">Product</th><th class="px-5 py-2">Warehouse</th>
                <th class="px-5 py-2 text-right">On-hand</th><th class="px-5 py-2 text-right">Reserved</th><th class="px-5 py-2 text-right">Available</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($stockSnapshot as $row)
                <tr>
                    <td class="px-5 py-2.5 text-xs text-gray-800">{{ $row['product'] }}</td>
                    <td class="px-5 py-2.5 text-xs text-gray-500">{{ $row['warehouse'] }}</td>
                    <td class="px-5 py-2.5 text-xs text-right font-mono">{{ number_format($row['on_hand']) }}</td>
                    <td class="px-5 py-2.5 text-xs text-right font-mono text-amber-600">{{ number_format($row['reserved']) }}</td>
                    <td class="px-5 py-2.5 text-xs text-right font-mono {{ $row['available'] < 0 ? 'text-red-600 font-bold' : 'text-emerald-600' }}">{{ number_format($row['available']) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-6 text-center text-xs text-gray-400">No stock records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Transfers — Quantity Inconsistencies</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $inconsistent->count() ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $inconsistent->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($inconsistent as $t)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-600">Transfer #{{ $t->id }} — {{ $t->product_name }}</td>
                    <td class="px-5 py-3 text-xs text-red-600">Transferred {{ $t->transferred_quantity }} exceeds requested {{ $t->quantity }}</td>
                </tr>
                @empty
                <tr><td class="px-5 py-6 text-center text-xs text-gray-400">No inconsistencies detected.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Reservation Records — Attribution Issues</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $unattributed->count() + $terminalLinked->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $unattributed->count() + $terminalLinked->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @foreach($unattributed as $r)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-800">{{ $r->product?->name }} — {{ $r->warehouse?->name }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono">{{ number_format($r->quantity - $r->consumed_quantity) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">Active reservation with no source record — legacy/unattributed</td>
                </tr>
                @endforeach
                @foreach($terminalLinked as $r)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-800">{{ $r->product?->name }} — Transfer #{{ $r->transfer_id }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono">{{ number_format($r->quantity - $r->consumed_quantity) }}</td>
                    <td class="px-5 py-3 text-xs text-amber-600">Source is {{ $r->transfer?->status }} — reservation still marked active</td>
                </tr>
                @endforeach
                @if($unattributed->isEmpty() && $terminalLinked->isEmpty())
                <tr><td colspan="3" class="px-5 py-6 text-center text-xs text-gray-400">All reservation records are correctly attributed.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">POS Sales Missing Stock Movement</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $posMissing->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $posMissing->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($posMissing as $s)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $s->sale_number }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">Legacy sale — predates warehouse posting or item was unmapped</td>
                </tr>
                @empty
                <tr><td class="px-5 py-6 text-center text-xs text-gray-400">All POS sales have movement records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Transfers — Company Issues</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $transfersNoCompany->count() + $transfersMismatch->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $transfersNoCompany->count() + $transfersMismatch->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @foreach($transfersNoCompany as $t)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-800">Transfer #{{ $t->id }} — {{ $t->product_name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">No company — warehouses: {{ $t->fromWarehouse?->name ?? '?' }} / {{ $t->toWarehouse?->name ?? '?' }}</td>
                    <td class="px-5 py-3 text-right">
                        @if($t->fromWarehouse?->company_id && $t->fromWarehouse?->company_id === $t->toWarehouse?->company_id)
                        <form method="POST" action="{{ route('admin.inventory.reconcile-transfer') }}" class="inline" onsubmit="return confirmAction(this, {title:'Reconcile company?', text:'Assign consistent warehouse company.', confirmText:'Reconcile'})">
                            @csrf
                            <input type="hidden" name="transfer_id" value="{{ $t->id }}">
                            <input type="hidden" name="reason" value="Consistent warehouse company — reconciled">
                            <button class="text-xs font-medium text-emerald-700 hover:text-emerald-900">Reconcile</button>
                        </form>
                        @else
                        <span class="text-[9px] text-red-500">Ambiguous — manual review</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                @foreach($transfersMismatch as $t)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-800">Transfer #{{ $t->id }} — {{ $t->product_name }}</td>
                    <td class="px-5 py-3 text-xs text-red-500">company_id ≠ from-warehouse company — possible mis-assignment, manual review</td>
                    <td class="px-5 py-3"></td>
                </tr>
                @endforeach
                @if($transfersNoCompany->isEmpty() && $transfersMismatch->isEmpty())
                <tr><td colspan="3" class="px-5 py-6 text-center text-xs text-gray-400">All transfers have consistent company ownership.</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Damaged Returns (Non-Stock Disposition)</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-500">{{ $damagedReturns->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($damagedReturns as $d)
                <tr>
                    <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $d->posReturn?->return_number }}</td>
                    <td class="px-5 py-3 text-xs text-gray-800">{{ $d->saleItem?->product_name }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono">{{ number_format($d->quantity) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">Not restocked — no quarantine schema; retained for write-off review</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-xs text-gray-400">No damaged returns.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-md border border-gray-200 lg:col-span-2">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Potentially Stale Reservations</h3>
            <span class="text-[10px] px-1.5 py-0.5 rounded {{ $staleReservations->count() ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600' }}">{{ $staleReservations->count() }}</span>
        </div>
        <table class="w-full">
            <tbody class="divide-y divide-gray-50">
                @forelse($staleReservations as $s)
                <tr>
                    <td class="px-5 py-3 text-xs text-gray-800">{{ $s->product?->name }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $s->warehouse?->name }}</td>
                    <td class="px-5 py-3 text-xs text-right font-mono text-amber-600">{{ number_format($s->reserved_quantity) }} reserved</td>
                    <td class="px-5 py-3 text-xs text-gray-400">No open transfer claims this reservation — manual review required</td>
                    <td class="px-5 py-3 text-right">
                        <form method="POST" action="{{ route('admin.inventory.release') }}" class="inline" onsubmit="return confirmAction(this, {title:'Release reservation?', text:'{{ number_format($s->reserved_quantity) }} units will return to available stock.', confirmText:'Release'})">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $s->product_id }}">
                            <input type="hidden" name="warehouse_id" value="{{ $s->warehouse_id }}">
                            <input type="hidden" name="reason" value="Stale reservation release — no open transfer claims it">
                            <button class="text-xs font-medium text-red-600 hover:text-red-800">Release</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-6 text-center text-xs text-gray-400">All reservations are claimed by open transfers.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">
            <p class="text-[10px] text-gray-400">No reservation expiry policy is configured — stale reservations are preserved until consumed or explicitly released. Corrections require an authorized manual release with an audit trail.</p>
        </div>
    </div>

</div>
@endsection
