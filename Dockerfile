# syntax=docker/dockerfile:1

# 1) Composer dependencies (no dev)
FROM composer:2.7 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# 2) Frontend build
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* .npmrc* ./
RUN npm ci --no-audit --no-fund || npm install --no-audit --no-fund
COPY . .
# Ensure vendor is available during Vite build for imports like vendor/livewire/flux/dist/flux.css
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# 3) Runtime (PHP-FPM + Nginx)
FROM php:8.2-fpm AS runtime

# Install system deps and PHP extensions
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    git unzip pkg-config libzip-dev libicu-dev libpng-dev libonig-dev libxml2-dev libsqlite3-dev \
    nginx supervisor \
 && docker-php-ext-install pdo_mysql pdo_sqlite zip bcmath intl \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Nginx config and Supervisor to run php-fpm + nginx
COPY deploy/nginx.conf /etc/nginx/nginx.conf
COPY deploy/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application source
COPY . .

# Copy built vendor and assets
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Ensure writable dirs
RUN mkdir -p storage/framework/{cache,data,sessions,testing,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Optimize Laravel (config/routes/views)
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache || true

# Create startup script to run migrations and start services
RUN echo '#!/bin/bash\n\
set -e\n\
echo "Running database migrations..."\n\
php artisan migrate --force\n\
echo "Starting services..."\n\
exec /usr/bin/supervisord -n' > /start.sh \
 && chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]


