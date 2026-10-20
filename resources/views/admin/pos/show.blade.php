@extends('layouts.admin')
@section('title', 'POS Sale Details - ' . config('app.name'))
@section('page_title', 'Sale Receipt')
@section('content')

<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <a href="{{ route('admin.pos.reports') }}" class="text-xs text-gray-400 hover:text-gray-700">&larr; Back to POS Reports</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.pos.index') }}" class="px-3 py-1.5 border border-gray-200 text-gray-700 text-xs font-medium rounded hover:bg-gray-50">Terminal</a>
        <button onclick="printReceipt()" class="px-3 py-1.5 bg-gray-900 text-white text-xs font-medium rounded hover:bg-gray-700">Print Receipt</button>
    </div>
</div>

{{-- Receipt paper on canvas --}}
<div class="flex justify-center">
    <div class="bg-gray-100 rounded-md border border-gray-200 p-4 md:p-8 w-full max-w-lg flex flex-col items-center">

        <div id="receipt" class="w-[300px] bg-white shadow-md">
            <div class="px-4 py-5 text-[11px] leading-relaxed font-mono text-gray-900">

                <div class="text-center">
                    <p class="font-bold">*** RISITI / RECEIPT ***</p>
                    <p class="font-bold text-[13px] mt-1">{{ strtoupper($company->name ?? config('app.name')) }}</p>
                    <p class="text-gray-600">{{ strtoupper($company->address ?? 'DAR ES SALAAM, TANZANIA') }}</p>
                    @if($company?->phone)<p class="text-gray-600">MOBILE: {{ $company->phone }}</p>@endif
                    @if($company?->tax_id)<p class="text-gray-600">TIN: {{ $company->tax_id }}</p>@endif
                    @if($company?->vrn)<p class="text-gray-600">VRN: {{ $company->vrn }}</p>@endif
                    <p class="text-gray-400 mt-1 text-[10px]">{{ str_repeat('=', 34) }}</p>
                </div>

                <div class="mt-1">
                    <div class="flex justify-between"><span>CUSTOMER NAME:</span><span>WALK-IN</span></div>
                    <div class="flex justify-between"><span>RECEIPT NO:</span><span>{{ str_replace('POS-', '', $posSale->sale_number) }}</span></div>
                    <div class="flex justify-between"><span>CASHIER:</span><span>{{ strtoupper(substr($posSale->cashier?->name ?? '—', 0, 16)) }}</span></div>
                    <div class="flex justify-between"><span>DATE:</span><span>{{ $posSale->created_at->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between"><span>TIME:</span><span>{{ $posSale->created_at->format('H:i:s') }}</span></div>
                    <p class="text-gray-400 mt-1 text-[10px]">{{ str_repeat('=', 34) }}</p>
                </div>

                <div class="mt-1">
                    @foreach($posSale->items as $item)
                    <p class="font-medium">{{ strtoupper($item->product_name) }}</p>
                    <div class="flex justify-between pl-3">
                        <span>{{ $item->quantity }} x {{ number_format($item->unit_price) }}</span>
                        <span>{{ number_format($item->total) }} A</span>
                    </div>
                    @endforeach
                    <p class="text-gray-400 mt-1 text-[10px]">{{ str_repeat('-', 34) }}</p>
                </div>

                <div class="mt-1">
                    <div class="flex justify-between"><span>TOTAL EXCL TAX:</span><span>{{ number_format($posSale->subtotal) }}</span></div>
                    @if($posSale->tax_amount > 0)
                    <div class="flex justify-between"><span>TAX A:</span><span>{{ number_format($posSale->tax_amount) }}</span></div>
                    @endif
                    @if($posSale->discount_amount > 0)
                    <div class="flex justify-between"><span>DISCOUNT:</span><span>-{{ number_format($posSale->discount_amount) }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold"><span>TOTAL INCL TAX:</span><span>{{ number_format($posSale->total_amount) }}</span></div>
                    <p class="text-gray-400 mt-1 text-[10px]">{{ str_repeat('=', 34) }}</p>
                </div>

                <div class="mt-1">
                    <div class="flex justify-between font-bold"><span>JUMLA / TOTAL (TZS):</span><span>{{ number_format($posSale->total_amount) }}</span></div>
                    <div class="flex justify-between"><span>{{ ['cash'=>'CASH / TASLIMU','card'=>'CARD / KADI','mobile'=>'MOBILE / SIMU'][$posSale->payment_method] ?? 'PAID' }}:</span><span>{{ number_format($posSale->paid_amount) }}</span></div>
                    <div class="flex justify-between"><span>CHANGE / CHENJI:</span><span>{{ number_format(max(0, $posSale->paid_amount - $posSale->total_amount)) }}</span></div>
                    <p class="text-gray-400 mt-1 text-[10px]">{{ str_repeat('-', 34) }}</p>
                </div>

                <div class="text-center mt-1">
                    <p class="text-gray-500">RECEIPT VERIFICATION CODE</p>
                    <p class="font-bold tracking-[0.2em]">{{ $verificationCode }}</p>
                    @if($qrSvg)
                    <div class="flex justify-center my-2"><img src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="QR" class="w-[110px] h-[110px]"></div>
                    @endif
                    <p class="text-gray-600">Z NUMBER: {{ $posSale->created_at->format('Ymd') }}/{{ substr($posSale->sale_number, -4) }}</p>
                    <p class="text-gray-400 mt-1 text-[10px]">{{ str_repeat('-', 34) }}</p>
                    <p class="font-bold mt-1">ASANTE KWA KUNUNUA!</p>
                    <p class="text-gray-600">THANK YOU, KARIBU TENA</p>
                    <p class="text-[9px] text-gray-400 mt-1">Bidhaa zilizonunuliwa hazirudishwi<br>Goods sold are not returnable</p>
                </div>
            </div>
            {{-- torn edge --}}
            <div style="height:8px;background:linear-gradient(-45deg,transparent 4px,#fff 0) 0 0/8px 8px repeat-x,linear-gradient(45deg,transparent 4px,#fff 0) 4px 0/8px 8px repeat-x;"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function printReceipt() {
    const w = window.open('', '_blank', 'width=320,height=720');
    const content = document.getElementById('receipt').innerHTML;
    w.document.write('<html><head><title>Receipt {{ $posSale->sale_number }}</title><style>body{font-family:monospace;font-size:11px;margin:10px;} .text-center{text-align:center;} .flex{display:flex;justify-content:space-between;} .justify-between{justify-content:space-between;} .justify-center{justify-content:center;} .font-bold{font-weight:700;} .pl-3{padding-left:12px;} .mt-1{margin-top:4px;} .my-2{margin:8px 0;} p{margin:0;} img{display:block;margin:0 auto;}</style></head><body>' + content + '</body></html>');
    w.document.close();
    setTimeout(() => w.print(), 400);
}
</script>
@endpush
@endsection
