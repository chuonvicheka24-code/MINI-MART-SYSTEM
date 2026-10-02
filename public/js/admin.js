/* ============================================================
   MINI MART — admin dashboard logic
   Initial state comes from window.MM_ADMIN_DATA (seeded server-side
   by Admin\DashboardController). Every mutation still updates these
   local arrays immediately (so the screen reacts instantly, exactly
   like the original prototype) and also fires a request to the
   matching Laravel endpoint so the change is actually persisted.
   ============================================================ */

const ADMIN = window.MM_ADMIN_DATA;
const ROUTES = window.MM_ADMIN_ROUTES;

let adminProducts = ADMIN.products.map(p => ({ ...p }));
let categories = [...ADMIN.categories];
let uploadedImageDataUrl = null;

let orders = ADMIN.orders.map(o => ({ ...o }));
let customers = ADMIN.customers.map(c => ({ ...c }));
let deliveryStaff = ADMIN.staff.map(s => ({ ...s }));
let messages = ADMIN.messages.map(m => ({ ...m }));
let settings = { ...ADMIN.settings };
let purchaseOrders = ADMIN.purchaseOrders.map(po => ({ ...po, items: po.items.map(i => ({ ...i })) }));
let promotions = ADMIN.promotions.map(p => ({ ...p, productIds: [...p.productIds], productNames: [...p.productNames] }));
let poLines = [];

function money(n){ return "$" + Number(n).toFixed(2); }
function getMessages(){ return messages; }

function api(url, options = {}){
  return fetch(url, {
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "X-CSRF-TOKEN": window.MM_CSRF,
    },
    ...options,
  }).then(r => r.json().then(data => ({ ok: r.ok, data })));
}

/* ---------- navigation ---------- */
function showPanel(name){
  document.querySelectorAll(".admin-panel").forEach(p => p.style.display = p.id === "panel-"+name ? "block" : "none");
  document.querySelectorAll(".side-link").forEach(a => a.classList.toggle("active", a.dataset.panel === name));
  const titles = {
    dashboard:"Dashboard", orders:"Orders", staff:"Staff Delivery", customers:"Customers",
    messages:"Customer Messages",
    inventory:"Stock Management", "purchase-orders":"Purchase Orders", promotions:"Promotions",
    reports:"Sales reports", settings:"Settings"
  };
  document.getElementById("panel-title").textContent = titles[name];
  renderAll();
  window.scrollTo(0,0);
}

function renderAll(){
  renderDashboard();
  populateNewItemCatSelect();
  renderOrdersTable();
  renderStaffPanel();
  renderCustomersTable();
  renderMessages();
  renderInventory();
  renderPurchaseOrders();
  renderPromotions();
  renderReports();
  renderDiscountPanel();
}

/* ---------- Dashboard ---------- */
function renderDashboard(){
  const low = adminProducts.filter(p => p.qty < settings.lowStockThreshold);
  const pendingOrders = orders.filter(o => o.status === "pending");
  const todaySales = orders.reduce((s,o)=>s+o.total,0);
  const unreadMsgs = getMessages().filter(m => !m.read);

  document.getElementById("kpi-products").textContent = adminProducts.length;
  document.getElementById("kpi-low").textContent = low.length;
  document.getElementById("kpi-pending").textContent = pendingOrders.length;
  document.getElementById("kpi-messages").textContent = unreadMsgs.length;
  document.getElementById("kpi-sales").textContent = money(todaySales);

  document.getElementById("low-stock-list").innerHTML = low.length
    ? low.map(p => `<li><i class="fa-solid fa-triangle-exclamation" style="color:var(--warn);"></i> <strong>${p.name}</strong> — ${p.qty} left${p.discountPercent ? ` <span class="status low">-${p.discountPercent}% on</span>` : ""} <button class="btn btn-small" style="margin-left:8px;" onclick="quickReorder(${p.id})">Order more</button></li>`).join("")
    : "<li>Every shelf is stocked above threshold.</li>";

  document.getElementById("alert-banner").style.display = pendingOrders.length ? "flex" : "none";
  document.getElementById("alert-count").textContent = pendingOrders.length;

  document.getElementById("msg-alert-banner").style.display = unreadMsgs.length ? "flex" : "none";
  document.getElementById("msg-alert-count").textContent = unreadMsgs.length;

  const badge = document.getElementById("side-msg-badge");
  badge.style.display = unreadMsgs.length ? "inline-block" : "none";
  badge.textContent = unreadMsgs.length;
}
function quickReorder(id){
  const p = adminProducts.find(x=>x.id===id);
  p.qty += 20;
  renderAll();
  api(`${ROUTES.products}/${id}/reorder`, { method: "POST" });
}

/* ---------- Add new item (with image upload preview) ---------- */
function populateNewItemCatSelect(){
  const sel = document.getElementById("new-item-cat");
  if(!sel) return;
  const current = sel.value;
  sel.innerHTML = categories.map(c => `<option${c===current?" selected":""}>${c}</option>`).join("");
}
function handleImagePreview(input){
  const preview = document.getElementById("new-item-preview");
  const icon = document.getElementById("new-item-preview-icon");
  const file = input.files[0];
  if(file){
    const reader = new FileReader();
    reader.onload = e => {
      uploadedImageDataUrl = e.target.result;
      preview.style.backgroundImage = `url(${uploadedImageDataUrl})`;
      icon.style.display = "none";
    };
    reader.readAsDataURL(file);
  }
}
function addNewItem(e){
  e.preventDefault();
  const name = document.getElementById("new-item-name").value.trim();
  const typedCat = document.getElementById("new-item-newcat").value.trim();
  const cat = typedCat || document.getElementById("new-item-cat").value;
  const price = parseFloat(document.getElementById("new-item-price").value) || 0;
  const qty = parseInt(document.getElementById("new-item-qty").value) || 0;
  const unit = document.getElementById("new-item-unit").value || "each";
  const expiryDate = document.getElementById("new-item-expiry").value || null;
  if(!name || !cat) return;

  api(ROUTES.products, {
    method: "POST",
    body: JSON.stringify({ name, category: cat, price, qty, unit, expiry_date: expiryDate, image: uploadedImageDataUrl }),
  }).then(({ ok, data }) => {
    if(!ok) return flashAlert(data.message || "Could not add that item.");
    adminProducts.push(data.product);
    if(data.isNewCategory) categories.unshift(cat);

    e.target.reset();
    document.getElementById("new-item-preview").style.backgroundImage = "";
    document.getElementById("new-item-preview-icon").style.display = "flex";
    uploadedImageDataUrl = null;
    renderAll();
    flashAlert(`"${name}" added to Products${data.isNewCategory ? ` (new category "${cat}" created)` : ` in ${cat}`} and New Arrivals.`);
  });
}

