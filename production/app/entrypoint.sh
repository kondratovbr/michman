#!/bin/sh
set -e

# Custom PHP Laravel app container entrypoint script

echo "Running Laravel entrypoint script..."

# Run all available Laravel config caching and optimizations.
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
