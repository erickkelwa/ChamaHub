#!/bin/bash

set -e

echo "=== Running Laravel deployment script ==="

cd /var/www/html

echo "--- Ensuring storage directories exist ---"
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "--- Clearing ALL caches ---"
php artisan optimize:clear || true

echo "--- Running migrations ---"
php artisan migrate --force

echo "--- Caching config and views only ---"
php artisan config:cache
php artisan view:cache

echo "--- Creating storage symlink ---"
php artisan storage:link --force || true

echo "=== Laravel deployment complete! ==="
