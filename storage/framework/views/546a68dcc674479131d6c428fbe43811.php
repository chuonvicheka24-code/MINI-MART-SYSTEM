<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $__env->yieldContent('title', 'Mini Mart — Fresh Groceries, Better Living'); ?></title>

<!-- Green Shopping Cart Browser Tab Favicon -->
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 576 512'><path fill='%2310b981' d='M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77 45.9C72.4 21.6 51.2 4 26.9 4H24C10.7 4 0 13.3 0 26.6zM176 512a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm320 0a48 48 0 1 0 0-96 48 48 0 1 0 0 96z'/></svg>">

<!-- Fixed Asset Path for ngrok / HTTP & HTTPS -->
<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>?v=20260908b">
<link rel="stylesheet" href="<?php echo e(asset('css/animations.css')); ?>?v=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="announce">
  <div class="wrap">
    <div class="announce-left">
      <span><i class="fa-solid fa-location-dot"></i> Veng Sreng Blvd, Phnom Penh </span>
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

<header class="site <?php echo $__env->yieldContent('header-class'); ?>">
  <div class="wrap head-row">
      <!-- Image Logo -->
      <a href="<?php echo e(route('home')); ?>" class="brand">
        <img src="<?php echo e(asset('Photo/minimartlogo.png')); ?>" alt="MiniMart Logo" style="height: 48px; width: auto; object-fit: contain;">
      </a>

    <form class="head-search" id="search-form">
      <input type="text" id="search-input" placeholder="Search for products, categories…" aria-label="Search products">
      <select aria-label="Category filter" id="search-cat"></select>
      <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <div class="head-actions">
      <?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->isAdmin()): ?>
          <!-- Admin Link -->
          <a href="<?php echo e(route('admin.index')); ?>" class="mini-link">
            <span class="glyph"><i class="fa-solid fa-user"></i></span>
            <span><?php echo e(auth()->user()->first_name); ?><br><strong>Store dashboard</strong></span>
          </a>
        <?php else: ?>
          <!-- Customer Link -->
          <div class="mini-link" style="display:flex; align-items:center; gap:8px;">
            <span class="glyph"><i class="fa-solid fa-user"></i></span>
            <span><?php echo e(auth()->user()->first_name); ?><br><strong>My Account</strong></span>
          </div>
        <?php endif; ?>

        <!-- Logout Form -->
        <form action="<?php echo e(route('logout')); ?>" method="POST" style="margin:0;">
          <?php echo csrf_field(); ?>
          <button type="submit" class="mini-link" style="background:none; border:none; cursor:pointer; padding:0; text-align:left;">
            <span class="glyph" style="color:#dc2626;"><i class="fa-solid fa-right-from-bracket"></i></span>
            <span style="color:#dc2626;">Log Out<br><strong style="color:#dc2626;">Exit</strong></span>
          </button>
        </form>
      <?php else: ?>
        <!-- Guest Sign In Link -->
        <a href="<?php echo e(route('login')); ?>" class="mini-link">
          <span class="glyph"><i class="fa-solid fa-user"></i></span>
          <span>Sign In / Register<br><strong>My Account</strong></span>
        </a>
      <?php endif; ?>

      <!-- Cart Link -->
      <a href="<?php echo e(route('cart.index')); ?>" class="mini-link head-cart">
        <span class="glyph"><i class="fa-solid fa-cart-shopping"></i><span class="cart-count" data-cart-count>0</span></span>
        <span>My Cart<br><strong data-head-cart-total>$0.00</strong></span>
      </a>
    </div>
  </div>

  
  <div class="wrap nav-row" style="display: flex; align-items: center; justify-content: center; gap: 32px; width: 100%;">
    
    
    <div class="cat-dropdown-wrap" tabindex="0" style="position: relative;">
      <button type="button" class="cat-toggle" onclick="toggleCatDropdown(event)" style="display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-bars"></i> Shop by Categories
      </button>
      
      <div class="mega-dropdown" id="mega-cat-menu" style="display: none; opacity: 0; transition: opacity 0.2s ease, transform 0.2s ease; transform: translateY(10px); position: absolute; top: 100%; left: 0; background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.12); border-radius: 16px; width: 360px; padding: 14px; z-index: 99999; border: 1px solid #e2e8f0; margin-top: 8px; grid-template-columns: 1fr 1fr; gap: 8px;">
        
        
        <a href="<?php echo e(route('products.index', ['cat' => 'All'])); ?>" style="grid-column: span 2; display: flex; align-items: center; gap: 10px; padding: 10px 14px; color: #065f46; text-decoration: none; font-weight: 700; background: #d1fae5; border-radius: 10px; transition: all 0.2s;">
          <i class="fa-solid fa-border-all text-success" style="font-size: 15px;"></i> All Categories
        </a>

        
        <?php if(isset($categories) && count($categories) > 0): ?>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $catName = is_object($category) ? ($category->name ?? $category->slug) : $category;
              $catIcon = isset($category->icon) && $category->icon ? $category->icon : 'fa-layer-group';
            ?>
            <a href="<?php echo e(route('products.index', ['cat' => $catName])); ?>" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; color: #0f172a; text-decoration: none; font-size: 14px; font-weight: 600; border-radius: 10px; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
              <i class="fa-solid <?php echo e($catIcon); ?> text-success" style="font-size: 14px; width: 18px; text-align: center;"></i> 
              <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo e($catName); ?></span>
            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      </div>
    </div>

    <script>
    function toggleCatDropdown(e) {
        e.stopPropagation();
        const menu = document.getElementById('mega-cat-menu');
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'grid';
            setTimeout(() => {
                menu.style.opacity = '1';
                menu.style.transform = 'translateY(0)';
            }, 10);
        } else {
            menu.style.opacity = '0';
            menu.style.transform = 'translateY(10px)';
            setTimeout(() => {
                menu.style.display = 'none';
            }, 200);
        }
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('mega-cat-menu');
        const wrap = document.querySelector('.cat-dropdown-wrap');
        if (menu && wrap && !wrap.contains(e.target)) {
            menu.style.opacity = '0';
            menu.style.transform = 'translateY(10px)';
            setTimeout(() => {
                menu.style.display = 'none';
            }, 200);
        }
    });
    </script>

    
    <nav class="nav-links" style="display: flex; align-items: center; justify-content: center; margin: 0; gap: 24px;">
      <a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">Home</a>
      <a href="<?php echo e(route('products.index')); ?>" class="<?php echo e(request()->routeIs('products.index') ? 'active' : ''); ?>">Products ▾</a>
      <a href="<?php echo e(route('cart.index')); ?>" class="<?php echo e(request()->routeIs('cart.index') ? 'active' : ''); ?>">Order</a>
      <a href="<?php echo e(route('returns.index')); ?>" class="<?php echo e(request()->routeIs('returns.index') ? 'active' : ''); ?>">Returns Policy</a>
      <a href="<?php echo e(route('contact.index')); ?>" class="<?php echo e(request()->routeIs('contact.index') ? 'active' : ''); ?>">Contact Us</a>
    </nav>

    
    <a href="<?php echo e(route('home')); ?>#flash-deals" class="flash-btn" style="display: inline-flex; align-items: center; gap: 6px;"><i class="fa-solid fa-bolt"></i> FLASH DEALS</a>
  </div>
