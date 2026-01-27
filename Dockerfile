# Optimized PHP-FPM image for production
FROM php:8.2-fpm

WORKDIR /var/www

# Install system dependencies in one layer and clean up immediately
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Get Composer (only what we need)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY --chown=www-data:www-data . /var/www

# Create Laravel directories and set permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Install production dependencies only (optimized)
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader --prefer-dist \
    && composer dump-autoload --optimize --classmap-authoritative \
    && rm -rf /root/.composer/cache \
    && rm -rf /tmp/* /var/tmp/*

EXPOSE 9000
CMD ["php-fpm"]
