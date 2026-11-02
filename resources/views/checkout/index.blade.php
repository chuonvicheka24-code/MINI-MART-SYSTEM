@extends('layouts.app')

@section('title', 'Checkout — Mini Mart')

@section('content')

<div class="page-head no-print">
  <div class="wrap">
    <h1>Delivery Information</h1>
    <p>Choose how it arrives, then confirm.</p>
  </div>
</div>

<section class="section no-print" id="checkout-section">
  <div class="wrap delivery-layout">

    <div class="card">
      <div class="field-row">
        <div class="field"><label for="truck-number">Truck number</label><input id="truck-number" placeholder="e.g. PP-1234"></div>
        <div class="field"><label for="location">Select location</label>
          <select id="location">
            <option>Phnom Penh</option><option>Siem Reap</option><option>Battambang</option><option>Sihanoukville</option>
          </select>
        </div>
      </div>
      <div class="field"><label for="address">Store to customer</label><input id="address" placeholder="#50, Mao Tse Toung Blvd"></div>

      <div class="field">
        <label>Transport type</label>
        <div class="transport-row" id="transport-row">
          <label class="radio-pill selected" data-mode="truck"><input type="radio" name="delivery" value="truck" checked> <i class="fa-solid fa-truck"></i> Truck</label>
          <label class="radio-pill" data-mode="moto"><input type="radio" name="delivery" value="moto"> <i class="fa-solid fa-motorcycle"></i> Motorcycle</label>
        </div>
      </div>

      <h3 style="margin-top:24px;">Contact for this order</h3>
      <div class="field-row">
        <div class="field"><label for="phone">Phone number</label><input id="phone" type="tel" placeholder="+855 12 345 678" value="{{ auth()->user()->phone ?? '' }}"></div>
        <div class="field"><label for="email">Email</label><input id="email" type="email" placeholder="you@example.com" value="{{ auth()->user()->email ?? '' }}"></div>
      </div>

      <div class="field"><label for="pay">Payment method</label>
        <select id="pay">
          <option selected>QR Scan</option>
        </select>
      </div>
    </div>

    <div>
      <div class="card" style="margin-bottom:20px; padding:10px;">
        <div class="map-card">
          <img src="{{ asset('Photo/150a7d6ac15444d505c789b0c017f9f1.jpg') }}" alt="Delivery route map">
          <svg class="route-svg" viewBox="0 0 400 260" preserveAspectRatio="none">
            <path d="M70,190 C140,140 180,230 260,150 S 330,70 340,60" fill="none" stroke="#1E63D6" stroke-width="4" stroke-linecap="round" stroke-dasharray="2 10"/>
          </svg>
          <span class="pin" style="left:52px; top:172px;"><i class="fa-solid fa-location-dot"></i></span>
          <span class="pin" style="left:322px; top:38px;"><i class="fa-solid fa-store"></i></span>
        </div>
      </div>

      <div class="card">
        <h3 style="margin-bottom:14px;">Cost total</h3>
        <div class="summary-row"><span>Items subtotal</span><span id="sum-subtotal">$0.00</span></div>
        <div class="summary-row"><span>Total delivery</span><span id="sum-delivery">$0.00</span></div>
        <div class="summary-row total"><span>Total cost</span><span id="sum-total">$0.00</span></div>
        <button class="btn btn-primary btn-block" style="margin-top:16px;" onclick="triggerCheckoutFlow()" id="confirm-btn">Confirm Delivery</button>
      </div>
    </div>

  </div>
</section>

