@extends('layouts.admin')
@section('title', 'POS Terminal - ' . config('app.name'))
@section('page_title', 'POS Terminal')
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-white rounded-xl border p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Products</h3>
            <input type="text" id="productSearch" placeholder="Search products..." class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none w-48">
        </div>
        <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-[60vh] overflow-y-auto">
            @foreach($products as $p)
            <div class="product-card cursor-pointer border rounded-lg p-3 hover:border-emerald-500 hover:bg-emerald-50/30 transition-all" data-id="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->sale_price }}" data-stock="{{ $p->stock_quantity }}" onclick="addToCart(this)">
                <div class="w-full h-16 bg-emerald-100 rounded mb-2 flex items-center justify-center"><svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                <p class="text-xs font-medium text-gray-900 truncate">{{ $p->name }}</p>
                <div class="flex items-center justify-between mt-1"><span class="text-xs font-bold text-emerald-700">${{ number_format($p->sale_price, 2) }}</span><span class="text-[10px] text-gray-400">{{ $p->stock_quantity }} in stock</span></div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="bg-white rounded-xl border p-6 flex flex-col">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Cart</h3>
        <div id="cartItems" class="flex-1 overflow-y-auto max-h-[40vh] space-y-2 mb-4">
            <p class="text-xs text-gray-400 text-center py-4" id="emptyCart">Cart is empty. Click products to add.</p>
        </div>
        <div class="space-y-2 border-t pt-4">
            <div class="flex justify-between text-xs"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900" id="cartSubtotal">$0.00</span></div>
            <div class="flex items-center justify-between text-xs"><span class="text-gray-500">Tax (%)</span><input type="number" id="taxRate" value="0" min="0" max="100" oninput="updateTotals()" class="w-16 px-2 py-1 rounded border border-gray-200 text-xs text-right"></div>
            <div class="flex justify-between text-xs"><span class="text-gray-500">Tax Amount</span><span class="text-gray-700" id="cartTax">$0.00</span></div>
            <div class="flex items-center justify-between text-xs"><span class="text-gray-500">Discount</span><input type="number" id="discountAmount" value="0" min="0" oninput="updateTotals()" class="w-20 px-2 py-1 rounded border border-gray-200 text-xs text-right"></div>
            <div class="flex justify-between text-sm font-bold border-t pt-2"><span class="text-gray-900">Total</span><span class="text-emerald-700" id="cartTotal">$0.00</span></div>
            <div class="flex items-center justify-between text-xs"><span class="text-gray-500">Payment Method</span><select id="paymentMethod" class="px-2 py-1 rounded border border-gray-200 text-xs"><option value="cash">Cash</option><option value="card">Card</option><option value="mobile">Mobile Money</option></select></div>
            <div class="flex items-center justify-between text-xs"><span class="text-gray-500">Paid Amount</span><input type="number" id="paidAmount" value="0" oninput="updateChange()" class="w-20 px-2 py-1 rounded border border-gray-200 text-xs text-right"></div>
            <div class="flex justify-between text-xs"><span class="text-gray-500">Change</span><span class="font-medium text-emerald-700" id="changeAmount">$0.00</span></div>
            <button onclick="processSale()" class="w-full py-3 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition-colors mt-2">Complete Sale</button>
        </div>
    </div>
