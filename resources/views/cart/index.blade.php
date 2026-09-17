@extends('layouts.app')

@section('title', 'Your cart — Mini Mart')

@section('content')

<div class="page-head">
  <div class="wrap">
    <h1>Your Cart</h1>
    <p>Review quantities and cost before you choose delivery.</p>
  </div>
</div>

<section class="section">
  <div class="wrap cart-layout" id="cart-wrap">

    <div class="card">
      <table class="order-table" id="order-table">
        <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr></thead>
        <tbody id="order-body"></tbody>
      </table>
      <p id="empty-cart" style="display:none; margin-top:8px;">Your basket is empty. <a href="{{ route('products.index') }}" style="text-decoration:underline; color:var(--brand-dark); font-weight:600;">Go find something fresh →</a></p>
    </div>

    <div class="card">
      <h3 style="margin-bottom:14px;">Select Delivery</h3>
      <div class="field"><select id="delivery-select">
        <option value="truck"><i class="fa-solid fa-truck"></i> Truck — TM-1042</option>
        <option value="moto"><i class="fa-solid fa-motorcycle"></i> Moto — MT-0087</option>
      </select></div>

      <div class="summary-row"><span>Items subtotal</span><span id="sum-subtotal">$0.00</span></div>
      <div class="summary-row"><span>Estimated delivery (10%)</span><span id="sum-delivery">$0.00</span></div>
      <div class="summary-row total"><span>Total</span><span id="sum-total">$0.00</span></div>
      <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block" style="margin-top:16px;" id="checkout-btn">Proceed to Payment</a>
      <p class="form-note" style="margin-top:12px;">Delivery fee is calculated as 10% of total cost and confirmed again at checkout.</p>
    </div>

  </div>
</section>

@endsection

@push('scripts')
<script>
  // Full catalog is needed to render line items (name/image/price) next to server-held quantities.
  window.MM_PRODUCTS = @json(\App\Models\Product::all()->map->toStorefrontArray());

  function renderCart(){
    const lines = cartLines();
    const body = document.getElementById("order-body");
    document.getElementById("empty-cart").style.display = lines.length ? "none" : "block";
    document.getElementById("order-table").style.display = lines.length ? "table" : "none";
    document.querySelector(".cart-layout .card:last-child").style.display = lines.length ? "block" : "none";

    body.innerHTML = lines.map(l => `
      <tr>
        <td>
          <div class="line-item">
            <img src="/${l.product.img}" alt="${l.product.name}">
            <div>
              <div class="line-name">${l.product.name}</div>
              <div class="line-unit">per ${l.product.unit}</div>
            </div>
          </div>
        </td>
        <td>${money(l.product.salePrice)}</td>
        <td>
          <div class="qty-control">
            <button onclick="changeQty(${l.id}, -1)" aria-label="Decrease quantity">−</button>
            <input type="text" value="${l.qty}" readonly>
            <button onclick="changeQty(${l.id}, 1)" aria-label="Increase quantity">+</button>
          </div>
        </td>
        <td>${money(l.product.salePrice * l.qty)}</td>
        <td><a class="remove-link" href="#" onclick="removeFromCart(${l.id}).then(renderCart); return false;" aria-label="Remove"><i class="fa-solid fa-trash"></i></a></td>
      </tr>
    `).join("");

    const subtotal = cartSubtotal();
    const delivery = subtotal * DELIVERY_RATE;
    document.getElementById("sum-subtotal").textContent = money(subtotal);
    document.getElementById("sum-delivery").textContent = money(delivery);
    document.getElementById("sum-total").textContent = money(subtotal + delivery);
    document.getElementById("checkout-btn").style.pointerEvents = lines.length ? "auto" : "none";
    document.getElementById("checkout-btn").style.opacity = lines.length ? "1" : ".5";
  }
  function changeQty(id, delta){
    const line = cartLines().find(l => l.id === id);
    const newQty = line ? line.qty + delta : 0;
    setCartQty(id, newQty).then(renderCart);
  }
  refreshCartCache().then(renderCart);
</script>
@endpush
