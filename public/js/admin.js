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
    inventory:"Stock Management", reports:"Sales reports", settings:"Settings"
  };
  document.getElementById("panel-title").textContent = titles[name];
  renderAll();
  if(name === "reports" && typeof Chart !== "undefined"){
    [chartRevenue, chartStatus, chartCategory].forEach(c => c && c.resize());
  }
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
  if(!name || !cat) return;

  api(ROUTES.products, {
    method: "POST",
    body: JSON.stringify({ name, category: cat, price, qty, image: uploadedImageDataUrl }),
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
  const p = adminProducts.find(x=>x.id===id);
  const price = prompt(`New price for ${p.name}`, p.price);
  if(price === null) return;
  const qty = prompt(`New quantity for ${p.name}`, p.qty);
  if(qty === null) return;
  p.price = parseFloat(price) || p.price;
  p.qty = parseInt(qty) ?? p.qty;
  renderAll();
  api(`${ROUTES.products}/${id}`, { method: "PUT", body: JSON.stringify({ price: p.price, qty: p.qty }) });
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
function renderInventory(){
  const rows = [...adminProducts].sort((a,b)=>a.qty-b.qty);
  document.getElementById("inventory-body").innerHTML = rows.map((p,i) => {
    const s = stockStatus(p.qty);
    return `
    <tr>
      <td>#${i+1}</td>
      <td><img class="row-thumb" src="/${p.img}" alt=""> ${p.name}${p.discountPercent ? ` <span class="status low" style="margin-left:4px;">-${p.discountPercent}%</span>` : ""}</td>
      <td>${p.cat}</td>
      <td>${p.qty}</td>
      <td><span class="status ${s.cls}">${s.label}</span></td>
      <td>
        <button class="icon-action edit" onclick="editProduct(${p.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
        <button class="icon-action delete" onclick="deleteProduct(${p.id})" title="Delete"><i class="fa-solid fa-trash"></i></button>
      </td>
    </tr>`;
  }).join("");
}
function exportInventory(){
  let csv = "Product,Category,Qty,Status\n";
  adminProducts.forEach(p => { csv += `${p.name},${p.cat},${p.qty},${stockStatus(p.qty).label}\n`; });
  const blob = new Blob([csv], { type:"text/csv" });
  const a = document.createElement("a");
  a.href = URL.createObjectURL(blob);
  a.download = "minimart-stock.csv";
  a.click();
}
function createPurchaseOrder(){
  const low = adminProducts.filter(p => p.qty < settings.lowStockThreshold * 2);
  if(!low.length) return flashAlert("Nothing needs restocking right now.");
  low.forEach(p => p.qty += 20);
  renderAll();
  flashAlert(`Purchase order created for ${low.length} item(s) — stock topped up.`);
  api(ROUTES.purchaseOrder, { method: "POST" });
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

  if(typeof Chart === "undefined") return; // CDN blocked / offline — KPIs above still work
  renderChartRevenue();
  renderChartStatus();
  renderChartCategory();
}

let chartRevenue, chartStatus, chartCategory;
const CHART_GREEN = "#80EF80", CHART_DARK = "#1E5C34", CHART_WARN = "#F5A623";

function renderChartRevenue(){
  const canvas = document.getElementById("chart-revenue");
  if(!canvas) return;
  const labels = orders.map(o => "#" + o.id);
  const data = orders.map(o => +o.total.toFixed(2));
  if(chartRevenue){
    chartRevenue.data.labels = labels;
    chartRevenue.data.datasets[0].data = data;
    chartRevenue.update();
    return;
  }
  chartRevenue = new Chart(canvas, {
    type: "bar",
    data: { labels, datasets: [{ label:"Order total ($)", data, backgroundColor: CHART_GREEN, borderRadius:6, maxBarThickness:36 }] },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true } } }
  });
}
function renderChartStatus(){
  const canvas = document.getElementById("chart-status");
  if(!canvas) return;
  const counts = { pending:0, out:0, done:0 };
  orders.forEach(o => { counts[o.status] = (counts[o.status]||0) + 1; });
  const data = [counts.pending, counts.out, counts.done];
  if(chartStatus){
    chartStatus.data.datasets[0].data = data;
    chartStatus.update();
    return;
  }
  chartStatus = new Chart(canvas, {
    type: "doughnut",
    data: {
      labels: ["Pending","Out for delivery","Delivered"],
      datasets: [{ data, backgroundColor:[CHART_WARN, CHART_GREEN, CHART_DARK] }]
    },
    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:"bottom" } } }
  });
}
function renderChartCategory(){
  const canvas = document.getElementById("chart-category");
  if(!canvas) return;
  const byCat = {};
  adminProducts.forEach(p => { byCat[p.cat] = (byCat[p.cat] || 0) + p.price * p.qty; });
  const labels = Object.keys(byCat);
  const data = labels.map(k => +byCat[k].toFixed(2));
  if(chartCategory){
    chartCategory.data.labels = labels;
    chartCategory.data.datasets[0].data = data;
    chartCategory.update();
    return;
  }
  chartCategory = new Chart(canvas, {
    type: "bar",
    data: { labels, datasets: [{ label:"Stock value ($)", data, backgroundColor: CHART_DARK, borderRadius:6 }] },
    options: { responsive:true, maintainAspectRatio:false, indexAxis:"y", plugins:{ legend:{ display:false } } }
  });
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
