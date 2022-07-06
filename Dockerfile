#
# This Dockerfile build a Laravel-based Michman application to deploy and run in production.
# App source code and static assets are baked in.
# App public uploads are supposed to be mounted as volumes at the deployment stage.
# App non-public static assests are supposed to be mounted as volumes at the deployment stage
# as well as the actual .env file
#
# The following build args are required:
# APP_VERSION
# SPARK_USERNAME
# SPARK_PASSWORD
#

#
# Static assets building stage
#
FROM node:17 AS static

RUN mkdir /root/michman

WORKDIR /root/michman

COPY --chown=root:root . /root/michman/

RUN mv .env.build .env

RUN \
    npm install && \
    npm run prod





#
# App image preparation stage
#
FROM ubuntu:22.04 as app

ARG PHP_VERSION=8.1

ARG APP_VERSION
ARG SPARK_USERNAME
ARG SPARK_PASSWORD

ENV DEBIAN_FRONTEND noninteractive
ENV PHP_VERSION ${PHP_VERSION}
ENV TZ=UTC
# Set up user:group to run PHP.
ENV USER=app
ENV GROUP=app

ENV PHP_INI_DIR=/usr/local/etc/php

# Project source code root directory inside an image. Absolute path.
ENV APP_ROOT="/home/$USER/michman"

