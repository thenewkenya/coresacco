#!/bin/bash
set -e

echo "Checking database connection..."
php artisan tinker --execute="echo 'DB Connection: ' . config('database.default'); echo 'DB Host: ' . config('database.connections.pgsql.host');"

echo "Running database migrations..."
php artisan migrate --force --database=pgsql

echo "Clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo "Forcing HTTPS..."
php artisan config:cache

echo "Starting services..."
exec /usr/bin/supervisord -n
