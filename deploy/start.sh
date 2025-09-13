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

echo "Clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo "Forcing HTTPS..."
php artisan config:cache

echo "Starting services..."
exec /usr/bin/supervisord -n
