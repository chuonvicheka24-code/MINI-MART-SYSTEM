<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Mini Mart — Fresh Groceries, Better Living')</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v=20260908b">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="announce">
  <div class="wrap">
    <div class="announce-left">
      <span><i class="fa-solid fa-location-dot"></i> Veng Sreng Blvd, Phnom Penh </span>
      <span><i class="fa-solid fa-truck"></i> Free Delivery on orders over $49</span>
      <span><i class="fa-solid fa-phone"></i> (123) 456-7890</span>
      <span><i class="fa-regular fa-clock"></i> Mon – Sun: 8:00 AM – 10:00 PM</span>
    </div>
    <div class="announce-social">
      <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
      <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
      <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
      <a href="#" aria-label="Pinterest"><i class="fa-brands fa-pinterest"></i></a>
    </div>
  </div>
</div>

<header class="site @yield('header-class')">
  <div class="wrap head-row">
    <a href="{{ route('home') }}" class="brand"><span class="cart-emoji"><i class="fa-solid fa-cart-shopping"></i></span><div>Mini<span>Mart</span><small>FRESH · QUALITY · EVERYDAY</small></div></a>

    <form class="head-search" id="search-form">
      <input type="text" id="search-input" placeholder="Search for products, categories…" aria-label="Search products">
      <select aria-label="Category filter" id="search-cat"></select>
      <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <div class="head-actions">
      @auth
        <a href="{{ route('admin.index') }}" class="mini-link" @unless(auth()->user()->isAdmin()) style="pointer-events:none; opacity:.5;" @endunless>
          <span class="glyph"><i class="fa-solid fa-user"></i></span>
          <span>{{ auth()->user()->first_name }}<br><strong>{{ auth()->user()->isAdmin() ? 'Store dashboard' : 'My Account' }}</strong></span>
        </a>
      @else
        <a href="{{ route('login') }}" class="mini-link"><span class="glyph"><i class="fa-solid fa-user"></i></span><span>Sign In / Register<br><strong>My Account</strong></span></a>
      @endauth
      <a href="{{ route('cart.index') }}" class="mini-link head-cart"><span class="glyph"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count" data-cart-count>0</span></span><span>My Cart<br><strong data-head-cart-total>$0.00</strong></span></a>
    </div>
  </div>

  <div class="wrap nav-row">
    <div class="cat-dropdown-wrap" tabindex="0">
      <button class="cat-toggle" onclick="location.href='{{ route('products.index') }}'"><i class="fa-solid fa-bars"></i> Shop by Categories</button>
      <div class="mega-dropdown" id="mega-cat-menu"></div>
    </div>
    <nav class="nav-links">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
      <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">Categories ▾</a>
      <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.index') ? 'active' : '' }}">Order</a>
      <a href="{{ route('products.index') }}?sort=new">New Arrivals</a>
      <a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.index') ? 'active' : '' }}">Contact Us</a>
    </nav>
    <a href="{{ route('home') }}#flash-deals" class="flash-btn"><i class="fa-solid fa-bolt"></i> FLASH DEALS</a>
  </div>
</header>

@yield('content')

<footer class="site @yield('header-class')">
  <div class="wrap">
    <div>
      <h4><span class="cart-emoji"><i class="fa-solid fa-cart-shopping"></i></span> Mini Mart</h4>
      <p style="opacity:.85;">Your one-stop shop for fresh groceries and household essentials. Quality products, trusted by thousands.</p>
      <div class="announce-social">
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
      </div>
    </div>
    <div>
      <h4>Quick Links</h4>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('products.index') }}">Categories</a>
      <a href="{{ route('cart.index') }}">Order</a>
      <a href="{{ route('products.index') }}?sort=new">New Arrivals</a>
    </div>
    <div>
      <h4>Customer Service</h4>
      <a href="{{ route('login') }}">My Account</a>
      <a href="{{ route('cart.index') }}">Order Tracking</a>
      <a href="{{ route('contact.index') }}">Contact Us</a>
      <a href="{{ route('contact.index') }}">Returns &amp; Refunds</a>
    </div>
    <div>
      <h4>Contact Us</h4>
      <p style="opacity:.85; margin-bottom:6px;"><i class="fa-solid fa-location-dot"></i> Veng Sreng Blvd, Phnom Penh</p>
      <p style="opacity:.85; margin-bottom:6px;"><i class="fa-solid fa-phone"></i> (+885) 456-7890</p>
      <p style="opacity:.85;"><i class="fa-solid fa-envelope"></i> support@minimart.example</p>
    </div>
  </div>
  <div class="foot-bottom">© {{ date('Y') }} Mini Mart. Built with Laravel.</div>
</footer>

<script>
  window.MM_CSRF = "{{ csrf_token() }}";
  window.MM_ROUTES = {
    cartData: "{{ route('cart.data') }}",
    cartAdd: "{{ route('cart.add') }}",
    cartSet: "{{ route('cart.set') }}",
    cartRemove: "{{ route('cart.remove') }}",
    checkoutStore: "{{ route('checkout.store') }}",
    contactStore: "{{ route('contact.store') }}",
    products: "{{ route('products.index') }}",
  };
  window.MM_CATEGORIES = @json($categories->pluck('name'));
  window.MM_CATEGORY_ICONS = @json($categories->pluck('icon', 'name'));
  window.MM_CATEGORY_IMAGES = @json($categories->pluck('image', 'name'));
</script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
