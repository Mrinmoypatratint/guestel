# Production Deployment Manual — Hostinger Premium PHP Hosting

## 1. Hosting Environment & Architectural Realities
Hostinger Premium PHP hosting provides a shared Linux environment with:
- **PHP 8.3+** (or 8.4) via LiteSpeed Web Server.
- **MySQL 8.x / MariaDB** database server.
- **SSH access** without root privileges.
- **No persistent background daemons**: No Docker, no Kubernetes, no Redis service, and no Supervisor.
- **Hostinger Cron Jobs** (executing via CLI every minute).

---

## 2. Directory Structure & Web Root Mapping

```
/home/u123456789/
├── domains/
│   └── yourdomain.com/
│       ├── hotel-guest-platform/       <-- Complete Laravel repository (SECURE, NON-PUBLIC)
│       │   ├── app/
│       │   ├── bootstrap/
│       │   ├── config/
│       │   ├── database/
│       │   ├── storage/
│       │   ├── vendor/
│       │   ├── .env
│       │   └── artisan
│       │
│       └── public_html/                <-- HOSTINGER WEB ROOT
│           └── (SYMLINK to ../hotel-guest-platform/public OR docroot pointed here)
```

> [!CRITICAL]
> **NEVER MOVE `index.php` INTO THE PROJECT ROOT!**
> In Hostinger hPanel, change the **Document Root** for your domain directly to:
> `domains/yourdomain.com/hotel-guest-platform/public`
> If hPanel does not allow custom document roots on your specific plan, replace the contents of `public_html` with a symbolic link:
> ```bash
> cd ~/domains/yourdomain.com
> rm -rf public_html
> ln -s hotel-guest-platform/public public_html
> ```

---

## 3. Step-by-Step Production Deployment

### Step 1: Enable SSH Access
1. Log in to Hostinger hPanel.
2. Navigate to **Advanced** → **SSH Access**.
3. Enable SSH and note your SSH IP, Port (usually 65002), and Username.

### Step 2: Clone Repository via SSH
```bash
ssh -p 65002 u123456789@your-server-ip
cd ~/domains/yourdomain.com
git clone https://github.com/your-org/hotel-guest-platform.git hotel-guest-platform
cd hotel-guest-platform
```

### Step 3: Configure Environment
```bash
cp .env.example .env
nano .env
```
Ensure the following variables are set:
```dotenv
APP_NAME="Grand Azure Hospitality"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_hotel
DB_USERNAME=u123456789_admin
DB_PASSWORD=YourSuperSecurePassword123!

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_STORE=file
QUEUE_CONNECTION=database
```

### Step 4: Install Dependencies & Generate Keys
```bash
# Generate unique encryption key
php artisan key:generate

# Install composer dependencies optimized for production
composer install --no-dev --optimize-autoloader --prefer-dist

# Build storage symlink for public image assets
php artisan storage:link
```

### Step 5: Frontend Assets Build
Because Hostinger shared hosting may not have modern Node.js versions:
1. Run `npm ci && npm run build` locally on your development workstation.
2. Commit or SFTP upload the resulting `public/build` directory to the server.

### Step 6: Database Migrations & Caching
```bash
# Execute idempotent schema migrations
php artisan migrate --force

# Compile and cache configuration, routes, and blade views
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 4. Hostinger Cron Jobs Configuration
Hostinger Premium executes background scheduling through cPanel/hPanel Scheduled Tasks.
Navigate to **Advanced** → **Cron Jobs** and configure:

| Job Type | Schedule | Command Line |
| :--- | :--- | :--- |
| **Laravel Scheduler** | `* * * * *` (Every min) | `cd /home/u123456789/domains/yourdomain.com/hotel-guest-platform && php artisan schedule:run >> /dev/null 2>&1` |
| **Database Queue Worker** | `*/2 * * * *` (Every 2 min) | `cd /home/u123456789/domains/yourdomain.com/hotel-guest-platform && php artisan queue:work --stop-when-empty --tries=3 >> /dev/null 2>&1` |

The scheduler handles:
- SLA escalation of overdue service requests (`requests:escalate-sla`).
- Invalidation of expired guest sessions and announcements.
- Daily aggregation of scan and dining analytics.

---

## 5. Post-Deployment Verification Checklist
Run these commands via SSH or curl to verify production readiness:

```bash
# 1. Health check endpoint (must return HTTP 200 {"status":"ok"})
curl -i https://yourdomain.com/health

# 2. Verify storage permissions
chmod -R 775 storage bootstrap/cache

# 3. Verify security headers
curl -I https://yourdomain.com/login | grep -E "X-Content-Type-Options|Content-Security-Policy|Strict-Transport-Security"

# 4. Verify permanent QR resolution
curl -I https://yourdomain.com/g/fd3ywa4gfuhv9comqlawfuhsmtuvx1bstmwg5wtwbcjrjpls
```
