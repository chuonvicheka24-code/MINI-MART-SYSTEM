<?php $__env->startSection('title', 'Mini Mart — Fresh Groceries, Better Living'); ?>

<?php $__env->startSection('content'); ?>

<div class="hero-band">
  <div class="wrap">
    <section class="hero2">
      <div class="hero-inner">
        <div>
          <p class="hero-kicker">FRESHNESS YOU CAN TRUST</p>
          <h1>Fresh Groceries, <span class="accent">Better Living</span></h1>
          <p class="lead">Get the freshest fruits, vegetables, and daily essentials delivered to your doorstep.</p>
          <div class="hero2-cta">
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary">Shop Now →</a>
            <a href="<?php echo e(route('home')); ?>#flash-deals" class="btn btn-outline">Explore Deals</a>
          </div>
          <div class="hero-trust">
            <div><i class="fa-solid fa-seedling"></i> <span><strong>Farm Fresh</strong><small>Quality Produce</small></span></div>
            <div><i class="fa-solid fa-truck"></i> <span><strong>Free Delivery</strong><small>On orders over $49</small></span></div>
            <div><i class="fa-solid fa-lock"></i> <span><strong>Secure Payment</strong><small>100% Protected</small></span></div>
            <div><i class="fa-solid fa-rotate-left"></i> <span><strong>Easy Returns</strong><small>7 Days Return</small></span></div>
          </div>
        </div>
        <div class="hero2-media">
          <div class="hero-badge"><span>30%</span>UP TO<br>OFF</div>
          <img src="<?php echo e(asset('Photo/150a7d6ac15444d505c789b0c017f9f1.jpg')); ?>" alt="Basket of fresh groceries">
        </div>
      </div>
    </section>
  </div>
</div>

