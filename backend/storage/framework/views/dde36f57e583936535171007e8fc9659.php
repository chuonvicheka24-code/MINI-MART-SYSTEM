<?php $__env->startSection('title', 'Checkout — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-head no-print">
  <div class="wrap">
    <h1>Delivery Information</h1>
    <p>Choose how it arrives, then confirm.</p>
  </div>
</div>

<section class="section no-print" id="checkout-section">
  <div class="wrap delivery-layout">

    <div class="card">
      <div class="field"><label for="location">Select store</label>
        <select id="location">
          <option value="Phnom Penh">Phnom Penh Store</option>
          <option value="Siem Reap">Siem Reap Store</option>
          <option value="Battambang">Battambang Store</option>
          <option value="Sihanoukville">Sihanoukville Store</option>
        </select>
      </div>
      <div class="field"><label for="address">Store to customer</label><input id="address" placeholder="#50, Mao Tse Toung Blvd"></div>

      <div class="field">
        <label>Transport type</label>
        <div class="transport-row" id="transport-row">
          <label class="radio-pill selected" data-mode="truck"><input type="radio" name="delivery" value="truck" checked> <i class="fa-solid fa-truck"></i> Truck</label>
          <label class="radio-pill" data-mode="moto"><input type="radio" name="delivery" value="moto"> <i class="fa-solid fa-motorcycle"></i> Motorcycle</label>
        </div>
        <p class="form-note" id="transport-note" style="margin-top:8px;"></p>
      </div>

      <h3 style="margin-top:24px;">Contact for this order</h3>
      <div class="field-row">
        <div class="field"><label for="phone">Phone number</label><input id="phone" type="tel" placeholder="+855 12 345 678" value="<?php echo e(auth()->user()->phone ?? ''); ?>"></div>
        <div class="field"><label for="email">Email</label><input id="email" type="email" placeholder="you@example.com" value="<?php echo e(auth()->user()->email ?? ''); ?>"></div>
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
        <div class="pick-bar">
          <input id="map-search" type="text" placeholder="Search place, street, landmark…" autocomplete="off">
          <button type="button" class="btn btn-primary" id="map-search-btn" title="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
          <button type="button" class="btn btn-ghost" id="map-locate-btn" title="Use my current location"><i class="fa-solid fa-location-crosshairs"></i></button>
        </div>
        <div id="pick-map"></div>
        <div class="pick-info" id="pick-info"><i class="fa-solid fa-hand-pointer"></i> Tap the map or drag the pin to choose where we deliver.</div>
        <input type="hidden" id="dest-lat"><input type="hidden" id="dest-lng">
      </div>

      <div class="card">
        <h3 style="margin-bottom:14px;">Cost total</h3>
        <div class="summary-row"><span>Items subtotal</span><span id="sum-subtotal">$0.00</span></div>
        <div class="summary-row"><span>Total delivery <small id="sum-km" style="color:#64748b;"></small></span><span id="sum-delivery">$0.00</span></div>
        <div class="summary-row total"><span>Total cost</span><span id="sum-total">$0.00</span></div>
        <button class="btn btn-primary btn-block" style="margin-top:16px;" onclick="triggerCheckoutFlow()" id="confirm-btn">Confirm Delivery</button>
      </div>
    </div>

  </div>
</section>


<div id="qr-payment-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; justify-content:center; align-items:center;">
  <div style="background:#fff; padding:30px; border-radius:20px; width:640px; max-width:92vw; position:relative; box-shadow:0 15px 35px rgba(0,0,0,0.2);">
    
    <h2 style="text-align:center; font-size:24px; font-weight:800; color:#0f172a; margin-top:0; margin-bottom:24px;">Scan for Pay</h2>

    <div style="display:grid; grid-template-columns: 1.1fr 1fr; gap:20px; align-items:center;">
      <div style="display:flex; flex-direction:column; gap:14px; font-size:15px; color:#0f172a;">
        <div><strong>Name:</strong> <span id="modal-name">MINI-MART</span></div>
        <div><strong>Date :</strong> <span id="modal-date"><?php echo e(date('M d, Y')); ?></span></div>
        <div><strong>invoice Id:</strong> <span id="modal-invoice">#1060</span></div>
        <div><strong>Total:</strong> <span id="modal-total" style="color:#10b981; font-weight:700;">$0.00</span></div>
      </div>

      <div style="text-align:center; background:#f8fafc; padding:16px; border-radius:14px; border:2px solid #e2e8f0;">
        <div onclick="payWithQr()" style="background:#fff; padding:8px; display:inline-block; border-radius:10px; border:2px solid #0f172a; cursor:pointer;" title="Click to confirm payment">
          <img src="<?php echo e(asset('Photo/qr')); ?>" alt="Scan for Pay QR" style="width:130px; height:130px; object-fit:contain; display:block;" onerror="this.onerror=null; this.src='<?php echo e(asset('Photo/qr.jpg')); ?>';">
        </div>
        <p style="font-size:12px; color:#64748b; margin-top:8px; margin-bottom:0; font-weight:600;">Scan to open payment</p>
      </div>
    </div>

    <button onclick="closeQrModal()" style="position:absolute; top:16px; right:20px; background:none; border:none; font-size:24px; cursor:pointer; color:#64748b;">&times;</button>
  </div>
</div>


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
          <div class="form-note" id="inv-date"><?php echo e(date('M d, Y, h:i A')); ?></div>
        </div>
      </div>
      <p><strong>Deliver via:</strong> <span id="inv-mode">Truck</span> · <strong>Pay by:</strong> <span id="inv-pay">QR Scan</span></p>
      <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
        <tbody id="inv-body"></tbody>
      </table>
      <div class="summary-row"><span>Items subtotal</span><span id="inv-subtotal">$0.00</span></div>
      <div class="summary-row"><span>Delivery</span><span id="inv-delivery">$0.00</span></div>
      <div class="summary-row total"><span>Total paid</span><span id="inv-total">$0.00</span></div>
    </div>
    
    <div style="max-width:640px; margin: 22px auto 0; display:flex; gap:12px; justify-content:center;" class="no-print">
      <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Print invoice</button>
      <a id="back-home-btn" href="<?php echo e(route('products.index')); ?>" class="btn btn-ghost">Order Process</a>
    </div>
  </div>
</section>


<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
  .pick-bar{ display:flex; gap:8px; margin-bottom:10px; }
  .pick-bar input{ flex:1; min-width:0; padding:10px 12px; border:1px solid #e2e8f0; border-radius:10px; }
  .pick-bar .btn{ padding:10px 14px; }
  #pick-map{ height:340px; border-radius:14px; overflow:hidden; z-index:1; }
  .pick-info{ margin-top:10px; font-size:14px; color:#475569; min-height:20px; }
  .pick-info.ok{ color:#047857; font-weight:600; }
  /* the global ".field input" style (full width, border, tinted background) was drawing a box behind the radio dot */
  .field .radio-pill input{ width:auto; padding:0; margin:0; border:0; background:none; box-shadow:none; accent-color:#15803d; }
  .radio-pill.disabled{ opacity:.45; pointer-events:none; }
  .pin-wrap{ font-size:30px; line-height:1; filter:drop-shadow(0 3px 4px rgba(0,0,0,.4)); }
  .pin-wrap.drop{ animation:pinDrop .35s ease-out; }
  @keyframes pinDrop{ from{ transform:translateY(-24px); opacity:0 } to{ transform:none; opacity:1 } }
</style>

<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
  window.MM_PRODUCTS = <?php echo json_encode(\App\Models\Product::all()->map->toStorefrontArray(), 15, 512) ?>;
  window.MM_DELIVERY = <?php echo json_encode(['truck' => (float) \App\Models\Setting::current()->truck_price, 'moto' => (float) \App\Models\Setting::current()->moto_price], 512) ?>;
  window.MM_RULES = <?php echo json_encode(['freeKm' => \App\Support\Delivery::FREE_KM, 'perKm' => \App\Support\Delivery::PER_KM, 'motoMaxKg' => \App\Support\Delivery::MOTO_MAX_KG, 'motoMaxUnits' => \App\Support\Delivery::MOTO_MAX_UNITS]); ?>;
  window.MM_DISTANCE = null;   // road km store -> customer, set by the map

  // Same rules as App\Support\Delivery (server re-checks everything at checkout).
  function unitInfo(unit){
    const u = String(unit || '').toLowerCase().trim();
    if (u === 'kg') return { kg: 1, byWeight: true };
    const m = u.match(/(\d*\.?\d+)\s*(kg|g|ml|l)\b/);
    if (m) { const n = parseFloat(m[1]); return { kg: m[2] === 'kg' ? n : (m[2] === 'g' || m[2] === 'ml') ? n / 1000 : n, byWeight: false }; }
    return { kg: 0, byWeight: false };
  }
  function cartLoad(){
    let kg = 0, units = 0;
    (typeof cartLines === 'function' ? cartLines() : []).forEach(l => {
      const q = parseFloat(l.qty) || 0, i = unitInfo(l.product && l.product.unit);
      kg += i.kg * q;
      if (!i.byWeight) units += q;
    });
    return { kg, units };
  }
  function motoAllowed(){
    const l = cartLoad();
    return l.kg <= MM_RULES.motoMaxKg && l.units <= MM_RULES.motoMaxUnits;
  }
  function extraKmCharge(){
    return window.MM_DISTANCE == null ? 0 : Math.max(0, window.MM_DISTANCE - MM_RULES.freeKm) * MM_RULES.perKm;
  }

  function selectedDeliveryFee(){
    const type = document.querySelector('input[name="delivery"]:checked')?.value || 'truck';
    return Math.round((Number(window.MM_DELIVERY[type] ?? 0) + extraKmCharge()) * 100) / 100;
  }

  function applyTransportRule(){
    const moto = document.querySelector('.radio-pill[data-mode="moto"]');
    const truck = document.querySelector('.radio-pill[data-mode="truck"]');
    const note = document.getElementById('transport-note');
    if (!moto || (typeof cartLines === 'function' && cartLines().length === 0)) return;
    const ok = motoAllowed(), l = cartLoad();
    moto.classList.toggle('disabled', !ok);
    moto.querySelector('input').disabled = !ok;
    if (!ok) {
      moto.classList.remove('selected');
      truck.classList.add('selected');
      truck.querySelector('input').checked = true;
    }
    note.textContent = ok
      ? 'Motorcycle is available for orders up to ' + MM_RULES.motoMaxKg + ' kg and ' + MM_RULES.motoMaxUnits + ' units (your order: ' + l.kg.toFixed(1) + ' kg, ' + l.units + ' units).'
      : 'Your order is ' + l.kg.toFixed(1) + ' kg / ' + l.units + ' units, over ' + MM_RULES.motoMaxKg + ' kg or ' + MM_RULES.motoMaxUnits + ' units, so only Truck delivery is available.';
  }

  function paintCheckoutSummary(){
    if (typeof cartSubtotal === 'function' && typeof cartLines === 'function') {
      applyTransportRule();
      const subtotal = cartSubtotal();
      const delivery = selectedDeliveryFee();
      const kmEl = document.getElementById("sum-km");
      if (kmEl) kmEl.textContent = window.MM_DISTANCE == null ? "(choose location on map)"
        : "(" + window.MM_DISTANCE.toFixed(1) + " km" + (window.MM_DISTANCE > MM_RULES.freeKm ? ", +$" + extraKmCharge().toFixed(2) : "") + ")";
      document.getElementById("sum-subtotal").textContent = money(subtotal);
      document.getElementById("sum-delivery").textContent = money(delivery);
      document.getElementById("sum-total").textContent = money(subtotal + delivery);

      if(cartLines().length === 0 && document.getElementById("invoice-section").style.display === "none"){
        document.getElementById("checkout-section").innerHTML =
          '<div class="wrap"><p>Your basket is empty. <a href="<?php echo e(route('products.index')); ?>" style="color:var(--brand-dark); text-decoration:underline; font-weight:600;">Add something first →</a></p></div>';
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
    if (!document.getElementById("dest-lat")?.value) {
      alert("Please choose your delivery location on the map first.");
      document.getElementById("pick-map")?.scrollIntoView({ behavior: "smooth", block: "center" });
      return;
    }
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
      location: document.getElementById("location")?.value || "Phnom Penh",
      address: document.getElementById("address")?.value || "#50, Mao Tse Toung Blvd",
      transport_type: document.querySelector('input[name="delivery"]:checked')?.value || "truck",
      phone: document.getElementById("phone")?.value || "0123456788",
      email: document.getElementById("email")?.value || "admin@minimart.com",
      payment_method: document.getElementById("pay")?.value || "QR Scan",
      ...(window.getPickedCoords ? window.getPickedCoords() : {}),
      distance_km: window.MM_DISTANCE,
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
      const delivery = res.order ? Number(res.order.delivery) : selectedDeliveryFee();
      const total = res.order ? Number(res.order.total) : subtotal + delivery;

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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
  const STORES = {                              // store of each city in the "Select store" list
    'Phnom Penh':    [11.5527, 104.8337],
    'Siem Reap':     [13.3633, 103.8564],
    'Battambang':    [13.0957, 103.2022],
    'Sihanoukville': [10.6093, 103.5296],
  };
  const citySel = document.getElementById('location');
  let STORE = STORES[citySel.value] || STORES['Phnom Penh'];
  const $ = id => document.getElementById(id);
  const info = $('pick-info');
  if (!$('pick-map') || typeof L === 'undefined') return;

  const map = L.map('pick-map').setView(STORE, 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

  const icon = (emoji, cls) => L.divIcon({ className: '', html: '<div class="pin-wrap ' + (cls||'') + '">' + emoji + '</div>', iconSize: [32, 32], iconAnchor: [16, 30] });
  const storeMarker = L.marker(STORE, { icon: icon('🏪'), interactive: false }).addTo(map);
  const storeTip = () => storeMarker.bindTooltip('MiniMart ' + citySel.value + ' Store');
  storeTip();

  let pin = null, routeLine = null, seq = 0;

  function setInfo(html, ok){ info.innerHTML = html; info.classList.toggle('ok', !!ok); }

  async function reverse(lat, lng){
    try {
      const r = await fetch('https://nominatim.openstreetmap.org/reverse?format=json&zoom=18&lat=' + lat + '&lon=' + lng);
      const j = await r.json();
      return j.display_name || null;
    } catch(e){ return null; }
  }

  async function drawRoute(lat, lng){
    try {
      const u = 'https://router.project-osrm.org/route/v1/driving/' + STORE[1] + ',' + STORE[0] + ';' + lng + ',' + lat + '?overview=full&geometries=geojson';
      const j = await (await fetch(u)).json();
      if (!j.routes || !j.routes[0]) return null;
      const pts = j.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
      if (routeLine) map.removeLayer(routeLine);
      routeLine = L.polyline(pts, { color: '#1E63D6', weight: 5, opacity: .9, dashArray: '2 10', lineCap: 'round' }).addTo(map);
      map.fitBounds(routeLine.getBounds(), { padding: [40, 40] });
      return { km: j.routes[0].distance / 1000, min: Math.max(1, Math.round(j.routes[0].duration / 60)) };
    } catch(e){ return null; }
  }

  async function choose(lat, lng, opts){
    opts = opts || {};
    const mine = ++seq;
    $('dest-lat').value = lat.toFixed(7);
    $('dest-lng').value = lng.toFixed(7);

    if (!pin) {
      pin = L.marker([lat, lng], { icon: icon('📍', 'drop'), draggable: true }).addTo(map);
      pin.on('dragend', () => { const p = pin.getLatLng(); choose(p.lat, p.lng); });
    } else {
      pin.setLatLng([lat, lng]);
    }
    setInfo('<i class="fa-solid fa-spinner fa-spin"></i> Finding address…');

    const [name, route] = await Promise.all([ opts.label ? Promise.resolve(opts.label) : reverse(lat, lng), drawRoute(lat, lng) ]);
    if (mine !== seq) return;                  // a newer pick replaced this one

    // distance for the delivery charge: road km, or straight line x1.3 if routing is down
    const straight = (() => { const R = 6371, r = x => x * Math.PI / 180, dLa = r(lat - STORE[0]), dLn = r(lng - STORE[1]);
      const h = Math.sin(dLa/2)**2 + Math.cos(r(STORE[0])) * Math.cos(r(lat)) * Math.sin(dLn/2)**2; return 2 * R * Math.asin(Math.sqrt(h)); })();
    window.MM_DISTANCE = route ? route.km : straight * 1.3;
    if (typeof paintCheckoutSummary === 'function') paintCheckoutSummary();

    const addr = name || (lat.toFixed(5) + ', ' + lng.toFixed(5));
    $('address').value = addr.length > 250 ? addr.slice(0, 250) : addr;
    setInfo('<i class="fa-solid fa-circle-check"></i> ' + addr + (route ? ' &middot; ' + route.km.toFixed(1) + ' km &middot; about ' + route.min + ' min' : ''), true);
  }

  map.on('click', e => choose(e.latlng.lat, e.latlng.lng));

  // Changing the store moves the start point of the route
  citySel.addEventListener('change', () => {
    STORE = STORES[citySel.value] || STORE;
    storeMarker.setLatLng(STORE);
    storeTip();
    if (pin) {
      const p = pin.getLatLng();
      choose(p.lat, p.lng);                       // redraw route from the new store
    } else {
      map.setView(STORE, 13);
    }
  });

  async function search(){
    const q = $('map-search').value.trim();
    if (!q) return;
    setInfo('<i class="fa-solid fa-spinner fa-spin"></i> Searching…');
    try {
      const j = await (await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=kh&q=' + encodeURIComponent(q))).json();
      if (!j[0]) { setInfo('No result found. Try another name, or tap the map.'); return; }
      map.setView([+j[0].lat, +j[0].lon], 17);
      choose(+j[0].lat, +j[0].lon, { label: j[0].display_name });
    } catch(e){ setInfo('Search failed. Tap the map instead.'); }
  }
  $('map-search-btn').addEventListener('click', search);
  $('map-search').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); search(); } });

  $('map-locate-btn').addEventListener('click', () => {
    if (!navigator.geolocation) { setInfo('Your browser cannot share location.'); return; }
    setInfo('<i class="fa-solid fa-spinner fa-spin"></i> Getting your location…');
    navigator.geolocation.getCurrentPosition(
      p => { map.setView([p.coords.latitude, p.coords.longitude], 17); choose(p.coords.latitude, p.coords.longitude); },
      () => setInfo('Location blocked. Allow it in the browser, or tap the map.'),
      { enableHighAccuracy: true, timeout: 10000 }
    );
  });

  // Make the payload carry the coordinates
  window.getPickedCoords = () => {
    const lat = parseFloat($('dest-lat').value), lng = parseFloat($('dest-lng').value);
    return (isNaN(lat) || isNaN(lng)) ? {} : { dest_lat: lat, dest_lng: lng };
  };
  setTimeout(() => map.invalidateSize(), 200);
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MINI-MART-SYSTEM\frontend\views/checkout/index.blade.php ENDPATH**/ ?>