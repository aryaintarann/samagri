#!/bin/bash

# Clear Cache
php artisan optimize:clear

# Run Migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm
