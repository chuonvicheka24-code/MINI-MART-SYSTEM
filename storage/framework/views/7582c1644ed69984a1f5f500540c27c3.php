<?php $__env->startSection('content'); ?>
<style>
   /* Back Button Styling */
    .btn-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #ffffff;
  color: #0f172a;
  font-weight: 600;
  font-size: 13.5px;
  padding: 8px 16px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  text-decoration: none;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  margin-bottom: 16px;
  transition: all 0.2s ease;
}

.btn-back:hover {
  background: #f8fafc;
  color: #10b981;
  border-color: #cbd5e1;
}

  /* Status Badges */
  .badge-status {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    letter-spacing: 0.2px;
  }

  .badge-status.status-delivered {
    background-color: #fef3c7;
    color: #92400e;
  }

  .badge-status.status-active {
    background-color: #d1fae5;
    color: #065f46;
  }
  /* Web Order Container */
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

  /* Stepper Outer Line Container */
  .stepper-line {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-top: 15px;
    position: relative;
  }

  .step-item {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
  }

  .step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 21px;
    left: 50%;
    width: 100%;
    height: 3px;
    background-color: #e2e8f0;
    z-index: 1;
  }

  .step-item.line-active:not(:last-child)::after {
    background-color: #10b981;
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
    flex-shrink: 0;
    position: relative;
    z-index: 2;
  }

  .step-node.active {
    background-color: #10b981;
    color: #ffffff;
    box-shadow: 0 0 0 1px #10b981;
  }

  .step-label {
    font-size: 12.5px;
    color: #334155;
    font-weight: 600;
    margin-top: 8px;
    max-width: 120px;
    line-height: 1.35;
    text-align: center;
    word-break: break-word;
  }

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
    width: 56px !important;
    height: 56px !important;
    max-width: 56px !important;
    max-height: 56px !important;
    border-radius: 8px;
    object-fit: cover;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
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


  /* ---------- Live delivery tracking map (Wownow-style) ---------- */
  .track-wrap { position: relative; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
  #track-map { width: 100%; height: 420px; background: #e5e7eb; }
  .track-sheet {
    position: absolute; left: 14px; right: 14px; bottom: 14px; z-index: 1100;
    background: #fff; border-radius: 16px; padding: 14px 16px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, .18);
  }
  .track-eta { font-size: 17px; font-weight: 700; color: #0f172a; }
  .track-sub { font-size: 12.5px; color: #64748b; margin-top: 2px; }
  .track-bar { height: 5px; background: #e2e8f0; border-radius: 99px; margin: 10px 0 12px; overflow: hidden; }
  .track-bar > div { height: 100%; width: 0; background: #10b981; border-radius: 99px; transition: width .8s linear; }
  .track-driver { display: flex; align-items: center; gap: 12px; }
  .track-avatar {
    width: 42px; height: 42px; border-radius: 50%; background: #d1fae5; color: #065f46;
    display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
  }
  .track-driver-name { font-weight: 700; font-size: 14.5px; color: #0f172a; }
  .track-driver-veh { font-size: 12.5px; color: #64748b; }
  .track-call {
    margin-left: auto; width: 42px; height: 42px; border-radius: 50%; background: #10b981; color: #fff;
    display: flex; align-items: center; justify-content: center; text-decoration: none; flex-shrink: 0;
  }
  .pin { font-size: 26px; line-height: 1; filter: drop-shadow(0 2px 3px rgba(0,0,0,.35)); }
  .pin-driver { font-size: 30px; }
  @media (max-width: 640px) { #track-map { height: 360px; } }

  /* Print Styles */
  @media print {
    header.site, footer.site, .btn-back, .no-print {
      display: none !important;
    }
    body {
      background: #ffffff !important;
    }
    .web-order-container {
      max-width: 100% !important;
      padding: 0 !important;
    }
    .order-card {
      border: none !important;
      box-shadow: none !important;
      padding: 10px 0 !important;
    }
  }

  @media (max-width: 640px) {
    .step-label {
      font-size: 11px;
      max-width: 85px;
    }
  }
</style>

<div class="container py-4 web-order-container">
    
<?php
  $st = strtolower($order->status ?? 'pending');
  $step = match($st) {
    'pending', 'placed' => 1,
    'out', 'delivering', 'out_for_delivery' => 2,
    'done', 'delivered', 'completed' => 3,
    default => 1,
  };

  $isTruck = strtolower($order->transport_type ?? '') === 'truck';
  $deliveryIcon = $isTruck ? 'fa-truck' : 'fa-motorcycle';
  $deliveryLabel = $isTruck 
    ? 'Truck' . ($order->truck_number ? ' (' . $order->truck_number . ')' : '')
    : 'Motorcycle';
?>

    
    <div class="order-header-wrapper mb-4">
        
        <div class="mb-3">
            <a href="<?php echo e(url()->previous() != url()->current() ? url()->previous() : route('orders.index')); ?>" class="btn-back">
              <i class="fa-solid fa-arrow-left"></i>
              <span>Back to Orders</span>
            </a>
        </div>

        
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="color: #0f172a; font-size: 24px;">Order #<?php echo e($order->id); ?></h3>
                <p class="text-muted mb-0" style="font-size: 14px;">
                    Placed on <?php echo e($order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A'); ?>

                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                
                <div id="order-badge-live" data-live>
                    <span class="badge-status <?php echo e($step == 3 ? 'status-delivered' : 'status-active'); ?>">
                        <?php echo e(ucfirst($order->status_label ?? $order->status ?? 'Pending')); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="order-card no-print" id="order-stepper-live" data-live>
        <div class="card-header-title">
            <i class="fa-solid fa-truck-fast text-success"></i> Delivery Status
        </div>
        
        <div class="stepper-line">
            <!-- Step 1 -->
            <div class="step-item <?php echo e($step >= 2 ? 'line-active' : ''); ?>">
                <div class="step-node <?php echo e($step >= 1 ? 'active' : ''); ?>">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <span class="step-label">Order Placed</span>
            </div>

            <!-- Step 2 -->
            <div class="step-item <?php echo e($step >= 3 ? 'line-active' : ''); ?>">
                <div class="step-node <?php echo e($step >= 2 ? 'active' : ''); ?>">
                    <i class="fa-solid <?php echo e($deliveryIcon); ?>"></i>
                </div>
                <span class="step-label">
                    Out for Delivery<br>
                    <small style="font-size: 11px; color: #64748b; font-weight: 500;"><?php echo e($deliveryLabel); ?></small>
                </span>
            </div>

            <!-- Step 3 -->
            <div class="step-item">
                <div class="step-node <?php echo e($step >= 3 ? 'active' : ''); ?>">
                    <i class="fa-solid fa-house-circle-check"></i>
                </div>
                <span class="step-label">Delivered</span>
            </div>
        </div>
    </div>


    
    <?php
      $mapQuery = trim(($order->address ?: $order->location ?: 'Phnom Penh') . ', Phnom Penh, Cambodia');
      $driverName  = $order->deliveryStaff->name  ?? 'Delivery Staff';
      $driverPhone = $order->deliveryStaff->phone ?? null;
    ?>
    <div class="order-card no-print" id="track-card" style="<?php echo e($step === 2 ? '' : 'display:none'); ?>">
      <div class="card-header-title">
        <i class="fa-solid fa-map-location-dot text-success"></i> Track Your Delivery
      </div>
      <div class="track-wrap">
        <div id="track-map"></div>
        <div class="track-sheet">
          <div class="track-eta" id="track-eta">Finding route…</div>
          <div class="track-sub" id="track-sub">Your order is on the way</div>
          <div class="track-bar"><div id="track-progress"></div></div>
          <div class="track-driver">
            <div class="track-avatar"><i class="fa-solid <?php echo e($deliveryIcon); ?>"></i></div>
            <div>
              <div class="track-driver-name"><?php echo e($driverName); ?></div>
              <div class="track-driver-veh"><?php echo e($deliveryLabel); ?></div>
            </div>
            <?php if($driverPhone): ?>
              <a class="track-call" href="tel:<?php echo e(preg_replace('/\s+/', '', $driverPhone)); ?>" title="Call driver">
                <i class="fa-solid fa-phone"></i>
              </a>
            <?php endif; ?>
          </div>
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

                    $imgSrc = $item->product->image_url ?? null;
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
            
            
            <div class="order-card" id="order-delivery-live" data-live>
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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div class="card-header-title mb-0" style="margin-bottom: 0 !important;">
                        <i class="fa-solid fa-receipt text-success"></i> Payment Summary
                    </div>
                    
                    
                    <button type="button" 
                            onclick="openInvoiceModal()" 
                            class="btn btn-sm btn-outline-success no-print" 
                            style="font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 8px; border: 1.5px solid #10b981; color: #065f46; background: #d1fae5; cursor: pointer;">
                        <i class="fa-solid fa-file-invoice me-1"></i> View Invoice
                    </button>
                </div>

                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value"><?php echo e(strtoupper($order->payment_method ?: 'CASH ON DELIVERY')); ?></span>
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


<div id="invoiceModal" class="no-print" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 720px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
<!--         
        
        <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h5 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 16px;">
                <i class="fa-solid fa-file-invoice text-success me-2"></i> Invoice #<?php echo e($order->id); ?>

            </h5>
            <button onclick="closeInvoiceModal()" style="border: none; background: transparent; font-size: 22px; cursor: pointer; color: #64748b;">&times;</button>
        </div> -->

        
        <div id="printableInvoice" style="padding: 32px; font-family: system-ui, -apple-system, sans-serif; background: #fff; color: #000;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0 0 2px 0; font-weight: 800; font-size: 26px; letter-spacing: -0.5px;">
                        <span style="color: #0f172a;">Mini</span><span style="color: #e11d48;">Mart</span>
                    </h3>
                    <div style="color: #64748b; font-size: 14px;">Veng Sreng Blvd, Phnom Penh</div>
                </div>
                <div style="text-align: right;">
                    <h4 style="margin: 0 0 2px 0; font-weight: 800; color: #0f172a; font-size: 18px;">Invoice #<?php echo e($order->id); ?></h4>
                    <div style="color: #64748b; font-size: 13px;"><?php echo e($order->created_at ? $order->created_at->format('D, M d, Y, g:i A') : 'N/A'); ?></div>
                </div>
            </div>

            <div style="border-bottom: 1px solid #d1d5db; margin-bottom: 20px;"></div>

            <div style="margin-bottom: 24px; font-size: 14.5px; color: #000;">
                <strong>Deliver via:</strong> <?php echo e(ucfirst($order->transport_type ?: 'Truck')); ?> &middot; 
                <strong>Pay by:</strong> <?php echo e(ucwords(str_replace('_', ' ', $order->payment_method ?: 'Cash on delivery'))); ?>

            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14.5px;">
                <thead>
                    <tr>
                        <th style="padding: 10px 0; text-align: left; color: #000; font-weight: 700;">Item</th>
                        <th style="padding: 10px 0; text-align: center; color: #000; font-weight: 700;">Qty</th>
                        <th style="padding: 10px 0; text-align: right; color: #000; font-weight: 700;">Price</th>
                        <th style="padding: 10px 0; text-align: right; color: #000; font-weight: 700;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $pName = $item->product->name ?? $item->product_name ?? 'Product';
                        $pQty = (float)($item->qty ?? 1);
                        $pPrice = (float)($item->price ?? ($item->product->salePrice ?? $item->product->price ?? 0));
                        $pUnit = $item->unit ?? $item->product->unit ?? '';
                        $lineTotal = $pPrice * $pQty;
                    ?>
                    <tr>
                        <td style="padding: 12px 0; color: #000;"><?php echo e($pName); ?></td>
                        <td style="padding: 12px 0; text-align: center; color: #000;"><?php echo e($pQty); ?> <?php echo e($pUnit); ?></td>
                        <td style="padding: 12px 0; text-align: right; color: #000;">$<?php echo e(number_format($pPrice, 2)); ?></td>
                        <td style="padding: 12px 0; text-align: right; color: #000;">$<?php echo e(number_format($lineTotal, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <div style="border-bottom: 1px solid #d1d5db; margin-bottom: 16px;"></div>

            <div style="font-size: 14.5px;">
                <div style="display: flex; justify-content: space-between; padding: 6px 0; color: #000;">
                    <span>Items subtotal</span>
                    <span>$<?php echo e(number_format($order->subtotal ?? 0, 2)); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 6px 0; color: #000;">
                    <span>Delivery Fee (10%)</span>
                    <span>$<?php echo e(number_format($order->delivery_fee ?? 0, 2)); ?></span>
                </div>
            </div>

            <div style="border-bottom: 1px solid #d1d5db; margin: 16px 0;"></div>

            <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 800; font-size: 20px; color: #000; padding-top: 4px;">
                <span>Total paid</span>
                <span>$<?php echo e(number_format($order->total ?? 0, 2)); ?></span>
            </div>
        </div>

        
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <button onclick="closeInvoiceModal()" style="padding: 8px 18px; border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; font-weight: 600; color: #475569; cursor: pointer;">
                Close
            </button>
            <div style="display: flex; gap: 10px;">
                
                <button onclick="printModalInvoice()" style="padding: 8px 18px; border: 1.5px solid #10b981; background: #ffffff; color: #065f46; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-download"></i> Download PDF
                </button>
                
                <button onclick="printModalInvoice()" style="padding: 8px 20px; border: none; background: #1E5C34; color: #fff; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-print"></i> Print Invoice
                </button>
            </div>
        </div>
    </div>
</div>


<script>
function openInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'flex';
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'none';
}

function printModalInvoice() {
    var printContents = document.getElementById('printableInvoice').innerHTML;
    var originalContents = document.body.innerHTML;
    
    document.body.innerHTML = '<div style="padding: 20px;">' + printContents + '</div>';
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
  const ORDER_ID   = <?php echo e($order->id); ?>;
  const STATUS_URL = <?php echo json_encode(route('orders.status'), 15, 512) ?>;
  const ARRIVED_URL = <?php echo json_encode(route('orders.arrived', $order->id), 512) ?>;
  const ADDRESS    = <?php echo json_encode($mapQuery, 15, 512) ?>;
  const IS_TRUCK   = <?php echo json_encode($isTruck, 15, 512) ?>;

  // Store location (lat, lng) — edit to your real store coordinates.
  const STORE = [11.5527, 104.8337];

  // 1 = real time. >1 makes the driver move faster so you can see it while testing.
  const SPEED = 6;

  const card = document.getElementById('track-card');
  let step      = <?php echo e($step); ?>;
  let startedAt = <?php echo e($step === 2 ? (optional($order->updated_at)->timestamp ?? 'null') : 'null'); ?>;
  startedAt = startedAt ? startedAt * 1000 - (Date.now() - <?php echo e(now()->timestamp); ?> * 1000) : null; // fix client/server clock gap

  let map = null, driver = null, route = [], cum = [], totalM = 0, totalSec = 0, timer = null;

  const el = id => document.getElementById(id);
  const pin = (emoji, cls) => L.divIcon({ className: '', html: '<div class="pin ' + (cls || '') + '">' + emoji + '</div>', iconSize: [30, 30], iconAnchor: [15, 28] });

  async function geocode() {
    try {
      const r = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=kh&q=' + encodeURIComponent(ADDRESS));
      const j = await r.json();
      if (j[0]) return [parseFloat(j[0].lat), parseFloat(j[0].lon)];
    } catch (e) {}
    return [STORE[0] + 0.02, STORE[1] + 0.02]; // fallback so the demo still works
  }

  async function getRoute(a, b) {
    try {
      const u = 'https://router.project-osrm.org/route/v1/driving/' + a[1] + ',' + a[0] + ';' + b[1] + ',' + b[0] + '?overview=full&geometries=geojson';
      const r = await fetch(u);
      const j = await r.json();
      if (j.routes && j.routes[0]) {
        return { pts: j.routes[0].geometry.coordinates.map(c => [c[1], c[0]]), sec: j.routes[0].duration };
      }
    } catch (e) {}
    return { pts: [a, b], sec: 600 }; // straight line fallback
  }

  function pointAt(frac) {
    const target = totalM * frac;
    for (let i = 1; i < cum.length; i++) {
      if (cum[i] >= target) {
        const seg = cum[i] - cum[i - 1] || 1;
        const t = (target - cum[i - 1]) / seg;
        return [route[i - 1][0] + (route[i][0] - route[i - 1][0]) * t, route[i - 1][1] + (route[i][1] - route[i - 1][1]) * t];
      }
    }
    return route[route.length - 1];
  }

  let arrivedSent = false;

  async function completeDelivery() {
    if (arrivedSent) return;
    arrivedSent = true;
    clearInterval(timer);
    el('track-eta').textContent = 'Delivered 🎉';
    el('track-sub').textContent = 'Your order has arrived. Enjoy!';
    el('track-progress').style.width = '100%';
    try {
      const r = await fetch(ARRIVED_URL, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': window.MM_CSRF, 'Accept': 'application/json' },
      });
      const d = await r.json();
      if (d.status === 'done') { step = 3; setTimeout(() => location.reload(), 2500); return; }
    } catch (e) {}
    arrivedSent = false;            // server said "too early" or network hiccup: try again
    timer = setInterval(tick, 3000);
  }

  function tick() {
    if (!driver || !startedAt || arrivedSent) return;
    const elapsed = (Date.now() - startedAt) / 1000 * SPEED;
    const frac = Math.min(1, elapsed / totalSec);
    driver.setLatLng(pointAt(frac));
    el('track-progress').style.width = Math.round(frac * 100) + '%';
    if (frac >= 1) { completeDelivery(); return; }
    const minLeft = Math.max(1, Math.ceil((totalSec * (1 - frac)) / 60));
    el('track-eta').textContent = 'Arriving in ' + minLeft + ' min';
    el('track-sub').textContent = 'Your order is on the way';
  }

  async function startMap() {
    if (map || typeof L === 'undefined') return;
    card.style.display = '';
    if (!startedAt) startedAt = Date.now();

    map = L.map('track-map', { zoomControl: true, attributionControl: true }).setView(STORE, 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

    const dest = await geocode();
    const r = await getRoute(STORE, dest);
    route = r.pts; totalSec = r.sec;

    cum = [0];
    for (let i = 1; i < route.length; i++) cum[i] = cum[i - 1] + map.distance(route[i - 1], route[i]);
    totalM = cum[cum.length - 1] || 1;

    L.polyline(route, { color: '#10b981', weight: 6, opacity: .9 }).addTo(map);
    L.marker(STORE, { icon: pin('🏪') }).addTo(map);
    L.marker(dest,  { icon: pin('🏠') }).addTo(map);
    driver = L.marker(route[0], { icon: pin(IS_TRUCK ? '🚚' : '🛵', 'pin-driver'), zIndexOffset: 1000 }).addTo(map);

    map.fitBounds(L.polyline(route).getBounds(), { paddingTopLeft: [30, 30], paddingBottomRight: [30, 190] });
    setTimeout(() => map.invalidateSize(), 200);

    tick();
    timer = setInterval(tick, 1000);
  }

  function stopMap() {
    clearInterval(timer);
    card.style.display = 'none';
  }

  async function poll() {
    try {
      const r = await fetch(STATUS_URL, { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } });
      const d = await r.json();
      const st = d.orders && d.orders[ORDER_ID];
      if (st === 'out' && step !== 2) { step = 2; startMap(); }
      if (st === 'done' && step === 2) { step = 3; stopMap(); }
    } catch (e) {}
  }

  if (step === 2) startMap();
  setInterval(poll, 5000);
})();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MINI-MART-SYSTEM\resources\views/orders/show.blade.php ENDPATH**/ ?>