{{-- STEP 2: Scan for Pay Modal --}}
<div id="qr-payment-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; justify-content:center; align-items:center;">
  <div style="background:#fff; padding:30px; border-radius:20px; width:640px; max-width:92vw; position:relative; box-shadow:0 15px 35px rgba(0,0,0,0.2);">
    
    <h2 style="text-align:center; font-size:24px; font-weight:800; color:#0f172a; margin-top:0; margin-bottom:24px;">Scan for Pay</h2>

    <div style="display:grid; grid-template-columns: 1.1fr 1fr; gap:20px; align-items:center;">
      <div style="display:flex; flex-direction:column; gap:14px; font-size:15px; color:#0f172a;">
        <div><strong>Name:</strong> <span id="modal-name">MINI-MART</span></div>
        <div><strong>Date :</strong> <span id="modal-date">{{ date('M d, Y') }}</span></div>
        <div><strong>invoice Id:</strong> <span id="modal-invoice">#1060</span></div>
        <div><strong>Total:</strong> <span id="modal-total" style="color:#10b981; font-weight:700;">$0.00</span></div>
      </div>

      <div style="text-align:center; background:#f8fafc; padding:16px; border-radius:14px; border:2px solid #e2e8f0;">
        <div onclick="payWithQr()" style="background:#fff; padding:8px; display:inline-block; border-radius:10px; border:2px solid #0f172a; cursor:pointer;" title="Click to confirm payment">
          <img src="{{ asset('Photo/qr') }}" alt="Scan for Pay QR" style="width:130px; height:130px; object-fit:contain; display:block;" onerror="this.onerror=null; this.src='{{ asset('Photo/qr.jpg') }}';">
        </div>
        <p style="font-size:12px; color:#64748b; margin-top:8px; margin-bottom:0; font-weight:600;">Scan to open payment</p>
      </div>
    </div>

    <button onclick="closeQrModal()" style="position:absolute; top:16px; right:20px; background:none; border:none; font-size:24px; cursor:pointer; color:#64748b;">&times;</button>
  </div>
</div>

