#!/bin/sh
set -eu

until php artisan migrate:status --no-interaction >/dev/null 2>&1; do
    echo "Waiting for database..."
    sleep 2
done

php artisan migrate --force --no-interaction
php artisan storage:link --force >/dev/null 2>&1 || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
