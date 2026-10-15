@extends('layouts.app')

@section('content')
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
    
@php
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
@endphp

    {{-- Top Navigation & Order Header --}}
    <div class="order-header-wrapper mb-4">
        {{-- Back Button --}}
        <div class="mb-3">
            <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('orders.index') }}" class="btn-back">
              <i class="fa-solid fa-arrow-left"></i>
              <span>Back to Orders</span>
            </a>
        </div>

        {{-- Order Info & Status Row --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="color: #0f172a; font-size: 24px;">Order #{{ $order->id }}</h3>
                <p class="text-muted mb-0" style="font-size: 14px;">
                    Placed on {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                {{-- Status Badge --}}
                <div id="order-badge-live" data-live>
                    <span class="badge-status {{ $step == 3 ? 'status-delivered' : 'status-active' }}">
                        {{ ucfirst($order->status_label ?? $order->status ?? 'Pending') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stepper Progress Card --}}
    <div class="order-card no-print" id="order-stepper-live" data-live>
        <div class="card-header-title">
            <i class="fa-solid fa-truck-fast text-success"></i> Delivery Status
        </div>
        
        <div class="stepper-line">
            <!-- Step 1 -->
            <div class="step-item {{ $step >= 2 ? 'line-active' : '' }}">
                <div class="step-node {{ $step >= 1 ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <span class="step-label">Order Placed</span>
            </div>

            <!-- Step 2 -->
            <div class="step-item {{ $step >= 3 ? 'line-active' : '' }}">
                <div class="step-node {{ $step >= 2 ? 'active' : '' }}">
                    <i class="fa-solid {{ $deliveryIcon }}"></i>
                </div>
                <span class="step-label">
                    Out for Delivery<br>
                    <small style="font-size: 11px; color: #64748b; font-weight: 500;">{{ $deliveryLabel }}</small>
                </span>
            </div>

            <!-- Step 3 -->
            <div class="step-item">
                <div class="step-node {{ $step >= 3 ? 'active' : '' }}">
                    <i class="fa-solid fa-house-circle-check"></i>
                </div>
                <span class="step-label">Delivered</span>
            </div>
        </div>
    </div>

    {{-- 2-Column Grid Layout --}}
    <div class="row">
        
        {{-- Left: Order Items Column --}}
        <div class="col-lg-7">
            <div class="order-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-bag-shopping text-success"></i> Order Items
                </div>

                @forelse($order->items as $item)
                @php
                    $productName = $item->product->name ?? $item->product_name ?? 'Product';
                    $itemQty = (float)($item->qty ?? 1);
                    $itemPrice = (float)($item->price ?? ($item->product->salePrice ?? $item->product->price ?? 0));
                    $lineTotal = $itemPrice * $itemQty;
                    $itemUnit = $item->unit ?? $item->product->unit ?? '';

                    $imgSrc = $item->product->image_url ?? null;
                @endphp
                <div class="item-row">
                    <div class="item-info">
                        @if($imgSrc)
                            <img src="{{ $imgSrc }}" 
                                 class="item-img" 
                                 alt="{{ $productName }}"
                                 onerror="this.onerror=null; this.src='https://placehold.co/56x56?text=No+Img';">
                        @else
                            <div class="item-img d-flex align-items-center justify-content-center bg-light text-muted">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif

                        <div>
                            <div class="item-name">{{ $productName }}</div>
                            <div class="item-qty">Quantity: {{ $itemQty }} {{ $itemUnit }}</div>
                        </div>
                    </div>
                    <div class="fw-bold" style="color: #0f172a;">
                        ${{ number_format($lineTotal, 2) }}
                    </div>
                </div>
                @empty
                <p class="text-muted mb-0">No items found in this order.</p>
                @endforelse
            </div>
        </div>

        {{-- Right: Delivery Details & Payment Summary Column --}}
        <div class="col-lg-5">
            
            {{-- Delivery Card --}}
            <div class="order-card" id="order-delivery-live" data-live>
                <div class="card-header-title">
                    <i class="fa-solid fa-location-dot text-success"></i> Delivery Details
                </div>
                <div class="info-row">
                    <span class="info-label">Customer Name</span>
                    <span class="info-value">{{ $order->customer_name ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $order->customer_phone ?: 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address</span>
                    <span class="info-value">{{ $order->address ?: ($order->location ?: 'Phnom Penh') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transport Type</span>
                    <span class="info-value">{{ ucfirst($order->transport_type ?: 'Motorcycle') }}</span>
                </div>
                @if($order->deliveryStaff)
                <div class="info-row">
                    <span class="info-label">Delivery Staff</span>
                    <span class="info-value">{{ $order->deliveryStaff->name }}</span>
                </div>
                @endif
            </div>

            {{-- Payment Card with View Invoice Button --}}
            <div class="order-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div class="card-header-title mb-0" style="margin-bottom: 0 !important;">
                        <i class="fa-solid fa-receipt text-success"></i> Payment Summary
                    </div>
                    
                    {{-- View Invoice Button --}}
                    <button type="button" 
                            onclick="openInvoiceModal()" 
                            class="btn btn-sm btn-outline-success no-print" 
                            style="font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 8px; border: 1.5px solid #10b981; color: #065f46; background: #d1fae5; cursor: pointer;">
                        <i class="fa-solid fa-file-invoice me-1"></i> View Invoice
                    </button>
                </div>

                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value">{{ strtoupper($order->payment_method ?: 'CASH ON DELIVERY') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Subtotal</span>
                    <span class="info-value">${{ number_format($order->subtotal ?? 0, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Delivery Fee</span>
                    <span class="info-value">${{ number_format($order->delivery_fee ?? 0, 2) }}</span>
                </div>
                <div class="info-row total-row">
                    <span class="fw-bold fs-5" style="color: #0f172a;">Total Amount</span>
                    <span class="fw-bold fs-5 text-success">${{ number_format($order->total ?? 0, 2) }}</span>
                </div>
                <div class="info-row" style="font-size: 12px; color: #64748b;">
                    <span>Exchange Rate (1 USD = 4,050 KHR)</span>
                    <span>៛{{ number_format(($order->total ?? 0) * 4050, 0) }} KHR</span>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- Invoice Modal Popup --}}
<div id="invoiceModal" class="no-print" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 720px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
<!--         
        {{-- Modal Header --}}
        <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <h5 style="margin: 0; font-weight: 700; color: #0f172a; font-size: 16px;">
                <i class="fa-solid fa-file-invoice text-success me-2"></i> Invoice #{{ $order->id }}
            </h5>
            <button onclick="closeInvoiceModal()" style="border: none; background: transparent; font-size: 22px; cursor: pointer; color: #64748b;">&times;</button>
        </div> -->

        {{-- Printable Invoice Body matching reference style --}}
        <div id="printableInvoice" style="padding: 32px; font-family: system-ui, -apple-system, sans-serif; background: #fff; color: #000;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0 0 2px 0; font-weight: 800; font-size: 26px; letter-spacing: -0.5px;">
                        <span style="color: #0f172a;">Mini</span><span style="color: #e11d48;">Mart</span>
                    </h3>
                    <div style="color: #64748b; font-size: 14px;">Veng Sreng Blvd, Phnom Penh</div>
                </div>
                <div style="text-align: right;">
                    <h4 style="margin: 0 0 2px 0; font-weight: 800; color: #0f172a; font-size: 18px;">Invoice #{{ $order->id }}</h4>
                    <div style="color: #64748b; font-size: 13px;">{{ $order->created_at ? $order->created_at->format('D, M d, Y, g:i A') : 'N/A' }}</div>
                </div>
            </div>

            <div style="border-bottom: 1px solid #d1d5db; margin-bottom: 20px;"></div>

            <div style="margin-bottom: 24px; font-size: 14.5px; color: #000;">
                <strong>Deliver via:</strong> {{ ucfirst($order->transport_type ?: 'Truck') }} &middot; 
                <strong>Pay by:</strong> {{ ucwords(str_replace('_', ' ', $order->payment_method ?: 'Cash on delivery')) }}
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
                    @foreach($order->items as $item)
                    @php
                        $pName = $item->product->name ?? $item->product_name ?? 'Product';
                        $pQty = (float)($item->qty ?? 1);
                        $pPrice = (float)($item->price ?? ($item->product->salePrice ?? $item->product->price ?? 0));
                        $pUnit = $item->unit ?? $item->product->unit ?? '';
                        $lineTotal = $pPrice * $pQty;
                    @endphp
                    <tr>
                        <td style="padding: 12px 0; color: #000;">{{ $pName }}</td>
                        <td style="padding: 12px 0; text-align: center; color: #000;">{{ $pQty }} {{ $pUnit }}</td>
                        <td style="padding: 12px 0; text-align: right; color: #000;">${{ number_format($pPrice, 2) }}</td>
                        <td style="padding: 12px 0; text-align: right; color: #000;">${{ number_format($lineTotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="border-bottom: 1px solid #d1d5db; margin-bottom: 16px;"></div>

            <div style="font-size: 14.5px;">
                <div style="display: flex; justify-content: space-between; padding: 6px 0; color: #000;">
                    <span>Items subtotal</span>
                    <span>${{ number_format($order->subtotal ?? 0, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 6px 0; color: #000;">
                    <span>Delivery Fee (10%)</span>
                    <span>${{ number_format($order->delivery_fee ?? 0, 2) }}</span>
                </div>
            </div>

            <div style="border-bottom: 1px solid #d1d5db; margin: 16px 0;"></div>

            <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 800; font-size: 20px; color: #000; padding-top: 4px;">
                <span>Total paid</span>
                <span>${{ number_format($order->total ?? 0, 2) }}</span>
            </div>
        </div>

        {{-- Modal Footer Actions (Download PDF + Print) --}}
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
            <button onclick="closeInvoiceModal()" style="padding: 8px 18px; border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; font-weight: 600; color: #475569; cursor: pointer;">
                Close
            </button>
            <div style="display: flex; gap: 10px;">
                {{-- Download PDF Button --}}
                <button onclick="printModalInvoice()" style="padding: 8px 18px; border: 1.5px solid #10b981; background: #ffffff; color: #065f46; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-download"></i> Download PDF
                </button>
                {{-- Print Button --}}
                <button onclick="printModalInvoice()" style="padding: 8px 20px; border: none; background: #1E5C34; color: #fff; border-radius: 8px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-print"></i> Print Invoice
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Vanilla JavaScript to Control Modal Opening and Printing --}}
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
@endsection