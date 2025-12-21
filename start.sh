#!/bin/bash

# Fix Git ownership issue
git config --global --add safe.directory /var/www

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build

# Clear Cache
php artisan optimize:clear

# Run Migrations with Retry
echo "Running migrations..."
for i in {1..10}; do
    php artisan migrate --force
    if [ $? -eq 0 ]; then
        echo "Migrations successful!"
        break
    fi
    echo "Migration failed (DB not ready?), retrying in 5 seconds... ($i/10)"
    sleep 5
done

# Start PHP-FPM
php-fpm
