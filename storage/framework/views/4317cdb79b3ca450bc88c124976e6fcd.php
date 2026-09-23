<?php $__env->startSection('title', 'Your cart — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-head">
  <div class="wrap">
    <h1>Your Cart</h1>
    <p>Review quantities and cost before proceeding to delivery.</p>
  </div>
</div>

<section class="section">
  <div class="wrap cart-layout" id="cart-wrap">

    <!-- Products Table -->
    <div class="card">
      <table class="order-table" id="order-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="order-body"></tbody>
      </table>
      <p id="empty-cart" style="display:none; margin-top:8px;">
        Your basket is empty. 
        <a href="<?php echo e(route('products.index')); ?>" style="text-decoration:underline; color:var(--brand-dark); font-weight:600;">Go find something fresh →</a>
      </p>
    </div>

    <!-- Cleaned Summary Card -->
    <div class="cart-summary-card card" style="padding:20px;">
      <h3 style="margin-bottom:15px;">Order Summary</h3>
      
      <div class="summary-row" style="display:flex; justify-content:space-between; margin-bottom:10px;">
        <span>Items subtotal</span>
        <strong id="sum-subtotal">$0.00</strong>
      </div>

      <div class="summary-row" style="display:flex; justify-content:space-between; margin-bottom:10px; color:var(--muted); font-size:14px;">
        <span>Estimated delivery (10%)</span>
        <span id="sum-delivery">$0.00</span>
      </div>

      <hr style="margin: 15px 0; border:0; border-top: 1px solid #eee;">

      <div class="summary-row total" style="display:flex; justify-content:space-between; margin-bottom:20px; font-size:18px;">
        <span>Total</span>
        <strong id="sum-total" style="color:#1b4d3e;">$0.00</strong>
      </div>

      <a href="<?php echo e(route('checkout.index')); ?>" id="checkout-btn" class="btn-checkout" style="display:block; width:100%; text-align:center; background:#1b4d3e; color:#fff; padding:12px; border-radius:8px; font-weight:700; text-decoration:none;">
        Confirm & Proceed to Delivery
      </a>
      
      <p class="form-note" style="margin-top:12px; font-size:12px; color:var(--muted); text-align:center;">
        Delivery fee is calculated as 10% of total cost and confirmed again at checkout.
      </p>
    </div>

  </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  window.MM_PRODUCTS = <?php echo json_encode(\App\Models\Product::all()->map->toStorefrontArray(), 15, 512) ?>;

  function renderCart(){
    const lines = cartLines();
    const body = document.getElementById("order-body");
    document.getElementById("empty-cart").style.display = lines.length ? "none" : "block";
    document.getElementById("order-table").style.display = lines.length ? "table" : "none";
    
    // Toggle right summary card visibility
    const summaryCard = document.querySelector(".cart-summary-card");
    if (summaryCard) {
      summaryCard.style.display = lines.length ? "block" : "none";
    }

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
            <input 
              type="number" 
              value="${l.qty}" 
              min="1" 
              style="width: 55px; text-align: center; font-weight: 600; border: 1px solid var(--line, #e5e7eb); border-radius: 4px; padding: 4px;"
              onchange="setManualQty(${l.id}, this.value)"
            >
            <button onclick="changeQty(${l.id}, 1)" aria-label="Increase quantity">+</button>
          </div>
        </td>
        <td>${money(l.product.salePrice * l.qty)}</td>
        <td><a class="remove-link" href="#" onclick="removeFromCart(${l.id}).then(renderCart); return false;" aria-label="Remove"><i class="fa-solid fa-trash"></i></a></td>
      </tr>
    `).join("");

    const subtotal = cartSubtotal();
    const delivery = subtotal * (window.DELIVERY_RATE || 0.1);
    
    document.getElementById("sum-subtotal").textContent = money(subtotal);
    document.getElementById("sum-delivery").textContent = money(delivery);
    document.getElementById("sum-total").textContent = money(subtotal + delivery);
    
    const checkoutBtn = document.getElementById("checkout-btn");
    if (checkoutBtn) {
      checkoutBtn.style.pointerEvents = lines.length ? "auto" : "none";
      checkoutBtn.style.opacity = lines.length ? "1" : ".5";
    }
  }

  function changeQty(id, delta){
    const line = cartLines().find(l => l.id === id);
    const newQty = line ? line.qty + delta : 0;
    setCartQty(id, newQty).then(renderCart);
  }

  // Handle direct typed values in the quantity field
  function setManualQty(id, value){
    let newQty = parseInt(value) || 1;
    if (newQty < 1) newQty = 1;
    setCartQty(id, newQty).then(renderCart);
  }

  refreshCartCache().then(renderCart);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/cart/index.blade.php ENDPATH**/ ?>