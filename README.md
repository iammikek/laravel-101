# Getting Fast at Laravel

A step-by-step **Laravel 13 + Eloquent** port of [fastAPI-101](https://github.com/iammikek/fastAPI-101) — same items/categories API, same crossover style as [symfony-101](https://github.com/iammikek/symfony-101), native Laravel conventions.

**Audience:** Laravel developers learning the *-101 family API shape, or Symfony/Python devs comparing how Laravel does the same monolith split.

**Monolith UI:** Laravel owns the JSON API plus a **server-rendered shop** at `/shop` (Blade + session auth) — see **[docs/frontend.md](docs/frontend.md)**.

---

## What's Included

1. **Laravel 13** — API routes without `/api` prefix (matches symfony-101 URLs)
2. **`User` model** — register/login/me, JWT (`php-open-source-saver/jwt-auth`)
3. **`Category` + `Item` models** — Eloquent, migrations, service layer
4. **Service layer** — `app/Services/` (mirrors symfony-101)
5. **Pagination** — `{ items, total, skip, limit }`
6. **Filtering** — `min_price`, `max_price`, `category_id`, `name_contains`
7. **Item stats** — `GET /items/stats/summary`
8. **JWT auth** — Bearer tokens on write endpoints
9. **Rate limiting** — 10/min auth, 60/min writes
10. **Catalog Shop** — Blade UI at `/shop` — **[docs/frontend.md](docs/frontend.md)**
11. **SQLite locally** — PostgreSQL in Docker (port **8003**)
12. **Tests** — 28 PHPUnit feature tests
13. **CI** — GitHub Actions

---

## Quick Start

### Local PHP (SQLite)

```bash
cd laravel-101
cp .env.example .env
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate
make serve
```

Open **http://127.0.0.1:8003/** — hello message  
**http://127.0.0.1:8003/items** — JSON list  
**http://127.0.0.1:8003/shop** — browser UI

### Docker (PostgreSQL)

```bash
docker compose up --build
```

API on **http://localhost:8003** (fastAPI-101 = 8000, django-101 = 8001, symfony-101 = 8002).

### Tests

```bash
composer install
php artisan test
```

---

## Project Structure

```
laravel-101/
├── app/
│   ├── Http/Controllers/       # JSON API
│   ├── Http/Controllers/Shop/  # Blade shop (/shop)
│   ├── Models/                 # User, Category, Item
│   ├── Services/               # Business logic
│   ├── Support/ApiSerializer.php
│   └── Exceptions/
├── resources/views/shop/       # Blade templates
├── public/shop/style.css
├── routes/api.php              # JWT routes (no prefix)
├── routes/web.php              # Session shop routes
├── docs/frontend.md
└── tests/Feature/                # 28 tests
```

---

## Catalog Shop (`/shop`)

| Shop (browser) | API (JSON) |
|----------------|------------|
| `/shop/register` — signup + auto-login | `POST /auth/register` |
| `/shop/login` — session cookie | `POST /auth/login` — JWT |
| `/shop/items` — HTML table + filters | `GET /items` |
| `/shop/items/new` — HTML form | `POST /items` — Bearer token |

The shop calls **`ItemService` / `UserService` directly** — it does not fetch `/items`.

- **Blade views** in `resources/views/shop/`
- **Session auth** on shop writes; **JWT** on API writes
- Header **API** link → raw JSON at `/items`

```bash
make serve
# http://127.0.0.1:8003/shop/register
curl -I http://127.0.0.1:8003/shop/style.css   # Content-Type: text/css
```

Full walkthrough: **[docs/frontend.md](docs/frontend.md)**

---

## Quick Reference

| Goal | Command |
|------|---------|
| Copy env | `cp .env.example .env` |
| Install deps | `composer install` |
| App + JWT secrets | `php artisan key:generate && php artisan jwt:secret` |
| Migrate | `php artisan migrate` |
| Run local | `make serve` → http://127.0.0.1:8003 |
| Open shop | http://127.0.0.1:8003/shop |
| Raw JSON items | http://127.0.0.1:8003/items |
| Run tests | `php artisan test` |
| Docker | `docker compose up --build` |

### API endpoints

| Path | Method | Auth | Purpose |
|------|--------|------|---------|
| `/` | GET | — | Hello message |
| `/health` | GET | — | Health check |
| `/auth/register` | POST | — | Create user |
| `/auth/login` | POST | — | Get JWT |
| `/auth/me` | GET | JWT | Current user |
| `/categories` | GET/POST | JWT on POST | List/create |
| `/categories/{id}` | GET/PATCH/DELETE | JWT on writes | CRUD |
| `/items` | GET/POST | JWT on POST | List/create |
| `/items/stats/summary` | GET | — | Statistics |
| `/items/{id}` | GET/PATCH/DELETE | JWT on writes | CRUD |

---

## *-101 family ports

| Repo | Port | Stack |
|------|------|-------|
| fastAPI-101 | 8000 | Python reference |
| django-101 | 8001 | Python + DRF + shop |
| symfony-101 | 8002 | PHP Symfony + shop |
| **laravel-101** | **8003** | **PHP Laravel + shop** |

---

## Compare with symfony-101

| | symfony-101 | laravel-101 |
|--|-------------|-------------|
| ORM | Doctrine | Eloquent |
| Shop views | Twig | Blade |
| API auth | Lexik JWT | jwt-auth |
| Session shop | Dual firewalls | `web` routes + `auth` |
| Services | `src/Service/` | `app/Services/` |
| Tests | 28 | 28 |

Same API response shapes. Same `/shop` monolith pattern.
