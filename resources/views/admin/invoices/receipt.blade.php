@extends('layouts.admin')
@section('title', 'Receipt ' . $receipt['receipt_number'])
@section('page_title', '')
@section('content')

<div class="mx-auto" style="max-width:760px;">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4 no-print">
        <div class="text-xs" style="color:#6E7570;">
            Receipt <b style="color:#1C2321;">{{ $receipt['receipt_number'] }}</b> &middot; {{ config('app.name') }}
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales-invoices.receipt-pdf', $salesInvoice) }}" target="_blank" class="px-4 py-2.5 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2" style="background:#C9A227;color:#23270F;" onmouseover="this.style.background='#B8941F'" onmouseout="this.style.background='#C9A227'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
            <a href="{{ route('admin.sales-invoices.show', $salesInvoice) }}" class="px-4 py-2.5 text-xs font-bold rounded-lg border transition-all" style="border-color:#E3DDCB;color:#6E7570;" onmouseover="this.style.background='#FBF9F2'" onmouseout="this.style.background='transparent'">Back to Invoice</a>
        </div>
    </div>

    {{-- View Toggle --}}
    <div class="flex gap-2 mb-4 no-print">
        <button onclick="showView('compact')" id="btn-compact" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all" style="background:#0F3D3E;color:#fff;">Compact</button>
        <button onclick="showView('full')" id="btn-full" class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all" style="background:#E3DDCB;color:#6E7570;" onmouseover="this.style.background='#C9A227'" onmouseout="this.style.background='#E3DDCB'">Full Receipt</button>
    </div>

    {{-- ═══ COMPACT RECEIPT (narrow, ticket-style) ═══ --}}
    <div id="receipt-compact" style="display:block;">
        <div style="max-width:520px;margin:0 auto;background:#fff;border-radius:6px;box-shadow:0 18px 40px -10px rgba(15,61,62,.25),0 0 0 1px #E3DDCB;overflow:hidden;position:relative;font-family:'Inter',sans-serif;">
            <div style="position:absolute;top:22px;right:-44px;background:#2F7A3D;color:#fff;font-size:11.5px;font-weight:700;letter-spacing:.12em;padding:5px 58px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;">PAID</div>

            <div style="text-align:center;padding:34px 36px 22px;border-bottom:1px dashed #E3DDCB;">
                <div style="width:40px;height:40px;border-radius:11px;margin:0 auto 12px;background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);overflow:hidden;display:flex;align-items:center;justify-content:center;">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" style="width:32px;height:32px;object-fit:contain;border-radius:6px;">
                </div>
                <div style="font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;">{{ config('app.name') }}</div>
                <div style="font-size:11px;color:#6E7570;margin-top:4px;line-height:1.5;">
                    {{ $salesInvoice->company?->name ?? 'ASYX Group' }}<br>
                    Dar es Salaam, Tanzania
                </div>
            </div>

            <div style="text-align:center;padding:22px 36px 6px;">
                <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.14em;color:#6E7570;">Kiasi Kilicholipwa</div>
                <div style="font-family:'Fraunces',serif;font-size:34px;color:#2F7A3D;margin:6px 0 2px;">{{ number_format($receipt['paid_amount'], 0) }} Tsh</div>
                <div style="font-size:12px;color:#6E7570;">Imelipwa kikamilifu &mdash; {{ $receipt['payment_date'] }}</div>
            </div>

            <div style="padding:24px 36px 6px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:9px 0;border-bottom:1px dashed #E3DDCB;">
                    <span style="color:#6E7570;">Receipt No.</span>
                    <b style="color:#1C2321;font-weight:600;font-family:'JetBrains Mono',monospace;font-size:12px;">{{ $receipt['receipt_number'] }}</b>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:9px 0;border-bottom:1px dashed #E3DDCB;">
                    <span style="color:#6E7570;">Invoice Ref.</span>
                    <b style="color:#1C2321;font-weight:600;font-family:'JetBrains Mono',monospace;font-size:12px;">{{ $salesInvoice->invoice_number }}</b>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:9px 0;border-bottom:1px dashed #E3DDCB;">
                    <span style="color:#6E7570;">Imelipwa na</span>
                    <b style="color:#1C2321;font-weight:600;">{{ $salesInvoice->customer?->name ?? 'N/A' }}</b>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:9px 0;border-bottom:1px dashed #E3DDCB;">
                    <span style="color:#6E7570;">Njia ya Malipo</span>
                    <b style="color:#1C2321;font-weight:600;">{{ $receipt['payments'][0]['method'] ?? 'Bank Transfer' }}</b>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:9px 0;border-bottom:1px dashed #E3DDCB;">
                    <span style="color:#6E7570;">Transaction ID</span>
                    <b style="color:#1C2321;font-weight:600;font-family:'JetBrains Mono',monospace;font-size:12px;">{{ $receipt['payments'][0]['transaction_id'] ?? 'N/A' }}</b>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:9px 0;border-bottom:1px dashed #E3DDCB;">
                    <span style="color:#6E7570;">Tarehe ya Malipo</span>
                    <b style="color:#1C2321;font-weight:600;">{{ $receipt['payment_date'] }}, {{ $receipt['payment_time'] }}</b>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:9px 0;">
                    <span style="color:#6E7570;">Maelezo</span>
                    <b style="color:#1C2321;font-weight:600;text-align:right;">{{ $salesInvoice->items->first()?->product_name ?? 'Invoice Payment' }}</b>
                </div>
            </div>

            <div style="height:14px;width:100%;background:linear-gradient(135deg,#F2F1ED 25%,transparent 25%) 0 0/10px 10px,linear-gradient(225deg,#F2F1ED 25%,transparent 25%) 0 0/10px 10px,#fff;margin-top:6px;"></div>

            <div style="padding:20px 36px 30px;text-align:center;background:#FBF9F2;">
                <div style="font-family:'Fraunces',serif;font-size:14px;color:#0F3D3E;margin-bottom:6px;">Asante kwa malipo yako</div>
                <div style="font-size:11px;color:#6E7570;line-height:1.6;">
                    Risiti hii ni uthibitisho rasmi wa malipo.<br>
                    Iwapo una swali lolote, wasiliana nasi: billing@asyxgroup.tz
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ FULL RECEIPT (detailed, with payment history) ═══ --}}
    <div id="receipt-full" style="display:none;">
        <div style="background:#fff;border-radius:4px;box-shadow:0 18px 40px -10px rgba(15,61,62,.18),0 0 0 1px #E5E7EA;padding:48px 52px 40px;font-family:'Inter',sans-serif;">

            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:34px;">
                <h1 style="font-size:26px;font-weight:800;margin:0;letter-spacing:-.01em;color:#17181A;">Receipt</h1>
                <div style="display:flex;align-items:center;gap:9px;">
                    <div style="width:26px;height:26px;border-radius:7px;background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);overflow:hidden;display:flex;align-items:center;justify-content:center;">
                        <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" style="width:20px;height:20px;object-fit:contain;border-radius:4px;">
                    </div>
                    <div style="font-size:18px;font-weight:700;letter-spacing:-.01em;color:#0F3D3E;">Asyx</div>
                </div>
            </div>

            <div style="font-size:13px;margin-bottom:30px;">
                <div style="display:flex;gap:8px;padding:2px 0;">
                    <div style="width:140px;color:#17181A;">Invoice number</div>
                    <div style="color:#17181A;">{{ $salesInvoice->invoice_number }}</div>
                </div>
                <div style="display:flex;gap:8px;padding:2px 0;">
                    <div style="width:140px;color:#17181A;">Receipt number</div>
                    <div style="color:#17181A;">{{ $receipt['receipt_number'] }}</div>
                </div>
                <div style="display:flex;gap:8px;padding:2px 0;">
                    <div style="width:140px;color:#17181A;">Date paid</div>
                    <div style="color:#17181A;">{{ $receipt['payment_date'] }}</div>
                </div>
            </div>

            <div style="display:flex;gap:60px;margin-bottom:34px;">
                <div>
                    <b style="display:block;font-size:13px;margin-bottom:6px;color:#17181A;">{{ config('app.name') }}</b>
                    <div style="font-size:13px;color:#17181A;line-height:1.6;">
                        {{ $salesInvoice->company?->name ?? 'ASYX Group' }}<br>
                        Dar es Salaam, Tanzania<br>
                        billing@asyxgroup.tz
                    </div>
                </div>
                <div>
                    <b style="display:block;font-size:13px;margin-bottom:6px;color:#17181A;">Bill to</b>
                    <div style="font-size:13px;color:#17181A;line-height:1.6;">
                        {{ $salesInvoice->customer?->name ?? 'N/A' }}<br>
                        {{ $salesInvoice->customer?->email ?? '' }}<br>
                        {{ $salesInvoice->customer?->phone ?? '' }}
                    </div>
                </div>
            </div>

            <div style="font-size:17px;font-weight:700;margin-bottom:18px;color:#17181A;">{{ number_format($receipt['paid_amount'], 2) }} Tsh paid on {{ $receipt['payment_date'] }}</div>

            <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:4px;">
                <thead>
                    <tr>
                        <th style="text-align:left;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Description</th>
                        <th style="text-align:right;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Qty</th>
                        <th style="text-align:right;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Unit price</th>
                        <th style="text-align:right;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesInvoice->items as $item)
                    <tr>
                        <td style="padding:14px 4px;border-bottom:1px solid #E5E7EA;vertical-align:top;color:#17181A;">{{ $item->product_name }}</td>
                        <td style="padding:14px 4px;border-bottom:1px solid #E5E7EA;vertical-align:top;text-align:right;color:#17181A;">{{ $item->quantity }}</td>
                        <td style="padding:14px 4px;border-bottom:1px solid #E5E7EA;vertical-align:top;text-align:right;color:#17181A;">{{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding:14px 4px;border-bottom:1px solid #E5E7EA;vertical-align:top;text-align:right;color:#17181A;">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:24px;text-align:center;color:#6B7177;">No items</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-left:auto;width:260px;margin-top:8px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 4px;border-bottom:1px solid #E5E7EA;">
                    <span style="color:#6B7177;">Subtotal</span>
                    <span style="color:#17181A;">{{ number_format($salesInvoice->subtotal, 2) }} Tsh</span>
                </div>
                @if($salesInvoice->tax_amount > 0)
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 4px;border-bottom:1px solid #E5E7EA;">
                    <span style="color:#6B7177;">VAT (18%)</span>
                    <span style="color:#17181A;">{{ number_format($salesInvoice->tax_amount, 2) }} Tsh</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 4px;border-bottom:1px solid #E5E7EA;">
                    <span style="color:#6B7177;">Total</span>
                    <span style="color:#17181A;">{{ number_format($salesInvoice->total_amount, 2) }} Tsh</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 4px;font-weight:700;border-bottom:none;">
                    <span style="color:#17181A;">Amount paid</span>
                    <span style="color:#0F3D3E;">{{ number_format($receipt['paid_amount'], 2) }} Tsh</span>
                </div>
            </div>

            <div style="font-size:15px;font-weight:700;margin:36px 0 14px;color:#17181A;">Payment history</div>
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr>
                        <th style="text-align:left;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Payment method</th>
                        <th style="text-align:left;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Date</th>
                        <th style="text-align:right;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Amount paid</th>
                        <th style="text-align:right;font-weight:600;color:#17181A;border-bottom:1.5px solid #17181A;padding:0 4px 10px;">Receipt number</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipt['payments'] as $pmt)
                    <tr>
                        <td style="padding:13px 4px;color:#17181A;">{{ $pmt['method'] }}</td>
                        <td style="padding:13px 4px;color:#17181A;">{{ $pmt['date'] }}</td>
                        <td style="padding:13px 4px;text-align:right;color:#17181A;">{{ number_format($pmt['amount'], 2) }} Tsh</td>
                        <td style="padding:13px 4px;text-align:right;color:#17181A;font-family:'JetBrains Mono',monospace;font-size:12px;">{{ $pmt['reference'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="padding:24px;text-align:center;color:#6B7177;">No payment records</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:40px;padding-top:14px;border-top:1px solid #E5E7EA;font-size:11.5px;color:#6B7177;text-align:right;">Page 1 of 1</div>
        </div>
    </div>
</div>

<style>
@media print {
    @page { margin: 0; size: A4; }
    body { background: #fff !important; padding: 0 !important; }
    body * { visibility: hidden; }
    #receipt-compact, #receipt-compact *,
    #receipt-full, #receipt-full * { visibility: visible; }
    #receipt-compact, #receipt-full { position: absolute; left: 0; top: 0; width: 210mm; min-height: 297mm; box-shadow: none !important; border-radius: 0 !important; }
    .no-print { display: none !important; }
    nav, header, .sidebar, .no-print { display: none !important; }
}
</style>

@push('scripts')
<script>
function showView(type) {
    var c = document.getElementById('receipt-compact');
    var f = document.getElementById('receipt-full');
    var bc = document.getElementById('btn-compact');
    var bf = document.getElementById('btn-full');
    if (type === 'compact') {
        c.style.display = 'block';
        f.style.display = 'none';
        bc.style.background = '#0F3D3E';
        bc.style.color = '#fff';
        bf.style.background = '#E3DDCB';
        bf.style.color = '#6E7570';
    } else {
        c.style.display = 'none';
        f.style.display = 'block';
        bf.style.background = '#0F3D3E';
        bf.style.color = '#fff';
        bc.style.background = '#E3DDCB';
        bc.style.color = '#6E7570';
    }
}
</script>
@endpush

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
@endpush
@endsection
