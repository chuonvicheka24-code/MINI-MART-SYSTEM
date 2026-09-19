<?php $__env->startSection('title', 'Contact — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-head">
  <div class="wrap">
    <h1>Get In Touch</h1>
    <p>We'd love to hear from you — questions about an order, a delivery, or stock.</p>
  </div>
</div>

<section class="section">
  <div class="wrap contact-layout">

    <div class="contact-info-card">
      <h3>Contact information</h3>
      <div class="contact-row">
        <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
        <div><div class="contact-label">Phone number</div>+855 12 345 678</div>
      </div>
      <div class="contact-row">
        <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
        <div><div class="contact-label">Email</div>minimart@gmail.com</div>
      </div>
      <div class="contact-row">
        <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
        <div><div class="contact-label">Address</div>#123, St. 271, Phnom Penh, Cambodia</div>
      </div>
      <div class="contact-row">
        <div class="contact-icon"><i class="fa-regular fa-clock"></i></div>
        <div><div class="contact-label">Store hours</div>7:00 AM – 10:00 PM, every day</div>
      </div>
    </div>

    <form class="form-card" id="contact-form" onsubmit="submitContact(event)">
      <div class="field"><label for="c-name">Your Name</label><input id="c-name" value="<?php echo e(auth()->user()->name ?? ''); ?>" required></div>
      <div class="field"><label for="c-email">Your Email</label><input id="c-email" type="email" value="<?php echo e(auth()->user()->email ?? ''); ?>" required></div>
      <div class="field"><label for="c-msg">Your Message</label><textarea id="c-msg" rows="5" required></textarea></div>
      <button class="btn btn-primary btn-block" type="submit">Send Message</button>
      <p id="sent" style="display:none; margin-top:12px; color: var(--brand-dark); font-weight:600;"><i class="fa-solid fa-circle-check"></i> Sent to the store — we'll reply within a day.</p>
    </form>

  </div>
</section>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function submitContact(e){
    e.preventDefault();
    sendMessageToAdmin({
      name: document.getElementById("c-name").value.trim(),
      email: document.getElementById("c-email").value.trim(),
      message: document.getElementById("c-msg").value.trim(),
    }).then(() => {
      document.getElementById("sent").style.display = "block";
      document.getElementById("contact-form").reset();
    });
  }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/contact/index.blade.php ENDPATH**/ ?>