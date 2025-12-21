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

# Run Migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm
