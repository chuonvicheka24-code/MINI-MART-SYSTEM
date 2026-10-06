<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Mini Mart</title>

<!-- Green Shopping Cart Browser Tab Favicon -->
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512'><path fill='%2310b981' d='M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77 45.9C72.4 21.6 51.2 4 26.9 4H24C10.7 4 0 13.3 0 26.6zM176 512a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm320 0a48 48 0 1 0 0-96 48 48 0 1 0 0 96z'/></svg>">
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v=20260908b">
<link rel="stylesheet" href="{{ asset('css/animations.css') }}?v=2">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="admin-shell">

  <aside class="admin-side">
    <a href="{{ route('home') }}" class="brand">Mini<span>Mart</span></a>
    <a href="#" class="side-link" data-panel="dashboard" onclick="showPanel('dashboard'); return false;"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
    <a href="#" class="side-link" data-panel="orders" onclick="showPanel('orders'); return false;"><i class="fa-solid fa-box"></i> Orders</a>
    <a href="#" class="side-link" data-panel="staff" onclick="showPanel('staff'); return false;"><i class="fa-solid fa-truck-fast"></i> Staff Delivery</a>
    <a href="#" class="side-link" data-panel="customers" onclick="showPanel('customers'); return false;"><i class="fa-solid fa-users"></i> Customers</a>
    <a href="#" class="side-link" data-panel="messages" onclick="showPanel('messages'); return false;"><i class="fa-solid fa-envelope"></i> Messages <span class="side-badge" id="side-msg-badge" style="display:none;">0</span></a>
    <a href="#" class="side-link" data-panel="inventory" onclick="showPanel('inventory'); return false;"><i class="fa-solid fa-clipboard-list"></i> Inventory</a>
    <a href="#" class="side-link" data-panel="purchase-orders" onclick="showPanel('purchase-orders'); return false;"><i class="fa-solid fa-truck-ramp-box"></i> Purchase Orders</a>
    <a href="#" class="side-link" data-panel="promotions" onclick="showPanel('promotions'); return false;"><i class="fa-solid fa-tags"></i> Promotions</a>
    <a href="#" class="side-link" data-panel="reports" onclick="showPanel('reports'); return false;"><i class="fa-solid fa-chart-line"></i> Reports</a>
    <a href="#" class="side-link" data-panel="settings" onclick="showPanel('settings'); return false;"><i class="fa-solid fa-gear"></i> Settings</a>
    <div class="side-foot">Logged in as <strong>{{ auth()->user()->first_name }}</strong><br>
      <a href="{{ route('home') }}" style="color:var(--brand);">← Back to storefront</a><br>
      <form method="POST" action="{{ route('logout') }}" style="margin-top:6px;">@csrf<button type="submit" style="background:none; border:none; color:var(--brand); padding:0; font:inherit; cursor:pointer; text-decoration:underline;">Log out</button></form>
    </div>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1 id="panel-title">Dashboard</h1>
      <div style="font-size:14px; font-weight:600; color:#5a6b5c;">Mini Mart Admin</div>
    </div>

    <div class="admin-content">

      <div class="alert-banner" id="alert-banner">
        <span><i class="fa-solid fa-bell"></i> <span id="alert-count">0</span> new order(s) waiting for delivery approval.</span>
        <button class="btn btn-small" onclick="showPanel('orders')">Review orders</button>
      </div>

      <div class="alert-banner" id="msg-alert-banner" style="background:var(--brand-soft); color:var(--brand-dark);">
        <span><i class="fa-solid fa-envelope-open-text"></i> <span id="msg-alert-count">0</span> new customer message(s) waiting for a reply.</span>
        <button class="btn btn-small" onclick="showPanel('messages')">Read messages</button>
      </div>

      <!-- ============ DASHBOARD ============ -->
      <section class="admin-panel" id="panel-dashboard">
        <div class="kpi-row kpi-row-5">
          <div class="kpi-card"><div class="kpi-label">Products</div><div class="kpi-value" id="kpi-products">0</div></div>
          <div class="kpi-card"><div class="kpi-label">Low stock items</div><div class="kpi-value" id="kpi-low">0</div></div>
          <div class="kpi-card"><div class="kpi-label">Orders pending approval</div><div class="kpi-value" id="kpi-pending">0</div></div>
          <div class="kpi-card"><div class="kpi-label">Unread messages</div><div class="kpi-value" id="kpi-messages">0</div></div>
          <div class="kpi-card"><div class="kpi-label">Sales today</div><div class="kpi-value" id="kpi-sales">$0.00</div></div>
        </div>

        <div class="admin-grid">
          <div>
            <div class="panel-card">
              <h3>Add new item</h3>
              <form onsubmit="addNewItem(event)">
                <label class="upload-box" id="new-item-preview" for="new-item-image"><span id="new-item-preview-icon"><i class="fa-solid fa-camera"></i></span>
                  <input type="file" id="new-item-image" accept="image/*" style="display:none;" onchange="handleImagePreview(this)">
                </label>
                <div class="field-row">
                  <div class="field"><label for="new-item-name">Product name</label><input id="new-item-name" required></div>
                  <div class="field"><label for="new-item-cat">Category</label>
                    <select id="new-item-cat"></select>
                  </div>
                </div>
                <div class="field">
                  <label for="new-item-newcat">Or create a new category (optional)</label>
                  <input id="new-item-newcat" placeholder="e.g. Organic — leave blank to use the dropdown">
                </div>
                <div class="field-row">
                  <div class="field"><label for="new-item-price">Cost</label><input id="new-item-price" type="number" step="0.01" min="0" required></div>
                  <div class="field"><label for="new-item-qty">Quantity</label><input id="new-item-qty" type="number" min="0" required></div>
                </div>
                <div class="field-row">
                  <div class="field"><label for="new-item-unit">Unit of measure</label>
                    <select id="new-item-unit">
                      <option value="each">each</option>
                      <option value="kg">kg</option>
                      <option value="g">g</option>
                      <option value="l">L</option>
                      <option value="ml">ml</option>
                      <option value="pack">pack</option>
                      <option value="box">box</option>
                      <option value="dozen">dozen</option>
                      <option value="bag">bag</option>
                      <option value="bottle">bottle</option>
                    </select>
                  </div>
                  <div class="field"><label for="new-item-expiry">Expiry date <span class="form-note" style="display:inline;">(optional)</span></label><input id="new-item-expiry" type="date"></div>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Add item</button>
              </form>
            </div>

            <div class="panel-card">
              <h3>Purchase orders needing delivery approval</h3>
              <p class="form-note">Approving alerts the customer and moves the order to "out for delivery."</p>
              <table class="admin-table">
                <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th></th></tr></thead>
                <tbody id="dash-pending-body"></tbody>
              </table>
            </div>
          </div>

          <div>
            <div class="panel-card">
              <h3>Needs reorder (qty under 10)</h3>
              <ul class="low-list" id="low-stock-list"></ul>
            </div>
          </div>
        </div>
      </section>

      <!-- ============ ORDERS ============ -->
      <section class="admin-panel" id="panel-orders">
        <div class="panel-card">
          <h3>List orders</h3>
          <table class="admin-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Qty ordered</th><th>Cost total</th><th>Delivery</th><th>Status</th><th>Action</th></tr></thead>
            <tbody id="orders-body"></tbody>
          </table>
        </div>
      </section>

      <!-- ============ STAFF DELIVERY ============ -->
      <section class="admin-panel" id="panel-staff">
        <div class="admin-grid">
          <div class="panel-card" style="max-width:480px;">
            <h3>Add delivery staff</h3>
            <form onsubmit="addStaff(event)">
              <div class="field"><label for="staff-name">Full name</label><input id="staff-name" required></div>
              <div class="field"><label for="staff-phone">Phone number</label><input id="staff-phone" required></div>
              
              <!-- Vehicle Selection & Number Input Block -->
              <div class="field-row">
                <div class="field">
                  <label for="staff-vehicle-type">Vehicle Type</label>
                  <select id="staff-vehicle-type" onchange="toggleVehiclePlaceholder()">
                    <option value="moto" selected>Motorcycle</option>
                    <option value="truck">Truck / Car</option>
                  </select>
                </div>
                <div class="field">
                  <label for="staff-vehicle" id="staff-vehicle-label">Moto Number</label>
                  <input id="staff-vehicle" placeholder="e.g. Moto MT-0087" required>
                </div>
              </div>

              <div class="field">
                <label for="staff-status">Status</label>
                <select id="staff-status">
                  <option value="available">Available</option>
                  <option value="delivery">On delivery</option>
                  <option value="off">Off duty</option>
                </select>
              </div>
              <button class="btn btn-primary btn-block" type="submit">Add staff</button>
            </form>
          </div>

          <div class="panel-card">
            <div class="table-toolbar">
              <h3 style="margin:0;">Delivery staff</h3>
              <span class="form-note" style="margin:0;" id="staff-count-note">0 staff</span>
            </div>
            <table class="admin-table">
              <thead><tr><th>Name</th><th>Phone</th><th>Vehicle</th><th>Active deliveries</th><th>Status</th><th>Action</th></tr></thead>
              <tbody id="staff-body"></tbody>
            </table>
            <p id="staff-empty" style="display:none; margin-top:8px;">No delivery staff added yet.</p>
          </div>
        </div>

        <div class="panel-card">
          <h3>Assign deliveries</h3>
          <p class="form-note" style="margin-top:-6px; margin-bottom:14px;">Pick who's driving each active order — this updates the courier shown on the Orders page too.</p>
          <table class="admin-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Assigned staff</th></tr></thead>
            <tbody id="staff-assign-body"></tbody>
          </table>
        </div>
      </section>

      <!-- ============ CUSTOMERS ============ -->
      <section class="admin-panel" id="panel-customers">
        <div class="panel-card">
          <h3>Customers</h3>
          <table class="admin-table">
            <thead><tr><th>Name</th><th>Phone number</th><th>Email</th><th>Orders placed</th></tr></thead>
            <tbody id="customers-body"></tbody>
          </table>
        </div>
      </section>

      <!-- ============ MESSAGES ============ -->
      <section class="admin-panel" id="panel-messages">
        <div class="panel-card">
          <div class="table-toolbar">
            <h3 style="margin:0;">Customer Messages</h3>
            <button class="btn btn-small" onclick="markAllMessagesRead()"><i class="fa-solid fa-envelope-open"></i> Mark all read</button>
          </div>
          <p class="form-note" style="margin-top:-6px; margin-bottom:14px;">Messages submitted from the storefront's Contact Us page land here.</p>
          <div id="messages-list"></div>
          <p id="messages-empty" style="display:none; margin-top:8px;">No messages yet — anything a customer sends from Contact Us will show up here.</p>
        </div>
      </section>

      <!-- ============ INVENTORY / STOCK MANAGEMENT ============ -->
      <section class="admin-panel" id="panel-inventory">
        <div class="panel-card">
          <div class="table-toolbar">
            <h3 style="margin:0;">Stock</h3>
            <div style="display:flex; gap:10px;">
              <button class="btn btn-small" onclick="exportInventory()"><i class="fa-solid fa-download"></i> Export</button>
              <button class="btn btn-small" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
              <button class="btn btn-small btn-accent" onclick="showPanel('purchase-orders')">+ Purchase Order</button>
            </div>
          </div>
          <table class="admin-table">
            <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Unit</th><th>Qty</th><th>Expiry</th><th>Status</th><th>Action</th></tr></thead>
            <tbody id="inventory-body"></tbody>
          </table>
        </div>
      </section>

      <!-- ============ PURCHASE ORDERS ============ -->
      <section class="admin-panel" id="panel-purchase-orders">
        <div class="admin-grid">
          <div>
            <div class="panel-card">
              <h3>New purchase order</h3>
              <p class="form-note">Manually build an order to send to a supplier. Stock is only added once you mark the order "Received".</p>
              <form onsubmit="submitPurchaseOrder(event)">
                <div class="field-row">
                  <div class="field"><label for="po-supplier">Supplier name</label><input id="po-supplier" required></div>
                  <div class="field"><label for="po-expected">Expected date <span class="form-note" style="display:inline;">(optional)</span></label><input id="po-expected" type="date"></div>
                </div>
                <div class="field">
                  <label for="po-notes">Notes <span class="form-note" style="display:inline;">(optional)</span></label>
                  <input id="po-notes" placeholder="e.g. Call ahead, delivery bay 2">
                </div>

                <div class="field-row" style="align-items:flex-end;">
                  <div class="field"><label for="po-line-product">Product</label><select id="po-line-product"></select></div>
                  <div class="field" style="max-width:110px;"><label for="po-line-qty">Qty</label><input id="po-line-qty" type="number" min="1" value="1"></div>
                  <div class="field" style="max-width:130px;"><label for="po-line-cost">Unit cost</label><input id="po-line-cost" type="number" min="0" step="0.01" placeholder="0.00"></div>
                  <div class="field" style="flex:0;"><button type="button" class="btn btn-small btn-outline" style="white-space:nowrap;" onclick="addPurchaseOrderLine()">+ Add line</button></div>
                </div>

                <ul class="low-list" id="po-line-list" style="margin-bottom:14px;"></ul>
                <p class="form-note" id="po-line-empty">No lines added yet — add at least one product above.</p>

                <button class="btn btn-primary btn-block" type="submit">Create purchase order</button>
              </form>
            </div>
          </div>

          <div>
            <div class="panel-card">
              <h3>Purchase orders</h3>
              <table class="admin-table">
                <thead><tr><th>#</th><th>Supplier</th><th>Items</th><th>Cost</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="po-body"></tbody>
              </table>
              <p id="po-empty" style="display:none; margin-top:8px;">No purchase orders yet.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ============ PROMOTIONS ============ -->
      <section class="admin-panel" id="panel-promotions">
        <div class="admin-grid">
          <div>
            <div class="panel-card">
              <h3>New promotion</h3>
              <p class="form-note">Runs a discount campaign across every product you select below.</p>
              <form onsubmit="submitPromotion(event)">
                <div class="field"><label for="promo-title">Promotion title</label><input id="promo-title" placeholder="e.g. Weekend Fresh Sale" required></div>
                <div class="field"><label for="promo-desc">Description <span class="form-note" style="display:inline;">(optional)</span></label><input id="promo-desc"></div>
                <div class="field-row">
                  <div class="field"><label for="promo-discount">Discount %</label><input id="promo-discount" type="number" min="1" max="90" value="10" required></div>
                  <div class="field"><label for="promo-starts">Start date <span class="form-note" style="display:inline;">(optional)</span></label><input id="promo-starts" type="date"></div>
                  <div class="field"><label for="promo-ends">End date <span class="form-note" style="display:inline;">(optional)</span></label><input id="promo-ends" type="date"></div>
                </div>
                <div class="field">
                  <label>Products in this promotion</label>
                  <div id="promo-product-list" style="max-height:220px; overflow-y:auto; border:1.5px solid var(--line); border-radius:var(--radius-sm); padding:10px 13px;"></div>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Launch promotion</button>
              </form>
            </div>
          </div>

          <div>
            <div class="panel-card">
              <h3>Promotions</h3>
              <table class="admin-table">
                <thead><tr><th>Title</th><th>Products</th><th>Discount</th><th>Dates</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="promotions-body"></tbody>
              </table>
              <p id="promotions-empty" style="display:none; margin-top:8px;">No promotions yet.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ============ REPORTS ============ -->
      <section class="admin-panel" id="panel-reports">
        <div class="panel-card">
          <div class="table-toolbar">
            <h3 style="margin:0;">Sales report summary</h3>
            <div style="display:flex; gap:10px;">
              <button class="btn btn-small" onclick="exportReport()"><i class="fa-solid fa-download"></i> Export CSV</button>
              <button class="btn btn-small" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
            </div>
          </div>
          <div class="kpi-row" style="grid-template-columns: repeat(4,1fr);">
            <div class="kpi-card"><div class="kpi-label">Total sales (recent orders)</div><div class="kpi-value" id="report-total">$0.00</div></div>
            <div class="kpi-card"><div class="kpi-label">Orders</div><div class="kpi-value" id="report-orders">0</div></div>
            <div class="kpi-card"><div class="kpi-label">Delivered</div><div class="kpi-value" id="report-delivered">0</div></div>
            <div class="kpi-card"><div class="kpi-label">Average order</div><div class="kpi-value" id="report-avg">$0.00</div></div>
          </div>
        </div>

        <div class="admin-grid">
          <div class="panel-card">
            <div class="chart-head">
              <div><h3>Sales by month</h3><p class="chart-sub">Click a bar to choose a month, then export its invoices.</p></div>
              <div class="chart-tools">
                <select id="report-month" class="chart-select" onchange="setReportMonth(this.value)"><option value="all">All months</option></select>
                <button class="btn btn-small btn-primary" onclick="exportInvoicesExcel()"><i class="fa-solid fa-file-excel"></i> Export Excel</button>
              </div>
            </div>
            <div class="chart-box" id="chart-revenue"></div>
          </div>
          <div class="panel-card">
            <div class="chart-head">
              <div><h3>Stock value by category</h3><p class="chart-sub">Total value of stock on hand per category.</p></div>
              <div class="chart-tools"><button class="btn btn-small btn-primary" onclick="exportStockExcel()"><i class="fa-solid fa-file-excel"></i> Export Excel</button></div>
            </div>
            <div class="chart-legend"><span><i class="dot dot-red"></i>Under $100</span><span><i class="dot dot-green"></i>$100 or more</span></div>
            <div class="chart-box chart-box-h" id="chart-category"></div>
          </div>
        </div>

        <div class="panel-card">
          <div class="chart-head">
            <div><h3>Purchase order report</h3><p class="chart-sub">Spend per vendor, scale $0 – $1,000 (grows if a vendor spends more). Cancelled orders excluded.</p></div>
            <div class="chart-tools"><select id="po-vendor" class="chart-select" onchange="setPOVendor(this.value)"><option value="all">All vendors</option></select><button class="btn btn-small btn-primary" onclick="exportPOExcel()"><i class="fa-solid fa-file-excel"></i> Export Excel</button></div>
          </div>
          <div class="chart-box chart-box-wide" id="chart-po"></div>
        </div>
      </section>

      <!-- ============ SETTINGS ============ -->
      <style>
  #panel-settings .settings-grid{ display:grid; grid-template-columns:repeat(auto-fit,minmax(360px,1fr)); gap:22px; align-items:start; }
  #panel-settings .panel-card{ margin-bottom:0; }
  #panel-settings .img-card{ grid-column:1 / -1; }
  #panel-settings .img-split{ display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:28px; }
  #panel-settings .img-box{ background:#f6fbf8; border:1px solid var(--line, #e3ece6); border-radius:14px; padding:18px; display:flex; flex-direction:column; gap:14px; }
  #panel-settings .img-title{ font-weight:700; font-size:14.5px; color:#1b3a2a; }
  #panel-settings .img-title span{ display:block; font-weight:400; font-size:12.5px; color:#6b7d72; margin-top:2px; }
  #panel-settings .hero-frame{ aspect-ratio:16/9; background:#fff; border-radius:12px; display:flex; align-items:center; justify-content:center; overflow:hidden; border:1px solid var(--line, #e3ece6); }
  #panel-settings .hero-frame img{ width:100%; height:100%; object-fit:contain; }
  #panel-settings .cat-row{ display:flex; gap:18px; align-items:center; }
  #panel-settings .cat-frame{ width:130px; height:130px; flex:none; border-radius:50%; overflow:hidden; background:#fff; border:3px solid #fff; box-shadow:0 2px 10px rgba(0,0,0,.12); }
  #panel-settings .cat-frame img{ width:100%; height:100%; object-fit:cover; }
  #panel-settings .cat-fields{ flex:1; display:flex; flex-direction:column; gap:12px; min-width:0; }
  #panel-settings .cat-fields select, #panel-settings .file-input{ width:100%; border:1.5px solid var(--line, #dfe8e2); border-radius:10px; padding:10px 12px; background:#fff; font-size:14px; }
  #panel-settings .file-input{ border-style:dashed; cursor:pointer; }
  #panel-settings .img-actions{ display:flex; flex-wrap:wrap; gap:10px; margin-top:auto; }
</style>
<section class="admin-panel" id="panel-settings">
        <div class="settings-grid">
        <div class="panel-card">
          <h3>Add discount</h3>
          <form onsubmit="applyDiscountForm(event)">
            <div class="field">
              <label for="discount-product">Product</label>
              <select id="discount-product" onchange="updateDiscountPreview()"></select>
            </div>
            <div class="field-row">
              <div class="field"><label for="discount-price-preview">Price</label><input id="discount-price-preview" disabled></div>
              <div class="field"><label for="discount-percent">Discount %</label><input id="discount-percent" type="number" min="0" max="90" step="1" value="10" oninput="updateDiscountPreview()"></div>
            </div>
            <div class="field">
              <label for="discount-sale-preview">Sale price</label>
              <input id="discount-sale-preview" disabled>
            </div>
            <button class="btn btn-primary btn-block" type="submit">Apply discount</button>
          </form>
          <div id="discount-active-wrap" style="margin-top:16px;">
            <p class="form-note" style="margin-bottom:8px;">Active discounts</p>
            <ul class="low-list" id="discount-active-list"></ul>
          </div>
        </div>

        <div class="panel-card">
          <h3>Store settings</h3>
          <form onsubmit="saveSettings(event)">
            <div class="field"><label for="set-name">Store name</label><input id="set-name"></div>
            <div class="field"><label for="set-hours">Opening hours</label><input id="set-hours"></div>
            <div class="field-row">
              <div class="field"><label for="set-rate">Delivery rate (%)</label><input id="set-rate" type="number" min="0" step="0.5"></div>
              <div class="field"><label for="set-threshold">Low-stock alert below</label><input id="set-threshold" type="number" min="0"></div>
            </div>
            <button class="btn btn-primary" type="submit">Save settings</button>
          </form>
        </div>

        <div class="panel-card img-card">
          <h3>Homepage images</h3>
          @php
            $siteSettings = \App\Models\Setting::current();
            $heroUrl = \App\Models\Product::resolveImageUrl($siteSettings->hero_image ?: \App\Http\Controllers\Admin\SiteImageController::DEFAULT_HERO);
          @endphp
          <div class="img-split">

            <div class="img-box">
              <div class="img-title">Hero banner <span>Big image on the home page</span></div>
              <div class="hero-frame"><img id="hero-preview" src="{{ $heroUrl }}" alt="Hero banner"></div>
              <input class="file-input" type="file" id="hero-file" accept="image/png,image/jpeg,image/webp" onchange="previewSiteImage(this,'hero-preview')">
              <div class="img-actions">
                <button class="btn btn-primary" type="button" onclick="uploadHero()"><i class="fa-solid fa-upload"></i> Upload hero image</button>
                <button class="btn btn-small" type="button" onclick="resetHero()">Reset to default</button>
              </div>
            </div>

            <div class="img-box">
              <div class="img-title">Category image <span>Round photos under "Shop By Category"</span></div>
              <div class="cat-row">
                <div class="cat-frame"><img id="cat-preview" src="" alt="Category image"></div>
                <div class="cat-fields">
                  <select id="cat-img-select" onchange="showCategoryImage()">
                    @foreach ($categories as $c)
                      <option value="{{ $c->id }}" data-img="{{ $c->image_url }}">{{ $c->name }}</option>
                    @endforeach
                  </select>
                  <input class="file-input" type="file" id="cat-file" accept="image/png,image/jpeg,image/webp" onchange="previewSiteImage(this,'cat-preview')">
                </div>
              </div>
              <div class="img-actions">
                <button class="btn btn-primary" type="button" onclick="uploadCategoryImage()"><i class="fa-solid fa-upload"></i> Upload category image</button>
              </div>
            </div>

          </div>
          <p class="form-note" style="margin-top:14px;">JPG, PNG or WEBP, up to 2 MB.</p>
        </div>
        </div>
      </section>

    </div>
  </main>