</header>

<?php echo $__env->yieldContent('content'); ?>

<footer class="site <?php echo $__env->yieldContent('header-class'); ?>">
  <div class="wrap">
    <div>
      <h4 style="display: flex; align-items: center; gap: 8px;">
         <img src="<?php echo e(asset('Photo/minimartlogo.png')); ?>" alt="MiniMart Logo" style="height: 48px; width: auto; object-fit: contain;">
      </h4>
      <p style="opacity:.85;">Your one-stop shop for fresh groceries and household essentials. Quality products, trusted by thousands.</p>
      <div class="announce-social">
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
      </div>
    </div>
    <div>
      <h4>Quick Links</h4>
      <a href="<?php echo e(route('home')); ?>">Home</a>
      <a href="<?php echo e(route('products.index')); ?>">Categories</a>
      <a href="<?php echo e(route('cart.index')); ?>">Order</a>
      <a href="<?php echo e(route('returns.index')); ?>">Returns Policy</a>
    </div>
    <div>
      <h4>Customer Service</h4>
      <a href="<?php echo e(route('login')); ?>">My Account</a>
      <a href="<?php echo e(route('cart.index')); ?>">Order Tracking</a>
      <a href="<?php echo e(route('contact.index')); ?>">Contact Us</a>
      <a href="<?php echo e(route('returns.index')); ?>">Returns &amp; Refunds</a>
    </div>
    <div>
      <h4>Contact Us</h4>
      <p style="opacity:.85; margin-bottom:6px;"><i class="fa-solid fa-location-dot"></i> Veng Sreng Blvd, Phnom Penh</p>
      <p style="opacity:.85; margin-bottom:6px;"><i class="fa-solid fa-phone"></i> (+885) 456-7890</p>
      <p style="opacity:.85;"><i class="fa-solid fa-envelope"></i> support@minimart.example</p>
    </div>
  </div>
  <div class="foot-bottom">© <?php echo e(date('Y')); ?> Mini Mart. Built with Laravel.</div>
</footer>

<script>
  window.MM_CSRF = "<?php echo e(csrf_token()); ?>";
  window.MM_ROUTES = {
    cartData: "<?php echo e(route('cart.data')); ?>",
    cartAdd: "<?php echo e(route('cart.add')); ?>",
    cartSet: "<?php echo e(route('cart.set')); ?>",
    cartRemove: "<?php echo e(route('cart.remove')); ?>",
    checkoutStore: "<?php echo e(route('checkout.store')); ?>",
    contactStore: "<?php echo e(route('contact.store')); ?>",
    products: "<?php echo e(route('products.index')); ?>",
    productsData: "<?php echo e(route('products.data')); ?>",
    <?php if(auth()->guard()->check()): ?>
    ordersStatus: "<?php echo e(route('orders.status')); ?>",
    <?php endif; ?>
  };
  window.MM_CATEGORIES = <?php echo json_encode($categories->pluck('name'), 15, 512) ?>;
  window.MM_CATEGORY_ICONS = <?php echo json_encode($categories->pluck('icon', 'name'), 512) ?>;
  window.MM_CATEGORY_IMAGES = <?php echo json_encode($categories->pluck('image', 'name'), 512) ?>;
  
  window.MM_PRODUCTS = <?php echo json_encode(isset($products) ? $products->map->toStorefrontArray() : [], 15, 512) ?>;
</script>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<script src="<?php echo e(asset('js/animations.js')); ?>?v=1"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/layouts/app.blade.php ENDPATH**/ ?>