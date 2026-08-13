FROM richarvey/nginx-php-fpm:3.1.6

# Copy application files
COPY . .

# Set web root to Laravel's public directory
ENV WEBROOT /var/www/html/public

# Let the base image handle composer install
ENV SKIP_COMPOSER 0

# PHP settings for production
ENV PHP_ERRORS_ON 0
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel environment
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Create necessary directories and set permissions
RUN mkdir -p /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Create the startup script
RUN mkdir -p /var/www/html/scripts

COPY scripts/00-laravel-deploy.sh /var/www/html/scripts/00-laravel-deploy.sh
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh
