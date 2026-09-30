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
  margin-bottom: 16px; /* Ensures clear spacing above the title */
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

  /* Individual step item */
  .step-item {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1; /* Equal spacing across nodes */
  }

  /* Base track segment connecting steps */
  .step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 21px; /* Vertically aligns with center of 42px circle */
    left: 50%; /* Starts at center of current node */
    width: 100%; /* Spans to center of next node */
    height: 3px;
    background-color: #e2e8f0; /* Default gray track */
    z-index: 1;
  }

  /* Active green track segment */
  .step-item.line-active:not(:last-child)::after {
    background-color: #10b981; /* Turns green when active */
  }

  /* Node layout */
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

  /* Order Item Row & Fixed Product Image Styling */
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

  /* Info Table Rows */
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

  /* Responsive Fixes */
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
            
            {{-- Status Badge --}}
            <div>
                <span class="badge-status {{ $step == 3 ? 'status-delivered' : 'status-active' }}">
                    {{ ucfirst($order->status_label ?? $order->status ?? 'Pending') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stepper Progress Card --}}
    <div class="order-card">
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

            <!-- Step 3 (No line after step 3) -->
            <div class="step-item">
                <div class="step-node {{ $step >= 3 ? 'active' : '' }}">
                    <i class="fa-solid fa-house-circle-check"></i>
                </div>
                <span class="step-label">Delivered</span>
            </div>
        </div>
    </div>

    {{-- 2. 2-Column Grid Layout --}}
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
            <div class="order-card">
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

            {{-- Payment Card --}}
            <div class="order-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-receipt text-success"></i> Payment Summary
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value">{{ strtoupper($order->payment_method ?: 'ABA PAY') }}</span>
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
@endsection