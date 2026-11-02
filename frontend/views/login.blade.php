@extends('layouts.app') {{-- Change to your layout if different --}}

@section('content')
<div class="container">
    <h2>Login</h2>
    <<form action="{{ route('login') }}" method="POST">
    @csrf
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Login</button>
</form>
</div>
@endsection