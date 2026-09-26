@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<style>
  .app-orders-wrapper {
    max-width: 900px;
    margin: 20px auto;
    padding: 0 15px;
    font-family: system-ui, -apple-system, sans-serif;
  }

  /* Product Catalog Section */
  .section-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 12px;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
  }

  .product-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px;
    text-align: center;
  }

  .product-img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 8px;
  }

  .product-name {
    font-weight: 600;
    font-size: 14px;
    color: #1F2937;
    margin-bottom: 4px;
  }

  .product-price {
    color: #e11d48;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 8px;
  }

  .btn-add-cart {
    width: 100%;
    padding: 6px;
    background: #e11d48;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
    cursor: pointer;
  }

  /* Tabs & History Styling */
  .app-tabs {
    display: flex;
    gap: 24px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 20px;
    padding-bottom: 8px;
  }

  .app-tab {
    font-size: 15px;
    font-weight: 600;
    color: #6b7280;
    text-decoration: none;
    padding-bottom: 10px;
  }

  .app-tab.active {
    color: #e11d48;
    border-bottom: 3px solid #e11d48;
  }

  .app-order-card {
    background: #ffffff;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 16px;
    border: 1px solid #e5e7eb;
  }
</style>

<div class="app-orders-wrapper">

    {{-- SECTION 1: Order New Items --}}
    <h3 class="section-title">Order New Items</h3>
    <div class="products-grid">
        @foreach($products as $product)
            <div class="product-card">
                <img src="{{ asset($product->image ?? 'images/placeholder.jpg') }}" class="product-img" alt="{{ $product->name }}">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-price">${{ number_format($product->sale_price, 2) }}</div>
                <a href="{{ route('checkout.index') }}" class="btn btn-add-cart">Order Now</a>
            </div>
        @endforeach
    </div>

    <hr class="my-4" style="border-color: #e5e7eb;">

    {{-- SECTION 2: Order History --}}
    <h3 class="section-title">My Past Orders</h3>

    @php $currentStatus = request('status'); @endphp
    <div class="app-tabs">
        <a href="{{ route('orders.index') }}" class="app-tab {{ !$currentStatus ? 'active' : '' }}">All Orders</a>
        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="app-tab {{ $currentStatus === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('orders.index', ['status' => 'out']) }}" class="app-tab {{ $currentStatus === 'out' ? 'active' : '' }}">In Progress</a>
        <a href="{{ route('orders.index', ['status' => 'done']) }}" class="app-tab {{ $currentStatus === 'done' ? 'active' : '' }}">Completed</a>
    </div>

    @forelse($orders as $o)
        <div class="app-order-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold">Order #{{ $o->id }}</span>
                <span class="badge bg-warning text-dark">{{ ucfirst($o->status) }}</span>
            </div>
            
            <div class="d-flex gap-3 overflow-auto mb-2">
                @foreach($o->items as $item)
                    <div style="width: 80px; text-align: center;">
                        <img src="{{ asset($item->product->image ?? 'images/placeholder.jpg') }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;">
                        <div style="font-size: 12px;" class="text-truncate">{{ $item->product_name }}</div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <div class="small text-muted">
                    {{ $o->placed_at ? $o->placed_at->format('d/m/Y H:i') : '' }} | Total: <strong>${{ number_format($o->total, 2) }}</strong>
                </div>
                <a href="{{ route('orders.show', $o->id) }}" class="btn btn-sm btn-outline-danger rounded-pill">View Details</a>
            </div>
        </div>
    @empty
        <div class="text-center py-4 bg-white rounded border">No orders found.</div>
    @endforelse

</div>
@endsection