<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $__env->yieldContent('title', 'Mini Mart — Fresh Groceries, Better Living'); ?></title>
<link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>?v=20260908b">
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
    <a href="<?php echo e(route('home')); ?>" class="brand"><span class="cart-emoji"><i class="fa-solid fa-cart-shopping"></i></span><div>Mini<span>Mart</span><small>FRESH · QUALITY · EVERYDAY</small></div></a>

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

    <!-- Logout Form (Works for both Admin and Customer) -->
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

  <div class="wrap nav-row">
    <div class="cat-dropdown-wrap" tabindex="0">
      <button class="cat-toggle" onclick="location.href='<?php echo e(route('products.index')); ?>'"><i class="fa-solid fa-bars"></i> Shop by Categories</button>
      <div class="mega-dropdown" id="mega-cat-menu"></div>
    </div>
    <nav class="nav-links">
      <a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">Home</a>
      <a href="<?php echo e(route('products.index')); ?>" class="<?php echo e(request()->routeIs('products.index') ? 'active' : ''); ?>">Products ▾</a>
      <a href="<?php echo e(route('cart.index')); ?>" class="<?php echo e(request()->routeIs('cart.index') ? 'active' : ''); ?>">Order</a>
      <a href="<?php echo e(route('products.index')); ?>?sort=new">New Arrivals</a>
      <a href="<?php echo e(route('contact.index')); ?>" class="<?php echo e(request()->routeIs('contact.index') ? 'active' : ''); ?>">Contact Us</a>
    </nav>
    <a href="<?php echo e(route('home')); ?>#flash-deals" class="flash-btn"><i class="fa-solid fa-bolt"></i> FLASH DEALS</a>
  </div>
</header>

<?php echo $__env->yieldContent('content'); ?>

<footer class="site <?php echo $__env->yieldContent('header-class'); ?>">
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
      <a href="<?php echo e(route('home')); ?>">Home</a>
      <a href="<?php echo e(route('products.index')); ?>">Categories</a>
      <a href="<?php echo e(route('cart.index')); ?>">Order</a>
      <a href="<?php echo e(route('products.index')); ?>?sort=new">New Arrivals</a>
    </div>
    <div>
      <h4>Customer Service</h4>
      <a href="<?php echo e(route('login')); ?>">My Account</a>
      <a href="<?php echo e(route('cart.index')); ?>">Order Tracking</a>
      <a href="<?php echo e(route('contact.index')); ?>">Contact Us</a>
      <a href="<?php echo e(route('contact.index')); ?>">Returns &amp; Refunds</a>
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
  };
  window.MM_CATEGORIES = <?php echo json_encode($categories->pluck('name'), 15, 512) ?>;
  window.MM_CATEGORY_ICONS = <?php echo json_encode($categories->pluck('icon', 'name'), 512) ?>;
  window.MM_CATEGORY_IMAGES = <?php echo json_encode($categories->pluck('image', 'name'), 512) ?>;
</script>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\MINI-MART-SYSTEM\resources\views/layouts/app.blade.php ENDPATH**/ ?>