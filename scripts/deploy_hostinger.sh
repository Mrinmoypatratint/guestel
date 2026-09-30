#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
command -v php >/dev/null || { echo "PHP is required"; exit 1; }
command -v composer >/dev/null || { echo "Composer is required"; exit 1; }
[ -f .env ] || { echo "Create .env from .env.example and set production credentials first."; exit 1; }
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
php artisan about --only=environment || true
printf '
Deployment commands completed. Verify /up, /health, login, mail, storage and tenant isolation.
'
