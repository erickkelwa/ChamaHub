FROM richarvey/nginx-php-fpm:3.1.6

# Set web root to Laravel's public directory
ENV WEBROOT /var/www/html/public

# PHP settings for production
ENV PHP_ERRORS_ON 0
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV SKIP_COMPOSER 1

# Laravel environment
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Copy application files
COPY . .

# Override nginx site config to fix Laravel routing (try_files for /login, /register etc.)
COPY nginx/default.conf /etc/nginx/sites-enabled/default.conf


# Install Composer dependencies explicitly
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Create necessary directories and set permissions
RUN rm -f /var/www/html/bootstrap/cache/routes-v7.php \
    && rm -f /var/www/html/bootstrap/cache/config.php \
    && mkdir -p /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy and enable the startup script
COPY scripts/00-laravel-deploy.sh /var/www/html/scripts/00-laravel-deploy.sh
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh
