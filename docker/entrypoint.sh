#!/bin/bash
set -e

echo "Starting RoL d20 container initialization..."

# Set directory permissions for Laravel runtime
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Wait for MySQL connection if DB_CONNECTION is mysql
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL at $DB_HOST:${DB_PORT:-3306}..."
    until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }"; do
        sleep 2
    done
    echo "MySQL database is available!"
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Sync static YAML rules to ref_* tables
echo "Synchronizing static rules..."
php artisan rules:sync --force

# Build search index
echo "Building unified search index..."
php artisan search:reindex

echo "RoL d20 ready! Starting Apache web server..."
exec apache2-foreground