<section class="section">
  <div class="wrap">
    <div class="section-head" style="justify-content:center; text-align:center; flex-direction:column; border-bottom:none;">
      <h2>Shop By Category</h2>
      <p>Every aisle, one tap away</p>
    </div>
    <div class="cat-scroll-wrap">
      <button class="cat-scroll-btn prev" onclick="scrollCats(-1)" aria-label="Scroll categories left"><i class="fa-solid fa-chevron-left"></i></button>
      <div class="cat-grid8" id="cat-grid8">
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('products.index')); ?>?cat=<?php echo e(urlencode($cat->name)); ?>" class="cat-card">
            <div class="cat-photo"><img src="<?php echo e(asset($cat->image)); ?>" alt="<?php echo e($cat->name); ?>"></div>
            <span class="cat-name"><?php echo e($cat->name); ?></span>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <button class="cat-scroll-btn next" onclick="scrollCats(1)" aria-label="Scroll categories right"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="wrap promo-split">
    <div class="promo-card dark">
      <div class="promo-inner">
        <p class="promo-kicker">LIMITED TIME OFFER</p>
        <h3>Weekend Super Saver</h3>
        <p>Enjoy up to 30% off on selected products. Hurry, offer valid till Sunday.</p>
        <a href="<?php echo e(route('home')); ?>#flash-deals" class="btn btn-accent" style="margin-top:10px;">Shop Now</a>
      </div>
      <div class="promo-circle">UP TO<span>30%</span>OFF</div>
      <div class="promo-media"><img src="<?php echo e(asset('Photo/150a7d6ac15444d505c789b0c017f9f1.jpg')); ?>" alt="Fresh vegetables on wooden crate"></div>
    </div>
    <div class="promo-card light">
      <div class="promo-inner" style="max-width:100%;">
        <p class="promo-kicker"><i class="fa-solid fa-bolt"></i> FAST DELIVERY</p>
        <h3>Get Delivery in 30 Minutes</h3>
        <p>Fast delivery at your doorstep, every single order.</p>
        <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary" style="margin-top:10px;">Order Now</a>
      </div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;" id="flash-deals">
  <div class="wrap">
    <div class="flash-header section-head">
      <div>
        <h2><i class="fa-solid fa-bolt" style="color:var(--brand-text);"></i> Flash Deals</h2>
        <p>Prices this good don't last — grab them before the timer runs out.</p>
      </div>
      <div class="countdown" id="countdown">
        <div class="box"><span id="cd-h">00</span><small>HRS</small></div>
        <div class="box"><span id="cd-m">00</span><small>MIN</small></div>
        <div class="box"><span id="cd-s">00</span><small>SEC</small></div>
      </div>
    </div>
    <div class="product-grid" id="deal-grid" style="grid-template-columns: repeat(6,1fr);">
      <?php $__currentLoopData = $dealIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php ($p = $products->firstWhere('id', $id)); ?>
        <?php if(! $p) continue; ?>
        <?php ($pct = $dealPct[$id]); ?>
        <?php ($was = $p->price / (1 - $pct / 100)); ?>
        <div class="deal-card">
          <div class="deal-media"><img src="<?php echo e(asset($p->image)); ?>" alt="<?php echo e($p->name); ?>"><span class="deal-off"><?php echo e($pct); ?>% OFF</span></div>
          <div class="deal-body">
            <div class="deal-name"><?php echo e($p->name); ?></div>
            <div class="deal-unit"><?php echo e($p->unit); ?></div>
            <div class="deal-price"><span class="now">$<?php echo e(number_format($p->price, 2)); ?></span><span class="was">$<?php echo e(number_format($was, 2)); ?></span></div>
            <button class="deal-add" onclick="addToCart(<?php echo e($p->id); ?>); this.textContent='Added ✓';">Add to Cart <i class="fa-solid fa-cart-shopping"></i></button>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;" id="new-arrivals">
  <div class="wrap">
    <div class="section-head">
      <h2><i class="fa-solid fa-box-open" style="color:var(--brand-text);"></i> New Arrivals</h2>
      <a href="<?php echo e(route('products.index')); ?>?sort=new" style="font-weight:600; color:var(--brand-dark); font-size:14px;">View All →</a>
    </div>
    <div class="product-grid" id="new-grid">
      <?php $__currentLoopData = $newArrivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="product-card">
          <div class="product-media">
            <img src="<?php echo e(asset($p->image)); ?>" alt="<?php echo e($p->name); ?>">
            <span class="new-flag">NEW</span>
          </div>
          <div class="product-body">
            <span class="product-cat"><?php echo e($p->category->name); ?></span>
            <span class="product-name"><?php echo e($p->name); ?></span>
            <div class="product-foot">
              <span class="price-tag">$<?php echo e(number_format($p->price, 2)); ?><br><small>per <?php echo e($p->unit); ?></small></span>
              <button class="add-btn" onclick="addToCart(<?php echo e($p->id); ?>); this.textContent='Added ✓';">Add to Cart</button>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="wrap">
    <div class="section-head" style="justify-content:center; text-align:center; flex-direction:column; border-bottom:none;">
      <h2>Why Choose Mini Mart?</h2>
    </div>
    <div class="why-strip">
      <div class="why-card"><div class="why-icon"><i class="fa-solid fa-trophy"></i></div><h4>Best Quality</h4><p>We deliver only the freshest &amp; finest products.</p></div>
      <div class="why-card"><div class="why-icon"><i class="fa-solid fa-dollar-sign"></i></div><h4>Affordable Prices</h4><p>Best prices &amp; exclusive offers on your favorite items.</p></div>
      <div class="why-card"><div class="why-icon"><i class="fa-solid fa-truck"></i></div><h4>Fast Delivery</h4><p>Lightning-fast delivery right at your doorstep.</p></div>
      <div class="why-card"><div class="why-icon"><i class="fa-solid fa-lock"></i></div><h4>100% Secure</h4><p>Your payments and data are safe with us always.</p></div>
      <div class="why-card"><div class="why-icon"><i class="fa-solid fa-rotate-left"></i></div><h4>Easy Returns</h4><p>Not satisfied? Easy returns within 7 days of delivery.</p></div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="wrap proof-row">
    <div class="testi-card">
      <h4 style="margin-bottom:14px;">What Our Customers Say</h4>
      <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
      <p style="font-size:14px; font-style:italic;">"Mini Mart has made grocery shopping so easy and convenient. The quality is always top-notch and delivery is super fast!"</p>
      <div class="testi-name">— Sarah J.</div>
    </div>
    <div class="stat-card">
      <div class="stat-num">50K+</div>
      <div>Happy Customers</div>
      <div class="stars" style="margin-top:8px;"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
      <div style="font-size:12px; opacity:.8;">4.85 Average Rating</div>
    </div>
    <div class="newsletter-card">
      <h4>Get Exclusive Offers</h4>
      <p style="font-size:13.5px; color:var(--muted);">Subscribe to get the best deals &amp; updates.</p>
      <form onsubmit="event.preventDefault(); this.reset(); alert('Subscribed!');">
        <input type="email" placeholder="Enter your email" required>
        <button class="btn btn-primary" type="submit">Subscribe</button>
      </form>
      <p class="form-note" style="margin-top:10px;">We respect your privacy. Unsubscribe anytime.</p>
    </div>
  </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function scrollCats(dir){
    const el = document.getElementById("cat-grid8");
    el.scrollBy({ left: dir * (el.clientWidth * 0.8), behavior: "smooth" });
  }
  function updateCatArrows(){
    const el = document.getElementById("cat-grid8");
    const prevBtn = document.querySelector(".cat-scroll-btn.prev");
    const nextBtn = document.querySelector(".cat-scroll-btn.next");
    if(!el || !prevBtn || !nextBtn) return;
    prevBtn.style.display = el.scrollLeft > 8 ? "flex" : "none";
    nextBtn.style.display = (el.scrollLeft + el.clientWidth < el.scrollWidth - 8) ? "flex" : "none";
  }
  document.getElementById("cat-grid8").addEventListener("scroll", updateCatArrows);
  window.addEventListener("resize", updateCatArrows);
  updateCatArrows();

  function tickCountdown(){
    const now = new Date();
    const midnight = new Date(now); midnight.setHours(24,0,0,0);
    const diff = Math.max(0, midnight - now);
    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    document.getElementById("cd-h").textContent = String(h).padStart(2,"0");
    document.getElementById("cd-m").textContent = String(m).padStart(2,"0");
    document.getElementById("cd-s").textContent = String(s).padStart(2,"0");
  }
  tickCountdown();
  setInterval(tickCountdown, 1000);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\MINI-MART-SYSTEM\resources\views/home/index.blade.php ENDPATH**/ ?>