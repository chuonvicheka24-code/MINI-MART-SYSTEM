<?php $__env->startSection('title', 'Checkout — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-head no-print">
  <div class="wrap">
    <h1>Delivery Information</h1>
    <p> Choose how it arrives, then confirm.</p>
  </div>
</div>

<section class="section no-print" id="checkout-section">
  <div class="wrap delivery-layout">

    <div class="card">
      <div class="field-row">
        <div class="field"><label for="truck-number">Truck number</label><input id="truck-number" value="PP-1234"></div>
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
        <div class="field"><label for="phone">Phone number</label><input id="phone" type="tel" placeholder="+855 12 345 678" value="<?php echo e(auth()->user()->phone ?? ''); ?>"></div>
        <div class="field"><label for="email">Email</label><input id="email" type="email" placeholder="you@example.com" value="<?php echo e(auth()->user()->email ?? ''); ?>"></div>
      </div>

      <div class="field"><label for="pay">Payment method</label>
        <select id="pay">
          <option>Cash on delivery</option>
          <option>QR Scan</option>
        </select>
      </div>
    </div>

    <div>
      <div class="card" style="margin-bottom:20px; padding:10px;">
        <div class="map-card">
          <img src="<?php echo e(asset('Photo/150a7d6ac15444d505c789b0c017f9f1.jpg')); ?>" alt="Delivery route map">
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
        <div class="summary-row"><span>Total delivery (10% of total cost)</span><span id="sum-delivery">$0.00</span></div>
        <div class="summary-row total"><span>Total cost</span><span id="sum-total">$0.00</span></div>
        <button class="btn btn-primary btn-block" style="margin-top:16px;" onclick="placeOrder()" id="confirm-btn">Confirm Delivery</button>
      </div>
    </div>

  </div>
</section>


<section class="section" id="invoice-section" style="display:none;">
  <div class="wrap">
    <div class="invoice">
      <div class="invoice-head">
        <div>
          <h2 style="margin-bottom:2px;">Mini<span style="color:var(--price);">Mart</span></h2>
          <p class="form-note">Veng Sreng Blvd, Phnom Penh</p>
        </div>
        <div style="text-align:right;">
          <div><strong>Invoice #<span id="inv-number"></span></strong></div>
          <div class="form-note" id="inv-date"></div>
        </div>
      </div>
      <p><strong>Deliver via:</strong> <span id="inv-mode"></span> · <strong>Pay by:</strong> <span id="inv-pay"></span></p>
      <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
        <tbody id="inv-body"></tbody>
      </table>
      <div class="summary-row"><span>Items subtotal</span><span id="inv-subtotal"></span></div>
      <div class="summary-row"><span>Delivery (10%)</span><span id="inv-delivery"></span></div>
      <div class="summary-row total"><span>Total paid</span><span id="inv-total"></span></div>
    </div>
    
    
    <div style="max-width:640px; margin: 22px auto 0; display:flex; gap:12px; justify-content:center;" class="no-print">
      <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print invoice</button>
      <a id="back-home-btn" href="#" class="btn btn-ghost">Order Process</a>
    </div>
  </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  window.MM_PRODUCTS = <?php echo json_encode(\App\Models\Product::all()->map->toStorefrontArray(), 15, 512) ?>;

  function paintSummary(){
    const subtotal = cartSubtotal();
    const delivery = subtotal * (window.DELIVERY_RATE || 0.1);
    document.getElementById("sum-subtotal").textContent = money(subtotal);
    document.getElementById("sum-delivery").textContent = money(delivery);
    document.getElementById("sum-total").textContent = money(subtotal + delivery);

    if(cartLines().length === 0 && document.getElementById("invoice-section").style.display === "none"){
      document.getElementById("checkout-section").innerHTML =
        '<div class="wrap"><p>Your basket is empty. <a href="<?php echo e(route('products.index')); ?>" style="color:var(--brand-dark); text-decoration:underline; font-weight:600;">Add something first →</a></p></div>';
    }
  }
  refreshCartCache().then(paintSummary);

  document.getElementById("transport-row")?.addEventListener("click", e => {
    const opt = e.target.closest(".radio-pill");
    if(!opt) return;
    document.querySelectorAll(".radio-pill").forEach(o => o.classList.remove("selected"));
    opt.classList.add("selected");
    opt.querySelector("input").checked = true;
  });

  function placeOrder(){
    const lines = cartLines();
    if(!lines.length) return;
    const btn = document.getElementById("confirm-btn");
    btn.disabled = true; 
    btn.textContent = "Placing order…";

    const payload = {
      truck_number: document.getElementById("truck-number")?.value || "",
      location: document.getElementById("location")?.value || "",
      address: document.getElementById("address")?.value || "",
      transport_type: document.querySelector('input[name="delivery"]:checked')?.value || "truck",
      phone: document.getElementById("phone")?.value || "",
      email: document.getElementById("email")?.value || "",
      payment_method: document.getElementById("pay")?.value || "Cash on delivery",
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
    .then(r => r.json().then(data => ({ ok: r.ok, data })))
    .then(({ ok, data }) => {
      if(!ok){ 
        alert(data.message || "Could not place the order."); 
        btn.disabled = false; 
        btn.textContent = "Confirm Delivery"; 
        return; 
      }

      // Populate Invoice UI Data
      const orderId = data.order_id || data.order?.number || data.id || "1043";
      document.getElementById("inv-number").textContent = orderId;
      document.getElementById("inv-date").textContent = new Date().toLocaleString('en-US', { 
        weekday: 'short', month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true 
      });
      document.getElementById("inv-mode").textContent = payload.transport_type === 'truck' ? 'Truck' : 'Motorcycle';
      document.getElementById("inv-pay").textContent = payload.payment_method;

      // Render items table using floating quantities and salePrice
      const subtotal = cartSubtotal();
      const delivery = subtotal * (window.DELIVERY_RATE || 0.10);
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

      // Set "Back to Home" URL to the Order Tracking view
      const targetUrl = data.redirect_url || (`/orders/` + orderId);
      document.getElementById("back-home-btn").href = targetUrl;

      // Clear cart session and switch views
      localStorage.removeItem("cart");
      refreshCartCache();

      document.getElementById("checkout-section").style.display = "none";
      if (document.querySelector(".page-head")) {
        document.querySelector(".page-head").style.display = "none";
      }
      document.getElementById("invoice-section").style.display = "block";
      window.scrollTo(0, 0);
    })
    .catch(err => {
      console.error(err);
      btn.disabled = false;
      btn.textContent = "Confirm Delivery";
    });
  }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/checkout/index.blade.php ENDPATH**/ ?>