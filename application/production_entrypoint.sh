#!/bin/sh
set -e

# Wait for the database to be ready (optional, but good practice if not handled by wait-for-it)
# echo "Waiting for database..."

# Run Laravel production commands
echo "Caching configuration..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Caching views..."
php artisan view:cache

echo "Linking storage..."
php artisan storage:link

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
