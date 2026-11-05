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
APP_HOST_URL="${RENDER_EXTERNAL_URL:-https://aptimedha.onrender.com}"
sed -i "s|^APP_URL=.*|APP_URL=${APP_HOST_URL}|" /var/www/html/.env

# SEC-02: Default to persistent database session driver; never force ephemeral cookie driver
SESSION_DRIVER_VAL="${SESSION_DRIVER:-database}"
if grep -q '^SESSION_DRIVER=' /var/www/html/.env; then
    sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=${SESSION_DRIVER_VAL}|" /var/www/html/.env
else
    echo "SESSION_DRIVER=${SESSION_DRIVER_VAL}" >> /var/www/html/.env
fi

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

# 4. SEC-03: Enforce persistent production APP_KEY; never generate dynamic disposable keys in production
CONTAINER_APP_ENV="${APP_ENV:-production}"
if [ "$CONTAINER_APP_ENV" = "production" ]; then
    # Inject persistent APP_KEY from environment if passed (e.g. from Render Dashboard)
    if [ -n "$APP_KEY" ]; then
        if grep -q '^APP_KEY=' /var/www/html/.env; then
            sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" /var/www/html/.env
        else
            echo "APP_KEY=${APP_KEY}" >> /var/www/html/.env
        fi
    fi

    # Strictly verify that a valid non-empty APP_KEY is present
    CURRENT_APP_KEY=$(grep '^APP_KEY=' /var/www/html/.env | cut -d '=' -f2-)
    if [ -z "$CURRENT_APP_KEY" ] || [ "$CURRENT_APP_KEY" = "base64:" ] || [ "$CURRENT_APP_KEY" = "null" ]; then
        echo "==========================================================================" >&2
        echo "CRITICAL STARTUP ERROR: Production APP_KEY is not configured!" >&2
        echo "A persistent APP_KEY must be provided via the Render Dashboard environment." >&2
        echo "Startup aborted to prevent encryption key regeneration." >&2
        echo "==========================================================================" >&2
        exit 1
    fi
else
    # In non-production/local environments, generate key if missing
    if ! grep -q '^APP_KEY=base64:' /var/www/html/.env; then
        php artisan key:generate --force
    fi
fi

# 5. Ensure storage permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 6. Run database migrations safely (SEC-01: db:seed removed from normal production startup)
php artisan migrate --force || true

# 7. Create storage link
php artisan storage:link --force || true

# 8. Clear all stale caches and warm up production config/views
php artisan cache:clear || true
php artisan view:clear || true
php artisan config:clear || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
