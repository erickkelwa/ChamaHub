#!/bin/bash

set -e

echo "=== Running Laravel deployment script ==="

cd /var/www/html

# --- Parse DATABASE_URL if provided by Render ---
# Render PostgreSQL databases expose a DATABASE_URL like:
# postgresql://user:pass@host:port/dbname
if [ -n "$DATABASE_URL" ]; then
    echo "--- Parsing DATABASE_URL for PostgreSQL ---"
    # Strip the scheme
    DB_URL_STRIPPED="${DATABASE_URL#*://}"
    DB_USERNAME=$(echo "$DB_URL_STRIPPED" | cut -d: -f1)
    DB_PASSWORD=$(echo "$DB_URL_STRIPPED" | cut -d@ -f1 | cut -d: -f2)
    DB_HOST=$(echo "$DB_URL_STRIPPED" | cut -d@ -f2 | cut -d: -f1)
    DB_PORT=$(echo "$DB_URL_STRIPPED" | cut -d@ -f2 | cut -d: -f2 | cut -d/ -f1)
    DB_DATABASE=$(echo "$DB_URL_STRIPPED" | cut -d/ -f2 | cut -d? -f1)
    export DB_CONNECTION=pgsql
    export DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD
    echo "    DB_HOST=$DB_HOST  DB_PORT=$DB_PORT  DB_DATABASE=$DB_DATABASE"
fi

echo "--- Ensuring storage directories exist ---"
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 777 storage bootstrap/cache

echo "--- Clearing ALL caches ---"
php artisan optimize:clear || true

echo "--- Running migrations ---"
php artisan migrate --force

echo "--- Seeding admin user ---"
php artisan db:seed --class=AdminSeeder --force || true

echo "--- Caching config and views only ---"
php artisan config:cache
php artisan view:cache

echo "--- Creating storage symlink ---"
php artisan storage:link --force || true

echo "=== Laravel deployment complete! ==="
