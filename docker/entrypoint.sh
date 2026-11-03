#!/bin/bash
set -e

# Set Apache listening port to $PORT (Render provides $PORT, defaults to 10000)
PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Configure Apache Directory permissions for mod_rewrite
cat << 'EOF' > /etc/apache2/conf-available/laravel.conf
<Directory /var/www/html/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
EOF
a2enconf laravel

# 1. Guarantee .env file exists in container
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# 2. Inject safe standalone production defaults if not set by environment
grep -q '^SESSION_DRIVER=' /var/www/html/.env && sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=cookie/' /var/www/html/.env || echo 'SESSION_DRIVER=cookie' >> /var/www/html/.env
grep -q '^CACHE_STORE=' /var/www/html/.env && sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' /var/www/html/.env || echo 'CACHE_STORE=file' >> /var/www/html/.env
grep -q '^QUEUE_CONNECTION=' /var/www/html/.env && sed -i 's/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=sync/' /var/www/html/.env || echo 'QUEUE_CONNECTION=sync' >> /var/www/html/.env

# Default to SQLite if no external DB host is set
if [ -z "$DB_HOST" ]; then
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' /var/www/html/.env
    sed -i 's/^DB_DATABASE=.*/DB_DATABASE=\/var\/www\/html\/database\/database.sqlite/' /var/www/html/.env
fi

# 3. Ensure SQLite database file exists and is writable
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/database
chmod -R 775 /var/www/html/database

# 4. Ensure APP_KEY exists in .env
if ! grep -q '^APP_KEY=base64:' /var/www/html/.env; then
    php artisan key:generate --force
fi

# 5. Ensure storage permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Run database migrations safely
php artisan migrate --force || true

# 7. Create storage link
php artisan storage:link --force || true

# 8. Clear and cache config/views
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
