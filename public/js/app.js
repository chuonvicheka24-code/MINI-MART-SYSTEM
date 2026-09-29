/* ============================================================
   MINI MART — shared header/cart logic (customer-facing pages)
   Product & category DATA now comes from the Laravel backend
   (injected per-page as window.MM_CATEGORIES / window.MM_PRODUCTS,
   and the cart lives in the server session, not localStorage) —
   but every function name below is kept the same as the original
   static-site app.js so the markup/onclick handlers never change.
   ============================================================ */

const CATEGORIES = window.MM_CATEGORIES || [];
const CATEGORY_ICONS = window.MM_CATEGORY_ICONS || {};
const CATEGORY_IMAGES = window.MM_CATEGORY_IMAGES || {};
function categoryIcon(cat){ return CATEGORY_ICONS[cat] || "fa-tag"; }

function matchesCategory(productCat, filter){
  return filter === "All" || productCat === filter;
}

function money(n){ return "$" + Number(n).toFixed(2); }

/* ---- cart: talks to the Laravel session-cart endpoints ---- */
let _cartCache = { lines: [], count: 0, subtotal: 0 };

function mmFetch(url, options = {}){
  return fetch(url, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
      "Accept": "application/json",
      "X-CSRF-TOKEN": window.MM_CSRF,
    },
    ...options,
  }).then(r => r.json());
}

function refreshCartCache(){
  return mmFetch(window.MM_ROUTES.cartData).then(data => {
    _cartCache = data;
    updateCartCount();
    refreshHeadCartTotal();
    return data;
  });
}

function addToCart(id, qty = 1){
  return mmFetch(window.MM_ROUTES.cartAdd, {
    method: "POST",
    body: JSON.stringify({ id, qty }),
  }).then(data => {
    _cartCache = data;
    updateCartCount();
    refreshHeadCartTotal();
    return data;
  });
}
function setCartQty(id, qty){
  return mmFetch(window.MM_ROUTES.cartSet, {
    method: "POST",
    body: JSON.stringify({ id, qty }),
  }).then(data => {
    _cartCache = data;
    updateCartCount();
    refreshHeadCartTotal();
    return data;
  });
}
function removeFromCart(id){ return setCartQty(id, 0); }

/** Cart lines merged with product info — needs window.MM_PRODUCTS on the page. */
/** Cart lines merged with product info — fallback directly to session item details */
function cartLines(){
  const products = window.MM_PRODUCTS || [];
  const lines = _cartCache.lines || [];

  return lines.map(l => {
    // 1. Try matching with window.MM_PRODUCTS
    const found = products.find(p => String(p.id) === String(l.id));

    // 2. If product found, use it
    if (found) {
      return { ...l, product: found };
    }

    // 3. Fallback: construct product object directly from item session data
    return {
      ...l,
      product: {
        id: l.id,
        name: l.name || l.title || ('Product #' + l.id),
        salePrice: parseFloat(l.price || l.salePrice || 0),
        unit: l.unit || 'item',
        img: l.img || l.image || 'images/placeholder.jpg'
      }
    };
  });
}
function cartSubtotal(){ return _cartCache.subtotal || 0; }
function cartCount(){ return _cartCache.count || 0; }

function updateCartCount(){
  document.querySelectorAll("[data-cart-count]").forEach(el => el.textContent = cartCount());
}
function refreshHeadCartTotal(){
  document.querySelectorAll("[data-head-cart-total]").forEach(el => el.textContent = money(cartSubtotal()));
}

const DELIVERY_RATE = 0.10; // 10% of total cost, per delivery flow (server confirms the real rate at checkout)

/* ---- contact form -> backend ---- */
function sendMessageToAdmin({ name, email, message }){
  return mmFetch(window.MM_ROUTES.contactStore, {
    method: "POST",
    body: JSON.stringify({ name, email, message }),
  });
}

/* ---- shared header wiring: search form + category select + mega menu ---- */
function wireHeaderSearch(){
  const form = document.getElementById("search-form");
  if(form){
    const catSelect = document.getElementById("search-cat");
    if(catSelect){
      catSelect.innerHTML = '<option value="All">All Categories</option>' +
        CATEGORIES.map(c => `<option>${c}</option>`).join("");
    }
    form.addEventListener("submit", e => {
      e.preventDefault();
      const qEl = document.getElementById("search-input");
      const q = qEl ? qEl.value : "";
      const cat = catSelect ? catSelect.value : "All";
      const params = new URLSearchParams();
      if(q) params.set("q", q);
      if(cat && cat !== "All") params.set("cat", cat);
      location.href = window.MM_ROUTES.products + "?" + params.toString();
    });
  }

  const megaMenu = document.getElementById("mega-cat-menu");
  if(megaMenu){
    megaMenu.innerHTML = CATEGORIES.map(c => `
      <a href="${window.MM_ROUTES.products}?cat=${encodeURIComponent(c)}"><i class="fa-solid ${categoryIcon(c)}"></i> ${c}</a>
    `).join("");
  }
}

/* ---- 4. Initialization (runs on every page that includes app.js) ---- */
document.addEventListener("DOMContentLoaded", () => {
  refreshCartCache();
  wireHeaderSearch();
});