</div>
@push('scripts')
<script>
let cart = [];
function addToCart(el) {
    let id = el.dataset.id, name = el.dataset.name, price = parseFloat(el.dataset.price), stock = parseInt(el.dataset.stock);
    let existing = cart.find(i => i.product_id === id);
    if (existing) { if (existing.quantity >= stock) { Swal.fire({icon:'warning',title:'Out of stock',text:'Only '+stock+' units available',confirmButtonColor:'#024938'}); return; } existing.quantity++; }
    else { if (stock < 1) return; cart.push({product_id: id, product_name: name, unit_price: price, quantity: 1, total: price}); }
    renderCart();
}
function removeFromCart(idx) { cart.splice(idx, 1); renderCart(); }
function updateQty(idx, delta) {
    let item = cart[idx];
    let el = document.querySelector('[data-id="'+item.product_id+'"]');
    let stock = parseInt(el.dataset.stock);
    item.quantity += delta;
    if (item.quantity < 1) { cart.splice(idx, 1); } else if (item.quantity > stock) { item.quantity = stock; }
    renderCart();
}
function renderCart() {
    let container = document.getElementById('cartItems');
    if (cart.length === 0) { container.innerHTML = '<p class="text-xs text-gray-400 text-center py-4" id="emptyCart">Cart is empty. Click products to add.</p>'; updateTotals(); return; }
    container.innerHTML = cart.map((item, idx) => `
        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
            <div class="flex-1"><p class="text-xs font-medium text-gray-900">${item.product_name}</p><p class="text-[10px] text-gray-400">$${item.unit_price.toFixed(2)} x ${item.quantity}</p></div>
            <div class="flex items-center gap-1"><button onclick="updateQty(${idx},-1)" class="w-5 h-5 rounded bg-gray-200 text-gray-600 text-xs">-</button><span class="text-xs font-medium w-6 text-center">${item.quantity}</span><button onclick="updateQty(${idx},1)" class="w-5 h-5 rounded bg-gray-200 text-gray-600 text-xs">+</button></div>
            <span class="text-xs font-bold text-emerald-700 w-16 text-right">$${(item.unit_price * item.quantity).toFixed(2)}</span>
            <button onclick="removeFromCart(${idx})" class="text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
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
    document.getElementById('cartSubtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('cartTax').textContent = '$' + tax.toFixed(2);
    document.getElementById('cartTotal').textContent = '$' + total.toFixed(2);
    document.getElementById('paidAmount').value = total.toFixed(2);
    updateChange();
}
function updateChange() {
    let total = parseFloat(document.getElementById('cartTotal').textContent.replace('$','')) || 0;
    let paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    document.getElementById('changeAmount').textContent = '$' + Math.max(0, paid - total).toFixed(2);
}
function processSale() {
    if (cart.length === 0) { Swal.fire({icon:'warning',title:'Empty cart',text:'Add products to cart first',confirmButtonColor:'#024938'}); return; }
    let subtotal = cart.reduce((s, i) => s + i.unit_price * i.quantity, 0);
    let taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    let tax = subtotal * taxRate / 100;
    let discount = parseFloat(document.getElementById('discountAmount').value) || 0;
    let total = subtotal + tax - discount;
    let paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    if (paid < total) { Swal.fire({icon:'error',title:'Insufficient payment',text:'Paid amount is less than total',confirmButtonColor:'#024938'}); return; }
    fetch('{{ route("admin.pos.store") }}', {
        method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({ subtotal, tax_amount: tax, discount_amount: discount, total_amount: total, paid_amount: paid, payment_method: document.getElementById('paymentMethod').value, items: cart.map(i => ({product_id: i.product_id, product_name: i.product_name, quantity: i.quantity, unit_price: i.unit_price, total: i.unit_price * i.quantity})) })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            Swal.fire({icon:'success',title:'Sale Completed!',text:'Sale #: ' + data.sale_number,confirmButtonColor:'#024938'}).then(() => { cart = []; renderCart(); location.reload(); });
        } else { Swal.fire({icon:'error',title:'Error',text:'Failed to process sale',confirmButtonColor:'#024938'}); }
    }).catch(e => Swal.fire({icon:'error',title:'Error',text:'Network error',confirmButtonColor:'#024938'}));
}
document.getElementById('productSearch').addEventListener('input', function() {
    let q = this.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(c => { c.style.display = c.dataset.name.toLowerCase().includes(q) ? '' : 'none'; });
});
</script>
@endpush
@endsection
