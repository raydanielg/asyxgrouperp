@extends('layouts.admin')
@section('title', 'Invoice ' . $salesInvoice->invoice_number)
@section('page_title', '')
@section('content')

<div class="max-w-[800px] mx-auto">
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-4 no-print">
        <div class="flex items-center gap-2 text-xs" style="color:#6E7570;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0F3D3E;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Invoice <b style="color:#1C2321;">{{ $salesInvoice->invoice_number }}</b> &middot; {{ config('app.name') }}
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales-invoices.receipt', $salesInvoice) }}" class="px-4 py-2.5 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2" style="background:#0F3D3E;color:#fff;" onmouseover="this.style.background='#0A2D2E'" onmouseout="this.style.background='#0F3D3E'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Receipt
            </a>
            <a href="{{ route('admin.sales-invoices.pdf', $salesInvoice) }}" target="_blank" class="px-4 py-2.5 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2" style="background:#C9A227;color:#23270F;" onmouseover="this.style.background='#B8941F'" onmouseout="this.style.background='#C9A227'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                PDF
            </a>
            <button onclick="window.print()" class="px-4 py-2.5 text-xs font-bold rounded-lg transition-all inline-flex items-center gap-2" style="background:#1C2321;color:#fff;" onmouseover="this.style.background='#0F3D3E'" onmouseout="this.style.background='#1C2321'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="{{ route('admin.sales-invoices.index') }}" class="px-4 py-2.5 text-xs font-bold rounded-lg border transition-all inline-flex items-center gap-2" style="border-color:#E3DDCB;color:#6E7570;" onmouseover="this.style.background='#FBF9F2'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    {{-- ═══ INVOICE A4 DOCUMENT ═══ --}}
    <div id="invoice-a4" style="background:#fff;width:210mm;min-height:297mm;margin:0 auto;box-shadow:0 20px 50px -12px rgba(15,61,62,.22),0 0 0 1px #E3DDCB;position:relative;font-family:'Inter',sans-serif;overflow:hidden;">

        {{-- Top accent bar --}}
        <div style="height:6px;background:linear-gradient(90deg,#0F3D3E 0%,#C9A227 50%,#0F3D3E 100%);"></div>

        {{-- Status badge --}}
        @php
            $stampColors = ['paid'=>'#2F7A3D','partial'=>'#C9A227','posted'=>'#0F3D3E','draft'=>'#6E7570','overdue'=>'#B23A2E'];
            $stampColor = $stampColors[$salesInvoice->status] ?? '#6E7570';
            $stampBg = ['paid'=>'#E2F0E5','partial'=>'#FAF3D8','posted'=>'#E0EEEF','draft'=>'#F0F0EE','overdue'=>'#FBE7E2'];
            $stampBgColor = $stampBg[$salesInvoice->status] ?? '#F0F0EE';
        @endphp
        <div style="position:absolute;top:30px;right:40px;z-index:10;">
            <div style="border:2.5px solid {{ $stampColor }};color:{{ $stampColor }};font-size:11px;font-weight:800;letter-spacing:.14em;padding:6px 18px;border-radius:4px;text-transform:uppercase;background:{{ $stampBgColor }};">{{ $salesInvoice->status }}</div>
        </div>

        {{-- Header --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:36px 48px 24px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#0F3D3E,#C9A227);flex-shrink:0;overflow:hidden;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(15,61,62,.2);">
                    <img src="{{ asset('asyxgrouplogo.png') }}" alt="ASYX" style="width:34px;height:34px;object-fit:contain;border-radius:6px;">
                </div>
                <div>
                    <div style="font-family:'Fraunces',serif;font-weight:700;font-size:18px;color:#0F3D3E;letter-spacing:-.01em;">{{ config('app.name') }}</div>
                    <div style="font-size:11px;color:#6E7570;margin-top:3px;line-height:1.5;">
                        {{ $salesInvoice->company?->name ?? 'ASYX Group' }}<br>
                        Dar es Salaam, Tanzania<br>
                        <span style="color:#0F3D3E;">billing@asyxgroup.tz</span>
                    </div>
                </div>
            </div>
            <div style="text-align:right;padding-top:4px;">
                <div style="font-family:'Fraunces',serif;font-size:28px;font-weight:700;color:#0F3D3E;letter-spacing:-.02em;line-height:1;">INVOICE</div>
                <div style="font-family:'JetBrains Mono',monospace;font-size:13px;color:#1C2321;margin-top:8px;font-weight:600;">{{ $salesInvoice->invoice_number }}</div>
            </div>
        </div>

        {{-- Meta bar --}}
        <div style="display:flex;gap:24px;padding:0 48px;margin-bottom:24px;">
            <div>
                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.12em;color:#6E7570;margin-bottom:4px;">Invoice Date</div>
                <div style="font-size:13px;color:#1C2321;font-weight:600;">{{ $salesInvoice->invoice_date->format('d M Y') }}</div>
            </div>
            <div>
                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.12em;color:#6E7570;margin-bottom:4px;">Due Date</div>
                <div style="font-size:13px;color:#1C2321;font-weight:600;">{{ $salesInvoice->due_date->format('d M Y') }}</div>
            </div>
            <div>
                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.12em;color:#6E7570;margin-bottom:4px;">Type</div>
                <div style="font-size:13px;color:#1C2321;font-weight:600;">{{ ucfirst($salesInvoice->type) }}</div>
            </div>
        </div>

        {{-- Divider --}}
        <div style="height:1px;background:linear-gradient(90deg,transparent,#E3DDCB 10%,#E3DDCB 90%,transparent);margin:0 48px 24px;"></div>

        {{-- Bill To --}}
        <div style="display:flex;justify-content:space-between;padding:0 48px;margin-bottom:28px;">
            <div>
                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.12em;color:#6E7570;margin-bottom:8px;">Billed To</div>
                <div style="font-size:15px;color:#1C2321;font-weight:700;">{{ $salesInvoice->customer?->name ?? 'N/A' }}</div>
                <div style="font-size:12px;color:#6E7570;line-height:1.6;margin-top:4px;">
                    @if($salesInvoice->customer?->email){{ $salesInvoice->customer->email }}<br>@endif
                    @if($salesInvoice->customer?->phone){{ $salesInvoice->customer->phone }}@endif
                </div>
            </div>
            @if($salesInvoice->warehouse)
            <div style="text-align:right;">
                <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.12em;color:#6E7570;margin-bottom:8px;">Warehouse</div>
                <div style="font-size:14px;color:#1C2321;font-weight:700;">{{ $salesInvoice->warehouse->name }}</div>
            </div>
            @endif
        </div>

        {{-- Items Table --}}
        <div style="padding:0 48px;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#0F3D3E;">
                        <th style="text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#fff;padding:12px 16px;border-radius:8px 0 0 0;">Description</th>
                        <th style="text-align:center;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#fff;padding:12px 8px;">Qty</th>
                        <th style="text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#fff;padding:12px 16px;">Unit Price</th>
                        <th style="text-align:right;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:#fff;padding:12px 16px;border-radius:0 8px 0 0;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesInvoice->items as $item)
                    <tr style="border-bottom:1px solid #F0EFEA;">
                        <td style="padding:14px 16px;color:#1C2321;font-weight:500;">{{ $item->product_name }}</td>
                        <td style="padding:14px 8px;text-align:center;color:#6E7570;">{{ $item->quantity }}</td>
                        <td style="padding:14px 16px;text-align:right;color:#6E7570;">TZS {{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding:14px 16px;text-align:right;color:#1C2321;font-weight:600;">TZS {{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding:32px 16px;text-align:center;color:#6E7570;font-size:12px;">No items on this invoice</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totals + Payment Info --}}
        <div style="display:flex;justify-content:space-between;padding:24px 48px 0;gap:40px;">
            {{-- Left: Notes + Payment info --}}
            <div style="flex:1;">
                @if($salesInvoice->notes)
                <div style="padding:16px 18px;background:#FBF9F2;border-radius:10px;border:1px solid #E3DDCB;margin-bottom:14px;">
                    <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.12em;color:#6E7570;margin-bottom:6px;">Notes</div>
                    <p style="font-size:12px;color:#1C2321;margin:0;line-height:1.6;">{{ $salesInvoice->notes }}</p>
                </div>
                @endif
                <div style="padding:16px 18px;background:#E0EEEF;border-radius:10px;">
                    <div style="font-size:9.5px;text-transform:uppercase;letter-spacing:.12em;color:#0F3D3E;margin-bottom:6px;">Payment Information</div>
                    <div style="font-size:12px;color:#1C2321;line-height:1.7;">
                        <b>Bank:</b> CRDB Bank<br>
                        <b>A/C Name:</b> ASYX Group Ltd<br>
                        <b>A/C No:</b> 0150-5521-3700<br>
                        <b>Swift:</b> CORUTZTZ
                    </div>
                </div>
            </div>

            {{-- Right: Totals --}}
            <div style="width:300px;flex-shrink:0;">
                <div style="background:#FBF9F2;border-radius:12px;padding:20px 22px;border:1px solid #E3DDCB;">
                    <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;color:#6E7570;">
                        <span>Subtotal</span>
                        <span style="color:#1C2321;font-weight:600;">TZS {{ number_format($salesInvoice->subtotal, 2) }}</span>
                    </div>
                    @if($salesInvoice->tax_amount > 0)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;color:#6E7570;border-top:1px solid #E3DDCB;">
                        <span>VAT (18%)</span>
                        <span style="color:#1C2321;font-weight:600;">TZS {{ number_format($salesInvoice->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($salesInvoice->discount_amount > 0)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:13px;color:#6E7570;border-top:1px solid #E3DDCB;">
                        <span>Discount</span>
                        <span style="color:#B23A2E;font-weight:600;">&minus; TZS {{ number_format($salesInvoice->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;padding:14px 0 10px;margin-top:6px;border-top:2px solid #0F3D3E;">
                        <span style="font-size:15px;font-weight:700;color:#0F3D3E;">Total</span>
                        <span style="font-family:'JetBrains Mono',monospace;font-size:16px;font-weight:700;color:#0F3D3E;">TZS {{ number_format($salesInvoice->total_amount, 2) }}</span>
                    </div>
                </div>

                {{-- Balance / Paid status --}}
                @if($salesInvoice->status != 'paid')
                <div style="margin-top:12px;padding:14px 18px;border-radius:10px;background:#FBE7E2;border:1px solid #F0C8C0;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:11px;color:#B23A2E;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Balance Due</span>
                    <b style="font-family:'JetBrains Mono',monospace;font-size:15px;color:#B23A2E;">TZS {{ number_format($salesInvoice->balance_amount, 2) }}</b>
                </div>
                @else
                <div style="margin-top:12px;padding:14px 18px;border-radius:10px;background:#E2F0E5;border:1px solid #C0DCC8;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:11px;color:#2F7A3D;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Paid in Full</span>
                    <b style="font-family:'JetBrains Mono',monospace;font-size:15px;color:#2F7A3D;">TZS {{ number_format($salesInvoice->paid_amount, 2) }}</b>
                </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div style="position:absolute;bottom:0;left:0;right:0;">
            <div style="height:1px;background:linear-gradient(90deg,transparent,#E3DDCB 10%,#E3DDCB 90%,transparent);margin:0 48px;"></div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 48px;background:#FBF9F2;">
                <div style="font-size:10.5px;color:#6E7570;">
                    Generated on {{ now()->format('l, F jS, Y \a\t H:i') }}
                </div>
                <div style="font-size:10.5px;color:#6E7570;">
                    {{ config('app.name') }} &middot; <span style="color:#0F3D3E;font-weight:600;">asyxgroup.tz</span>
                </div>
            </div>
            <div style="height:4px;background:linear-gradient(90deg,#0F3D3E 0%,#C9A227 50%,#0F3D3E 100%);"></div>
        </div>
    </div>
</div>

<style>
#invoice-a4 { font-family: 'Inter','Nunito',system-ui,sans-serif; }
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
