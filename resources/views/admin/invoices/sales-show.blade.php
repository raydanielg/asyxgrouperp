@extends('layouts.admin')
@section('title', 'Invoice ' . $salesInvoice->invoice_number)
@section('page_title', '')
@section('content')

<div class="max-w-[760px] mx-auto">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4 max-w-[760px] no-print">
        <div class="text-xs" style="color:#6E7570;">
            Invoice <b style="color:#1C2321;">{{ $salesInvoice->invoice_number }}</b> &middot; {{ config('app.name') }}
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2.5 text-xs font-bold rounded-lg transition-all flex items-center gap-2" style="background:#C9A227;color:#23270F;" onmouseover="this.style.background='#B8941F'" onmouseout="this.style.background='#C9A227'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </button>
            <a href="{{ route('admin.sales-invoices.index') }}" class="px-4 py-2.5 text-xs font-bold rounded-lg border transition-all" style="border-color:#E3DDCB;color:#6E7570;" onmouseover="this.style.background='#FBF9F2'" onmouseout="this.style.background='transparent'">Back</a>
        </div>
    </div>

    {{-- ═══ INVOICE A4 ═══ --}}
    <div id="invoice-a4" style="background:#fff;border-radius:6px;box-shadow:0 18px 40px -10px rgba(15,61,62,.25),0 0 0 1px #E3DDCB;overflow:hidden;position:relative;font-family:'Inter',sans-serif;">

        {{-- Stamp --}}
        @php
            $stampColors = ['paid'=>'#2F7A3D','partial'=>'#C9A227','posted'=>'#0F3D3E','draft'=>'#6E7570','overdue'=>'#B23A2E'];
            $stampColor = $stampColors[$salesInvoice->status] ?? '#6E7570';
        @endphp
        <div style="position:absolute;top:26px;right:-52px;background:{{ $stampColor }};color:#fff;font-size:12px;font-weight:700;letter-spacing:.12em;padding:6px 68px;transform:rotate(35deg);box-shadow:0 4px 10px rgba(0,0,0,.15);z-index:10;">{{ strtoupper($salesInvoice->status) }}</div>

        {{-- Head --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:38px 44px 26px;border-bottom:1px solid #E3DDCB;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;border-radius:10px;background:conic-gradient(from 90deg,#C9A227,#8C5E2A,#0F3D3E,#C9A227);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" style="width:30px;height:30px;object-fit:contain;border-radius:4px;">
                </div>
                <div>
                    <div style="font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;">{{ config('app.name') }}</div>
                    <div style="font-size:11.5px;color:#6E7570;margin-top:3px;line-height:1.5;">
                        {{ $salesInvoice->company?->name ?? 'ASYX Group' }}<br>
                        Dar es Salaam, Tanzania
                    </div>
                </div>
            </div>
            <div style="text-align:right;">
                <h1 style="font-family:'Fraunces',serif;font-size:24px;margin:0 0 8px;color:#1C2321;">Invoice {{ $salesInvoice->invoice_number }}</h1>
                <div style="font-size:12px;color:#6E7570;line-height:1.6;">
                    Invoice Date: <b style="color:#1C2321;">{{ $salesInvoice->invoice_date->format('M d, Y') }}</b><br>
                    Due Date: <b style="color:#1C2321;">{{ $salesInvoice->due_date->format('M d, Y') }}</b>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div style="padding:30px 44px;">

            {{-- Customer --}}
            <div style="display:flex;justify-content:space-between;margin-bottom:28px;">
                <div>
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;">Invoiced To</div>
                    <b style="display:block;font-size:14.5px;color:#1C2321;">{{ $salesInvoice->customer?->name ?? 'N/A' }}</b>
                    <div style="font-size:12.5px;color:#6E7570;line-height:1.6;margin-top:2px;">
                        {{ $salesInvoice->customer?->email ?? '' }}<br>
                        {{ $salesInvoice->customer?->phone ?? '' }}
                    </div>
                </div>
                @if($salesInvoice->warehouse)
                <div style="text-align:right;">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;">Warehouse</div>
                    <b style="display:block;font-size:14.5px;color:#1C2321;">{{ $salesInvoice->warehouse->name }}</b>
                </div>
                @endif
            </div>

            {{-- Items Table --}}
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <tr>
                    <th style="text-align:left;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Description</th>
                    <th style="text-align:center;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Qty</th>
                    <th style="text-align:right;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Unit Price</th>
                    <th style="text-align:right;font-size:10.5px;text-transform:uppercase;letter-spacing:.08em;color:#6E7570;border-bottom:1.5px solid #1C2321;padding:0 4px 10px;">Total</th>
                </tr>
                @forelse($salesInvoice->items as $item)
                <tr>
                    <td style="padding:14px 4px;border-bottom:1px solid #E3DDCB;color:#1C2321;">{{ $item->product_name }}</td>
                    <td style="padding:14px 4px;border-bottom:1px solid #E3DDCB;text-align:center;color:#1C2321;">{{ $item->quantity }}</td>
                    <td style="padding:14px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#1C2321;">{{ number_format($item->unit_price, 2) }} Tsh</td>
                    <td style="padding:14px 4px;border-bottom:1px solid #E3DDCB;text-align:right;color:#1C2321;font-weight:600;">{{ number_format($item->total_amount, 2) }} Tsh</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:24px 4px;text-align:center;color:#6E7570;font-size:12px;">No items</td>
                </tr>
                @endforelse
            </table>

            {{-- Totals --}}
            <div style="margin-left:auto;width:280px;margin-top:14px;font-size:13.5px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #E3DDCB;color:#6E7570;">
                    <span>Sub Total</span>
                    <b style="color:#1C2321;font-weight:600;">{{ number_format($salesInvoice->subtotal, 2) }} Tsh</b>
                </div>
                @if($salesInvoice->tax_amount > 0)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #E3DDCB;color:#6E7570;">
                    <span>18.00% VAT</span>
                    <b style="color:#1C2321;font-weight:600;">{{ number_format($salesInvoice->tax_amount, 2) }} Tsh</b>
                </div>
                @endif
                @if($salesInvoice->discount_amount > 0)
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #E3DDCB;color:#6E7570;">
                    <span>Discount</span>
                    <b style="color:#B23A2E;font-weight:600;">&minus;{{ number_format($salesInvoice->discount_amount, 2) }} Tsh</b>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;padding:14px 0 8px;border-bottom:none;color:#0F3D3E;font-weight:700;font-size:17px;">
                    <span>Total</span>
                    <b style="color:#0F3D3E;">{{ number_format($salesInvoice->total_amount, 2) }} Tsh</b>
                </div>
            </div>

            {{-- Balance Bar --}}
            @if($salesInvoice->status != 'paid')
            <div style="margin-top:24px;padding:16px 20px;border-radius:10px;background:#FBE7E2;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:#B23A2E;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Balance Due</span>
                <b style="font-family:'JetBrains Mono',monospace;font-size:17px;color:#B23A2E;">{{ number_format($salesInvoice->balance_amount, 2) }} Tsh</b>
            </div>
            @else
            <div style="margin-top:24px;padding:16px 20px;border-radius:10px;background:#E2F0E5;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:#2F7A3D;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Paid in Full</span>
                <b style="font-family:'JetBrains Mono',monospace;font-size:17px;color:#2F7A3D;">{{ number_format($salesInvoice->paid_amount, 2) }} Tsh</b>
            </div>
            @endif

            {{-- Notes --}}
            @if($salesInvoice->notes)
            <div style="margin-top:20px;padding:14px 16px;background:#FBF9F2;border-radius:8px;border:1px solid #E3DDCB;">
                <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:4px;">Notes</div>
                <p style="font-size:12.5px;color:#1C2321;margin:0;line-height:1.5;">{{ $salesInvoice->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div style="padding:18px 44px;border-top:1px solid #E3DDCB;background:#FBF9F2;font-size:11px;color:#6E7570;text-align:center;">
            PDF Generated on {{ now()->format('l, F jS, Y') }} &middot; {{ config('app.name') }}
        </div>
    </div>
</div>

<style>
#invoice-a4 { font-family: 'Inter','Nunito',system-ui,sans-serif; }
#invoice-a4 h1 { font-family: 'Fraunces','Georgia',serif; }
@media print {
    @page { margin: 0; size: A4; }
    body { background: #fff !important; padding: 0 !important; }
    body * { visibility: hidden; }
    #invoice-a4, #invoice-a4 * { visibility: visible; }
    #invoice-a4 { position: absolute; left: 0; top: 0; width: 210mm; min-height: 297mm; box-shadow: none !important; border-radius: 0 !important; }
    .no-print { display: none !important; }
    nav, header, .sidebar, .no-print { display: none !important; }
}
</style>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
@endpush
@endsection
