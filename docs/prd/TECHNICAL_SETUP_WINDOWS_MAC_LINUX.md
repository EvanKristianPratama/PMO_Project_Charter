# PMO Project Charter

Technical setup documentation for Windows, macOS, and Linux.

## 1. Tech Stack

- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Vue 3 + Inertia.js + Vite
- Auth: Google SSO (Laravel Socialite)
- Database: SQLite (default) or MySQL/MariaDB

## 2. Minimum Requirements

- Git
- PHP `>= 8.2`
- Composer `>= 2.x`
- Node.js `>= 20 LTS`
- npm `>= 10`
- Database:
  - SQLite (quick local setup), or
  - MySQL 8+ / MariaDB 10.6+

Recommended PHP extensions:

- `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`
- `pdo_sqlite` (SQLite mode)
- `pdo_mysql` (MySQL mode)

## 3. Prerequisites Per OS

### Windows (PowerShell)

Install tools using `winget` (or installer manual if preferred):

```powershell
winget install --id Git.Git -e
winget install --id OpenJS.NodeJS.LTS -e
winget install --id Composer.Composer -e
```

For PHP/MySQL, use one of:

- official installer/manual setup, or
- local stack tool (Laragon/XAMPP), as long as required versions are met.

### macOS (Homebrew)

```bash
brew update
brew install git php composer node mysql
```

### Linux (Ubuntu/Debian example)

```bash
sudo apt update
sudo apt install -y git unzip curl ca-certificates
sudo apt install -y php php-cli php-mbstring php-xml php-bcmath php-curl php-sqlite3 php-mysql
sudo apt install -y composer nodejs npm
```

If `nodejs` version is too old, install Node LTS from NodeSource/NVM.

## 4. Clone & Install Project

```bash
git clone https://github.com/EvanKristianPratama/PMO_Project_Charter.git
cd PMO_Project_Charter
composer install
npm install
```

Create `.env`:

- Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

- macOS/Linux:

```bash
cp .env.example .env
```

Generate app key:

```bash
php artisan key:generate
```

## 5. Database Setup

### Option A: SQLite (fastest for local)

Create SQLite file:

- Windows (PowerShell):

```powershell
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite | Out-Null }
```

- macOS/Linux:

```bash
touch database/database.sqlite
```

Set in `.env`:

```env
DB_CONNECTION=sqlite
# DB_DATABASE will default to database/database.sqlite from config/database.php
```

Run migration + seeder:

```bash
php artisan migrate --seed
```

### Option B: MySQL/MariaDB

Create database, then set `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pmo_pc
DB_USERNAME=root
DB_PASSWORD=your_password
```

Optional SSL CA (if required by your environment):

```env
MYSQL_ATTR_SSL_CA=/absolute/path/to/ca.pem
```

If you use a remote MySQL service on Windows and want to use the admin backup feature, these dump-specific settings are often useful:

```env
DB_DUMP_HOST=your-db-hostname
DB_DUMP_RESOLVE_HOST=false
# DB_DUMP_BINARY_PATH=C:/path/to/mysql/bin
```

Then run:

```bash
php artisan migrate --seed
```

## 6. Google SSO Configuration

App login uses Google OAuth.

Set these in `.env`:

```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

On Google Cloud Console:

- add OAuth redirect URI exactly as `GOOGLE_REDIRECT_URI`
- for local usage, keep scheme/host/port consistent with `php artisan serve`

## 7. Run Application (Development)

### Recommended (all-in-one)

```bash
composer dev
```

This runs:

- Laravel server
- queue listener
- log stream
- Vite dev server

### Alternative (separate terminals)

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Terminal 3 (optional, for queue jobs):

```bash
php artisan queue:listen --tries=1 --timeout=0
```

## 8. Production Build

```bash
npm run build
php artisan optimize
```

## 9. Testing & Useful Commands

Run tests:

```bash
php artisan test
```

Clear all caches:

```bash
php artisan optimize:clear
```

DB reset + seed:

```bash
php artisan migrate:fresh --seed
```

## 10. Troubleshooting

- `Vite manifest not found`:
  - run `npm run dev` (local dev) or `npm run build` (production)
- `SQLSTATE ... no such table`:
  - run `php artisan migrate --seed`
- Login always pending/rejected:
  - user status must be approved in admin flow/database
- `Backup failed ... Can't create TCP/IP socket (10106)` or `Can't connect to MySQL server ... (10013)` on Windows:
  - allow `mysqldump.exe` through Windows Firewall / antivirus
  - confirm the database host and port are reachable from the app machine
  - for remote MySQL, set `DB_DUMP_HOST` to the database hostname and `DB_DUMP_RESOLVE_HOST=false`
  - verify `MYSQL_ATTR_SSL_CA` or the CA file used by `DB_DUMP_EXTRA_OPTION`
  - run `php artisan optimize:clear` after changing env values
- Permission issue (macOS/Linux):
  - run `chmod -R ug+rwx storage bootstrap/cache`
- Config not updated after env change:
  - run `php artisan optimize:clear`

## 11. Security Notes

- Never commit `.env` to repository.
- Rotate Google OAuth secrets if exposed.
- Admin seeder data is for local bootstrap only; enforce secure credentials and role governance in non-local environments.
