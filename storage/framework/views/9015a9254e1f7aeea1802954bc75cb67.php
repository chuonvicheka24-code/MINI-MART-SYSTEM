<?php $__env->startSection('title', 'Create account — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>

<section class="section" style="display:flex; justify-content:center; padding-top:64px;">
  <form class="form-card" style="max-width:460px; width:100%;" method="POST" action="<?php echo e(route('register')); ?>">
    <?php echo csrf_field(); ?>
    <h2 style="margin-bottom:6px;">Create your account</h2>
    <p class="form-note" style="margin-bottom:20px;">Faster checkout, saved address, and order history.</p>
    <?php if($errors->any()): ?>
      <p class="form-note" style="color:var(--danger); margin-bottom:14px;"><?php echo e($errors->first()); ?></p>
    <?php endif; ?>
    <div class="field-row">
      <div class="field"><label for="r-first">First name</label><input id="r-first" name="first_name" value="<?php echo e(old('first_name')); ?>" required></div>
      <div class="field"><label for="r-last">Last name</label><input id="r-last" name="last_name" value="<?php echo e(old('last_name')); ?>" required></div>
    </div>
    <div class="field"><label for="r-email">Email</label><input id="r-email" name="email" type="email" value="<?php echo e(old('email')); ?>" required></div>
    <div class="field"><label for="r-phone">Phone number</label><input id="r-phone" name="phone" type="tel" value="<?php echo e(old('phone')); ?>" required></div>
    <div class="field"><label for="r-address">Address</label><input id="r-address" name="address" value="<?php echo e(old('address')); ?>" required></div>
    <div class="field"><label for="r-pass">Password</label><input id="r-pass" name="password" type="password" required></div>
    <button class="btn btn-primary btn-block" type="submit">Create account</button>
    <p class="form-switch">Already have an account? <a href="<?php echo e(route('login')); ?>" style="color:var(--brand-dark); text-decoration:underline; font-weight:600;">Log in</a></p>
  </form>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\MINI-MART-SYSTEM\resources\views/auth/register.blade.php ENDPATH**/ ?>