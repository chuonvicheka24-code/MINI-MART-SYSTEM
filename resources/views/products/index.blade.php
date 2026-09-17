@extends('layouts.app')

@section('title', 'Products — Mini Mart')

@section('content')

<section class="section" style="padding-top:26px;">
  <div class="wrap">
    <div class="crumb"><a href="{{ route('home') }}">Home</a> &nbsp;›&nbsp; <span id="crumb-cat">All Products</span></div>

    <div class="products-layout">
      <aside class="filter-card">
        <h4>Categories</h4>
        <ul class="filter-list" id="filter-list"></ul>
      </aside>

      <div>
        <div class="products-toolbar">
          <h2 id="result-label" style="margin:0; font-size:22px;">All Products</h2>
          <select class="sort-select" id="sort-select">
            <option value="pop">Sort by: Popularity</option>
            <option value="new">Newest Arrivals</option>
            <option value="low">Price: low to high</option>
            <option value="high">Price: high to low</option>
            <option value="name">Name: A–Z</option>
          </select>
        </div>
        <div class="product-grid" id="grid"></div>
        <p id="empty-msg" style="display:none; margin-top:24px;">No products match that search — try another category or clear the search box.</p>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  window.MM_PRODUCTS = @json($products->map->toStorefrontArray());

  const params = new URLSearchParams(location.search);
  let activeCat = params.get("cat") || "All";
  let query = (params.get("q") || "").toLowerCase();
  let sort = params.get("sort") === "new" ? "new" : "pop";

  document.getElementById("filter-list").innerHTML = [
    `<li><a href="#" data-cat="All"><i class="fa-solid fa-border-all"></i> All Products</a></li>`,
    ...CATEGORIES.map(c => `<li><a href="#" data-cat="${c}"><i class="fa-solid ${categoryIcon(c)}"></i> ${c}</a></li>`)
  ].join("");
  document.getElementById("sort-select").value = sort;

  const grid = document.getElementById("grid");
  const emptyMsg = document.getElementById("empty-msg");
  const resultLabel = document.getElementById("result-label");
  const crumbCat = document.getElementById("crumb-cat");
  const searchInput = document.getElementById("search-input");
  searchInput.value = query;

  function render(){
    document.querySelectorAll("#filter-list a").forEach(a=>{
      a.classList.toggle("active", a.dataset.cat === activeCat);
    });
    let items = window.MM_PRODUCTS.filter(p => matchesCategory(p.cat, activeCat));
    if(query) items = items.filter(p => p.name.toLowerCase().includes(query));

    if(sort === "low") items = [...items].sort((a,b)=>a.price-b.price);
    if(sort === "high") items = [...items].sort((a,b)=>b.price-a.price);
    if(sort === "name") items = [...items].sort((a,b)=>a.name.localeCompare(b.name));
    if(sort === "new") items = [...items].sort((a,b)=> new Date(b.dateAdded) - new Date(a.dateAdded));

    const label = activeCat === "All" ? "All Products" : activeCat;
    crumbCat.textContent = sort === "new" && activeCat === "All" ? "New Arrivals" : label;
    resultLabel.textContent = query
      ? `${items.length} result${items.length===1?"":"s"} for "${query}"`
      : (sort === "new" && activeCat === "All" ? "New Arrivals" : label);

    emptyMsg.style.display = items.length ? "none" : "block";
    grid.innerHTML = items.map(p => `
      <div class="product-card">
        <div class="product-media">
          <img src="/${p.img}" alt="${p.name}">
          ${p.qty < 10 ? `<span class="stock-flag">Only ${p.qty} left</span>` : ""}
        </div>
        <div class="product-body">
          <span class="product-cat">${p.cat}</span>
          <span class="product-name">${p.name}</span>
          <div class="product-foot">
            <span class="price-tag">${money(p.salePrice)}<br><small>per ${p.unit}</small></span>
            <button class="add-btn" onclick="addToCart(${p.id}); this.textContent='Added ✓';">Add to Cart</button>
          </div>
        </div>
      </div>
    `).join("");
  }

  document.getElementById("filter-list").addEventListener("click", e=>{
    const a = e.target.closest("a");
    if(!a) return;
    e.preventDefault();
    activeCat = a.dataset.cat; render();
  });
  document.getElementById("sort-select").addEventListener("change", e=>{ sort = e.target.value; render(); });

  render();
</script>
@endpush
