# White Wolf Emporium

A Witcher-themed e-commerce store built with Laravel 11, PostgreSQL, and Vite.

## Prerequisites

| Tool | Version | Check |
|------|---------|-------|
| Docker & Docker Compose | Latest | `docker --version` |
| — *or for native setup* — | | |
| PHP | 8.4+ | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 22+ | `node -v` |
| PostgreSQL | 16+ | `psql --version` |

## Quick Start (Docker) — macOS & Windows

Docker is the recommended setup. It works identically on both platforms.

### 1. Clone and configure

```bash
git clone <repo-url> white-wolf-emporium
cd white-wolf-emporium
cp .env.example .env
```

Edit `.env` and set your database credentials (or keep the defaults):

```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

> **Important:** When running via Docker, set `DB_HOST=db` (the Docker service name). When running natively, set `DB_HOST=127.0.0.1`.

### 2. Start the containers

```bash
docker compose up -d
```

This starts three services:
- **app** — PHP 8.4 on `http://localhost:8000`
- **vite** — Vite dev server on `http://localhost:5173` (HMR)
- **db** — PostgreSQL 16 on `localhost:5432`

On first run, Composer and npm dependencies are installed automatically.

### 3. Generate app key and run migrations

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

### 4. Open the app

- **Storefront:** http://localhost:8000
- **Admin panel:** http://localhost:8000/admin/login

Default admin credentials (from seeder):
- Email: `admin@whitewolf.nv`
- Password: `password`

### Stopping

```bash
docker compose down          # stop containers (data persists)
docker compose down -v       # stop and delete database volume
```

---

## Native Setup — macOS

### 1. Install dependencies

```bash
brew install php@8.4 composer node postgresql@16
brew services start postgresql@16
```

### 2. Create database

```bash
createdb laravel
createuser laravel -P        # enter a password when prompted
psql -c "GRANT ALL PRIVILEGES ON DATABASE laravel TO laravel;"
```

### 3. Configure and install

```bash
cp .env.example .env
# Edit .env: set DB_HOST=127.0.0.1, DB_DATABASE, DB_USERNAME, DB_PASSWORD
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 4. Run

Open two terminal tabs:

```bash
# Tab 1 — PHP server
php artisan serve

# Tab 2 — Vite dev server
npm run dev
```

- **Storefront:** http://localhost:8000
- **Admin:** http://localhost:8000/admin/login

---

## Native Setup — Windows

### 1. Install dependencies

Install the following:
- **PHP 8.4:** Download from https://windows.php.net/download — extract to `C:\php`, add to PATH. Enable these extensions in `php.ini`: `pdo_pgsql`, `pgsql`, `openssl`, `mbstring`, `fileinfo`, `curl`.
- **Composer:** Download and run the installer from https://getcomposer.org/download/
- **Node.js 22+:** Download from https://nodejs.org
- **PostgreSQL 16:** Download from https://www.postgresql.org/download/windows/ — remember the password you set for the `postgres` user.

### 2. Create database

Open pgAdmin or a terminal:

```sql
CREATE DATABASE laravel;
CREATE USER laravel WITH PASSWORD 'secret';
GRANT ALL PRIVILEGES ON DATABASE laravel TO laravel;
```

### 3. Configure and install

```cmd
copy .env.example .env
REM Edit .env: set DB_HOST=127.0.0.1, DB_DATABASE=laravel, DB_USERNAME=laravel, DB_PASSWORD=secret
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

### 4. Run

Open two terminals:

```cmd
REM Terminal 1
php artisan serve

REM Terminal 2
npm run dev
```

- **Storefront:** http://localhost:8000
- **Admin:** http://localhost:8000/admin/login

---

## Running Tests

```bash
# Docker
docker compose exec app php artisan test

# Native
php artisan test
```

## Project Structure

```
app/
├── Http/Controllers/       # Request handlers
│   ├── Admin/              # Admin product management
│   ├── Auth/               # Login, register, admin login
│   ├── CartController      # Shopping cart
│   ├── CheckoutController  # Order placement
│   └── HomeController      # Landing page
├── Models/                 # Eloquent models
├── Services/               # CartResolver, OrderOptionService
└── Http/Requests/          # Form validation
database/
├── migrations/             # Schema
└── seeders/                # Sample data + admin user
resources/
├── css/                    # Vite-processed CSS (storefront + admin)
├── js/                     # Vite-processed JS
└── views/                  # Blade templates
public/
├── css/                    # Static mockup CSS (design reference)
└── js/                     # Static mockup JS (design reference)
```