/* ---------- Product edit / delete (used by the Inventory table) ---------- */
function editProduct(id){
  const p = adminProducts.find(x => x.id === id);
  if (!p) return;

  // 1. Remove existing edit modal if present
  let modal = document.getElementById("admin-edit-product-modal");
  if (modal) modal.remove();

  // 2. Create Modal HTML
  modal = document.createElement("div");
  modal.id = "admin-edit-product-modal";
  modal.style.cssText = "position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:9999;";
  
  modal.innerHTML = `
    <div style="background:#fff; padding:24px; border-radius:12px; width:100%; max-width:440px; box-shadow:0 10px 25px rgba(0,0,0,0.15);">
      <h3 style="margin-top:0; margin-bottom:16px; font-size:18px; color:#111827;">Edit ${p.name}</h3>
      <form id="edit-product-form">
        <div style="margin-bottom:12px;">
          <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px; color:#374151;">Product Name</label>
          <input type="text" id="edit-p-name" value="${p.name}" class="form-control" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;" required>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px; color:#374151;">Price ($)</label>
            <input type="number" step="0.01" id="edit-p-price" value="${p.price}" class="form-control" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;" required>
          </div>
          <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px; color:#374151;">Quantity</label>
            <input type="number" id="edit-p-qty" value="${p.qty}" class="form-control" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;" required>
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px; color:#374151;">Unit (e.g., kg, pack)</label>
            <input type="text" id="edit-p-unit" value="${p.unit || 'each'}" class="form-control" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
          </div>
          <div>
            <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px; color:#374151;">Expiry Date</label>
            <input type="date" id="edit-p-exp" value="${p.expiryDate || p.exp_date || ''}" class="form-control" style="width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px;">
          </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px;">
          <button type="button" class="btn" onclick="document.getElementById('admin-edit-product-modal').remove()" style="padding:8px 16px; background:#e5e7eb; color:#374151; border:none; border-radius:6px; cursor:pointer;">Cancel</button>
          <button type="submit" class="btn btn-primary" style="padding:8px 16px; background:#1b4d3e; color:#fff; border:none; border-radius:6px; cursor:pointer; font-weight:600;">Update Product</button>
        </div>
      </form>
    </div>
  `;

  document.body.appendChild(modal);

  // 3. Handle Form Submission & API Call
  document.getElementById("edit-product-form").addEventListener("submit", function(e) {
    e.preventDefault();
    
    p.name = document.getElementById("edit-p-name").value.trim();
    p.price = parseFloat(document.getElementById("edit-p-price").value) || p.price;
    p.qty = parseInt(document.getElementById("edit-p-qty").value) ?? p.qty;
    p.unit = document.getElementById("edit-p-unit").value.trim() || p.unit;
    p.expiryDate = document.getElementById("edit-p-exp").value || null;

    renderAll();
    modal.remove();

    api(`${ROUTES.products}/${id}`, {
      method: "PUT",
      body: JSON.stringify({
        name: p.name,
        price: p.price,
        qty: p.qty,
        unit: p.unit,
        expiry_date: p.expiryDate,
        clear_expiry_date: !p.expiryDate,
      }),
    }).then(({ ok }) => {
      if(typeof flashAlert === 'function') {
        flashAlert(ok ? "Product updated successfully!" : "Failed to update product.");
      }
    });
  });
}
function deleteProduct(id){
  if(!confirm("Remove this product from the catalog?")) return;
  adminProducts = adminProducts.filter(p => p.id !== id);
  renderAll();
  api(`${ROUTES.products}/${id}`, { method: "DELETE" });
}

/* ---------- Orders panel ---------- */
function renderOrdersTable(){
  const body = document.getElementById("orders-body");
  body.innerHTML = orders.map(o => `
    <tr>
      <td>#${o.id}</td>
      <td>${o.customer}</td>
      <td>${o.items}</td>
      <td>${money(o.total)}</td>
      <td>${o.mode}</td>
      <td><span class="status ${o.status}">${o.status === "pending" ? "Pending" : o.status === "out" ? "Out for delivery" : "Delivered"}</span></td>
      <td>
        ${o.status === "pending" ? `<button class="btn btn-small btn-primary" onclick="approveDelivery(${o.id})">Approve delivery</button>` : ""}
        ${o.status === "out" ? `<button class="btn btn-small" onclick="markDelivered(${o.id})">Mark delivered</button>` : ""}
      </td>
    </tr>
  `).join("");
}
function approveDelivery(id){
  const o = orders.find(x=>x.id===id);
  o.status = "out";
  renderAll();
  flashAlert(`Order #${id} approved — customer ${o.customer} has been notified.`);
  api(`${ROUTES.orders}/${id}/approve`, { method: "POST" });
}
function markDelivered(id){
  const o = orders.find(x=>x.id===id);
  o.status = "done";
  renderAll();
  api(`${ROUTES.orders}/${id}/deliver`, { method: "POST" });
}
function flashAlert(msg){
  const el = document.getElementById("toast");
  el.textContent = msg;
  el.style.display = "block";
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(()=> el.style.display = "none", 3500);
}

/* ---------- Staff Delivery panel ---------- */
function activeDeliveryCount(vehicle){
  return orders.filter(o => o.mode === vehicle && o.status !== "done").length;
}
function renderStaffPanel(){
  const body = document.getElementById("staff-body");
  if(!body) return; // panel not in DOM yet

  document.getElementById("staff-empty").style.display = deliveryStaff.length ? "none" : "block";
  document.getElementById("staff-count-note").textContent = `${deliveryStaff.length} staff`;

  const statusMeta = {
    available: { cls:"instock", label:"Available" },
    delivery:  { cls:"out",     label:"On delivery" },
    off:       { cls:"need",    label:"Off duty" },
  };

  body.innerHTML = deliveryStaff.map(s => {
    const meta = statusMeta[s.status] || statusMeta.available;
    return `
    <tr>
      <td>${s.name}</td>
      <td>${s.phone}</td>
      <td>${s.vehicle}</td>
      <td>${activeDeliveryCount(s.vehicle)}</td>
      <td><span class="status ${meta.cls}">${meta.label}</span></td>
      <td>
        <button class="icon-action edit" onclick="editStaff(${s.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
        <button class="icon-action delete" onclick="deleteStaff(${s.id})" title="Remove"><i class="fa-solid fa-trash"></i></button>
      </td>
    </tr>`;
  }).join("");

  const assignBody = document.getElementById("staff-assign-body");
  const active = orders.filter(o => o.status !== "done");
  assignBody.innerHTML = active.length
    ? active.map(o => {
        const current = deliveryStaff.find(s => s.vehicle === o.mode);
        return `
        <tr>
          <td>#${o.id}</td>
          <td>${o.customer}</td>
          <td><span class="status ${o.status}">${o.status === "pending" ? "Pending" : "Out for delivery"}</span></td>
          <td>
            <select onchange="assignStaffToOrder(${o.id}, this.value)">
              <option value="">— Unassigned —</option>
              ${deliveryStaff.map(s => `<option value="${s.id}"${current && current.id === s.id ? " selected" : ""}>${s.name} (${s.vehicle})</option>`).join("")}
            </select>
          </td>
        </tr>`;
      }).join("")
    : `<tr><td colspan="4">No active orders to assign.</td></tr>`;
}
function addStaff(e){
  e.preventDefault();
  const name = document.getElementById("staff-name").value.trim();
  const phone = document.getElementById("staff-phone").value.trim();
  const vehicle = document.getElementById("staff-vehicle").value.trim();
  const status = document.getElementById("staff-status").value;
  if(!name || !phone || !vehicle) return;

  api(ROUTES.staff, { method: "POST", body: JSON.stringify({ name, phone, vehicle, status }) })
    .then(({ ok, data }) => {
      if(!ok) return flashAlert(data.message || "Could not add staff.");
      deliveryStaff.push(data.staff);
      e.target.reset();
      renderAll();
      flashAlert(`${name} added to delivery staff.`);
    });
}
function editStaff(id){
  const s = deliveryStaff.find(x=>x.id===id);
  if(!s) return;
  const name = prompt("Full name", s.name);
  if(name === null) return;
  const phone = prompt("Phone number", s.phone);
  if(phone === null) return;
  const vehicle = prompt("Vehicle / ID", s.vehicle);
  if(vehicle === null) return;

  s.name = name.trim() || s.name;
  s.phone = phone.trim() || s.phone;
  const oldVehicle = s.vehicle;
  s.vehicle = vehicle.trim() || s.vehicle;
  orders.forEach(o => { if(o.mode === oldVehicle) o.mode = s.vehicle; }); // keep assigned orders pointing at this staffer
  renderAll();
  api(`${ROUTES.staff}/${id}`, { method: "PUT", body: JSON.stringify({ name: s.name, phone: s.phone, vehicle: s.vehicle }) });
}
function deleteStaff(id){
  const s = deliveryStaff.find(x=>x.id===id);
  if(!s) return;
  if(!confirm(`Remove ${s.name} from delivery staff?`)) return;
  deliveryStaff = deliveryStaff.filter(x=>x.id!==id);
  renderAll();
  api(`${ROUTES.staff}/${id}`, { method: "DELETE" });
}
function assignStaffToOrder(orderId, staffId){
  const o = orders.find(x=>x.id===orderId);
  if(!o) return;
  if(!staffId){
    o.mode = "Unassigned";
    o.staffId = null;
    renderAll();
    api(`${ROUTES.orders}/${orderId}/assign`, { method: "POST", body: JSON.stringify({ staff_id: null }) });
    return;
  }
  const s = deliveryStaff.find(x=>x.id===parseInt(staffId));
  if(!s) return;
  o.mode = s.vehicle;
  o.staffId = s.id;
  renderAll();
  flashAlert(`Order #${o.id} assigned to ${s.name}.`);
  api(`${ROUTES.orders}/${orderId}/assign`, { method: "POST", body: JSON.stringify({ staff_id: s.id }) });
}

