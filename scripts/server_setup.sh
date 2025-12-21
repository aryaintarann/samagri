#!/bin/bash

# Exit on error
set -e

echo "Starting Server Setup for Samagri..."

# 1. Update System
echo "Updating system..."
apt update && apt upgrade -y
apt install -y software-properties-common curl zip unzip git acl

# 2. Add PHP Repository
# Note: Ubuntu 25.04 ("Plucky") is very new. The ondrej/php PPA might not support it yet.
# However, Ubuntu 25.x defaults to PHP 8.3/8.4 which is compatible.
# We skip the PPA for now and rely on default repos.
# add-apt-repository ppa:ondrej/php -y
# apt update

# 3. Install PHP (Default Version) and Extensions
echo "Installing PHP..."
# Using generic package names (php, php-fpm) ensures we get the version native to this Ubuntu release (likely 8.3 or 8.4)
apt install -y php php-fpm php-cli php-mysql php-sqlite3 php-curl \
    php-gd php-mbstring php-xml php-zip php-bcmath php-intl

# 4. Install Nginx
echo "Installing Nginx..."
apt install -y nginx

# 5. Install Composer
echo "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# 6. Install Node.js (LTS)
echo "Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# 7. Install MariaDB Server
echo "Installing MariaDB Server..."
apt install -y mariadb-server
systemctl start mariadb
systemctl enable mariadb

# 8. Install Cloudflared
echo "Installing Cloudflare Tunnel..."
curl -L --output cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
dpkg -i cloudflared.deb
rm cloudflared.deb

# 8. Setup Application Directory
echo "Setting up application directory..."
mkdir -p /var/www/samagri
chown -R www-data:www-data /var/www/samagri
chmod -R 775 /var/www/samagri

# 9. Add current user to www-data group (optional but helpful)
usermod -aG www-data $USER

echo "Server setup complete! Next steps:"
echo "1. Configure Nginx (see deployment guide)"
echo "2. Deploy application code"
echo "3. Run 'cloudflared tunnel login'"
