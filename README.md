# Mini Mart System

Grocery storefront + admin dashboard built on Laravel 11.

```
MINI-MART-SYSTEM/
├── backend/    Laravel app: controllers, models, routes, config, tests, artisan, composer.json
├── frontend/   Blade views (views/), public web root (public/: css, js, images, Photo, index.php), Vite (package.json)
└── database/   migrations/, seeders/, factories/, database.sqlite, schema/schema.sql
```

Laravel is told where the other folders are in `backend/bootstrap/app.php`
(database path, public path) and `backend/config/view.php` (Blade views path).

## First-time setup

```bash
cd backend
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
php artisan migrate --seed    # only if database/database.sqlite is empty/missing
```

If `database/database.sqlite` does not exist, create an empty file first (`database/database.sqlite`).

## Run

```bash
cd backend
php artisan serve             # serves frontend/public as the web root -> http://127.0.0.1:8000
```

Optional, only if you use Vite assets:

```bash
cd frontend
npm install
npm run dev
```

## Tests

```bash
cd backend
php artisan test
```

## Production web server

Point the web root (Apache `DocumentRoot` / Nginx `root`) at `frontend/public`.