/* ---------- Customers panel ---------- */
function renderCustomersTable(){
  document.getElementById("customers-body").innerHTML = customers.map(c => `
    <tr>
      <td>${c.name}</td>
      <td>${c.phone}</td>
      <td>${c.email}</td>
      <td>${c.orders}</td>
    </tr>
  `).join("");
}

/* ---------- Messages panel (customer contact-form -> admin inbox) ---------- */
function escapeHtml(s){
  return String(s).replace(/[&<>"']/g, c => ({ "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#39;" }[c]));
}
function timeAgo(iso){
  if(!iso) return "";
  const diffMs = Date.now() - new Date(iso).getTime();
  const mins = Math.round(diffMs / 60000);
  if(mins < 1) return "just now";
  if(mins < 60) return `${mins}m ago`;
  const hrs = Math.round(mins / 60);
  if(hrs < 24) return `${hrs}h ago`;
  return `${Math.round(hrs / 24)}d ago`;
}
function renderMessages(){
  const msgs = getMessages();
  const list = document.getElementById("messages-list");
  document.getElementById("messages-empty").style.display = msgs.length ? "none" : "block";
  list.innerHTML = msgs.map(m => `
    <div class="panel-card" style="margin-bottom:12px; ${m.read ? "" : "border-left:4px solid var(--brand-dark);"}">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
        <div>
          <div style="font-weight:700; display:flex; align-items:center; gap:8px;">
            ${escapeHtml(m.name || "Anonymous")}
            ${m.read ? "" : '<span class="status pending">New</span>'}
          </div>
          <div class="form-note" style="margin-top:2px;">${escapeHtml(m.email || "no email given")} · ${timeAgo(m.date)}</div>
        </div>
        <div style="display:flex; gap:8px;">
          ${m.read ? "" : `<button class="icon-action edit" title="Mark read" onclick="markMessageRead(${m.id})"><i class="fa-solid fa-envelope-open"></i></button>`}
          <button class="icon-action delete" title="Delete" onclick="deleteMessage(${m.id})"><i class="fa-solid fa-trash"></i></button>
        </div>
      </div>
      <p style="margin:10px 0 0; font-size:14px;">${escapeHtml(m.message || "")}</p>

      <div style="margin-top:12px; padding-top:12px; border-top:1px dashed var(--line);">
        ${m.reply ? `
          <div class="form-note" style="margin-bottom:6px;"><i class="fa-solid fa-reply"></i> <strong>Store reply</strong> · ${timeAgo(m.repliedAt)}</div>
          <p style="font-size:13.5px; background:var(--brand-soft); padding:9px 12px; border-radius:8px; margin-bottom:8px;">${escapeHtml(m.reply)}</p>
        ` : ""}
        <div style="display:flex; gap:8px;">
          <textarea id="reply-input-${m.id}" rows="2" placeholder="${m.reply ? "Update your reply…" : "Write a reply to this customer…"}" style="flex:1; border:1.5px solid var(--line); border-radius:8px; padding:8px 10px; font-family:inherit; font-size:13.5px; resize:vertical;"></textarea>
          <button class="btn btn-small btn-primary" style="align-self:flex-end;" onclick="sendReply(${m.id})"><i class="fa-solid fa-paper-plane"></i> Send</button>
        </div>
      </div>
    </div>
  `).join("");
}
function sendReply(id){
  const input = document.getElementById(`reply-input-${id}`);
  const text = input ? input.value.trim() : "";
  if(!text) return;
  api(`${ROUTES.messages}/${id}/reply`, { method: "POST", body: JSON.stringify({ reply: text }) })
    .then(({ ok, data }) => {
      if(!ok) return flashAlert(data.message || "Could not send that reply.");
      const m = messages.find(x => x.id === id);
      if(m){ m.reply = data.message.reply; m.repliedAt = data.message.replied_at; m.read = true; }
      renderAll();
      flashAlert("Reply sent to the customer.");
    });
}
function markMessageRead(id){
  const m = messages.find(x => x.id === id);
  if(m) m.read = true;
  renderAll();
  api(`${ROUTES.messages}/${id}/read`, { method: "POST" });
}
function markAllMessagesRead(){
  messages.forEach(m => m.read = true);
  renderAll();
  api(`${ROUTES.messages}/read-all`, { method: "POST" });
}
function deleteMessage(id){
  if(!confirm("Delete this message?")) return;
  messages = messages.filter(m => m.id !== id);
  renderAll();
  api(`${ROUTES.messages}/${id}`, { method: "DELETE" });
}

/* ---------- Inventory / Stock management panel ---------- */
function stockStatus(qty){
  if(qty < settings.lowStockThreshold) return { cls:"need", label:"Need to Order" };
  if(qty < settings.lowStockThreshold * 2) return { cls:"low", label:"Low Stock" };
  return { cls:"instock", label:"In Stock" };
}
function daysUntil(dateStr){
  if(!dateStr) return null;
  return Math.ceil((new Date(dateStr + "T00:00:00") - new Date(new Date().toDateString())) / 86400000);
}
function expiryStatus(dateStr){
  if(!dateStr) return { cls:"", label:"—" };
  const days = daysUntil(dateStr);
  if(days < 0) return { cls:"need", label:`Expired ${dateStr}` };
  if(days <= 30) return { cls:"low", label:`${dateStr} (${days}d)` };
  return { cls:"instock", label:dateStr };
}
function renderInventory(){
  const rows = [...adminProducts].sort((a,b)=>a.qty-b.qty);
  document.getElementById("inventory-body").innerHTML = rows.map((p,i) => {
    const s = stockStatus(p.qty);
    const ex = expiryStatus(p.expiryDate);
    return `
    <tr>
      <td>#${i+1}</td>
      <td><img class="row-thumb" src="${p.img}" alt=""> ${p.name}${p.discountPercent ? ` <span class="status low" style="margin-left:4px;">-${p.discountPercent}%</span>` : ""}</td>
      <td>${p.cat}</td>
      <td>${p.unit || "each"}</td>
      <td>${p.qty}</td>
      <td>${ex.cls ? `<span class="status ${ex.cls}">${ex.label}</span>` : ex.label}</td>
      <td><span class="status ${s.cls}">${s.label}</span></td>
      <td>
        <button class="icon-action edit" onclick="editProduct(${p.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
        <button class="icon-action delete" onclick="deleteProduct(${p.id})" title="Delete"><i class="fa-solid fa-trash"></i></button>
      </td>
    </tr>`;
  }).join("");
}
function exportInventory(){
  let csv = "Product,Category,Unit,Qty,Expiry,Status\n";
  adminProducts.forEach(p => { csv += `${p.name},${p.cat},${p.unit || "each"},${p.qty},${p.expiryDate || ""},${stockStatus(p.qty).label}\n`; });
  const blob = new Blob([csv], { type:"text/csv" });
  const a = document.createElement("a");
  a.href = URL.createObjectURL(blob);
  a.download = "minimart-stock.csv";
  a.click();
}
/* ---------- Purchase Orders panel (manual — nothing here is auto-generated) ---------- */
function populatePOProductSelect(){
  const sel = document.getElementById("po-line-product");
  if(!sel) return;
  const current = sel.value;
  const all = [...adminProducts].sort((a,b)=>a.name.localeCompare(b.name));
  sel.innerHTML = all.length
    ? all.map(p => `<option value="${p.id}"${String(p.id)===current?" selected":""}>${p.name} (${p.unit || "each"}, ${p.qty} in stock)</option>`).join("")
    : `<option value="">No products yet</option>`;
}
function addPurchaseOrderLine(){
  const sel = document.getElementById("po-line-product");
  const productId = parseInt(sel.value);
  const p = adminProducts.find(x=>x.id===productId);
  if(!p) return flashAlert("Pick a product first.");
  const qty = parseInt(document.getElementById("po-line-qty").value) || 1;
  const unitCost = parseFloat(document.getElementById("po-line-cost").value) || 0;

  const existing = poLines.find(l => l.productId === productId);
  if(existing){ existing.qty += qty; existing.unitCost = unitCost || existing.unitCost; }
  else poLines.push({ productId, name: p.name, unit: p.unit || "each", qty, unitCost });

  document.getElementById("po-line-qty").value = 1;
  document.getElementById("po-line-cost").value = "";
  renderPOLineList();
}
function removePurchaseOrderLine(productId){
  poLines = poLines.filter(l => l.productId !== productId);
  renderPOLineList();
}
function renderPOLineList(){
  const list = document.getElementById("po-line-list");
  const empty = document.getElementById("po-line-empty");
  if(!list) return;
  empty.style.display = poLines.length ? "none" : "block";
  list.innerHTML = poLines.map(l => `
    <li><strong>${l.name}</strong> — ${l.qty} ${l.unit} @ ${money(l.unitCost)}
      <button type="button" class="icon-action delete" style="margin-left:8px;" onclick="removePurchaseOrderLine(${l.productId})" title="Remove"><i class="fa-solid fa-xmark"></i></button>
    </li>`).join("");
}
function submitPurchaseOrder(e){
  e.preventDefault();
  const supplier = document.getElementById("po-supplier").value.trim();
  const expectedDate = document.getElementById("po-expected").value || null;
  const notes = document.getElementById("po-notes").value.trim();
  if(!supplier) return flashAlert("Enter a supplier name.");
  if(!poLines.length) return flashAlert("Add at least one product line to the order.");

  api(ROUTES.purchaseOrders, {
    method: "POST",
    body: JSON.stringify({
      supplier_name: supplier,
      expected_date: expectedDate,
      notes,
      items: poLines.map(l => ({ product_id: l.productId, qty: l.qty, unit_cost: l.unitCost })),
    }),
  }).then(({ ok, data }) => {
    if(!ok) return flashAlert(data.message || "Could not create that purchase order.");
    purchaseOrders.unshift(data.purchaseOrder);
    poLines = [];
    e.target.reset();
    renderAll();
    flashAlert(`Purchase order sent to ${supplier} for ${data.purchaseOrder.items.length} product(s).`);
  });
}
function receivePurchaseOrder(id){
  const po = purchaseOrders.find(x=>x.id===id);
  if(!po) return;
  if(!confirm(`Mark this order from ${po.supplier} as received? Stock will be added to inventory.`)) return;

  api(`${ROUTES.purchaseOrders}/${id}/receive`, { method: "POST" }).then(({ ok, data }) => {
    if(!ok) return flashAlert(data.message || "Could not receive that order.");
    po.status = "received";
    data.products.forEach(updated => {
      const idx = adminProducts.findIndex(x=>x.id===updated.id);
      if(idx !== -1) adminProducts[idx] = updated;
    });
    renderAll();
    flashAlert(`Order received — stock updated for ${data.products.length} product(s).`);
  });
}
function cancelPurchaseOrder(id){
  if(!confirm("Cancel this purchase order?")) return;
  api(`${ROUTES.purchaseOrders}/${id}`, { method: "DELETE" }).then(({ ok, data }) => {
    if(!ok) return flashAlert(data.message || "Could not cancel that order.");
    const po = purchaseOrders.find(x=>x.id===id);
    if(po) po.status = "cancelled";
    renderAll();
  });
}
function renderPurchaseOrders(){
  populatePOProductSelect();
  renderPOLineList();

  const body = document.getElementById("po-body");
  if(!body) return;
  const statusMeta = { pending:{cls:"pending",label:"Pending"}, received:{cls:"instock",label:"Received"}, cancelled:{cls:"need",label:"Cancelled"} };
  document.getElementById("po-empty").style.display = purchaseOrders.length ? "none" : "block";
  body.innerHTML = purchaseOrders.map(po => {
    const meta = statusMeta[po.status] || statusMeta.pending;
    const itemsSummary = po.items.map(i => `${i.qty}× ${i.name}`).join(", ");
    return `
    <tr>
      <td>#${po.id}</td>
      <td>${po.supplier}</td>
      <td>${itemsSummary}</td>
      <td>${money(po.totalCost)}</td>
      <td><span class="status ${meta.cls}">${meta.label}</span></td>
      <td>
        ${po.status === "pending" ? `
          <button class="btn btn-small btn-primary" onclick="receivePurchaseOrder(${po.id})">Receive</button>
          <button class="icon-action delete" onclick="cancelPurchaseOrder(${po.id})" title="Cancel"><i class="fa-solid fa-xmark"></i></button>
        ` : ""}
      </td>
    </tr>`;
  }).join("");
}

/* ---------- Promotions panel ---------- */
function populatePromotionProductList(){
  const wrap = document.getElementById("promo-product-list");
  if(!wrap) return;
  const checked = new Set(Array.from(wrap.querySelectorAll("input:checked")).map(i => parseInt(i.value)));
  const all = [...adminProducts].sort((a,b)=>a.name.localeCompare(b.name));
  
  wrap.innerHTML = all.length
    ? all.map(p => {
        const expDate = p.exp_date || p.expiry_date || p.exp || 'N/A';
        return `
          <label style="display: flex; align-items: center; justify-content: space-between; padding: 6px 4px; border-bottom: 1px solid #f0f0f0; width: 100%; box-sizing: border-box; cursor: pointer;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <input type="checkbox" value="${p.id}"${checked.has(p.id)?" checked":""} style="margin: 0; cursor: pointer;">
              <span style="font-weight: 500; font-size: 13.5px; color: #1f2937;">${p.name}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <span style="background: #fee2e2; color: #dc2626; padding: 2px 6px; border-radius: 4px; font-size: 11.5px; font-weight: 600;">Exp: ${expDate}</span>
              <span style="color: #059669; font-weight: 700; font-size: 13px;">${money(p.price)}</span>
            </div>
          </label>`;
      }).join("")
    : `<p class="form-note">No products yet.</p>`;
}
function submitPromotion(e){
  e.preventDefault();
  const title = document.getElementById("promo-title").value.trim();
  const description = document.getElementById("promo-desc").value.trim();
  const discountPercent = parseFloat(document.getElementById("promo-discount").value) || 0;
  const startsAt = document.getElementById("promo-starts").value || null;
  const endsAt = document.getElementById("promo-ends").value || null;
  const productIds = Array.from(document.querySelectorAll("#promo-product-list input:checked")).map(i => parseInt(i.value));
  if(!title) return flashAlert("Give the promotion a title.");
  if(!productIds.length) return flashAlert("Pick at least one product for the promotion.");

  api(ROUTES.promotions, {
    method: "POST",
    body: JSON.stringify({ title, description, discount_percent: discountPercent, starts_at: startsAt, ends_at: endsAt, product_ids: productIds }),
  }).then(({ ok, data }) => {
    if(!ok) return flashAlert(data.message || "Could not create that promotion.");
    promotions.unshift(data.promotion);
    data.products.forEach(updated => {
      const idx = adminProducts.findIndex(x=>x.id===updated.id);
      if(idx !== -1) adminProducts[idx] = updated;
    });
    e.target.reset();
    document.getElementById("promo-discount").value = 10;
    renderAll();
    flashAlert(`"${title}" launched across ${productIds.length} product(s).`);
  });
}
function endPromotion(id){
  const promo = promotions.find(x=>x.id===id);
  if(!promo) return;
  if(!confirm(`End "${promo.title}" now? The discount will be removed from its products.`)) return;

  api(`${ROUTES.promotions}/${id}`, { method: "DELETE" }).then(({ ok, data }) => {
    if(!ok) return flashAlert(data.message || "Could not end that promotion.");
    promo.status = "ended";
    data.products.forEach(updated => {
      const idx = adminProducts.findIndex(x=>x.id===updated.id);
      if(idx !== -1) adminProducts[idx] = updated;
    });
    renderAll();
    flashAlert(`"${promo.title}" ended.`);
  });
}
function renderPromotions(){
  populatePromotionProductList();

  const body = document.getElementById("promotions-body");
  if(!body) return;
  const statusMeta = { active:{cls:"instock",label:"Active"}, scheduled:{cls:"pending",label:"Scheduled"}, ended:{cls:"need",label:"Ended"} };
  document.getElementById("promotions-empty").style.display = promotions.length ? "none" : "block";
  body.innerHTML = promotions.map(promo => {
    const meta = statusMeta[promo.status] || statusMeta.active;
    const dates = [promo.startsAt, promo.endsAt].filter(Boolean).join(" → ") || "No end date";
    return `
    <tr>
      <td>${promo.title}</td>
      <td>${promo.productNames.join(", ")}</td>
      <td>-${promo.discountPercent}%</td>
      <td>${dates}</td>
      <td><span class="status ${meta.cls}">${meta.label}</span></td>
      <td>${promo.status !== "ended" ? `<button class="btn btn-small" onclick="endPromotion(${promo.id})">End</button>` : ""}</td>
    </tr>`;
  }).join("");
}

/* ---------- Reports panel ---------- */
function renderReports(){
  const totalSales = orders.reduce((s,o)=>s+o.total,0);
  const delivered = orders.filter(o=>o.status==="done").length;
  const avg = orders.length ? totalSales/orders.length : 0;
  document.getElementById("report-total").textContent = money(totalSales);
  document.getElementById("report-orders").textContent = orders.length;
  document.getElementById("report-delivered").textContent = delivered;
  document.getElementById("report-avg").textContent = money(avg);

  renderChartRevenue();
  renderChartCategory();
  renderChartPO();
}

/* ---------- Charts (dependency-free SVG) ---------- */
const GRADS = { green:["#9BF59B","#3FBF63"], red:["#FF9B92","#D8342A"], dark:["#4FA874","#1E5C34"] };
let reportMonth = "all";

function chartDefs(id){
  return "<defs>" + Object.entries(GRADS).map(([k,[a,b]]) =>
    `<linearGradient id="${id}-${k}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="${a}"/><stop offset="1" stop-color="${b}"/></linearGradient>`).join("") + "</defs>";
}
function niceMax(v){
  const step = Math.pow(10, Math.floor(Math.log10(Math.max(v,1))));
  const n = v / step, f = [1,1.2,1.5,2,2.5,3,4,5,6,8,10].find(x => n <= x + 1e-9);
  return f * step;
}
const shorten = (t, n) => t.length > n ? t.slice(0, n - 1) + "…" : t;
function bindTip(el, onClick){
  let tip = document.getElementById("chart-tip");
  if(!tip){ tip = document.createElement("div"); tip.id = "chart-tip"; tip.className = "chart-tip"; document.body.appendChild(tip); }
  el.onmousemove = e => {
    const t = e.target.closest("[data-tip]");
    if(!t){ tip.style.opacity = 0; return; }
    tip.innerHTML = t.dataset.tip; tip.style.opacity = 1;
    tip.style.left = (e.clientX + 14) + "px"; tip.style.top = (e.clientY - 12) + "px";
  };
  el.onmouseleave = () => { tip.style.opacity = 0; };
  el.onclick = onClick ? (e => { const t = e.target.closest("[data-key]"); if(t) onClick(t.dataset.key); }) : null;
}

/* vertical bars */
function drawBarChart(elId, items, opts = {}){
  const el = document.getElementById(elId); if(!el) return;
  if(!items.length){ el.innerHTML = '<div class="bar-empty">No data yet</div>'; return; }
  const W = Math.max(el.clientWidth, 300), H = el.clientHeight || 240;
  const m = { t:26, r:10, b:34, l:opts.left || 48 }, iw = W - m.l - m.r, ih = H - m.t - m.b;
  const fmt = opts.format || (v => v);
  const rawMax = Math.max(...items.map(i => i.value), 1);
  const fixed = opts.minMax && rawMax <= opts.minMax;          // e.g. always show $0 - $1,000
  const ticks = fixed ? 5 : 4;
  const max = fixed ? opts.minMax : niceMax(rawMax);
  const slot = iw / items.length, bw = Math.min(slot * 0.6, opts.maxBar || 46);
  let svg = `<svg viewBox="0 0 ${W} ${H}" width="100%" height="${H}" role="img" aria-label="${opts.label || "Bar chart"}">${chartDefs(elId)}`;
  for(let i = 0; i <= ticks; i++){
    const v = max * i / ticks, y = m.t + ih - (v / max) * ih;
    svg += `<line x1="${m.l}" x2="${W - m.r}" y1="${y}" y2="${y}" stroke="#E3F1E5" ${i ? 'stroke-dasharray="4 5"' : ""}/>`;
    svg += `<text x="${m.l - 8}" y="${y + 4}" text-anchor="end" font-size="11" fill="#8A9B8E">${fmt(+v.toFixed(2))}</text>`;
  }
  items.forEach((it, i) => {
    const h = Math.max((it.value / max) * ih, it.value > 0 ? 3 : 0);
    const x = m.l + slot * i + (slot - bw) / 2, y = m.t + ih - h, cx = x + bw / 2;
    const dim = opts.selected && opts.selected !== "all" && opts.selected !== it.key ? 0.35 : 1;
    svg += `<g data-key="${it.key}" data-tip="<b>${it.tip || it.label}</b><br>${fmt(it.value)}" style="cursor:${opts.onClick ? "pointer" : "default"}" opacity="${dim}">
      <rect x="${x}" y="${m.t}" width="${bw}" height="${ih}" fill="transparent"/>
      <rect class="bar" x="${x}" y="${y}" width="${bw}" height="${h}" rx="7" fill="url(#${elId}-${it.color || opts.color || "green"})"/></g>`;
    if(it.value > 0) svg += `<text x="${cx}" y="${y - 7}" text-anchor="middle" font-size="11" font-weight="700" fill="#1E5C34" opacity="${dim}">${fmt(it.value)}</text>`;
    svg += `<text x="${cx}" y="${H - m.b + 18}" text-anchor="middle" font-size="11" fill="#66786B">${shorten(it.label, opts.labelMax || 10)}</text>`;
  });
  el.innerHTML = svg + "</svg>";
  bindTip(el, opts.onClick);
}

/* line chart (area + points) */
function drawLineChart(elId, items, opts = {}){
  const el = document.getElementById(elId); if(!el) return;
  if(!items.length){ el.innerHTML = '<div class="bar-empty">No data yet</div>'; return; }
  const W = Math.max(el.clientWidth, 300), H = el.clientHeight || 240;
  const m = { t:28, r:28, b:34, l:52 }, iw = W - m.l - m.r, ih = H - m.t - m.b;
  const fmt = opts.format || (v => v);
  const max = niceMax(Math.max(...items.map(i => i.value), 1));
  const px = i => items.length === 1 ? m.l + iw / 2 : m.l + iw * i / (items.length - 1);
  const py = v => m.t + ih - (v / max) * ih;
  let svg = `<svg viewBox="0 0 ${W} ${H}" width="100%" height="${H}" role="img" aria-label="${opts.label || "Line chart"}"><defs><linearGradient id="${elId}-area" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#3FBF63" stop-opacity=".35"/><stop offset="1" stop-color="#3FBF63" stop-opacity="0"/></linearGradient></defs>`;
  for(let i = 0; i <= 4; i++){
    const v = max * i / 4, y = py(v);
    svg += `<line x1="${m.l}" x2="${W - m.r}" y1="${y}" y2="${y}" stroke="#E3F1E5" ${i ? 'stroke-dasharray="4 5"' : ""}/>`;
    svg += `<text x="${m.l - 8}" y="${y + 4}" text-anchor="end" font-size="11" fill="#8A9B8E">${fmt(+v.toFixed(2))}</text>`;
  }
  const pts = items.map((it, i) => [px(i), py(it.value)]);
  if(items.length > 1){
    svg += `<path d="M${pts[0][0]},${m.t + ih} ${pts.map(p => "L" + p[0] + "," + p[1]).join(" ")} L${pts[pts.length-1][0]},${m.t + ih} Z" fill="url(#${elId}-area)"/>`;
    svg += `<polyline points="${pts.map(p => p.join(",")).join(" ")}" fill="none" stroke="#1E8E45" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>`;
  }
  items.forEach((it, i) => {
    const [x, y] = pts[i], sel = opts.selected === it.key;
    svg += `<g data-key="${it.key}" data-tip="<b>${it.tip || it.label}</b><br>${fmt(it.value)}" style="cursor:${opts.onClick ? "pointer" : "default"}">
      <rect x="${x - 22}" y="${m.t}" width="44" height="${ih}" fill="transparent"/>
      <circle class="dotpt" cx="${x}" cy="${y}" r="${sel ? 8 : 5.5}" fill="#fff" stroke="${sel ? "#16261A" : "#1E8E45"}" stroke-width="3"/></g>`;
    svg += `<text x="${x}" y="${y - 13}" text-anchor="middle" font-size="11" font-weight="700" fill="#1E5C34">${fmt(it.value)}</text>`;
    svg += `<text x="${x}" y="${H - m.b + 18}" text-anchor="middle" font-size="11" fill="#66786B">${it.label}</text>`;
  });
  el.innerHTML = svg + "</svg>";
  bindTip(el, opts.onClick);
}

/* horizontal bars, with an optional threshold line */
function drawHBarChart(elId, items, opts = {}){
  const el = document.getElementById(elId); if(!el) return;
  if(!items.length){ el.innerHTML = '<div class="bar-empty">No data yet</div>'; return; }
  const W = Math.max(el.clientWidth, 260), rowH = 26;
  const m = { t:8, r:56, b:22, l:92 }, H = items.length * rowH + m.t + m.b, iw = W - m.l - m.r;
  const fmt = opts.format || (v => v);
  const max = niceMax(Math.max(...items.map(i => i.value), opts.threshold || 1, 1) * 1.05);
  let svg = `<svg viewBox="0 0 ${W} ${H}" width="100%" height="${H}" role="img" aria-label="${opts.label || "Bar chart"}">${chartDefs(elId)}`;
  for(let i = 0; i <= 4; i++){
    const x = m.l + iw * i / 4;
    svg += `<line x1="${x}" x2="${x}" y1="${m.t}" y2="${H - m.b}" stroke="#E3F1E5" ${i ? 'stroke-dasharray="4 5"' : ""}/>`;
    svg += `<text x="${x}" y="${H - 6}" text-anchor="middle" font-size="10.5" fill="#8A9B8E">${fmt(+(max * i / 4).toFixed(0))}</text>`;
  }
  if(opts.threshold){
    const tx = m.l + (opts.threshold / max) * iw;
    svg += `<line x1="${tx}" x2="${tx}" y1="${m.t}" y2="${H - m.b}" stroke="#16261A" stroke-width="1.3" stroke-dasharray="5 4"/>`;
  }
  items.forEach((it, i) => {
    const y = m.t + i * rowH + 4, bh = rowH - 9, w = Math.max((it.value / max) * iw, it.value > 0 ? 3 : 0);
    svg += `<g data-tip="<b>${it.tip || it.label}</b><br>${fmt(it.value)}">
      <rect x="${m.l}" y="${y - 3}" width="${iw + m.r - 6}" height="${rowH - 3}" fill="transparent"/>
      <text x="${m.l - 8}" y="${y + bh / 2 + 4}" text-anchor="end" font-size="11.5" fill="#33473A">${shorten(it.label, 13)}</text>
      <rect class="bar" x="${m.l}" y="${y}" width="${w}" height="${bh}" rx="6" fill="url(#${elId}-${it.color || "dark"})"/>
      <text x="${m.l + w + 6}" y="${y + bh / 2 + 4}" font-size="11" font-weight="700" fill="#1E5C34">${fmt(it.value)}</text></g>`;
  });
  el.innerHTML = svg + "</svg>";
  bindTip(el);
}

/* ---- data helpers ---- */
const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
function monthKey(iso){
  const d = iso ? new Date(iso) : null;
  return d && !isNaN(d) ? d.getFullYear() + "-" + String(d.getMonth() + 1).padStart(2, "0") : "unknown";
}
function monthLabel(k){
  if(k === "unknown") return "No date";
  const [y, mo] = k.split("-"); return MONTHS[+mo - 1] + " " + y;
}
function salesByMonth(){
  const map = {};
  orders.forEach(o => { const k = monthKey(o.placedAt); (map[k] = map[k] || { key:k, value:0, count:0, done:0 }); map[k].value += o.total; map[k].count++; if(o.status === "done") map[k].done++; });
  return Object.values(map).sort((a, b) => a.key.localeCompare(b.key));
}

function renderChartRevenue(){
  const months = salesByMonth();
  const sel = document.getElementById("report-month");
  if(sel){
    if(reportMonth !== "all" && !months.some(m => m.key === reportMonth)) reportMonth = "all";
    sel.innerHTML = '<option value="all">All months</option>' + months.map(m => `<option value="${m.key}">${monthLabel(m.key)}</option>`).join("");
    sel.value = reportMonth;
  }
  drawLineChart("chart-revenue",
    months.map(m => ({ key:m.key, label:monthLabel(m.key), value:+m.value.toFixed(2), tip:`${monthLabel(m.key)} · ${m.count} invoice${m.count === 1 ? "" : "s"}` })),
    { format:v => "$" + v, label:"Sales by month", selected:reportMonth, onClick:k => setReportMonth(reportMonth === k ? "all" : k) });
}
function setReportMonth(k){ reportMonth = k; renderChartRevenue(); }

function renderChartCategory(){
  const byCat = {};
  adminProducts.forEach(p => { byCat[p.cat] = (byCat[p.cat] || 0) + p.price * p.qty; });
  drawHBarChart("chart-category",
    Object.keys(byCat).map(k => ({ label:k, value:+byCat[k].toFixed(2), color:byCat[k] >= 100 ? "green" : "red" })),
    { format:v => "$" + v, threshold:100, label:"Stock value by category" });
}

let poVendor = "all";
function setPOVendor(v){ poVendor = v; renderChartPO(); }
function filteredPOs(){ return purchaseOrders.filter(po => poVendor === "all" || po.supplier === poVendor); }

function renderChartPO(){
  const sel = document.getElementById("po-vendor");
  if(sel){
    const vendors = [...new Set(purchaseOrders.map(po => po.supplier || "Unknown"))].sort((a, b) => a.localeCompare(b));
    if(poVendor !== "all" && !vendors.includes(poVendor)) poVendor = "all";
    sel.innerHTML = '<option value="all">All vendors</option>' + vendors.map(v => `<option value="${v}">${v}</option>`).join("");
    sel.value = poVendor;
  }
  const live = filteredPOs().filter(po => po.status !== "cancelled");
  const fmt = v => "$" + v.toLocaleString("en-US");
  const opts = { format:fmt, color:"dark", label:"Purchase orders", maxBar:64, labelMax:14, minMax:100, left:64 };
  if(poVendor === "all"){
    const map = {};
    live.forEach(po => { const k = po.supplier || "Unknown"; (map[k] = map[k] || { value:0, count:0 }); map[k].value += po.totalCost; map[k].count++; });
    drawBarChart("chart-po",
      Object.keys(map).map(k => ({ key:k, label:k, value:+map[k].value.toFixed(2), tip:`${k} · ${map[k].count} order${map[k].count === 1 ? "" : "s"}` })),
      { ...opts, onClick:k => setPOVendor(k) });
  } else {
    // one vendor selected: show each of its purchase orders
    drawBarChart("chart-po",
      live.slice().sort((a, b) => a.id - b.id).map(po => ({ key:"po" + po.id, label:"PO #" + po.id, value:+po.totalCost.toFixed(2), tip:`${po.supplier} · PO #${po.id} · ${po.status}` })),
      opts);
  }
}
window.addEventListener("resize", () => {
  const panel = document.getElementById("panel-reports");
  if(panel && panel.style.display !== "none") renderReports();
});

/* ---- Export invoices to Excel (.xlsx) ---- */
function exportInvoicesExcel(){
  const list = orders.filter(o => reportMonth === "all" || monthKey(o.placedAt) === reportMonth)
                     .sort((a, b) => (b.placedAt || "").localeCompare(a.placedAt || ""));
  if(!list.length){ alert("No invoices for this month."); return; }
  const statusName = { pending:"Pending", out:"Out for delivery", done:"Delivered" };
  const fmtDate = iso => { const d = iso ? new Date(iso) : null; return d && !isNaN(d) ? `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,"0")}-${String(d.getDate()).padStart(2,"0")} ${String(d.getHours()).padStart(2,"0")}:${String(d.getMinutes()).padStart(2,"0")}` : ""; };
  const total = list.reduce((s, o) => s + o.total, 0);
  const invoices = [["Invoice #","Date","Customer","Items","Total ($)","Status","Delivery mode"]]
    .concat(list.map(o => ["#" + o.id, fmtDate(o.placedAt), o.customer, o.items, { money:+o.total.toFixed(2) }, statusName[o.status] || o.status, o.mode || ""]))
    .concat([["TOTAL","","",list.reduce((s, o) => s + o.items, 0),{ money:+total.toFixed(2) },"",""]]);
  const summary = [["Month","Invoices","Sales ($)","Delivered"]]
    .concat(salesByMonth().filter(m => reportMonth === "all" || m.key === reportMonth).map(m => [monthLabel(m.key), m.count, { money:+m.value.toFixed(2) }, m.done]));
  XLSXLite.download([
    { name:"Invoices", rows:invoices, widths:[12,18,26,8,12,18,18] },
    { name:"Monthly summary", rows:summary, widths:[16,10,12,10] }
  ], `minimart-invoices-${reportMonth === "all" ? "all-months" : reportMonth}.xlsx`);
}
/* ---- Export stock value (Excel) ---- */
function exportStockExcel(){
  const cats = {};
  adminProducts.forEach(p => { const c = cats[p.cat] = cats[p.cat] || { n:0, value:0 }; c.n++; c.value += p.price * p.qty; });
  const catRows = [["Category","Products","Stock value ($)","Level"]].concat(
    Object.keys(cats).map(k => [k, cats[k].n, { money:+cats[k].value.toFixed(2) }, cats[k].value >= 100 ? "$100 or more" : "Under $100"]));
  const prodRows = [["Product","Category","Unit price ($)","Qty","Stock value ($)"]].concat(
    adminProducts.slice().sort((a, b) => String(a.cat).localeCompare(String(b.cat)) || a.name.localeCompare(b.name))
      .map(p => [p.name, p.cat || "", { money:+p.price.toFixed(2) }, p.qty, { money:+(p.price * p.qty).toFixed(2) }]));
  XLSXLite.download([
    { name:"Stock by category", rows:catRows, widths:[22,10,16,16] },
    { name:"Products", rows:prodRows, widths:[30,20,14,8,16] }
  ], "minimart-stock-value.xlsx");
}

/* ---- Export purchase orders (Excel) ---- */
function exportPOExcel(){
  const list = filteredPOs();
  if(!list.length){ alert("No purchase orders for this vendor."); return; }
  const label = { pending:"Pending", received:"Received", cancelled:"Cancelled" };
  const poRows = [["PO #","Supplier","Status","Created","Expected date","Items","Total cost ($)"]].concat(
    list.map(po => ["#" + po.id, po.supplier, label[po.status] || po.status, (po.createdAt || "").slice(0, 10), po.expectedDate || "",
      po.items.map(i => `${i.qty}× ${i.name}`).join(", "), { money:+po.totalCost.toFixed(2) }]));
  const sup = {};
  list.filter(po => po.status !== "cancelled").forEach(po => { const c = sup[po.supplier] = sup[po.supplier] || { n:0, value:0 }; c.n++; c.value += po.totalCost; });
  const supRows = [["Supplier","Orders","Total spend ($)"]].concat(Object.keys(sup).map(k => [k, sup[k].n, { money:+sup[k].value.toFixed(2) }]));
  XLSXLite.download([
    { name:"Purchase orders", rows:poRows, widths:[8,22,12,12,14,50,14] },
    { name:"Spend by supplier", rows:supRows, widths:[24,10,16] }
  ], `minimart-purchase-orders-${poVendor === "all" ? "all-vendors" : poVendor.replace(/[^\w-]+/g, "_")}.xlsx`);
}

function exportReport(){
  let csv = "Order ID,Customer,Items,Total,Status,Mode,Placed\n";
  orders.forEach(o => { csv += `${o.id},${o.customer},${o.items},${o.total},${o.status},${o.mode},${o.placed}\n`; });
  const blob = new Blob([csv], { type:"text/csv" });
  const a = document.createElement("a");
  a.href = URL.createObjectURL(blob);
  a.download = "minimart-sales-report.csv";
  a.click();
}

/* ---------- Discount low-stock items (Settings panel) ---------- */
function getSalePrice(p){
  return p.discountPercent ? +(p.price * (1 - p.discountPercent / 100)).toFixed(2) : p.price;
}
function lowStockProducts(){
  return adminProducts.filter(p => p.qty < settings.lowStockThreshold);
}
function renderDiscountPanel(){
  const select = document.getElementById("discount-product");
  if(!select) return; // panel not in DOM yet

  const all = [...adminProducts].sort((a,b)=>a.name.localeCompare(b.name));
  const current = select.value;
  select.innerHTML = all.length
    ? all.map(p => `<option value="${p.id}"${String(p.id)===current?" selected":""}>${p.name} (${p.qty} left)${p.discountPercent ? ` — ${p.discountPercent}% off` : ""}</option>`).join("")
    : `<option value="">No products yet</option>`;

  updateDiscountPreview();

  const list = document.getElementById("discount-active-list");
  const discounted = adminProducts.filter(p => p.discountPercent);
  list.innerHTML = discounted.length
    ? discounted.map(p => `<li><strong>${p.name}</strong> — ${money(p.price)} <i class="fa-solid fa-arrow-right" style="font-size:11px; color:#8a9a8c;"></i> <span style="color:var(--brand-dark); font-weight:700;">${money(getSalePrice(p))}</span> (-${p.discountPercent}%) <button class="btn btn-small" style="margin-left:8px;" onclick="removeDiscount(${p.id})">Remove</button></li>`).join("")
    : "<li>No active discounts.</li>";
}
function updateDiscountPreview(){
  const select = document.getElementById("discount-product");
  const pricePreview = document.getElementById("discount-price-preview");
  const salePreview = document.getElementById("discount-sale-preview");
  if(!select || !pricePreview || !salePreview) return;
  const p = adminProducts.find(x => String(x.id) === select.value);
  const pct = parseFloat(document.getElementById("discount-percent").value) || 0;
  if(!p){
    pricePreview.value = "";
    salePreview.value = "";
    return;
  }
  pricePreview.value = money(p.price);
  salePreview.value = money(+(p.price * (1 - Math.min(Math.max(pct,0),90) / 100)).toFixed(2));
}
function applyDiscountForm(e){
  e.preventDefault();
  const select = document.getElementById("discount-product");
  const p = adminProducts.find(x => String(x.id) === select.value);
  if(!p) return flashAlert("Pick a product first.");
  let pct = parseFloat(document.getElementById("discount-percent").value);
  if(isNaN(pct) || pct < 0) pct = 0;
  if(pct > 90) pct = 90;
  p.discountPercent = pct || undefined;
  renderAll();
  flashAlert(pct > 0
    ? `${p.name} discounted ${pct}% — now ${money(getSalePrice(p))}.`
    : `Discount removed for ${p.name}.`);
  api(`${ROUTES.products}/${p.id}/discount`, { method: "POST", body: JSON.stringify({ percent: pct }) });
}
function removeDiscount(id){
  const p = adminProducts.find(x => x.id === id);
  if(!p) return;
  delete p.discountPercent;
  renderAll();
  flashAlert(`Discount removed for ${p.name}.`);
  api(`${ROUTES.products}/${id}/discount`, { method: "DELETE" });
}

/* ---------- Settings panel ---------- */
function saveSettings(e){
  e.preventDefault();
  settings.storeName = document.getElementById("set-name").value;
  settings.hours = document.getElementById("set-hours").value;
  settings.deliveryRate = parseFloat(document.getElementById("set-rate").value) || settings.deliveryRate;
  settings.lowStockThreshold = parseInt(document.getElementById("set-threshold").value) || settings.lowStockThreshold;
  flashAlert("Settings saved.");
  renderAll();
  api(ROUTES.settings, {
    method: "PUT",
    body: JSON.stringify({
      store_name: settings.storeName,
      hours: settings.hours,
      delivery_rate: settings.deliveryRate,
      low_stock_threshold: settings.lowStockThreshold,
    }),
  });
}

document.addEventListener("DOMContentLoaded", ()=>{
  document.getElementById("set-name").value = settings.storeName;
  document.getElementById("set-hours").value = settings.hours;
  document.getElementById("set-rate").value = settings.deliveryRate;
  document.getElementById("set-threshold").value = settings.lowStockThreshold;
  showPanel("dashboard");
});


/* ============================================================
   AUTO REFRESH — polls /admin/api/data and re-renders the live
   panels without a page reload. Pauses while the tab is hidden
   or while you are typing in a form, so nothing you are editing
   gets wiped.
   ============================================================ */
const AUTO_REFRESH_MS = 100;   // change to taste (10000 = 10 seconds)
let autoRefreshBusy = false;

function isUserEditing(){
  const el = document.activeElement;
  return !!el && ["INPUT", "TEXTAREA", "SELECT"].includes(el.tagName);
}

function applyServerData(d){
  const prevPending = orders.filter(o => o.status === "pending").length;
  const prevUnread  = messages.filter(m => !m.read).length;

  adminProducts  = d.products.map(p => ({ ...p }));
  categories     = [...d.categories];
  orders         = d.orders.map(o => ({ ...o }));
  customers      = d.customers.map(c => ({ ...c }));
  deliveryStaff  = d.staff.map(s => ({ ...s }));
  messages       = d.messages.map(m => ({ ...m }));
  settings       = { ...d.settings };
  purchaseOrders = d.purchaseOrders.map(po => ({ ...po, items: po.items.map(i => ({ ...i })) }));
  promotions     = d.promotions.map(p => ({ ...p, productIds: [...p.productIds], productNames: [...p.productNames] }));

  // data-driven views only (skip form/select builders so inputs keep their values)
  renderDashboard();
  renderOrdersTable();
  renderStaffPanel();
  renderCustomersTable();
  renderMessages();
  renderInventory();
  renderPurchaseOrders();
  renderPromotions();
  renderReports();

  const nowPending = orders.filter(o => o.status === "pending").length;
  const nowUnread  = messages.filter(m => !m.read).length;
  if (nowPending > prevPending && typeof flashAlert === "function") flashAlert("New order received!");
  else if (nowUnread > prevUnread && typeof flashAlert === "function") flashAlert("New customer message!");
}

function refreshAdminData(){
  if (autoRefreshBusy || document.hidden || isUserEditing()) return Promise.resolve();
  autoRefreshBusy = true;
  return fetch(ROUTES.data, {
    headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
    credentials: "same-origin",
    cache: "no-store",
  })
    .then(r => {
      if (r.status === 401 || r.status === 419) { window.location.reload(); return null; } // session expired
      return r.ok ? r.json() : null;
    })
    .then(d => { if (d) applyServerData(d); })
    .catch(() => {})                       // network blip — try again next tick
    .finally(() => { autoRefreshBusy = false; });
}

setInterval(refreshAdminData, AUTO_REFRESH_MS);
document.addEventListener("visibilitychange", () => { if (!document.hidden) refreshAdminData(); });
