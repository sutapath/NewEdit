
echo "Running composer"
# composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache 
php artisan serve --host=0.0.0.0 --port=8000  
# echo "Running migrations..."
# php artisan migrate --force