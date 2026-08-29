#!/bin/sh
set -e

echo "🚀 Starting Rudood Platform Container..."

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# If DB is configured, run migrations automatically
if [ -n "$DB_HOST" ] && [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "⏳ Waiting for PostgreSQL ($DB_HOST:$DB_PORT) to be ready..."
    until pg_isready -h "$DB_HOST" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-rudood_user}" -d "${DB_DATABASE:-rudood_db}" > /dev/null 2>&1; do
        sleep 2
    done
    echo "✅ PostgreSQL is ready. Running database migrations..."
    php artisan migrate --force --no-interaction || true
fi

# Execute CMD (supervisord)
exec "$@"
