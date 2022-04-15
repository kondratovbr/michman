#!/bin/sh
set -e

# Custom PHP app container entrypoint script.
# Copied from https://github.com/docker-library/php/blob/14a53e7cbf88bd59af72d086418844a3e0b4160a/7.4/alpine3.12/fpm/docker-php-entrypoint

echo "Running PHP entrypoint script..."

# Cache app configs, which cannot be done at build-time since the actual .env file with secure credentials
# only becomes available at runtime.
php artisan config:cache
# Now cache routes, which isn't done at build-time since some of the routes depend on environment.
php artisan route:cache

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"
