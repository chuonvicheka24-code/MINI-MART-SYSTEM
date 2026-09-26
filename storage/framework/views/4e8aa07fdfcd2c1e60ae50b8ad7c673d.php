

<?php $__env->startSection('content'); ?>
<style>
  .web-order-container {
    max-width: 1140px;
    margin: 0 auto;
    font-family: system-ui, -apple-system, sans-serif;
  }

  /* Stepper Header Card */
  .order-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }
  .card-header-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .stepper-line {
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 0 40px;
    margin-top: 15px;
  }
  .stepper-line::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 80px;
    right: 80px;
    height: 3px;
    background-color: #e2e8f0;
    z-index: 1;
  }
  .stepper-progress {
    position: absolute;
    top: 20px;
    left: 80px;
    height: 3px;
    background-color: #10b981;
    z-index: 1;
    transition: width 0.3s ease;
  }
  .step-item {
    position: relative;
    z-index: 2;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .step-node {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background-color: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border: 3px solid #ffffff;
    box-shadow: 0 0 0 1px #cbd5e1;
  }
  .step-node.active {
    background-color: #10b981;
    color: #ffffff;
    box-shadow: 0 0 0 1px #10b981;
  }
  .step-label {
    font-size: 13px;
    color: #334155;
    font-weight: 600;
    margin-top: 8px;
  }

  /* Items List Styling */
  .item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
  }
  .item-row:last-child {
    border-bottom: none;
  }
  .item-info {
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .item-img {
    width: 56px;
    height: 56px;
    border-radius: 8px;
    object-fit: cover;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
  }
  .item-name {
    font-weight: 600;
    font-size: 15px;
    color: #0f172a;
  }
  .item-qty {
    font-size: 13px;
    color: #64748b;
  }

  /* Summary Table Key-Value Rows */
  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
  }
  .info-label {
    color: #64748b;
  }
  .info-value {
    color: #0f172a;
    font-weight: 600;
    text-align: right;
  }
  .total-row {
    border-top: 2px dashed #e2e8f0;
    margin-top: 12px;
    padding-top: 12px;
  }
</style>