</div>

<div class="toast" id="toast"></div>


<script>
  // ---- Homepage images (Settings page) ----
  const SITE_IMG = {
    hero: "{{ route('admin.siteImages.hero') }}",
    category: id => "{{ url('/admin/api/site-images/category') }}/" + id,
  };
  function siteToast(msg){ (typeof flashAlert === "function") ? flashAlert(msg) : alert(msg); }

  function previewSiteImage(input, imgId){
    if(input.files && input.files[0]) document.getElementById(imgId).src = URL.createObjectURL(input.files[0]);
  }

  async function sendSiteImage(url, input, method = "POST"){
    if(!input.files || !input.files[0]){ siteToast("Choose an image first."); return null; }
    const fd = new FormData();
    fd.append("image", input.files[0]);
    try{
      const r = await fetch(url, { method, body: fd, headers: { "Accept":"application/json", "X-CSRF-TOKEN": window.MM_CSRF } });
      const d = await r.json().catch(() => ({}));
      if(!r.ok){
        siteToast((d.errors && d.errors.image && d.errors.image[0]) || d.message || "Upload failed (max 2 MB, JPG/PNG/WEBP).");
        return null;
      }
      return d;
    }catch(e){ siteToast("Upload failed. Check your connection."); return null; }
  }

  async function uploadHero(){
    const input = document.getElementById("hero-file");
    const d = await sendSiteImage(SITE_IMG.hero, input);
    if(d){ document.getElementById("hero-preview").src = d.url; input.value = ""; siteToast("Hero image updated. Refresh the home page to see it."); }
  }

  function resetHero(){
    fetch(SITE_IMG.hero, { method:"DELETE", headers:{ "Accept":"application/json", "X-CSRF-TOKEN": window.MM_CSRF } })
      .then(r => r.json()).then(d => { document.getElementById("hero-preview").src = d.url; siteToast("Hero image reset to default."); });
  }

  function showCategoryImage(){
    const sel = document.getElementById("cat-img-select");
    if(!sel.options.length) return;
    document.getElementById("cat-preview").src = sel.selectedOptions[0].dataset.img;
    document.getElementById("cat-file").value = "";
  }

  async function uploadCategoryImage(){
    const sel = document.getElementById("cat-img-select");
    const input = document.getElementById("cat-file");
    const d = await sendSiteImage(SITE_IMG.category(sel.value), input);
    if(d){
      sel.selectedOptions[0].dataset.img = d.url;
      document.getElementById("cat-preview").src = d.url;
      input.value = "";
      siteToast(sel.selectedOptions[0].textContent + " image updated.");
    }
  }

  document.addEventListener("DOMContentLoaded", showCategoryImage);
