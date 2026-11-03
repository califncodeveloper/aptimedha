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

# Ensure permissions for storage and bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate APP_KEY if not provided
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run database setup if SQLite is used
if [ "${DB_CONNECTION}" = "sqlite" ]; then
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
    chown www-data:www-data /var/www/html/database/database.sqlite
    php artisan migrate --force || true
fi

# Cache configurations for production performance
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
