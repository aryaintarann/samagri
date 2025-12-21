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

# Ensure APP_KEY exists
php artisan key:generate --force --skip-if-exists

# Run Migrations with Retry
echo "Running migrations..."
count=0
while [ $count -lt 10 ]; do
    php artisan migrate --force
    if [ $? -eq 0 ]; then
        echo "Migrations successful!"
        break
    fi
    count=$((count + 1))
    echo "Migration failed (DB not ready?), retrying in 5 seconds... ($count/10)"
    sleep 5
done

# Start PHP-FPM
php-fpm