</script>

<script>
  window.MM_CSRF = "{{ csrf_token() }}";
  window.MM_ADMIN_DATA = @json($data);
  window.MM_ADMIN_ROUTES = {
    products: "{{ url('/admin/api/products') }}",
    purchaseOrders: "{{ url('/admin/api/purchase-orders') }}",
    promotions: "{{ url('/admin/api/promotions') }}",
    orders: "{{ url('/admin/api/orders') }}",
    staff: "{{ url('/admin/api/staff') }}",
    messages: "{{ url('/admin/api/messages') }}",
    settings: "{{ route('admin.settings.update') }}",
    data: "{{ route('admin.data') }}",
  };

  // Toggle label and placeholder depending on vehicle selection
  function toggleVehiclePlaceholder() {
    const typeSelect = document.getElementById('staff-vehicle-type');
    const vehicleLabel = document.getElementById('staff-vehicle-label');
    const vehicleInput = document.getElementById('staff-vehicle');

    if (typeSelect.value === 'truck') {
      vehicleLabel.textContent = 'Truck / Car Number';
      vehicleInput.placeholder = 'e.g. Truck TM-1042';
    } else {
      vehicleLabel.textContent = 'Moto Number';
      vehicleInput.placeholder = 'e.g. Moto MT-0087';
    }
  }
