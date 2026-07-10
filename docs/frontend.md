# Catalog Shop frontend

This document explains how the **browser UI** at `/shop` was built — a classic Laravel full-stack frontend that sits alongside the JSON API, not a JavaScript client calling `/items`.

---

## Two interfaces, one database

| | JSON API | Catalog Shop (`/shop`) |
|--|----------|------------------------|
| Response | `application/json` | `text/html` |
| Auth | JWT Bearer token | Session cookie |
| Routes | `routes/api.php` (no prefix) | `routes/web.php` + `web` middleware |
| Views | — | Blade in `resources/views/shop/` |
| Validation | Controller + `Validator` | Controller + `$request->validate()` |

The shop calls **`ItemService` and `UserService` directly** — it does not HTTP-call `/items`. Same monolith pattern as symfony-101 and django-101.

---

## Architecture

```
/shop/*  ──► App\Http\Controllers\Shop\*  ──► Form validation in controller
                                              │
                                              ▼
                                        App\Services\*  ──► Eloquent models  ──► DB

/items   ──► ItemController  ──► (same) App\Services\ItemService  ──► DB
```

**Symfony parallel:** `/shop/*` ≈ web routes + Blade + session auth; `/items` ≈ API routes + JWT.

---

## Shop URLs

| URL | Controller | Auth | Purpose |
|-----|------------|------|---------|
| `/shop` | `ShopHomeController` | Public | Landing + catalog stats |
| `/shop/items` | `ShopItemController::index` | Public | Browse/filter items |
| `/shop/items/{id}` | `ShopItemController::show` | Public | Item detail |
| `/shop/items/new` | `ShopItemController::create` | Session login | Add item via HTML form |
| `/shop/register` | `ShopAuthController::register` | Public | Signup + auto-login |
| `/shop/login` | `ShopAuthController::login` | Public | Session login |
| `/shop/logout` | `ShopAuthController::logout` | POST | End session |

Header **API** link → `GET /items` (raw JSON in the browser).

---

## Key files

```
laravel-101/
├── app/
│   ├── Http/Controllers/         # JSON API
│   ├── Http/Controllers/Shop/    # Blade shop
│   ├── Services/                 # Shared business logic
│   └── Support/ApiSerializer.php
├── resources/views/
│   ├── layouts/app.blade.php
│   └── shop/*.blade.php
├── public/shop/style.css
├── routes/api.php                # JWT API (no /api prefix)
├── routes/web.php                # /shop session routes
└── bootstrap/app.php             # Exception JSON shape + health routes
```

CSS is in `public/shop/style.css`. **`php artisan serve`** serves static assets with correct MIME types (unlike Symfony’s PHP built-in server quirk).

---

## Dual auth

| Action | Shop (browser) | API (JSON) |
|--------|----------------|------------|
| Register | `POST /shop/register` → session | `POST /auth/register` → user JSON |
| Login | `POST /shop/login` → session cookie | `POST /auth/login` → JWT |
| Write items | Session on `/shop/items/new` | `POST /items` with Bearer token |

API routes use the `api` JWT guard (`auth:api`). Shop routes use the default `web` session guard (`auth`).

---

## Try it

```bash
make serve
# http://127.0.0.1:8003/shop/register
curl -I http://127.0.0.1:8003/shop/style.css   # Content-Type: text/css
```

1. Open `/shop/register` → create account (logged in automatically).
2. Go to `/shop/items/new` → add an item.
3. Browse `/shop/items` and open a detail page.
4. Click **API** in the header → see the same data as JSON.
