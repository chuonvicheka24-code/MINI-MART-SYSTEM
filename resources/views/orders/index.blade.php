@extends('layouts.app')

@section('title', 'My Orders — Mini Mart')

@section('content')
<style>
  .orders-wrapper { max-width: 1100px; margin: 30px auto; padding: 0 20px; font-family: inherit; }
  
  /* Filter Tabs */
  .order-tabs { display: flex; gap: 12px; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; padding-bottom: 2px; }
  .order-tab { padding: 10px 20px; font-size: 15px; font-weight: 600; color: #64748b; text-decoration: none; border-bottom: 3px solid transparent; transition: all 0.2s; }
  .order-tab:hover { color: #e11d48; }
  .order-tab.active { color: #e11d48; border-bottom-color: #e11d48; }

  /* Order Cards */
  .order-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
  .order-card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; margin-bottom: 16px; }
  
  .store-info { display: flex; align-items: center; gap: 10px; font-size: 16px; font-weight: 700; color: #0f172a; }
  .badge-status { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; text-transform: capitalize; }
  .status-pending { background: #fef3c7; color: #92400e; }
  .status-out { background: #e0f2fe; color: #0369a1; }
  .status-done { background: #d1fae5; color: #065f46; }

  /* Product Items Horizontal Layout */
  .order-body { display: flex; justify-content: space-between; align-items: center; }
  .items-list { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 8px; flex: 1; margin-right: 20px; }
  .item-thumb { flex: 0 0 80px; text-align: center; }
  .item-thumb img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #f1f5f9; background: #f8fafc; }
  .item-title { font-size: 12px; color: #475569; margin-top: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 80px; }

  /* Order Summary & Actions */
  .order-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; min-width: 200px; border-left: 1px solid #f1f5f9; padding-left: 20px; }
  .total-price { font-size: 18px; font-weight: 700; color: #0f172a; }
  .btn-details { display: inline-block; padding: 8px 20px; border-radius: 8px; border: 1px solid #e11d48; color: #e11d48; font-weight: 600; text-decoration: none; transition: 0.2s; font-size: 14px; }
  .btn-details:hover { background: #e11d48; color: #fff; }
</style>

<div class="orders-wrapper">
  <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 20px; color: #0f172a;">My Orders</h2>

  {{-- Status Filter Tabs --}}
  <div class="order-tabs">
    <a href="{{ route('orders.index') }}" class="order-tab {{ !request('status') ? 'active' : '' }}">All Orders</a>
    <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="order-tab {{ request('status') === 'pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('orders.index', ['status' => 'out']) }}" class="order-tab {{ request('status') === 'out' ? 'active' : '' }}">In Progress</a>
    <a href="{{ route('orders.index', ['status' => 'done']) }}" class="order-tab {{ request('status') === 'done' ? 'active' : '' }}">Completed</a>
  </div>

  {{-- Order Cards List --}}
  @forelse($orders as $o)
    <div class="order-card">
      <div class="order-card-header">
        <div class="store-info">
          <i class="fa-solid fa-store" style="color: #e11d48;"></i> Mini Mart Store
          <span style="font-size: 13px; font-weight: 400; color: #64748b; margin-left: 10px;">
            Invoice #{{ $o->id }} • {{ $o->created_at ? $o->created_at->format('M d, Y h:i A') : '' }}
          </span>
        </div>
        <span class="badge-status status-{{ $o->status }}">
          {{ $o->status === 'done' ? 'Completed' : ($o->status === 'out' ? 'Out for Delivery' : 'Pending') }}
        </span>
      </div>

      <div class="order-body">
        {{-- Product Thumbnails List --}}
        <div class="items-list">
          @foreach($o->items as $item)
            <div class="item-thumb">
              <img src="{{ asset($item->product->image ?? 'images/placeholder.jpg') }}" alt="{{ $item->product->name ?? 'Product' }}">
              <div class="item-title">{{ $item->product->name ?? 'Item' }}</div>
            </div>
          @endforeach
        </div>

        {{-- Order Right Action Box --}}
        <div class="order-actions">
          <div style="font-size: 13px; color: #64748b;">Total ({{ count($o->items) }} item{{ count($o->items) > 1 ? 's' : '' }})</div>
          <div class="total-price">${{ number_format($o->total, 2) }}</div>
          <a href="{{ route('orders.show', $o->id) }}" class="btn-details">
            View Details
          </a>
        </div>
      </div>
    </div>
  @empty
    <div style="text-align:center; padding: 60px 0; color: #94a3b8; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0;">
      <i class="fa-solid fa-bag-shopping" style="font-size: 48px; margin-bottom: 12px;"></i>
      <p style="font-size: 16px; margin: 0;">No order history available.</p>
    </div>
  @endforelse
</div>
@endsection