</script>
<script src="{{ asset('js/xlsx-lite.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>
<script src="{{ asset('js/animations.js') }}?v=1"></script>
<script src="{{ asset('js/report-animations.js') }}?v=1"></script>
<script>
  const origDash = renderDashboard;
  renderDashboard = function(){
    origDash();
    document.getElementById("dash-pending-body").innerHTML = orders.filter(o=>o.status==="pending").map(o => `
      <tr><td>#${o.id}</td><td>${o.customer}</td><td>${money(o.total)}</td>
      <td><button class="btn btn-small btn-primary" onclick="approveDelivery(${o.id})">Approve</button></td></tr>
    `).join("") || "<tr><td colspan='4'>Nothing waiting — all caught up.</td></tr>";
  };

  // Override promotion product list rendering for single-line alignment
  if (typeof renderPromoFormProducts === 'function') {
    const origPromoRender = renderPromoFormProducts;
    renderPromoFormProducts = function() {
      const container = document.getElementById("promo-product-list");
      if (!container) return;
      
      container.innerHTML = products.map(p => `
        <label style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f0f0f0; cursor: pointer; white-space: nowrap; width: 100%;">
          <span style="display: inline-flex; align-items: center; gap: 8px;">
            <input type="checkbox" name="product_ids[]" value="${p.id}">
            <strong style="font-weight: 500;">${p.name}</strong>
          </span>
          <span style="color: #666; font-size: 13px;">— ${money(p.salePrice || p.price)}</span>
        </label>
      `).join("");
    };
  }
</script>
</body>
</html>