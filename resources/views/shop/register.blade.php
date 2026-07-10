@extends('layouts.app')

@section('title', 'Create account')

@section('content')
<section class="panel narrow">
  <h1>Create account</h1>
  <p class="muted">
    Sign up here for the shop. The JSON API still has its own
    <code>POST /auth/register</code> endpoint for mobile apps and curl.
  </p>

  <form method="post" action="{{ route('shop.register') }}">
    @csrf
    @if ($errors->any())
      <p class="form-error">{{ $errors->first() }}</p>
    @endif
    <div class="mb-3">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="{{ old('email') }}" required>
      @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <div class="mb-3">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="mb-3">
      <label for="password_confirmation">Confirm password</label>
      <input type="password" id="password_confirmation" name="password_confirmation" required>
    </div>
    <div class="actions">
      <button type="submit" class="button">Create account</button>
    </div>
  </form>

  <p class="muted">
    Already have an account?
    <a href="{{ route('shop.login') }}">Log in</a>
  </p>
</section>
@endsection
