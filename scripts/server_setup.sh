#!/bin/bash

# Exit on error
set -e

echo "Starting Server Setup for Samagri..."

# 1. Update System
echo "Updating system..."
apt update && apt upgrade -y
apt install -y software-properties-common curl zip unzip git acl

# 2. Add PHP Repository (if on older Ubuntu, but 25 might have it, safe to add ppa:ondrej/php for latest)
# Ubuntu 24.04+ generally has new PHP, but ondrej PPA is standard for flexibility
add-apt-repository ppa:ondrej/php -y
apt update

# 3. Install PHP 8.2 and Extensions
echo "Installing PHP 8.2..."
apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-sqlite3 php8.2-curl \
    php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl

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
