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

RUN \
    npm install && \
    npm run prod





#
# App image preparation stage
#

FROM php:8.1-fpm AS app

ARG APP_VERSION
ARG SPARK_USERNAME
ARG SPARK_PASSWORD

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Set up user:group to run PHP.
ENV USER=app
ENV GROUP=app

# Project source code root directory inside an image. Absolute path.
ENV APP_ROOT="/home/$USER/michman"

# Ensure the image is using UTC timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Copy a community script that eases installation of PHP extensions and Composer.
# It downloads all dependencies, properly configures all extensions and removes unnecesary packages afterwards.
# Docs: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get -y update && \
    # Add tini to use as an entrypoint, cron for scheduling; git and zip are for composer downloads.
    apt-get -y install tini cron git zip && \
    install-php-extensions @composer-2 && \
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
    # Cleanup after ourselves. \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/{apt,dpkg,cache,log} /tmp/* /var/tmp/* && \
    # Remove whatever is inside app root, just in case.
    rm -rf "$APP_ROOT/*"

# Create a non-root user and group that will be used for running the app.
RUN useradd -U -m $USER

# Prepare cron to run artisan scheduler.
# It won't be happening by default - we need to change the default CMD at deployment to something like:
# ["crond", "-f"]
# Don't forget to also change a user to root - cron can only be ran as root and scheduler is intended to run as root as well.
RUN echo "* * * * * php $APP_ROOT/artisan schedule:run > /dev/stdout 2>/dev/stderr" | crontab -u root -





#
# Application stage
#

# TODO: Add some basic container hardening, like: remove apk and curl, for example. Google something else.
# TODO: Consider using a hardening script, I had examples in my previos projects.

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
    composer check-platform-reqs \
        # Less junk in the console.
        --no-interaction \
        # Plugins and scripts are inherently unsafe to run as root and aren't needed here anyway.
        --no-plugins --no-scripts \
    && \
    composer install \
        --no-interaction --no-cache \
        --no-plugins --no-scripts \
        # Do not install dev-dependencies.
        --no-dev \
        # Don't generate autoload classes at this stage - this will be done below.
        --no-autoloader \
    && \
    rm auth.json

# Copy a customized entrypoint script that performs environement-dependent Laravel optimizations at runtime.
COPY --chown=$USER:$GROUP production/app/entrypoint.sh "$APP_ROOT/entrypoint"
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
COPY --chown=$USER:$GROUP production/app/verify-version.sh "$APP_ROOT/"
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

# Copy a slightly customized and slightly hardened production PHP configuration.
COPY production/app/php.ini-production "$PHP_INI_DIR/php.ini"

# Requested version of the app.
ARG APP_VERSION

# Run a script to verify the app version.
# It will fail with code 1 if the version on sources isn't the one requested in APP_VERSION arg,
# or if the app can't even start for some reason.
RUN $APP_ROOT/verify-version.sh

# Declare anonymous volumes for the writable directories requried to persist data and run containers in read-only mode.
VOLUME "$APP_ROOT/bootstrap" "$APP_ROOT/storage"

# This port may be used to access php-fpm.
EXPOSE 9000

# Use tini as an entrypoint, before running the official script from the source image.
ENTRYPOINT [ "/usr/bin/tini", "--", "/home/app/michman/entrypoint" ]

# By default - start by simply running php-fpm.
CMD [ "php-fpm" ]
