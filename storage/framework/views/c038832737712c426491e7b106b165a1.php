

<?php $__env->startSection('title', 'Return Policy — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>
<div class="wrap" style="padding: 40px 0; max-width: 900px; margin: 0 auto;">
  <div style="font-size: 14px; color: #666; margin-bottom: 20px;">
    <a href="<?php echo e(route('home')); ?>" style="color: var(--brand, #10b981); text-decoration: none;">Home</a> &rsaquo; Return Policy
  </div>

  <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 24px; color: #111;">Return & Refund Policy</h1>

  <div style="background: #fff; padding: 32px; border-radius: 12px; border: 1px solid var(--line, #e5e7eb); line-height: 1.7; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
    <h3 style="font-size: 18px; color: #111; margin-top: 0;">1. Eligibility for Returns</h3>
    <p style="color: #4b5563;">Items can be returned within <strong>1-3 days</strong> of receipt if they are damaged, defective, or incorrect upon delivery.</p>

    <h3 style="font-size: 18px; color: #111; margin-top: 24px;">2. Perishable Items</h3>
    <p style="color: #4b5563;">Fresh produce, dairy, meat, and frozen items must be inspected upon delivery. Any quality issues must be reported within <strong>24 hours</strong>.</p>

    <h3 style="font-size: 18px; color: #111; margin-top: 24px;">3. How to Request a Return</h3>
    <p style="color: #4b5563;">To submit a return request, please reach out via our <a href="<?php echo e(route('contact.index')); ?>" style="color: var(--brand, #10b981); font-weight: 600;">Contact Us page</a> with your order number and photos of the affected product.</p>

    <h3 style="font-size: 18px; color: #111; margin-top: 24px;">4. Refunds</h3>
    <p style="color: #4b5563;">Approved refunds will be credited back to your payment account or issued as store voucher within 2 to 5 business days.</p>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/returns.blade.php ENDPATH**/ ?>