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

<div id="dn-sheet" style="max-width:760px;margin:0 auto;background:#fff;border-radius:6px;box-shadow:0 18px 40px -10px rgba(15,61,62,.18),0 0 0 1px #E3DDCB;overflow:hidden;font-family:'Inter',sans-serif;">
    <div style="padding:38px 44px 26px;border-bottom:1px solid #E3DDCB;display:flex;justify-content:space-between;align-items:flex-start;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="flex-shrink:0;">
                <img src="{{ asset('asyxgrouplogo.png') }}" alt="{{ config('app.name') }}" style="height:46px;max-width:160px;object-fit:contain;">
            </div>
            <div>
                <div style="font-family:'Fraunces',serif;font-weight:700;font-size:17px;color:#0F3D3E;">{{ $company?->name ?? config('app.name') }}</div>
                <div style="font-size:11.5px;color:#6E7570;margin-top:3px;line-height:1.55;">
                    {{ $company?->address ?? 'Dar es Salaam, Tanzania' }}<br>
                    @if($company?->tax_id)<span>TIN: {{ $company->tax_id }}</span>@endif
                    @if($company?->tax_id && $company?->vrn) &middot; @endif
                    @if($company?->vrn)<span>VRN: {{ $company->vrn }}</span>@endif
                </div>
            </div>
        </div>
        <div style="text-align:right;">
            <h1 style="font-family:'Fraunces',serif;font-size:22px;margin:0 0 8px;color:#1C2321;">Delivery Note</h1>
            <div style="font-size:12px;color:#6E7570;line-height:1.6;">
                DN No: <b style="color:#1C2321;">{{ $deliveryNote->delivery_note_number }}</b><br>
                Date: <b style="color:#1C2321;">{{ $deliveryNote->delivery_date->format('M d, Y') }}</b>
            </div>
        </div>
    </div>

    <div style="padding:30px 44px;">
        <div style="display:flex;gap:60px;margin-bottom:28px;">
            <div>
                <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;">Supplier</div>
                <b style="display:block;font-size:14px;color:#1C2321;">{{ $deliveryNote->supplier?->name ?? 'N/A' }}</b>
                <div style="font-size:12px;color:#6E7570;line-height:1.6;margin-top:2px;">
                    {{ $deliveryNote->supplier?->email ?? '' }}<br>
                    {{ $deliveryNote->supplier?->phone ?? '' }}
                </div>
            </div>
            <div>
                <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;">Delivery Info</div>
                <div style="font-size:12.5px;color:#1C2321;line-height:1.7;">
                    <b>Delivered By:</b> {{ $deliveryNote->delivered_by ?? '—' }}<br>
                    <b>Received By:</b> {{ $deliveryNote->received_by ?? '—' }}<br>
                    <b>Vehicle:</b> {{ $deliveryNote->vehicle_number ?? '—' }}<br>
                    <b>Status:</b> {{ ucfirst(str_replace('_',' ',$deliveryNote->status)) }}
                </div>
            </div>
        </div>

        @if($deliveryNote->lpo)
        <div style="margin-bottom:24px;padding:12px 16px;background:#FBF9F2;border-radius:8px;border:1px solid #E3DDCB;">
            <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:3px;">Linked LPO</div>
            <div style="font-size:12.5px;color:#1C2321;">{{ $deliveryNote->lpo->lpo_number }} — {{ $deliveryNote->lpo->title ?? 'Purchase Order' }}</div>
        </div>
        @endif

        @if($deliveryNote->notes)
        <div style="margin-bottom:28px;">
            <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.1em;color:#6E7570;margin-bottom:6px;">Notes</div>
            <p style="font-size:12.5px;color:#1C2321;margin:0;line-height:1.5;">{{ $deliveryNote->notes }}</p>
        </div>
        @endif

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
                Printed on {{ now()->format('l, F jS, Y') }}<br>
                {{ config('app.name') }}
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page { margin: 8mm; size: A4; }
    html, body { background: #fff !important; padding: 0 !important; margin: 0 !important; font-size: 10pt; }
    body * { visibility: hidden; }
    #dn-sheet, #dn-sheet * { visibility: visible; }
    #dn-sheet {
        position: absolute;
        left: 0;
        top: 0;
        width: 194mm;
        box-shadow: none !important;
        border-radius: 0 !important;
        transform: scale(0.95);
        transform-origin: top left;
    }
    .no-print, nav, header, .sidebar { display: none !important; }
}
</style>
@endsection
