#!/bin/bash

# Exit on error
set -e

APP_DIR="/var/www/samagri"

echo "Deploying Samagri to $APP_DIR..."

# Navigate to app directory
cd $APP_DIR

# 1. Pull latest code
# Note: Ensure you have set up git credentials or ssh keys for this to work without password prompt
echo "Pulling latest code..."
git pull origin master

# 2. Install PHP Dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Install Node Dependencies & Build Assets
echo "Building frontend assets..."
npm install
npm run build

# 4. Run Migrations
echo "Running database migrations..."
php artisan migrate --force

# 5. Cache Config/Routes
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Fix Permissions
echo "Fixing permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# 7. Restart PHP-FPM
echo "Reloading PHP-FPM..."
# Reload the default php-fpm service.
# If this fails, check the exact service name with "systemctl list-units | grep php"
service php*-fpm reload

echo "Deployment complete! Application is now live."
