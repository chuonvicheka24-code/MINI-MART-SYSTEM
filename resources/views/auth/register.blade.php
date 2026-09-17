@extends('layouts.app')

@section('title', 'Create account — Mini Mart')

@section('content')

<section class="section" style="display:flex; justify-content:center; padding-top:64px;">
  <form class="form-card" style="max-width:460px; width:100%;" method="POST" action="{{ route('register') }}">
    @csrf
    <h2 style="margin-bottom:6px;">Create your account</h2>
    <p class="form-note" style="margin-bottom:20px;">Faster checkout, saved address, and order history.</p>
    @if ($errors->any())
      <p class="form-note" style="color:var(--danger); margin-bottom:14px;">{{ $errors->first() }}</p>
    @endif
    <div class="field-row">
      <div class="field"><label for="r-first">First name</label><input id="r-first" name="first_name" value="{{ old('first_name') }}" required></div>
      <div class="field"><label for="r-last">Last name</label><input id="r-last" name="last_name" value="{{ old('last_name') }}" required></div>
    </div>
    <div class="field"><label for="r-email">Email</label><input id="r-email" name="email" type="email" value="{{ old('email') }}" required></div>
    <div class="field"><label for="r-phone">Phone number</label><input id="r-phone" name="phone" type="tel" value="{{ old('phone') }}" required></div>
    <div class="field"><label for="r-address">Address</label><input id="r-address" name="address" value="{{ old('address') }}" required></div>
    <div class="field"><label for="r-pass">Password</label><input id="r-pass" name="password" type="password" required></div>
    <button class="btn btn-primary btn-block" type="submit">Create account</button>
    <p class="form-switch">Already have an account? <a href="{{ route('login') }}" style="color:var(--brand-dark); text-decoration:underline; font-weight:600;">Log in</a></p>
  </form>
</section>

@endsection
