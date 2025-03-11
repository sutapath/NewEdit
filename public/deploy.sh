#!/bin/bash
echo "Deploy script running..."

# ✅ ติดตั้ง PHP และ Composer ก่อน
echo "Installing PHP..."
apt-get update && apt-get install -y php-cli


echo "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# ✅ รัน Laravel commands
echo "Running composer"
composer install --no-dev --optimize-autoloader

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache
