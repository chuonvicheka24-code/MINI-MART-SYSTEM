@extends('layouts.app')

@section('title', 'Log in — Mini Mart')

@section('content')

<section class="section" style="display:flex; justify-content:center; padding-top:64px;">
  <form class="form-card" style="max-width:420px; width:100%;" method="POST" action="{{ route('login') }}">
    @csrf
    <h2 style="margin-bottom:6px;">Welcome back</h2>
    <p class="form-note" style="margin-bottom:20px;">Log in to see your past orders and saved address.</p>
    @if ($errors->any())
      <p class="form-note" style="color:var(--danger); margin-bottom:14px;">{{ $errors->first() }}</p>
    @endif
    <div class="field"><label for="l-email">Email</label><input id="l-email" name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com"></div>
    <div class="field"><label for="l-pass">Password</label><input id="l-pass" name="password" type="password" required placeholder="••••••••"></div>
    <button class="btn btn-primary btn-block" type="submit">Log in</button>
    <p class="form-switch">New to Mini Mart? <a href="{{ route('register') }}" style="color:var(--brand-dark); text-decoration:underline; font-weight:600;">Create an account</a></p>
    <p class="form-note" style="margin-top:14px;">Store admin demo login: <strong>admin@minimart.com</strong> / <strong>1234</strong></p>
  </form>
</section>

@endsection