# Ensure the image is using UTC timezone.
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Copy a community script that eases installation of PHP extensions and Composer.
# It downloads all dependencies, properly configures all extensions and removes unnecesary packages afterwards.
# Docs: https://github.com/mlocati/docker-php-extension-installer
#COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get -y update && \
    # Add tini to use as an entrypoint, cron for scheduling; git and zip are for composer downloads. \
    # software-properties-common comes with add-apt-repository command we're using.
    apt-get -y install tini cron git zip && \
    # Add apt packages required only for building.
    apt-get -y install software-properties-common && \
    # Install PHP and extensions from a ppa:ondrej/php repo.
    LC_ALL=C.UTF-8 add-apt-repository -y -u ppa:ondrej/php && \
    apt-get -y update && \
    apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-fpm \
        # Required by Laravel
        php${PHP_VERSION}-intl  \
        php${PHP_VERSION}-readline \
        php${PHP_VERSION}-ldap \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-mbstring  \
        php${PHP_VERSION}-imap \
        php${PHP_VERSION}-xml  \
        php${PHP_VERSION}-zip  \
        php${PHP_VERSION}-soap \
        php${PHP_VERSION}-msgpack  \
        php${PHP_VERSION}-igbinary \
        # Application-specific
        php${PHP_VERSION}-mysqli \
        php${PHP_VERSION}-sqlite3 \
        php${PHP_VERSION}-redis \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-ds \
        php${PHP_VERSION}-imagick \
    && \
    # Install Composer using the official script.
    php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    # Remove apt packages we don't need at runtime.
    apt-get -y remove software-properties-common && \
    # Remove the script, we don't need it inside the image.
    #rm /usr/local/bin/install-php-extensions && \
    # Cleanup after ourselves.
    apt-get -y autoremove && apt-get -y clean && rm -rf /var/lib/{apt,dpkg,cache,log} /tmp/* /var/tmp/* && \
    # Remove whatever is inside app root, just in case.
    rm -rf "$APP_ROOT/*"

# Copy a slightly customized and slightly hardened production PHP configuration.
COPY deployment/app/php-fpm.ini "$PHP_INI_DIR/php-fpm.ini"
COPY deployment/app/php.ini "$PHP_INI_DIR/php.ini"

# Symlink the currently configured PHP version to php-fpm.
RUN ln -s /usr/sbin/php-fpm${PHP_VERSION} /usr/sbin/php-fpm

# Create a non-root user and group that will be used for running the app.
RUN useradd -U -m $USER

# Prepare cron to run artisan scheduler.
# It won't be happening by default - we need to change the default CMD at deployment to something like:
# ["crond", "-f"]
# Don't forget to also change a user to root - cron can only be ran as root and scheduler is intended to run as root as well.
RUN echo "* * * * * php $APP_ROOT/artisan schedule:run > /dev/stdout 2>/dev/stderr" | crontab -u root -



# TODO: Add some basic container hardening, like: remove apk and curl, for example. Google something else.
# TODO: Consider using a hardening script, I had examples in my previos projects.

#
# Application stage
#

# Composer needs to be run from the app user,
# the app itself will be run from an arbitrary user as well for security reasons.
USER $USER:$GROUP

# Set the working directory to the root of the application to easily run things like artisan and composer.
# Also, composer will install dependencies into this directory.
RUN mkdir "$APP_ROOT"
WORKDIR "$APP_ROOT"

# Copy composer.json and lock to install dependencies.
# We split dependencies installation and source copying into two stages so Docker can utilize caches sometimes
# to speed up image building.
COPY --chown=$USER:$GROUP composer.json "$APP_ROOT/"
COPY --chown=$USER:$GROUP composer.lock "$APP_ROOT/"

# See: https://laravel.com/docs/8.x/deployment#optimization
# Verify PHP platform dependencies and install app dependencies using composer.
# Autoload optimization is performed below, after copying sources.
RUN \
    # This trick speeds up Composer greatly for some reason.
    composer config --global repo.packagist composer https://repo.packagist.org && \
    # This project uses one package from a protected repo - Laravel Spark,
    # so we have to give Composer credentials for it.
    # Credentials must be provided as a build arg in docker build command.
    echo "{\"http-basic\": {\"spark.laravel.com\": {\"username\": \"$SPARK_USERNAME\",\"password\": \"$SPARK_PASSWORD\"}}}" > auth.json && \
    composer install \
        --no-interaction --no-cache \
        --no-plugins --no-scripts \
        # Do not install dev-dependencies.
        --no-dev \
        # Don't generate autoload classes at this stage - this will be done below.
        --no-autoloader \
    && \
    composer check-platform-reqs \
        # Less junk in the console.
        --no-interaction \
        # Plugins and scripts are inherently unsafe to run as root and aren't needed here anyway.
        --no-plugins --no-scripts \
    && \
    rm auth.json

# Copy a customized entrypoint script that performs environement-dependent Laravel optimizations at runtime.
COPY --chown=$USER:$GROUP deployment/app/entrypoint.sh "$APP_ROOT/entrypoint"
RUN chmod uga+x "$APP_ROOT/entrypoint"

# Copy sources.
# Slashes are very important here! Sources must be copied in the existing folder,
# since we already have Composer packages in there.
COPY --chown=$USER:$GROUP ./ "$APP_ROOT/"

# Copy compiled static files.
COPY --from=static --chown=$USER:$GROUP /root/michman/public "$APP_ROOT/public"

# Use a placehodler .env file for running artisan during build.
RUN mv "$APP_ROOT/.env.build" "$APP_ROOT/.env"

# Copy a tiny script that will verify the app version.
COPY --chown=$USER:$GROUP deployment/app/verify-version.sh "$APP_ROOT/"
RUN chmod +x "$APP_ROOT/verify-version.sh"

# This dummy file needs to exist, otherwise post-autoload composer scripts fail.
RUN touch ./database/database.sqlite

# Composer autoload and Laravel package discovery.
# NOTE: Laravel optimizations and caching aren't performed here,
# because some of them depend on the environment and
# require actual secure credentials that are only provided at deployment stage.
RUN composer dump-autoload \
    --optimize --classmap-authoritative \
    --no-interaction \
    --no-plugins --no-scripts \
    --no-dev

# Requested version of the app.
ARG APP_VERSION

# Run a script to verify the app version.
# It will fail with code 1 if the version on sources isn't the one requested in APP_VERSION arg,
# or if the app can't even start for some reason.
RUN $APP_ROOT/verify-version.sh

# Declare anonymous volumes for the writable directories requried to persist data and run containers in read-only mode.
VOLUME "$APP_ROOT/bootstrap" "$APP_ROOT/storage" "/tmp"

# This port may be used to access php-fpm.
EXPOSE 9000

# Use tini as an entrypoint, before running the official script from the source image.
ENTRYPOINT [ "/usr/bin/tini", "--", "/home/app/michman/entrypoint" ]

# By default - start by simply running php-fpm.
CMD [ "php-fpm", "--nodaemonize", "--fpm-config", "/usr/local/etc/php/php-fpm.ini" ]