<div class="container py-4 web-order-container">
    
    <?php
      // Step calculations
      $st = $order->status;
      $step = match($st) {
        'pending' => 1,
        'out' => 2,
        'done' => 3,
        default => 1,
      };

      // Vehicle transport display settings
      $isTruck = strtolower($order->transport_type ?? '') === 'truck';
      $deliveryIcon = $isTruck ? 'fa-truck' : 'fa-motorcycle';
      $deliveryLabel = $isTruck 
        ? 'Truck' . ($order->truck_number ? ' (' . $order->truck_number . ')' : '')
        : 'Motorcycle';
    ?>

    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a;">Order #<?php echo e($order->id); ?></h4>
            <span class="text-muted small">Placed on <?php echo e($order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A'); ?></span>
        </div>
        <div>
            <span class="badge bg-success px-3 py-2 fs-6"><?php echo e(ucfirst($order->status_label ?? $order->status)); ?></span>
        </div>
    </div>

    
    <div class="order-card">
        <div class="card-header-title">
            <i class="fa-solid fa-truck-fast text-success"></i> Delivery Status
        </div>
        <div class="stepper-line">
            <div class="stepper-progress" style="width: <?php echo e(($step - 1) * 50); ?>%;"></div>
            
            <div class="step-item">
                <div class="step-node <?php echo e($step >= 1 ? 'active' : ''); ?>">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <span class="step-label">Order Placed</span>
            </div>

            <div class="step-item">
                <div class="step-node <?php echo e($step >= 2 ? 'active' : ''); ?>">
                    <i class="fa-solid <?php echo e($deliveryIcon); ?>"></i>
                </div>
                <span class="step-label">
                    Out for Delivery <?php echo e($deliveryLabel); ?>

                </span>
            </div>

            <div class="step-item">
                <div class="step-node <?php echo e($step >= 3 ? 'active' : ''); ?>">
                    <i class="fa-solid fa-house-circle-check"></i>
                </div>
                <span class="step-label">Delivered</span>
            </div>
        </div>
    </div>

    
    <div class="row">
        
        
        <div class="col-lg-7">
            <div class="order-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-bag-shopping text-success"></i> Order Items
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $productName = $item->product->name ?? $item->product_name ?? 'Product';
                    $itemQty = (float)($item->qty ?? 1);
                    $itemPrice = (float)($item->price ?? ($item->product->salePrice ?? $item->product->price ?? 0));
                    $lineTotal = $itemPrice * $itemQty;
                    $itemUnit = $item->unit ?? $item->product->unit ?? '';

                    $img = $item->product->image ?? $item->image ?? null;
                    if ($img) {
                        if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
                            $imgSrc = $img;
                        } elseif (\Illuminate\Support\Str::startsWith($img, 'storage/') || \Illuminate\Support\Str::startsWith($img, '/storage/')) {
                            $imgSrc = asset($img);
                        } elseif (\Illuminate\Support\Str::startsWith($img, 'Photo/')) {
                            $imgSrc = asset($img);
                        } else {
                            $imgSrc = asset('storage/' . ltrim($img, '/'));
                        }
                    } else {
                        $imgSrc = null;
                    }
                ?>
                <div class="item-row">
                    <div class="item-info">
                        <?php if($imgSrc): ?>
                            <img src="<?php echo e($imgSrc); ?>" 
                                 class="item-img" 
                                 alt="<?php echo e($productName); ?>"
                                 onerror="this.onerror=null; this.src='https://placehold.co/56x56?text=No+Img';">
                        <?php else: ?>
                            <div class="item-img d-flex align-items-center justify-content-center bg-light text-muted">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        <?php endif; ?>

                        <div>
                            <div class="item-name"><?php echo e($productName); ?></div>
                            <div class="item-qty">Quantity: <?php echo e($itemQty); ?> <?php echo e($itemUnit); ?></div>
                        </div>
                    </div>
                    <div class="fw-bold" style="color: #0f172a;">
                        $<?php echo e(number_format($lineTotal, 2)); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-muted mb-0">No items found in this order.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="col-lg-5">
            
            
            <div class="order-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-location-dot text-success"></i> Delivery Details
                </div>
                <div class="info-row">
                    <span class="info-label">Customer Name</span>
                    <span class="info-value"><?php echo e($order->customer_name ?: 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value"><?php echo e($order->customer_phone ?: 'N/A'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address</span>
                    <span class="info-value"><?php echo e($order->address ?: ($order->location ?: 'Phnom Penh')); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transport Type</span>
                    <span class="info-value"><?php echo e(ucfirst($order->transport_type ?: 'Motorcycle')); ?></span>
                </div>
                <?php if($order->deliveryStaff): ?>
                <div class="info-row">
                    <span class="info-label">Delivery Staff</span>
                    <span class="info-value"><?php echo e($order->deliveryStaff->name); ?></span>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="order-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-receipt text-success"></i> Payment Summary
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value"><?php echo e(strtoupper($order->payment_method ?: 'ABA PAY')); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Subtotal</span>
                    <span class="info-value">$<?php echo e(number_format($order->subtotal ?? 0, 2)); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Delivery Fee</span>
                    <span class="info-value">$<?php echo e(number_format($order->delivery_fee ?? 0, 2)); ?></span>
                </div>
                <div class="info-row total-row">
                    <span class="fw-bold fs-5" style="color: #0f172a;">Total Amount</span>
                    <span class="fw-bold fs-5 text-success">$<?php echo e(number_format($order->total ?? 0, 2)); ?></span>
                </div>
                <div class="info-row" style="font-size: 12px; color: #64748b;">
                    <span>Exchange Rate (1 USD = 4,050 KHR)</span>
                    <span>៛<?php echo e(number_format(($order->total ?? 0) * 4050, 0)); ?> KHR</span>
                </div>
            </div>

        </div>

    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/orders/show.blade.php ENDPATH**/ ?>