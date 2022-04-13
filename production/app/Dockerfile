FROM PHP:8.1-fpm

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Ensure the image is using UTC timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#WORKDIR /var/www/html

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    bcmath \
    ds \
    imagick \
    pcntl \
    redis \
    intl

# Use the built-in production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
