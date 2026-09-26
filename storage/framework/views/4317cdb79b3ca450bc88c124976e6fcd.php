<?php $__env->startSection('title', 'Your cart — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>

<style>
  /* Styles for the Past Orders Section */
  .orders-history-section {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px dashed #e5e7eb;
  }

  .history-title {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 20px;
  }

  .history-order-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .history-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f3f4f6;
  }

  .history-store-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 15px;
    color: #111827;
  }

  .history-store-icon {
    width: 24px;
    height: 24px;
    background-color: #e11d48;
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
  }

  .history-status-badge {
    font-size: 13px;
    font-weight: 600;
  }
  .status-done { color: #059669; }
  .status-out { color: #0284c7; }
  .status-pending { color: #d97706; }

  .history-items-row {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding-bottom: 10px;
    margin-bottom: 12px;
  }

  .history-item-box {
    width: 80px;
    flex-shrink: 0;
  }

  .history-item-img {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    background-color: #f3f4f6;
    border: 1px solid #f3f4f6;
  }

  .history-item-name {
    font-size: 12px;
    color: #374151;
    margin-top: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .history-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px dashed #f3f4f6;
  }

  .history-meta {
    font-size: 13px;
    color: #6b7280;
  }

  .btn-history-detail {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    border: 1px solid #e11d48;
    color: #e11d48;
    background: transparent;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.2s;
  }

  .btn-history-detail:hover {
    background-color: #e11d48;
    color: #ffffff;
  }
</style>

<div class="page-head">
  <div class="wrap">
    <h1>Your Cart</h1>
    <p>Review quantities and cost before proceeding to delivery.</p>
  </div>
</div>

<section class="section">
  <div class="wrap" style="max-width: 1100px; margin: 0 auto; padding: 0 15px;">

    <!-- PART 1: SHOPPING CART -->
    <div class="cart-layout" id="cart-wrap">
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

      <!-- Summary Card -->
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

    <!-- PART 2: PAST ORDERS HISTORY -->
    <div class="orders-history-section">
      <h2 class="history-title"><i class="fa-solid fa-clock-rotate-left" style="color: #e11d48; margin-right: 8px;"></i>Past Order History</h2>

      <?php if(isset($pastOrders) && $pastOrders->count() > 0): ?>
          <?php $__currentLoopData = $pastOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                  $statusText = match($o->status) {
                      'done' => 'Completed',
                      'out' => 'In Progress',
                      default => 'Pending',
                  };
              ?>

              <div class="history-order-card">
                  <div class="history-card-header">
                      <div class="history-store-title">
                          <div class="history-store-icon"><i class="fa-solid fa-store"></i></div>
                          Mini Mart Store
                          <i class="fa-solid fa-chevron-right" style="font-size: 11px; color: #9ca3af;"></i>
                      </div>
                      <span class="history-status-badge status-<?php echo e($o->status); ?>">
                          <?php echo e($statusText); ?>

                      </span>
                  </div>

                  <div class="history-items-row">
                      <?php $__currentLoopData = $o->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php
                              $img = $item->product->image ?? 'images/placeholder.jpg';
                              $imgUrl = \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) 
                                  ? $img 
                                  : asset($img);
                          ?>
                          <div class="history-item-box">
                              <img src="<?php echo e($imgUrl); ?>" class="history-item-img" alt="<?php echo e($item->product_name ?? 'Product'); ?>" onerror="this.onerror=null; this.src='https://placehold.co/80x80?text=Item';">
                              <div class="history-item-name"><?php echo e($item->product_name ?? ($item->product->name ?? 'Item')); ?></div>
                          </div>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>

                  <div class="history-card-footer">
                      <div class="history-meta">
                          <?php echo e($o->placed_at ? $o->placed_at->format('d/m/Y H:i') : ($o->created_at ? $o->created_at->format('d/m/Y H:i') : '')); ?>

                          <span style="margin-left: 10px;">Total: <strong style="color: #111827;">$<?php echo e(number_format($o->total, 2)); ?></strong></span>
                      </div>

                      <a href="<?php echo e(route('orders.show', $o->id)); ?>" class="btn-history-detail">
                          View Order Details
                      </a>
                  </div>
              </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php else: ?>
          <div style="text-align: center; padding: 40px 0; background: #fff; border-radius: 12px; border: 1px solid #e5e7eb;">
              <i class="fa-solid fa-receipt" style="font-size: 36px; color: #9ca3af; margin-bottom: 10px;"></i>
              <p style="color: #6b7280; font-size: 14px; margin: 0;">No past orders found in your history.</p>
          </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  window.MM_PRODUCTS = <?php echo json_encode(\App\Models\Product::all()->map->toStorefrontArray(), 15, 512) ?>;

  function renderCart(){
    // Safety check to ensure cartLines is available
    const rawLines = (typeof cartLines === 'function') ? cartLines() : [];
    
    // Filter out invalid or missing items so JS map doesn't fail
    const lines = rawLines.filter(l => l && (l.product || l.product_id));

    const body = document.getElementById("order-body");
    const emptyCartMsg = document.getElementById("empty-cart");
    const orderTable = document.getElementById("order-table");
    const summaryCard = document.querySelector(".cart-summary-card");

    if (emptyCartMsg) emptyCartMsg.style.display = lines.length ? "none" : "block";
    if (orderTable) orderTable.style.display = lines.length ? "table" : "none";
    if (summaryCard) summaryCard.style.display = lines.length ? "block" : "none";

    if (!lines.length) return;

    body.innerHTML = lines.map(l => {
      // Fallback product data if product record was newly updated
      const p = l.product || {
        name: 'Item #' + l.id,
        salePrice: l.price || 0,
        unit: 'unit',
        img: 'images/placeholder.jpg'
      };

      const imgSrc = p.img ? (p.img.startsWith('http') ? p.img : '/' + p.img) : 'https://placehold.co/50x50?text=Item';
      const lineTotal = (p.salePrice || 0) * (l.qty || 1);

      return `
        <tr>
          <td>
            <div class="line-item" style="display: flex; align-items: center; gap: 12px;">
              <img src="${imgSrc}" alt="${p.name}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;" onerror="this.onerror=null; this.src='https://placehold.co/50x50?text=Item';">
              <div>
                <div class="line-name" style="font-weight: 600;">${p.name}</div>
                <div class="line-unit" style="font-size: 12px; color: #6b7280;">per ${p.unit || 'item'}</div>
              </div>
            </div>
          </td>
          <td>${money(p.salePrice || 0)}</td>
          <td>
            <div class="qty-control" style="display: flex; align-items: center; gap: 4px;">
              <button onclick="changeQty(${l.id}, -1)" aria-label="Decrease quantity" style="padding: 2px 8px;">−</button>
              <input 
                type="number" 
                value="${l.qty}" 
                min="0.01" 
                step="0.01" 
                style="width: 65px; text-align: center; font-weight: 600; border: 1px solid var(--line, #e5e7eb); border-radius: 4px; padding: 4px;"
                onchange="setManualQty(${l.id}, this.value)"
              >
              <button onclick="changeQty(${l.id}, 1)" aria-label="Increase quantity" style="padding: 2px 8px;">+</button>
            </div>
          </td>
          <td>${money(lineTotal)}</td>
          <td>
            <a class="remove-link" href="#" onclick="removeFromCart(${l.id}).then(renderCart); return false;" aria-label="Remove" style="color: #e11d48;">
              <i class="fa-solid fa-trash"></i>
            </a>
          </td>
        </tr>
      `;
    }).join("");

    const subtotal = (typeof cartSubtotal === 'function') ? cartSubtotal() : 0;
    const delivery = subtotal * (window.DELIVERY_RATE || 0.1);
    
    if (document.getElementById("sum-subtotal")) document.getElementById("sum-subtotal").textContent = money(subtotal);
    if (document.getElementById("sum-delivery")) document.getElementById("sum-delivery").textContent = money(delivery);
    if (document.getElementById("sum-total")) document.getElementById("sum-total").textContent = money(subtotal + delivery);
    
    const checkoutBtn = document.getElementById("checkout-btn");
    if (checkoutBtn) {
      checkoutBtn.style.pointerEvents = lines.length ? "auto" : "none";
      checkoutBtn.style.opacity = lines.length ? "1" : ".5";
    }
  }

  function changeQty(id, delta){
    const line = cartLines().find(l => l.id === id);
    const newQty = line ? Math.max(0.01, parseFloat((line.qty + delta).toFixed(2))) : 0;
    setCartQty(id, newQty).then(renderCart);
  }

  function setManualQty(id, value){
    let newQty = parseFloat(value) || 1;
    if (newQty < 0.01) newQty = 0.01;
    setCartQty(id, parseFloat(newQty.toFixed(2))).then(renderCart);
  }

  // Refresh cart cache and render
  if (typeof refreshCartCache === 'function') {
    refreshCartCache().then(renderCart);
  } else {
    document.addEventListener("DOMContentLoaded", renderCart);
  }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/cart/index.blade.php ENDPATH**/ ?>