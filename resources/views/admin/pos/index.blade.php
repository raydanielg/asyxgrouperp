@extends('layouts.admin')
@section('title', 'POS Terminal - ' . config('app.name'))
@section('page_title', '')
@section('content')
<div class="pos-wrap flex flex-col overflow-hidden" style="height:100vh;">

    {{-- Terminal bar --}}
    <div class="bg-slate-900 px-4 py-2.5 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" title="Exit POS" class="w-7 h-7 rounded bg-white/10 hover:bg-white/20 flex items-center justify-center text-slate-300 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
            <div>
                <p class="text-sm font-bold text-white leading-tight">POS Terminal</p>
                <p class="text-[10px] text-slate-400">{{ auth()->user()->name }} · {{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-[11px] text-slate-400">{{ $products->count() }} products</span>
            <button onclick="toggleFullscreen()" title="Toggle fullscreen" class="w-7 h-7 rounded bg-white/10 hover:bg-white/20 flex items-center justify-center text-slate-300 hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"/></svg>
            </button>
            <a href="{{ route('admin.pos.reports') }}" class="text-[11px] font-medium text-slate-300 hover:text-white">Reports →</a>
        </div>
    </div>

    {{-- Terminal body --}}
    <div class="flex flex-1 min-h-0">

        {{-- Products --}}
        <div class="flex-1 flex flex-col min-w-0 bg-gray-50/50">
            <div class="px-4 py-3 border-b border-gray-200 bg-white flex-shrink-0">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="productSearch" placeholder="Search products..." class="w-full pl-9 pr-3 py-2 rounded border border-gray-200 text-xs outline-none focus:border-gray-400" autofocus>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-3">
                <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-2.5">
                @foreach($products as $p)
                <button type="button" class="product-card text-left bg-white border rounded-md p-3 transition-all {{ $p->stock_quantity < 1 ? 'border-gray-200 opacity-40 cursor-not-allowed' : 'border-gray-200 hover:border-gray-900 hover:shadow-sm cursor-pointer' }}" data-id="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->sale_price }}" data-stock="{{ $p->stock_quantity }}" onclick="addToCart(this)" {{ $p->stock_quantity < 1 ? 'disabled' : '' }}>
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-[11px] font-bold text-gray-500 flex-shrink-0">{{ strtoupper(substr($p->name, 0, 2)) }}</span>
                        <span class="text-[10px] {{ $p->stock_quantity < 1 ? 'text-red-500' : 'text-gray-400' }}">{{ $p->stock_quantity < 1 ? 'Out' : $p->stock_quantity . ' left' }}</span>
                    </div>
                    <p class="text-xs font-medium text-gray-900 leading-snug line-clamp-2 min-h-[32px]">{{ $p->name }}</p>
                    <p class="text-xs font-bold text-gray-900 mt-1.5 font-mono">{{ number_format($p->sale_price) }}</p>
                </button>
                @endforeach
                </div>
            </div>
        </div>

        {{-- Cart panel --}}
        <div class="w-[360px] flex-shrink-0 bg-white border-l border-gray-200 flex flex-col">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500">Current Sale</h3>
                <button onclick="clearCart()" class="text-[11px] text-gray-400 hover:text-red-500">Clear</button>
            </div>

            <div id="cartItems" class="flex-1 overflow-y-auto p-3 space-y-2">
                <p class="text-xs text-gray-400 text-center py-10" id="emptyCart">Cart is empty.<br>Click products to add.</p>
            </div>

            {{-- Totals --}}
            <div class="border-t border-gray-200 flex-shrink-0">
                <div class="px-4 py-3 space-y-2">
                    <div class="flex justify-between text-xs"><span class="text-gray-500">Subtotal</span><span class="font-mono font-medium text-gray-900" id="cartSubtotal">0</span></div>
                    <div class="flex items-center justify-between text-xs"><span class="text-gray-500">Tax %</span><input type="number" id="taxRate" value="0" min="0" max="100" oninput="updateTotals()" class="w-16 px-2 py-1 rounded border border-gray-200 text-xs text-right font-mono outline-none focus:border-gray-400"></div>
                    <div class="flex justify-between text-xs"><span class="text-gray-500">Tax Amount</span><span class="font-mono text-gray-700" id="cartTax">0</span></div>
                    <div class="flex items-center justify-between text-xs"><span class="text-gray-500">Discount</span><input type="number" id="discountAmount" value="0" min="0" oninput="updateTotals()" class="w-20 px-2 py-1 rounded border border-gray-200 text-xs text-right font-mono outline-none focus:border-gray-400"></div>
                </div>
                <div class="bg-slate-900 px-4 py-3 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">Total</span>
                    <span class="text-lg font-bold text-white font-mono" id="cartTotal">0</span>
                </div>
                <div class="px-4 py-3 space-y-2 border-t border-gray-100">
                    <div class="grid grid-cols-3 gap-1.5">
                        <button type="button" onclick="setPay('cash')" class="payBtn px-2 py-1.5 text-[11px] font-medium rounded border transition-colors" data-method="cash">Cash</button>
                        <button type="button" onclick="setPay('card')" class="payBtn px-2 py-1.5 text-[11px] font-medium rounded border transition-colors" data-method="card">Card</button>
                        <button type="button" onclick="setPay('mobile')" class="payBtn px-2 py-1.5 text-[11px] font-medium rounded border transition-colors" data-method="mobile">Mobile</button>
                    </div>
                    <input type="hidden" id="paymentMethod" value="cash">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">Paid</span>
                        <input type="number" id="paidAmount" value="0" min="0" oninput="updateChange()" class="w-28 px-2 py-1.5 rounded border border-gray-200 text-xs text-right font-mono font-bold outline-none focus:border-gray-400">
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Change</span>
                        <span class="font-mono font-bold text-emerald-600" id="changeAmount">0</span>
                    </div>
                    <button onclick="processSale()" id="completeSaleBtn" class="w-full py-3 bg-slate-900 text-white text-sm font-bold rounded hover:bg-slate-700 transition-colors">
                        Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let cart = [];

function setPay(method) {
    document.getElementById('paymentMethod').value = method;
    document.querySelectorAll('.payBtn').forEach(b => {
        const active = b.dataset.method === method;
        b.classList.toggle('bg-slate-900', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('border-slate-900', active);
        b.classList.toggle('border-gray-200', !active);
        b.classList.toggle('text-gray-600', !active);
    });
}
setPay('cash');

function addToCart(el) {
    if (el.disabled) return;
    let id = el.dataset.id, name = el.dataset.name, price = parseFloat(el.dataset.price), stock = parseInt(el.dataset.stock);
    let existing = cart.find(i => i.product_id === id);
    if (existing) {
        if (existing.quantity >= stock) { Swal.fire({icon:'warning',title:'Out of stock',text:'Only '+stock+' units available',confirmButtonColor:'#0f172a'}); return; }
        existing.quantity++;
    } else {
        if (stock < 1) return;
        cart.push({product_id: id, product_name: name, unit_price: price, quantity: 1});
    }
    renderCart();
}

function removeFromCart(idx) { cart.splice(idx, 1); renderCart(); }
function clearCart() { cart = []; renderCart(); }

function updateQty(idx, delta) {
    let item = cart[idx];
    let el = document.querySelector('[data-id="'+item.product_id+'"]');
    let stock = parseInt(el.dataset.stock);
    item.quantity += delta;
    if (item.quantity < 1) { cart.splice(idx, 1); }
    else if (item.quantity > stock) { item.quantity = stock; }
    renderCart();
}

function renderCart() {
    let container = document.getElementById('cartItems');
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-xs text-gray-400 text-center py-10" id="emptyCart">Cart is empty.<br>Click products to add.</p>';
        updateTotals(); return;
    }
    container.innerHTML = cart.map((item, idx) => `
        <div class="flex items-center gap-2 p-2.5 bg-gray-50 rounded border border-gray-100">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-gray-900 truncate">${item.product_name}</p>
                <p class="text-[10px] text-gray-400 font-mono">${item.unit_price.toLocaleString()} × ${item.quantity}</p>
            </div>
            <div class="flex items-center gap-1 flex-shrink-0">
                <button onclick="updateQty(${idx},-1)" class="w-5 h-5 rounded bg-white border border-gray-200 text-gray-600 text-xs leading-none hover:border-gray-400">−</button>
                <span class="text-xs font-mono font-medium w-5 text-center">${item.quantity}</span>
                <button onclick="updateQty(${idx},1)" class="w-5 h-5 rounded bg-white border border-gray-200 text-gray-600 text-xs leading-none hover:border-gray-400">+</button>
            </div>
            <span class="text-xs font-mono font-bold text-gray-900 w-16 text-right flex-shrink-0">${Math.round(item.unit_price * item.quantity).toLocaleString()}</span>
            <button onclick="removeFromCart(${idx})" class="text-gray-300 hover:text-red-500 flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    `).join('');
    updateTotals();
}

function updateTotals() {
    let subtotal = cart.reduce((s, i) => s + i.unit_price * i.quantity, 0);
    let taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    let tax = subtotal * taxRate / 100;
    let discount = parseFloat(document.getElementById('discountAmount').value) || 0;
    let total = subtotal + tax - discount;
    document.getElementById('cartSubtotal').textContent = Math.round(subtotal).toLocaleString();
    document.getElementById('cartTax').textContent = Math.round(tax).toLocaleString();
    document.getElementById('cartTotal').textContent = 'TZS ' + Math.round(total).toLocaleString();
    document.getElementById('paidAmount').value = total.toFixed(0);
    updateChange();
}

function updateChange() {
    let total = parseFloat(document.getElementById('cartTotal').textContent.replace(/[^0-9.-]/g, '')) || 0;
    let paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    document.getElementById('changeAmount').textContent = Math.round(Math.max(0, paid - total)).toLocaleString();
}

function processSale() {
    if (cart.length === 0) { Swal.fire({icon:'warning',title:'Empty cart',text:'Add products to cart first',confirmButtonColor:'#0f172a'}); return; }
    let subtotal = cart.reduce((s, i) => s + i.unit_price * i.quantity, 0);
    let taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    let tax = subtotal * taxRate / 100;
    let discount = parseFloat(document.getElementById('discountAmount').value) || 0;
    let total = subtotal + tax - discount;
    let paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    if (paid < total) { Swal.fire({icon:'error',title:'Insufficient payment',text:'Paid amount is less than total',confirmButtonColor:'#0f172a'}); return; }
    const btn = document.getElementById('completeSaleBtn');
    btn.disabled = true; btn.textContent = 'Processing...';
    fetch('{{ route("admin.pos.store") }}', {
        method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ subtotal, tax_amount: tax, discount_amount: discount, total_amount: total, paid_amount: paid, payment_method: document.getElementById('paymentMethod').value, items: cart.map(i => ({product_id: i.product_id, product_name: i.product_name, quantity: i.quantity, unit_price: i.unit_price, total: i.unit_price * i.quantity})) })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            Swal.fire({icon:'success',title:'Sale Completed!',text:'Sale #: ' + data.sale_number,confirmButtonColor:'#0f172a'}).then(() => { cart = []; renderCart(); location.reload(); });
        } else { Swal.fire({icon:'error',title:'Error',text:'Failed to process sale',confirmButtonColor:'#0f172a'}); }
    }).catch(e => Swal.fire({icon:'error',title:'Error',text:'Network error',confirmButtonColor:'#0f172a'}))
    .finally(() => { btn.disabled = false; btn.textContent = 'Complete Sale'; });
}

document.getElementById('productSearch').addEventListener('input', function() {
    let q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(c => { c.style.display = c.dataset.name.toLowerCase().includes(q) ? '' : 'none'; });
});

function toggleFullscreen() {
    if (document.fullscreenElement) { document.exitFullscreen(); }
    else { document.documentElement.requestFullscreen().catch(() => {}); }
}
</script>
<style>
/* POS = fullscreen terminal: hide admin chrome */
aside#adminSidebar { display: none !important; }
header { display: none !important; }
.lg\:ml-64 { margin-left: 0 !important; }
main { padding: 0 !important; }
body { overflow: hidden; }
</style>
@endpush
@endsection
