<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Catalog Shop') · laravel-101</title>
  <link rel="stylesheet" href="{{ asset('shop/style.css') }}">
</head>
<body>
  <header class="site-header">
    <div class="container header-inner">
      <a class="brand" href="{{ route('shop.home') }}">Catalog Shop</a>
      <nav class="nav">
        <a href="{{ route('shop.home') }}">Home</a>
        <a href="{{ route('shop.items.index') }}">Items</a>
        <a href="/items">API</a>
      </nav>
      <div class="auth">
        @auth
          <span class="muted">{{ auth()->user()->email }}</span>
          <form method="post" action="{{ route('shop.logout') }}">
            @csrf
            <button type="submit" class="link-button">Log out</button>
          </form>
        @else
          <a href="{{ route('shop.login') }}">Log in</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="container">
    @foreach (['success', 'error', 'info'] as $type)
      @if (session($type))
        <ul class="messages">
          <li class="message {{ $type }}">{{ session($type) }}</li>
        </ul>
      @endif
    @endforeach

    @yield('content')
  </main>

  <footer class="site-footer">
    <div class="container">
      <p class="muted">
        Server-rendered Blade templates — same services as the JSON API.
      </p>
    </div>
  </footer>
</body>
</html>
