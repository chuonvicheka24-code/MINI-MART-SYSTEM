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
          <option>Cash on delivery</option>
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
        <div onclick="openMobilePayment()" style="background:#fff; padding:8px; display:inline-block; border-radius:10px; border:2px solid #0f172a; cursor:pointer;" title="Click to open mobile payment">
          <img src="{{ asset('Photo/qr') }}" alt="Scan for Pay QR" style="width:130px; height:130px; object-fit:contain; display:block;" onerror="this.onerror=null; this.src='{{ asset('Photo/qr.jpg') }}';">
        </div>
        <p style="font-size:12px; color:#64748b; margin-top:8px; margin-bottom:0; font-weight:600;">Scan to open payment</p>
      </div>
    </div>

    <button onclick="closeQrModal()" style="position:absolute; top:16px; right:20px; background:none; border:none; font-size:24px; cursor:pointer; color:#64748b;">&times;</button>
  </div>
</div>

{{-- STEP 3: Mobile Payment Simulation Overlay --}}
<div id="mobile-pay-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15, 23, 42, 0.85); z-index:100000; justify-content:center; align-items:center;">
  
  <div style="width:360px; height:640px; background:#0d233a; border-radius:32px; border:3px solid #1e293b; box-shadow:0 20px 40px rgba(0,0,0,0.5); display:flex; flex-direction:column; justify-content:space-between; overflow:hidden; position:relative; color:#fff; font-family:sans-serif;">
    
    <!-- Mobile Top Bar -->
    <div style="padding:14px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.05);">
      <button onclick="closeMobilePay()" style="background:none; border:none; color:#94a3b8; font-size:14px; cursor:pointer; font-weight:600;">Back</button>
      <div style="width:10px; height:10px; background:#38bdf8; border-radius:50%;"></div>
    </div>

    <!-- PAGE 1: Review Total -->
    <div id="mobile-page-1" style="display:flex; flex-direction:column; align-items:center; justify-content:center; flex-grow:1; text-align:center; padding:20px;">
      <!-- White Circle Badge so logo is clearly visible -->
      <div style="width:80px; height:80px; background:#ffffff; border:3px solid #38bdf8; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:16px; overflow:hidden; padding:8px; box-shadow:0 4px 12px rgba(0,0,0,0.25);">
        <img src="{{ asset('Photo/minimartlogo.png') }}" alt="Mini Mart Logo" style="width:100%; height:100%; object-fit:contain;">
      </div>
      <h2 style="font-size:20px; font-weight:700; margin:0 0 10px 0; letter-spacing:0.5px; color:#ffffff;">MINI-MART</h2>
      <p style="font-size:16px; color:#e2e8f0; margin:0;">Total : <span class="mobile-amount-val" style="font-weight:700;">$0.00</span></p>
    </div>

    <!-- PAGE 2: 6-Digit PIN Entry Keypad -->
    <div id="mobile-page-2" style="display:none; flex-direction:column; align-items:center; justify-content:center; flex-grow:1; text-align:center; padding:20px;">
      <!-- White Circle Badge for Logo -->
      <div style="width:65px; height:65px; background:#ffffff; border:2px solid #38bdf8; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:12px; overflow:hidden; padding:6px; box-shadow:0 4px 10px rgba(0,0,0,0.25);">
        <img src="{{ asset('Photo/minimartlogo.png') }}" alt="Mini Mart Logo" style="width:100%; height:100%; object-fit:contain;">
      </div>
      <h3 style="font-size:15px; font-weight:700; margin:0 0 4px 0; color:#ffffff;">Enter 6-Digit PIN</h3>
      <div style="font-size:18px; font-weight:700; color:#38bdf8; margin-bottom:16px;" class="mobile-amount-val">$0.00</div>

      <!-- PIN Password Dots -->
      <div style="display:flex; gap:12px; margin-bottom:24px;" id="pin-dots">
        <div style="width:14px; height:14px; border-radius:50%; background:#475569;" class="dot"></div>
        <div style="width:14px; height:14px; border-radius:50%; background:#475569;" class="dot"></div>
        <div style="width:14px; height:14px; border-radius:50%; background:#475569;" class="dot"></div>
        <div style="width:14px; height:14px; border-radius:50%; background:#475569;" class="dot"></div>
        <div style="width:14px; height:14px; border-radius:50%; background:#475569;" class="dot"></div>
        <div style="width:14px; height:14px; border-radius:50%; background:#475569;" class="dot"></div>
      </div>

      <!-- Keypad Grid -->
      <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; width:230px; margin:0 auto;">
        <button onclick="pressPin('1')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">1</button>
        <button onclick="pressPin('2')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">2</button>
        <button onclick="pressPin('3')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">3</button>
        <button onclick="pressPin('4')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">4</button>
        <button onclick="pressPin('5')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">5</button>
        <button onclick="pressPin('6')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">6</button>
        <button onclick="pressPin('7')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">7</button>
        <button onclick="pressPin('8')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">8</button>
        <button onclick="pressPin('9')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">9</button>
        <button onclick="clearPin()" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#38bdf8; font-size:14px; font-weight:600; cursor:pointer;">C</button>
        <button onclick="pressPin('0')" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#fff; font-size:18px; font-weight:600; cursor:pointer;">0</button>
        <button onclick="popPin()" style="height:48px; border-radius:50%; background:rgba(255,255,255,0.08); border:none; color:#f43f5e; font-size:16px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-delete-left"></i></button>
      </div>
    </div>

    <!-- PAGE 3: English Success Receipt Status -->
    <div id="mobile-page-3" style="display:none; flex-direction:column; align-items:center; justify-content:center; flex-grow:1; text-align:center; padding:20px;">
      <div style="width:50px; height:50px; background:#38bdf8; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:12px; box-shadow:0 0 15px rgba(56,189,248,0.5);">
        <i class="fa-solid fa-check" style="color:#0d233a; font-size:24px; font-weight:900;"></i>
      </div>
      <h3 style="font-size:18px; font-weight:700; margin:0 0 14px 0; color:#fff;">Done</h3>

      <div style="background:#fff; color:#0f172a; width:100%; border-radius:14px; padding:14px; text-align:left; font-size:12px;">
        <div style="display:flex; align-items:center; gap:12px; border-bottom:1px solid #e2e8f0; padding-bottom:10px; margin-bottom:10px;">
          <!-- White background circle for clear logo contrast -->
          <div style="width:42px; height:42px; border-radius:50%; background:#ffffff; border:1px solid #cbd5e1; display:flex; align-items:center; justify-content:center; overflow:hidden; padding:4px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
            <img src="{{ asset('Photo/minimartlogo.png') }}" alt="Mini Mart Logo" style="width:100%; height:100%; object-fit:contain;">
          </div>
          <div>
            <div style="font-weight:700; font-size:15px; color:#ef4444;" class="mobile-amount-val">$0.00</div>
            <div style="font-size:11px; color:#64748b; font-weight:600;">MINI-MART</div>
          </div>
        </div>

        <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#64748b;"><span>Transaction Date:</span><span style="color:#0f172a; font-weight:600;">{{ date('M d, Y') }}</span></div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#64748b;"><span>Transaction ID:</span><span style="color:#0f172a; font-weight:600;">32621549</span></div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px; color:#64748b;"><span>Receiving Bank:</span><span style="color:#0f172a; font-weight:600;">ABA Bank Plc.</span></div>
      </div>
    </div>

    <!-- Bottom Action Button for Page 1 & 3 -->
    <div id="mobile-btn-wrap" style="width:100%; padding:16px;">
      <button id="mobile-action-btn" onclick="handleFirstPagePayment()" style="width:100%; padding:16px; background:#f59e0b; color:#fff; border:none; border-radius:12px; font-weight:800; font-size:16px; letter-spacing:1px; cursor:pointer;">
        PAYMENT
      </button>
    </div>

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

  let pinCode = "";

  function triggerCheckoutFlow(){
    const payMethod = document.getElementById("pay")?.value;
    const subtotal = (typeof cartSubtotal === 'function') ? cartSubtotal() : 4.20;
    const delivery = selectedDeliveryFee();
    const totalStr = money(subtotal + delivery);
    
    document.getElementById("modal-total").textContent = totalStr;
    document.querySelectorAll(".mobile-amount-val").forEach(el => el.textContent = totalStr);

    if(payMethod === "QR Scan") {
      document.getElementById("qr-payment-modal").style.display = 'flex';
    } else {
      executeOrderPlacement();
    }
  }

  function closeQrModal() {
    document.getElementById('qr-payment-modal').style.display = 'none';
  }

  function openMobilePayment() {
    closeQrModal();
    pinCode = "";
    updatePinDots();
    document.getElementById("mobile-pay-overlay").style.display = 'flex';
    document.getElementById("mobile-page-1").style.display = 'flex';
    document.getElementById("mobile-page-2").style.display = 'none';
    document.getElementById("mobile-page-3").style.display = 'none';
    document.getElementById("mobile-btn-wrap").style.display = 'block';
    document.getElementById("mobile-action-btn").textContent = "PAYMENT";
  }

  function closeMobilePay() {
    document.getElementById("mobile-pay-overlay").style.display = 'none';
  }

  function handleFirstPagePayment() {
    document.getElementById("mobile-page-1").style.display = 'none';
    document.getElementById("mobile-page-2").style.display = 'flex';
    document.getElementById("mobile-btn-wrap").style.display = 'none';
  }

  function pressPin(num) {
    if(pinCode.length < 6) {
      pinCode += num;
      updatePinDots();
    }
    if(pinCode.length === 6) {
      setTimeout(() => {
        document.getElementById("mobile-page-2").style.display = 'none';
        document.getElementById("mobile-page-3").style.display = 'flex';
        document.getElementById("mobile-btn-wrap").style.display = 'block';
        document.getElementById("mobile-action-btn").textContent = "Done";
        document.getElementById("mobile-action-btn").onclick = function() {
          closeMobilePay();
          executeOrderPlacement();
        };
      }, 400);
    }
  }

  function popPin() {
    pinCode = pinCode.slice(0, -1);
    updatePinDots();
  }

  function clearPin() {
    pinCode = "";
    updatePinDots();
  }

  function updatePinDots() {
    const dots = document.querySelectorAll("#pin-dots .dot");
    dots.forEach((dot, index) => {
      dot.style.background = index < pinCode.length ? "#38bdf8" : "#475569";
    });
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