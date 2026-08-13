#!/bin/bash

echo "Running Laravel deployment script..."

# Cache configuration
cd /var/www/html

# Run migrations (--force needed for production)
php artisan migrate --force

# Clear and cache config/routes/views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link --force

echo "Laravel deployment complete!"
