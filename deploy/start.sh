#!/bin/bash
set -e

echo "Environment variables:"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_DATABASE: $DB_DATABASE"

echo "Checking database connection..."
php artisan config:clear
php artisan tinker --execute="echo 'DB Connection: ' . config('database.default'); echo 'DB Host: ' . config('database.connections.pgsql.host');"

echo "Running database migrations..."
php artisan migrate --force --database=pgsql

echo "Setting up Flux assets..."
mkdir -p public/flux
cp vendor/livewire/flux/dist/flux.min.js public/flux/flux.js || echo "Flux JS not found"
cp vendor/livewire/flux/dist/flux-lite.min.js public/flux/flux-lite.js || echo "Flux Lite JS not found"
chown -R www-data:www-data public/flux || echo "Could not set Flux permissions"

echo "Setting up admin user..."
php artisan sacco:setup-roles --admin-email=admin@esacco.com --admin-password=AdminPassword123! || echo "Admin user may already exist"

echo "Fixing admin user roles..."
php /var/www/html/deploy/fix-admin.php || echo "Admin fix script failed"

echo "Setting up caching..."
# Set cache driver to redis if available, otherwise database
php artisan config:set cache.default redis || php artisan config:set cache.default database
php artisan config:set session.driver redis || php artisan config:set session.driver database
php artisan config:set queue.default redis || php artisan config:set queue.default database

echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Clearing old caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo "Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting services..."
exec /usr/bin/supervisord -n
