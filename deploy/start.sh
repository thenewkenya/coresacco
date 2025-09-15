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

echo "Setting up Inertia.js assets..."
# Ensure build directory exists and has proper permissions
mkdir -p public/build
chown -R www-data:www-data public/build || echo "Could not set build permissions"

echo "Setting up admin user..."
php artisan sacco:setup-roles --admin-email=admin@coresacco.com --admin-password=AdminPassword123! || echo "Admin user may already exist"

echo "Fixing admin user roles..."
php /var/www/html/deploy/fix-admin.php || echo "Admin fix script failed"

echo "Setting up caching..."
# Configure caching via environment variables
if [ -n "$REDIS_URL" ]; then
    echo "Using Redis for caching..."
    export CACHE_DRIVER=redis
    export SESSION_DRIVER=redis
    export QUEUE_CONNECTION=redis
else
    echo "Using database for caching..."
    export CACHE_DRIVER=database
    export SESSION_DRIVER=database
    export QUEUE_CONNECTION=database
fi

echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Clearing old caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo "Ensuring Inertia.js assets are accessible..."
# Ensure all public assets have proper permissions
chown -R www-data:www-data public/ || echo "Could not set public permissions"
chmod -R 755 public/ || echo "Could not set public permissions"

echo "Rebuilding optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting services..."
exec /usr/bin/supervisord -n
