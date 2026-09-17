<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Mini Mart</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v=20260908b">
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
              <div class="field-row">
                <div class="field"><label for="staff-phone">Phone number</label><input id="staff-phone" required></div>
                <div class="field"><label for="staff-vehicle">Vehicle / ID</label><input id="staff-vehicle" placeholder="e.g. Moto MT-0099" required></div>
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
            <h3 style="margin:0;">Stock / Purchase Order</h3>
            <div style="display:flex; gap:10px;">
              <button class="btn btn-small" onclick="exportInventory()"><i class="fa-solid fa-download"></i> Export</button>
              <button class="btn btn-small" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
              <button class="btn btn-small btn-accent" onclick="createPurchaseOrder()">+ Purchase Order</button>
            </div>
          </div>
          <table class="admin-table">
            <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Qty</th><th>Status</th><th>Action</th></tr></thead>
            <tbody id="inventory-body"></tbody>
          </table>
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
            <h3>Revenue by order</h3>
            <div class="chart-box"><canvas id="chart-revenue"></canvas></div>
          </div>
          <div class="panel-card">
            <h3>Order status breakdown</h3>
            <div class="chart-box"><canvas id="chart-status"></canvas></div>
          </div>
        </div>

        <div class="panel-card">
          <h3>Stock value by category</h3>
          <div class="chart-box chart-box-wide"><canvas id="chart-category"></canvas></div>
        </div>
      </section>

      <!-- ============ SETTINGS ============ -->
      <section class="admin-panel" id="panel-settings">
        <div class="admin-grid">
        <div class="panel-card" style="max-width:520px;">
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

        <div class="panel-card" style="max-width:520px;">
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
        </div>
      </section>

    </div>
  </main>
</div>

<div class="toast" id="toast"></div>

<script>
  window.MM_CSRF = "{{ csrf_token() }}";
  window.MM_ADMIN_DATA = @json($data);
  window.MM_ADMIN_ROUTES = {
    products: "{{ url('/admin/api/products') }}",
    purchaseOrder: "{{ route('admin.purchaseOrder') }}",
    orders: "{{ url('/admin/api/orders') }}",
    staff: "{{ url('/admin/api/staff') }}",
    messages: "{{ url('/admin/api/messages') }}",
    settings: "{{ route('admin.settings.update') }}",
  };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
<script>
  const origDash = renderDashboard;
  renderDashboard = function(){
    origDash();
    document.getElementById("dash-pending-body").innerHTML = orders.filter(o=>o.status==="pending").map(o => `
      <tr><td>#${o.id}</td><td>${o.customer}</td><td>${money(o.total)}</td>
      <td><button class="btn btn-small btn-primary" onclick="approveDelivery(${o.id})">Approve</button></td></tr>
    `).join("") || "<tr><td colspan='4'>Nothing waiting — all caught up.</td></tr>";
  };
</script>
</body>
</html>
