@extends('layouts.admin')
@section('title', 'Delivery Note ' . $deliveryNote->delivery_note_number)
@section('page_title', 'Delivery Note ' . $deliveryNote->delivery_note_number)
@section('content')

<div class="mb-4 flex items-center justify-between no-print">
    <a href="{{ route('admin.delivery-notes.index') }}" class="text-xs text-gray-500 hover:text-emerald-600">&larr; Back to Delivery Notes</a>
    <div class="flex items-center gap-2">
        <button onclick="window.print()" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </button>
    </div>
</div>

<div id="dn-sheet" style="width:210mm;min-height:297mm;margin:0 auto;background:#fff;overflow:hidden;font-family:'Inter',sans-serif;display:flex;flex-direction:column;padding:10mm 12mm 0;box-shadow:0 18px 40px -10px rgba(70,25,90,.18),0 0 0 1px #E3DDCB;">

    <header style="position:relative;height:32mm;margin-bottom:12mm;">
        <div style="position:absolute;inset:0;background:#5B2170;clip-path:polygon(0 0,100% 0,100% 82%,26% 82%,16% 100%,0 100%);"></div>
        <div style="position:absolute;top:0;left:0;width:58%;height:9mm;background:#F3EEDC;clip-path:polygon(0 0,100% 0,92% 100%,7% 100%,0 55%);"></div>
        <div style="position:absolute;top:6.2mm;left:30%;background:#F3EEDC;color:#5B2170;font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:11pt;letter-spacing:1.6px;padding:1.4mm 5mm;">ASYX GROUP COMPANY LIMITED</div>
        <div style="position:absolute;top:3.5mm;right:5mm;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:26pt;letter-spacing:3px;line-height:1;">DELIVERY NOTE</div>
        <div style="position:absolute;top:14.5mm;left:30%;right:5mm;color:#EFE3F7;font-size:6.8pt;display:flex;flex-wrap:wrap;gap:1mm 4mm;align-items:center;">
            <span><b>☎</b> +255 755 432 071</span>
            <span><b>✆</b> +255 625 001 100</span>
            <span><b>✉</b> info@asyx.co.tz</span>
            <span><b>◎</b> asyxgroupcompany</span>
            <span><b>🌐</b> www.asyx.co.tz</span>
        </div>
        <div style="position:absolute;top:14.5mm;right:5mm;text-align:right;color:#D9C6E6;font-size:5.6pt;line-height:1.4;max-width:40mm;">
            <b style="color:#fff;font-size:6pt;display:block;">TROPICAL CENTER, 3RD FLOOR</b>
            New Bagamoyo Road · Plot No. 30/00, House No. 301 · P.O. Box 31587 · Dar es Salaam
        </div>
        <div style="position:absolute;left:26%;right:0;bottom:1.5mm;height:6mm;background:#B06F2C;clip-path:polygon(3.5% 0,100% 0,100% 100%,0 100%);display:flex;align-items:center;justify-content:center;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10.5pt;letter-spacing:.6px;">TIN: 108-800-186&nbsp; | &nbsp;VRN: 40-009570-M</div>
        <div style="position:absolute;top:1.5mm;left:6mm;z-index:2;display:flex;flex-direction:column;align-items:center;">
            <svg viewBox="0 0 100 100" aria-hidden="true" style="width:16mm;height:16mm;">
                <path d="M50 8 A42 42 0 1 0 92 50" fill="none" stroke="#D91F26" stroke-width="12" stroke-linecap="round"/>
                <path d="M50 25 A25 25 0 1 1 25 50" fill="none" stroke="#14235A" stroke-width="12" stroke-linecap="round"/>
                <circle cx="50" cy="50" r="6.5" fill="#B06F2C"/>
            </svg>
            <div style="font-family:'Barlow Semi Condensed',sans-serif;font-weight:800;font-size:13pt;letter-spacing:4px;color:#B06F2C;line-height:1.05;text-shadow:0 0 2px rgba(0,0,0,.25);">ASYX</div>
            <div style="font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:6pt;letter-spacing:5px;color:#fff;">GROUP</div>
        </div>
    </header>
    <div style="height:1.1mm;background:#B06F2C;margin:0 0 0;"></div>

    <section style="display:grid;grid-template-columns:1.15fr .85fr;gap:14mm;margin:10mm 0 9mm;">
        <div>
            <h3 style="font-weight:800;font-size:10.5pt;letter-spacing:.2px;margin-bottom:1.5mm;">DELIVERED TO / SUPPLIER:</h3>
            <div style="border-bottom:1px solid #1A1D26;padding:1mm 2mm;font-weight:500;min-height:6.5mm;">{{ $deliveryNote->supplier?->name ?? 'N/A' }}</div>
            <div style="border-bottom:1px solid #1A1D26;padding:1mm 2mm;font-weight:500;min-height:6.5mm;">{{ $deliveryNote->supplier?->email ?? '' }}</div>
            <div style="border-bottom:1px solid #1A1D26;padding:1mm 2mm;font-weight:500;min-height:6.5mm;">{{ $deliveryNote->supplier?->phone ?? '' }}</div>
            <div style="border-bottom:1px solid #1A1D26;padding:1mm 2mm;font-weight:500;min-height:6.5mm;">{{ $deliveryNote->supplier?->address ?? '' }}</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:1.6mm;">
            <div style="display:grid;grid-template-columns:auto 1fr;gap:3mm;align-items:end;">
                <span style="font-weight:800;font-size:10.5pt;letter-spacing:.2px;">DN NO:</span>
                <span style="border-bottom:1px solid #1A1D26;text-align:center;padding:0 2mm .6mm;font-weight:600;min-height:5.5mm;">{{ $deliveryNote->delivery_note_number }}</span>
            </div>
            <div style="display:grid;grid-template-columns:auto 1fr;gap:3mm;align-items:end;">
                <span style="font-weight:800;font-size:10.5pt;letter-spacing:.2px;">DATE:</span>
                <span style="border-bottom:1px solid #1A1D26;text-align:center;padding:0 2mm .6mm;font-weight:600;min-height:5.5mm;">{{ $deliveryNote->delivery_date->format('d/m/Y') }}</span>
            </div>
            <div style="display:grid;grid-template-columns:auto 1fr;gap:3mm;align-items:end;">
                <span style="font-weight:800;font-size:10.5pt;letter-spacing:.2px;">STATUS:</span>
                <span style="border-bottom:1px solid #1A1D26;text-align:center;padding:0 2mm .6mm;font-weight:600;min-height:5.5mm;">{{ strtoupper(str_replace('_',' ',$deliveryNote->status)) }}</span>
            </div>
        </div>
    </section>

    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th style="width:10mm;background:#5B2170;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10pt;letter-spacing:.8px;padding:2mm;border:1.3px solid #2B2E38;text-transform:uppercase;text-align:center;">S/N</th>
                <th style="background:#B06F2C;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10pt;letter-spacing:.8px;padding:2mm;border:1.3px solid #2B2E38;text-transform:uppercase;">Description</th>
                <th style="width:14mm;background:#B06F2C;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10pt;letter-spacing:.8px;padding:2mm;border:1.3px solid #2B2E38;text-transform:uppercase;text-align:center;">Qty</th>
                <th style="width:34mm;background:#B06F2C;color:#fff;font-family:'Barlow Semi Condensed',sans-serif;font-weight:700;font-size:10pt;letter-spacing:.8px;padding:2mm;border:1.3px solid #2B2E38;text-transform:uppercase;text-align:center;">Condition</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deliveryNote->items ?? [] as $i => $item)
            <tr>
                <td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;vertical-align:middle;font-weight:500;text-align:center;">{{ $i + 1 }}</td>
                <td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;vertical-align:middle;font-weight:500;">{{ $item->description ?? $item->product?->name ?? '—' }}</td>
                <td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;vertical-align:middle;font-weight:500;text-align:center;">{{ $item->quantity ?? 1 }}</td>
                <td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;vertical-align:middle;font-weight:500;text-align:center;">{{ $item->condition ?? 'Good' }}</td>
            </tr>
            @empty
            <tr><td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;text-align:center;">—</td><td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;">No items recorded</td><td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;text-align:center;">—</td><td style="border:1.3px solid #2B2E38;padding:2.5mm 2mm;text-align:center;">—</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:6mm;padding:4mm;background:#F2F4F9;border-radius:4px;">
        <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#14235A;font-weight:800;margin-bottom:2mm;">Delivery Info</div>
        <div style="font-size:11pt;color:#1A1D26;line-height:1.7;">
            <b>Delivered By:</b> {{ $deliveryNote->delivered_by ?? '—' }} &nbsp;&nbsp;|&nbsp;&nbsp;
            <b>Received By:</b> {{ $deliveryNote->received_by ?? '—' }} &nbsp;&nbsp;|&nbsp;&nbsp;
            <b>Vehicle:</b> {{ $deliveryNote->vehicle_number ?? '—' }}
        </div>
        @if($deliveryNote->lpo)
        <div style="margin-top:3mm;font-size:11pt;color:#1A1D26;"><b>Linked LPO:</b> {{ $deliveryNote->lpo->lpo_number }} — {{ $deliveryNote->lpo->title ?? 'Purchase Order' }}</div>
        @endif
        @if($deliveryNote->notes)
        <div style="margin-top:3mm;font-size:11pt;color:#1A1D26;"><b>Notes:</b> {{ $deliveryNote->notes }}</div>
        @endif
    </div>

    <div style="margin-top:40px;display:flex;justify-content:space-between;align-items:flex-end;font-size:12px;color:#6E7570;">
        <div>
            <div style="margin-bottom:20px;">
                <div style="border-bottom:1px solid #1C2321;width:200px;margin-bottom:4px;"></div>
                <div style="color:#1C2321;font-weight:500;">Received By Signature</div>
            </div>
            <div>
                <div style="border-bottom:1px solid #1C2321;width:200px;margin-bottom:4px;"></div>
                <div style="color:#1C2321;font-weight:500;">Delivered By Signature</div>
            </div>
        </div>
        <div style="text-align:right;">
            Printed on {{ now()->format('d/m/Y') }}
        </div>
    </div>

    <div style="margin-top:auto;padding-bottom:10mm;padding-top:12mm;">
        <div style="background:#5B2170;color:#F0E6F6;padding:3mm 8mm;font-size:6.9pt;line-height:1.6;text-align:center;font-weight:600;">
            Software and hardware distribution, Customized software and Mobile apps development and re-engineering,
            enterprise software solutions, IT systems Security and Auditing, Computerized Systems Integration,
            Artificial Intelligence (AI) and Machine Learning, Statistics and Big Data Analytics,
            Customer Specific Annual Maintenance Support Contracts (AMCs) and ICT Consultancy and Training.
        </div>
        <div style="display:flex;height:2mm;margin-top:1.2mm;">
            <div style="flex:1.2;background:#D91F26;"></div>
            <div style="flex:1;background:#14235A;"></div>
        </div>
    </div>
</div>

<style>
@media print {
    @page { margin: 0; size: A4; }
    html, body { background: #fff !important; padding: 0 !important; margin: 0 !important; font-size: 10pt; }
    body * { visibility: hidden; }
    #dn-sheet, #dn-sheet * { visibility: visible; }
    #dn-sheet {
        position: absolute;
        left: 0;
        top: 0;
        width: 210mm;
        min-height: 297mm;
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 0;
        padding: 10mm 12mm 0;
    }
    .no-print, nav, header, .sidebar { display: none !important; }
}
</style>
@endsection
