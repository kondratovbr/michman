#
# This Dockerfile build a Laravel-based Michman application to deploy and run in production.
# App source code and static assets are baked in.
# App public uploads are supposed to be mounted as volumes at the deployment stage.
# App non-public static assests are supposed to be mounted as volumes at the deployment stage
# as well as the actual .env file
#
# The following build args are required:
# APP_VERSION
#

FROM php:8.1-fpm

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Set up user:group to run PHP.
ENV USER=app
ENV GROUP=app

# Project source code root directory inside an image. Absolute path.
ENV APP_ROOT=/var/www

# Ensure the image is using UTC timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Copy a community script that eases installation of php extensions.
# It downloads all dependencies, properly configures all extensions and removes unnecesary packages afterwards.
# Docs: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

# Get Composer as well.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apt-get -y update && \
    # Add tini to use as an entrypoint, cron for scheduling and git for composer downloads.
    apt-get -y install tini cron git && \
    install-php-extensions \
        bcmath \
        ds \
        imagick \
        pcntl \
        redis \
        intl \
        gd \
        && \
    # Remove the script, we don't need it inside the image.
    rm /usr/local/bin/install-php-extensions && \
    # Cleanup after ourselves.
    apt-get clean && \
    apt-get autoremove -y && \
    rm -rf /var/lib/{apt,dpkg,cache,log} && \
    # Remove whatever is inside app root, just in case.
    rm -rf "$APP_ROOT/*"

# Prepare cron to run artisan scheduler.
# It won't be happening by default - you need to change the default CMD to something like:
# ["crond", "-f"]
# Don't forget to also change a user to root - cron can only be ran as root and scheduler is intended to run as root as well.
RUN echo "* * * * * php /var/www/artisan schedule:run > /dev/stdout 2>/dev/stderr" | crontab -u root -

# Copy a slightly customized and slightly hardened production configuration.
COPY production/app/php.ini-production "$PHP_INI_DIR/php.ini"



#
# Application stage
#

# TODO: Add some basic container hardening, like: remove apk and curl, for example. Google something else.
# TODO: Consider using a hardening script, I had examples in my previos projects.

# Copy composer.json and lock to install dependencies.
# We split dependencies installation and source copying into two stages so Docker can utilize caches sometimes
# to speed up image building.
COPY composer.json "$APP_ROOT/"
COPY composer.lock "$APP_ROOT/"

# Set the working directory to the root of the application to easily run things like artisan and composer.
# Also, composer will install dependencies into this directory.
WORKDIR "$APP_ROOT"

# See: https://laravel.com/docs/8.x/deployment#optimization
# Verify PHP platform dependencies and install app dependencies using composer.
# Autoload optimization is performed below, after copying sources.
RUN \
    composer check-platform-reqs \
        # Less junk in the console.
        --no-interaction \
        # Plugins and scripts are inherently unsafe to run as root and aren't needed here anyway.
        --no-plugins --no-scripts \
        && \
    composer install \
        --no-interaction \
        --no-plugins --no-scripts \
        # Do not install dev-dependencies.
        --no-dev \
        # Don't generate autoload classes at this stage - this will be done below.
        --no-autoloader

# Copy sources.
# Slashes are very important here! Sources must be copied in the existing folder,
# since we already have Composer packages in there.
COPY ./ "$APP_ROOT/"

# Copy a customized entrypoint script that performs environement-dependent Laravel optimizations at runtime.
COPY production/app/entrypoint.sh "$APP_ROOT/entrypoint"

# Set permissions and ownership for the entrypoint script.
RUN chown $USER:$GROUP "$APP_ROOT/entrypoint" && \
    chmod uga+x "$APP_ROOT/entrypoint"

# Copy a placehodler .env.build file that will be used during build.
COPY .env.build "$APP_ROOT/.env"

# Copy a tiny script that will verify the app version.
COPY production/app/verify-version.sh "$APP_ROOT/"
RUN chmod +x "$APP_ROOT/verify-version.sh"

# Laravel package discovery. It is usually run automatically through a Composer script,
# but we disabled those scripts for clarity and security.
# See composer.json for reference.
RUN composer dump-autoload \
    --optimize --classmap-authoritative \
    --no-interaction \
    --no-plugins --no-scripts \
    --no-dev && \
    php "$APP_ROOT/artisan" package:discover --ansi && \
    # Run all Laravel optimizations.
    php "$APP_ROOT/artisan" optimize && \
    # NOTE: config:cache isn't run because it requires all environment variables to be set, which isn't the case
    # at the build stage, since security credentials are passed at the deployment stage.
    # artisan config:cache is run by the custom entrypoint script.
    #php artisan config:cache && \
    # Routes also aren't cached because some of them depend on environment.
    #php "$APP_ROOT/artisan" route:cache && \
    php "$APP_ROOT/artisan" view:cache && \
    php "$APP_ROOT/artisan" event:cache && \
    # Change ownership of the whole app root folder
    chown -R $USER:$GROUP "$APP_ROOT"

# Requested version of the app.
ARG APP_VERSION

# Run a script to verify the app version.
# It will fail with code 1 if the version on sources isn't the one requested in APP_VERSION arg.
RUN $APP_ROOT/verify-version.sh

# Declare anonymous volumes for this directories to persist data and run containers in read-only mode.
VOLUME /var/www/bootstrap

# PHP will be run from an arbitrary user.
USER $USER:$GROUP

# This port may be used to access php-fpm.
EXPOSE 9000

# Use tini as an entrypoint, before running the official script from the source image.
ENTRYPOINT [ "/sbin/tini", "--", "/var/www/entrypoint" ]

# By default - start by simply running php-fpm. dockerize script can be set up in docker-compose files.
CMD [ "php-fpm" ]
