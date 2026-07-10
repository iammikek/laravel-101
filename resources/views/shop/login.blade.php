@extends('layouts.app')

@section('title', 'Log in')

@section('content')
<section class="panel narrow">
  <h1>Log in</h1>
  <p class="muted">
    Browser session auth — different from <code>POST /auth/login</code>, which returns a JWT for the API.
  </p>

  @error('email')
    <p class="form-error">{{ $message }}</p>
  @enderror

  <form method="post" action="{{ route('shop.login') }}">
    @csrf
    <div class="mb-3">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
    </div>
    <div class="mb-3">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="actions">
      <button type="submit" class="button">Log in</button>
    </div>
  </form>

  <p class="muted">
    No account?
    <a href="{{ route('shop.register') }}">Create account</a>
  </p>
</section>
@endsection
