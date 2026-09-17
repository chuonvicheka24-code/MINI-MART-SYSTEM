<?php $__env->startSection('title', 'Log in — Mini Mart'); ?>

<?php $__env->startSection('content'); ?>

<section class="section" style="display:flex; justify-content:center; padding-top:64px;">
  <form class="form-card" style="max-width:420px; width:100%;" method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <h2 style="margin-bottom:6px;">Welcome back</h2>
    <p class="form-note" style="margin-bottom:20px;">Log in to see your past orders and saved address.</p>
    <?php if($errors->any()): ?>
      <p class="form-note" style="color:var(--danger); margin-bottom:14px;"><?php echo e($errors->first()); ?></p>
    <?php endif; ?>
    <div class="field"><label for="l-email">Email</label><input id="l-email" name="email" type="email" value="<?php echo e(old('email')); ?>" required placeholder="you@example.com"></div>
    <div class="field"><label for="l-pass">Password</label><input id="l-pass" name="password" type="password" required placeholder="••••••••"></div>
    <button class="btn btn-primary btn-block" type="submit">Log in</button>
    <p class="form-switch">New to Mini Mart? <a href="<?php echo e(route('register')); ?>" style="color:var(--brand-dark); text-decoration:underline; font-weight:600;">Create an account</a></p>
    <p class="form-note" style="margin-top:14px;">Store admin demo login: <strong>admin@minimart.test</strong> / <strong>password</strong></p>
  </form>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\minimart\resources\views/auth/login.blade.php ENDPATH**/ ?>