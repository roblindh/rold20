#!/bin/bash
set -e

echo "Starting RoL d20 container initialization..."

# Ensure Apache runtime directories exist
mkdir -p /var/run/apache2 /var/lock/apache2 /var/log/apache2

# Set directory permissions for Laravel runtime (don't fail on NAS permission limits)
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache || true

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Wait for MySQL connection if DB_CONNECTION is mysql
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL at ${DB_HOST:-db}:${DB_PORT:-3306}..."
    until php -r "try { new PDO('mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . (getenv('DB_DATABASE') ?: 'rold20'), getenv('DB_USERNAME') ?: 'rold20_user', getenv('DB_PASSWORD') ?: 'rold20_pass'); exit(0); } catch (Exception \$e) { exit(1); }"; do
        sleep 2
    done
    echo "MySQL database is available!"
fi

# Run Laravel setup if artisan exists
if [ -f "/var/www/html/artisan" ]; then
    echo "Running database migrations..."
    php /var/www/html/artisan migrate --force || true

    echo "Synchronizing static rules..."
    php /var/www/html/artisan rules:sync --force || true

    echo "Building unified search index..."
    php /var/www/html/artisan search:reindex || true
fi

echo "RoL d20 ready! Starting Apache web server..."
. /etc/apache2/envvars
exec /usr/sbin/apache2 -D FOREGROUND
