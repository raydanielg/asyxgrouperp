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

    {{-- ═══ PROFORMA INVOICE A4 DOCUMENT ═══ --}}
    @php
        $company = $salesInvoice->company;
        $companyName = $company?->legal_name ?? $company?->name ?? 'ASYX GROUP COMPANY LIMITED';
        $companyAddress = $company?->address ?? 'TROPICAL CENTER, 3RD FLOOR, NEW BAGAMOYO ROAD';
        $companyCity = $company?->city ?? 'Dar es Salaam';
        $companyCountry = $company?->country ?? 'Tanzania';
        $companyPhone = $company?->phone ?? '+255 755 432 071';
        $companyPhone2 = $company?->phone_2 ?? '+255 625 001 100';
        $companyEmail = $company?->email ?? 'info@asyx.co.tz';
        $companyWebsite = $company?->website ?? 'www.asyx.co.tz';
        $companyTax = $company?->tax_id ?? '108-800-186';
        $companyVrn = $company?->registration_number ?? '40-009570-M';
        $companyBox = $company?->postal_box ?? '31587';
    @endphp
    <div id="invoice-a4" class="proforma-page">

        {{-- HEADER --}}
        <div class="header">
            <div class="logo">
                <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                    <g>
                        <path d="M60 14 A46 46 0 0 1 106 60 L86 60 A26 26 0 0 0 60 34 Z" fill="#6c2f80"/>
                        <path d="M106 60 A46 46 0 0 1 60 106 L60 86 A26 26 0 0 0 86 60 Z" fill="#8a6a2f"/>
                        <path d="M60 106 A46 46 0 0 1 14 60 L34 60 A26 26 0 0 0 60 86 Z" fill="#6c2f80"/>
                        <path d="M14 60 A46 46 0 0 1 60 14 L60 34 A26 26 0 0 0 34 60 Z" fill="#8a6a2f"/>
                        <circle cx="60" cy="60" r="15" fill="#5b2d6e"/>
                        <circle cx="60" cy="60" r="7" fill="#fff"/>
                    </g>
                    <text x="60" y="118" text-anchor="middle" font-family="Arial Black,Arial" font-weight="900" font-size="15" fill="#8a6a2f">ASYX</text>
                </svg>
            </div>
            <div class="header-right">
                <div class="header-band">
                    <div style="display:flex;flex-direction:column;justify-content:center;flex:1;">
                        <div class="company-tab">{{ strtoupper($companyName) }}</div>
                        <div class="contact">
                            <div class="row1">
                                <span>&#9743; {{ $companyPhone }}</span>
                                @if($companyPhone2)<span>&#128222; {{ $companyPhone2 }}</span>@endif
                                <span>&#9993; {{ $companyEmail }}</span>
                                <span class="addr">&#9873; {{ strtoupper($companyAddress) }}</span>
                            </div>
                            <div class="row2">
                                <span>&#127760; {{ $companyWebsite }}</span>
                                <span class="addr">PLOT NO. 30/00 | HOUSE NO. 301<br>P.O.Box {{ $companyBox }} - {{ strtoupper($companyCity) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="proforma-title">{{ strtoupper($salesInvoice->type) === 'proforma' ? 'PROFORMA' : 'INVOICE' }}</div>
                </div>
                <div class="tin-bar">TIN: {{ $companyTax }} &nbsp;|&nbsp; VRN: {{ $companyVrn }}</div>
            </div>
        </div>

        <div class="header-underline"></div>

        {{-- BILL TO + META --}}
        <div class="info">
            <div class="bill-to">
                <div class="title">{{ strtoupper($salesInvoice->type) === 'proforma' ? 'PROFORMA INVOICE TO:' : 'INVOICE TO:' }}</div>
                <div class="lines">
                    <div>{{ $salesInvoice->customer?->name ?? 'N/A' }}</div>
                    @if($salesInvoice->customer?->email)<div>{{ $salesInvoice->customer->email }}</div>@endif
                    @if($salesInvoice->customer?->phone)<div>{{ $salesInvoice->customer->phone }}</div>@endif
                    @if($salesInvoice->warehouse)<div><b>Warehouse:</b> {{ $salesInvoice->warehouse->name }}</div>@endif
                </div>
            </div>
            <div class="inv-meta">
                <table>
                    <tr><td class="label">INVOICE NO:</td><td class="val">{{ $salesInvoice->invoice_number }}</td></tr>
                    <tr><td class="label">INVOICE DATE:</td><td class="val">{{ $salesInvoice->invoice_date->format('d/m/Y') }}</td></tr>
                    <tr><td class="label">DUE DATE:</td><td class="val">{{ $salesInvoice->due_date->format('d/m/Y') }}</td></tr>
                    <tr><td class="label">STATUS:</td><td class="val">{{ strtoupper($salesInvoice->status) }}</td></tr>
                </table>
            </div>
        </div>

        {{-- ITEMS --}}
        <div class="items-wrap">
            <table class="items">
                <thead>
                    <tr>
                        <th class="c-sn">S/N</th>
                        <th class="c-item">ITEM</th>
                        <th class="c-spare">SPARE PART</th>
                        <th class="c-qty">QTY</th>
                        <th class="c-price">UNIT PRICE</th>
                        <th class="c-amt">AMOUNT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesInvoice->items as $index => $item)
                    <tr>
                        <td class="c-sn">{{ $index + 1 }}</td>
                        <td class="c-item">{{ $item->product_name }}</td>
                        <td class="c-spare">{{ $item->description ?? '-' }}</td>
                        <td class="c-qty">{{ $item->quantity }}</td>
                        <td class="c-price">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="c-amt">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:24px;">No items on this invoice</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="totals">
                <table>
                    <tr>
                        <td class="t-label">Sub-Total:</td>
                        <td class="t-val">{{ number_format($salesInvoice->subtotal, 2) }}</td>
                    </tr>
                    @if($salesInvoice->tax_amount > 0)
                    <tr>
                        <td class="t-label">VAT (18%):</td>
                        <td class="t-val">{{ number_format($salesInvoice->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($salesInvoice->discount_amount > 0)
                    <tr>
                        <td class="t-label">Discount:</td>
                        <td class="t-val">{{ number_format($salesInvoice->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand">
                        <td class="t-label">GRAND TOTAL</td>
                        <td class="t-val">{{ number_format($salesInvoice->total_amount, 2) }}</td>
                    </tr>
                    @if($salesInvoice->status != 'paid')
                    <tr>
                        <td class="t-label" style="color:#c0392b;">BALANCE DUE:</td>
                        <td class="t-val" style="color:#c0392b;">{{ number_format($salesInvoice->balance_amount, 2) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- TERMS + STAMP --}}
        <div class="mid">
            <div class="terms">
                <div class="t-title">TERMS &amp; CONDITIONS:</div>
                @if($salesInvoice->payment_terms)
                    <div style="font-size:13px;line-height:1.7;margin-bottom:8px;">{{ $salesInvoice->payment_terms }}</div>
                @endif
                <ol>
                    <li>Prices are quoted in {{ $company?->currency ?? 'TZS' }}</li>
                    <li>Prices are subject to change without prior notice</li>
                    @if($salesInvoice->notes)<li>{{ $salesInvoice->notes }}</li>@endif
                </ol>
            </div>
            <div class="stamp">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <path id="topArc" d="M 30 100 A 70 70 0 0 1 170 100" />
                        <path id="botArc" d="M 32 100 A 68 68 0 0 0 168 100" />
                    </defs>
                    <circle cx="100" cy="100" r="78" fill="none" stroke="#1f3a93" stroke-width="3"/>
                    <circle cx="100" cy="100" r="70" fill="none" stroke="#1f3a93" stroke-width="1.5"/>
                    <text font-family="Arial" font-weight="bold" font-size="13" fill="#1f3a93" letter-spacing="1.5">
                        <textPath href="#topArc" startOffset="50%" text-anchor="middle">{{ strtoupper($companyName) }}</textPath>
                    </text>
                    <text font-family="Arial" font-weight="bold" font-size="12" fill="#1f3a93" letter-spacing="1.5">
                        <textPath href="#botArc" startOffset="50%" text-anchor="middle">P.O. Box {{ $companyBox }}, {{ strtoupper($companyCity) }}</textPath>
                    </text>
                    <text x="46" y="104" font-size="18" fill="#1f3a93">&#9733;</text>
                    <text x="140" y="104" font-size="18" fill="#1f3a93">&#9733;</text>
                    <g>
                        <circle cx="100" cy="100" r="32" fill="none" stroke="#1f3a93" stroke-width="3"/>
                        <path d="M100 78 A22 22 0 0 1 122 100 A22 22 0 0 1 100 122" fill="none" stroke="#1f3a93" stroke-width="7"/>
                        <path d="M100 122 A22 22 0 0 1 78 100" fill="none" stroke="#1f3a93" stroke-width="7"/>
                        <circle cx="100" cy="100" r="9" fill="#1f3a93"/>
                        <circle cx="100" cy="100" r="4" fill="#fff"/>
                    </g>
                </svg>
            </div>
        </div>

        <div class="thanks">Thank You For Your Business</div>

        {{-- FOOTER --}}
        <div class="footer-wrap">
            <div class="footer">
                Software and hardware distribution, Customized software and Mobile apps development and
                re-engineering, enterprise software solutions, IT systems Security and Auditing, Computerized Systems Integration,
                Artificial intelligence (AI) and Machine Learning, Statistics and Big Data Analytics, Customer Specific Annual
                Maintenance Support Contracts (AMCs) and ICT Consultancy and Training.
            </div>
            <div class="footer-accent">
                <span class="a1"></span><span class="a2"></span><span class="a3"></span><span class="a4"></span>
            </div>
        </div>
    </div>
</div>

<style>
.proforma-page {
    width: 210mm;
    min-height: 297mm;
    margin: 0 auto;
    background: #fff;
    box-shadow: 0 6px 24px rgba(0,0,0,.25);
    position: relative;
    font-family: "Segoe UI", Arial, Helvetica, sans-serif;
    color: #1c1c1c;
    overflow: hidden;
}
.header {
    display: flex;
    align-items: stretch;
    padding: 14px 20px 0 20px;
}
.logo {
    width: 120px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-right: 6px;
}
.logo svg { width: 110px; height: auto; }
.header-right { flex: 1; display: flex; flex-direction: column; }
.header-band {
    background: linear-gradient(90deg, #5b2d6e 0%, #5b2d6e 55%, #8e2b6b 100%);
    display: flex;
    align-items: stretch;
    border-radius: 4px 4px 0 0;
    overflow: hidden;
    min-height: 78px;
}
.company-tab {
    background: linear-gradient(180deg, #efe9d8, #ddd4bb);
    color: #3a2a10;
    font-weight: 800;
    font-size: 14.5px;
    letter-spacing: .3px;
    display: flex;
    align-items: center;
    padding: 0 16px;
    margin: 10px 0 10px 10px;
    border-radius: 16px;
    white-space: nowrap;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,.05);
    max-width: 265px;
}
.contact {
    flex: 1;
    color: #fff;
    font-size: 8px;
    line-height: 1.55;
    padding: 12px 10px 0 14px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.contact .row1 { display: flex; gap: 14px; flex-wrap: wrap; font-weight: 600; }
.contact .row2 { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 3px; }
.contact .addr { margin-top: 4px; font-size: 7px; opacity: .95; line-height: 1.4; max-width: 150px; }
.contact span { white-space: nowrap; }
.proforma-title {
    color: #fff;
    font-size: 40px;
    font-weight: 800;
    letter-spacing: 1px;
    padding: 0 18px;
    display: flex;
    align-items: center;
    font-family: "Arial Black", Arial, sans-serif;
}
.tin-bar {
    background: linear-gradient(90deg, #8a6a2f, #a9843a);
    color: #fff;
    text-align: right;
    font-weight: 800;
    font-size: 12px;
    letter-spacing: .5px;
    padding: 5px 16px;
    border-radius: 0 0 4px 4px;
}
.header-underline {
    height: 2px;
    background: linear-gradient(90deg, #8a6a2f, #a9843a);
    margin: 0 20px;
}
.info {
    display: flex;
    justify-content: space-between;
    padding: 26px 28px 10px 28px;
    gap: 20px;
}
.bill-to { font-size: 13px; flex: 1; }
.bill-to .title { font-weight: 800; margin-bottom: 8px; letter-spacing: .3px; }
.bill-to .lines { margin-left: 18px; }
.bill-to .lines div {
    border-bottom: 1px solid #333;
    padding: 3px 0 4px 0;
    max-width: 360px;
}
.inv-meta { font-size: 13px; width: 270px; }
.inv-meta table { width: 100%; border-collapse: collapse; }
.inv-meta td { padding: 4px 0; vertical-align: bottom; }
.inv-meta .label { font-weight: 800; color: #4a2359; white-space: nowrap; padding-right: 10px; }
.inv-meta .val { text-align: right; border-bottom: 1px solid #333; min-width: 110px; }
.items-wrap { padding: 14px 28px 0 28px; }
table.items { width: 100%; border-collapse: collapse; font-size: 12.5px; }
table.items th {
    background: linear-gradient(180deg, #c39a44, #a9843a);
    color: #fff;
    font-weight: 800;
    letter-spacing: .3px;
    padding: 6px 8px;
    border: 1px solid #6f5a2a;
    text-align: center;
}
table.items td {
    border: 1px solid #6f5a2a;
    padding: 10px 8px;
    vertical-align: middle;
}
.c-sn { width: 34px; text-align: center; }
.c-item { width: 190px; }
.c-spare { width: 190px; }
.c-qty { width: 44px; text-align: center; }
.c-price { width: 100px; text-align: right; }
.c-amt { width: 110px; text-align: right; }
.totals { display: flex; justify-content: flex-end; }
.totals table { border-collapse: collapse; font-size: 13px; margin-top: -1px; }
.totals td { border: 1px solid #6f5a2a; padding: 7px 10px; }
.totals .t-label { font-weight: 800; color: #127a8a; width: 150px; }
.totals .t-val { text-align: right; font-weight: 800; width: 120px; background: #efefef; }
.totals .grand td {
    background: #1f4e79;
    color: #fff;
    border-color: #1a3f63;
    font-size: 15px;
}
.totals .grand .t-label { color: #fff; }
.totals .grand .t-val { background: #1f4e79; color: #fff; }
.mid {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 46px 28px 0 28px;
    gap: 20px;
}
.terms .t-title {
    color: #c0392b;
    font-weight: 800;
    font-size: 17px;
    text-decoration: underline;
    margin-bottom: 8px;
    letter-spacing: .3px;
}
.terms ol { margin-left: 20px; font-size: 13px; line-height: 1.9; }
.stamp { width: 170px; flex-shrink: 0; display: flex; justify-content: center; }
.stamp svg { width: 160px; height: 160px; }
.thanks {
    font-weight: 800;
    font-size: 14px;
    padding: 40px 28px 0 28px;
}
.footer-wrap {
    position: absolute;
    bottom: 22px;
    left: 0;
    right: 0;
    padding: 0 20px;
}
.footer {
    background: #5b2d6e;
    color: #fff;
    text-align: center;
    font-size: 9.5px;
    font-weight: 700;
    line-height: 1.6;
    padding: 14px 26px;
    border-radius: 4px 4px 0 0;
}
.footer-accent {
    height: 7px;
    display: flex;
    border-radius: 0 0 4px 4px;
    overflow: hidden;
}
.footer-accent span { flex: 1; }
.footer-accent .a1 { background: #a9843a; }
.footer-accent .a2 { background: #c0392b; }
.footer-accent .a3 { background: #1f4e79; }
.footer-accent .a4 { background: #5b2d6e; }

@media print {
    body { background: #fff; padding: 0; }
    .proforma-page { box-shadow: none; margin: 0; width: auto; min-height: auto; }
    @page { size: A4; margin: 0; }
    .no-print { display: none !important; }
    nav, header, .sidebar, .no-print { display: none !important; }
}
@media (max-width: 820px) {
    .proforma-page { width: 100%; }
    .proforma-title { font-size: 28px; }
    .info { flex-direction: column; }
    .inv-meta { width: 100%; }
}
</style>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
@endpush
@endsection