{{-- STEP 4: Full Web Invoice View --}}
<section class="section" id="invoice-section" style="display:none;">
  <div class="wrap">
    <div class="invoice">
      <div class="invoice-head">
        <div>
          <h2 style="margin-bottom:2px;">Mini<span style="color:var(--price);">Mart</span></h2>
          <p class="form-note">Veng Sreng Blvd, Phnom Penh</p>
        </div>
        <div style="text-align:right;">
          <div><strong>Invoice #<span id="inv-number">1060</span></strong></div>
          <div class="form-note" id="inv-date">{{ date('M d, Y, h:i A') }}</div>
        </div>
      </div>
      <p><strong>Deliver via:</strong> <span id="inv-mode">Truck</span> · <strong>Pay by:</strong> <span id="inv-pay">QR Scan</span></p>
      <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
        <tbody id="inv-body"></tbody>
      </table>
      <div class="summary-row"><span>Items subtotal</span><span id="inv-subtotal">$0.00</span></div>
      <div class="summary-row"><span>Delivery (10%)</span><span id="inv-delivery">$0.00</span></div>
      <div class="summary-row total"><span>Total paid</span><span id="inv-total">$0.00</span></div>
    </div>
    
    <div style="max-width:640px; margin: 22px auto 0; display:flex; gap:12px; justify-content:center;" class="no-print">
      <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print invoice</button>
      <a id="back-home-btn" href="{{ route('products.index') }}" class="btn btn-ghost">Order Process</a>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  window.MM_PRODUCTS = @json(\App\Models\Product::all()->map->toStorefrontArray());
  window.MM_DELIVERY = @json(['truck' => (float) \App\Models\Setting::current()->truck_price, 'moto' => (float) \App\Models\Setting::current()->moto_price]);

  function selectedDeliveryFee(){
    const type = document.querySelector('input[name="delivery"]:checked')?.value || 'truck';
    return Number(window.MM_DELIVERY[type] ?? 0);
  }

  function paintCheckoutSummary(){
    if (typeof cartSubtotal === 'function' && typeof cartLines === 'function') {
      const subtotal = cartSubtotal();
      const delivery = selectedDeliveryFee();
      document.getElementById("sum-subtotal").textContent = money(subtotal);
      document.getElementById("sum-delivery").textContent = money(delivery);
      document.getElementById("sum-total").textContent = money(subtotal + delivery);

      if(cartLines().length === 0 && document.getElementById("invoice-section").style.display === "none"){
        document.getElementById("checkout-section").innerHTML =
          '<div class="wrap"><p>Your basket is empty. <a href="{{ route('products.index') }}" style="color:var(--brand-dark); text-decoration:underline; font-weight:600;">Add something first →</a></p></div>';
      }
    }
  }

  if (typeof refreshCartCache === 'function') {
    refreshCartCache().then(paintCheckoutSummary);
  } else {
    paintCheckoutSummary();
  }

  document.getElementById("transport-row")?.addEventListener("click", e => {
    const opt = e.target.closest(".radio-pill");
    if(!opt) return;
    document.querySelectorAll(".radio-pill").forEach(o => o.classList.remove("selected"));
    opt.classList.add("selected");
    opt.querySelector("input").checked = true;
    paintCheckoutSummary();
  });

  function triggerCheckoutFlow(){
    const payMethod = document.getElementById("pay")?.value;
    const subtotal = (typeof cartSubtotal === 'function') ? cartSubtotal() : 4.20;
    const delivery = selectedDeliveryFee();
    const totalStr = money(subtotal + delivery);
    
    document.getElementById("modal-total").textContent = totalStr;

    if(payMethod === "QR Scan") {
      document.getElementById("qr-payment-modal").style.display = 'flex';
    } else {
      executeOrderPlacement();
    }
  }

  function closeQrModal() {
    document.getElementById('qr-payment-modal').style.display = 'none';
  }

  function payWithQr() {
    closeQrModal();
    executeOrderPlacement();
  }

  function executeOrderPlacement(){
    const lines = typeof cartLines === 'function' ? cartLines() : [];
    const btn = document.getElementById("confirm-btn");
    if (btn) {
      btn.disabled = true; 
      btn.textContent = "Processing invoice…";
    }

    const payload = {
      truck_number: document.getElementById("truck-number")?.value || "PP-1234",
      location: document.getElementById("location")?.value || "Phnom Penh",
      address: document.getElementById("address")?.value || "#50, Mao Tse Toung Blvd",
      transport_type: document.querySelector('input[name="delivery"]:checked')?.value || "truck",
      phone: document.getElementById("phone")?.value || "0123456788",
      email: document.getElementById("email")?.value || "admin@minimart.com",
      payment_method: document.getElementById("pay")?.value || "QR Scan",
    };

    fetch(window.MM_ROUTES.checkoutStore, {
      method: "POST",
      headers: { 
        "Content-Type": "application/json", 
        "Accept": "application/json", 
        "X-CSRF-TOKEN": window.MM_CSRF 
      },
      body: JSON.stringify(payload),
    })
    .then(r => r.json().catch(() => ({ ok: r.ok }))).then(res => {
      // Server refused the order (e.g. not enough stock): tell the customer, resync the basket, stop.
      if (res && res.message && !res.order) {
        alert(res.message);
        if (typeof refreshCartCache === 'function') refreshCartCache().then(() => location.reload());
        return;
      }
      const orderId = res.order_id || res.order?.number || res.id || "1060";
      document.getElementById("inv-number").textContent = orderId;
      document.getElementById("inv-date").textContent = new Date().toLocaleString('en-US', { 
        weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true 
      });
      document.getElementById("inv-mode").textContent = payload.transport_type === 'truck' ? 'Truck' : 'Motorcycle';
      document.getElementById("inv-pay").textContent = payload.payment_method;

      const subtotal = (typeof cartSubtotal === 'function') ? cartSubtotal() : 4.20;
      const delivery = Number(window.MM_DELIVERY[payload.transport_type] ?? 0);
      const total = subtotal + delivery;

      let itemsHtml = '';
      lines.forEach(line => {
        const qty = parseFloat(line.qty);
        const price = line.product.salePrice ?? line.product.price;
        itemsHtml += `
          <tr>
            <td>${line.product.name}</td>
            <td>${qty} ${line.product.unit || ''}</td>
            <td>${money(price)}</td>
            <td>${money(price * qty)}</td>
          </tr>
        `;
      });
      
      document.getElementById("inv-body").innerHTML = itemsHtml;
      document.getElementById("inv-subtotal").textContent = money(subtotal);
      document.getElementById("inv-delivery").textContent = money(delivery);
      document.getElementById("inv-total").textContent = money(total);

      const targetUrl = res.redirect_url || (`/orders/` + orderId);
      document.getElementById("back-home-btn").href = targetUrl;

      localStorage.removeItem("cart");
      if (typeof refreshCartCache === 'function') refreshCartCache();

      document.getElementById("checkout-section").style.display = "none";
      const pageHead = document.querySelector(".page-head");
      if (pageHead) pageHead.style.display = "none";
      
      document.getElementById("invoice-section").style.display = "block";
      window.scrollTo(0, 0);
    }).catch(err => {
      console.error(err);
      document.getElementById("checkout-section").style.display = "none";
      document.getElementById("invoice-section").style.display = "block";
      window.scrollTo(0, 0);
    });
  }
</script>
@endpush