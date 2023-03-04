FROM ubuntu:22.04

ARG PHP_VERSION=8.2
ARG NODE_VERSION=18

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

WORKDIR /var/www/html

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git sqlite3 libcap2-bin libpng-dev python2 supervisor \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN apt-get update && apt-get install -y software-properties-common \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y php$PHP_VERSION-cli php$PHP_VERSION-dev \
        # Required by Laravel
        php$PHP_VERSION-intl  \
        php$PHP_VERSION-readline  \
        php$PHP_VERSION-ldap  \
        php$PHP_VERSION-gd  \
        php$PHP_VERSION-curl \
        php$PHP_VERSION-mbstring \
        php$PHP_VERSION-imap \
        php$PHP_VERSION-xml \
        php$PHP_VERSION-zip \
        php$PHP_VERSION-soap \
        php$PHP_VERSION-msgpack \
        php$PHP_VERSION-igbinary \
        # Unused atm, but just in case
        php$PHP_VERSION-pgsql \
        php$PHP_VERSION-sqlite3 \
        php$PHP_VERSION-memcached \
        # Application-specific
        php$PHP_VERSION-mysql \
        php$PHP_VERSION-redis \
        php$PHP_VERSION-bcmath \
        php$PHP_VERSION-ds \
        php$PHP_VERSION-imagick \
        php$PHP_VERSION-ds \
        # Dev-only
        php$PHP_VERSION-xdebug \
        php$PHP_VERSION-pcov \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get update && apt-get install -y \
        nodejs \
        mysql-client \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

COPY php.ini /etc/php/$PHP_VERSION/cli/conf.d/99-docker.ini

ARG XDEBUG=true
ARG XDEBUG_PORT=9003
ARG XDEBUG_CONFIG=/etc/php/$PHP_VERSION/mods-available/xdebug.ini
RUN echo "xdebug.max_nesting_level=512" >> $XDEBUG_CONFIG
RUN if [ "${XDEBUG}" = 'true' ]; then \
    echo "xdebug.client_port=${XDEBUG_PORT}" >> $XDEBUG_CONFIG \
    && echo "xdebug.mode=develop,debug" >> $XDEBUG_CONFIG \
    && echo "xdebug.start_with_request=yes" >> $XDEBUG_CONFIG \
    && echo "xdebug.log_level=0" >> $XDEBUG_CONFIG \
    && echo "xdebug.client_host=host.docker.internal" >> $XDEBUG_CONFIG ;\
fi;
RUN if [ "${XDEBUG}" = 'false' ]; then \
    echo "xdebug.mode=off" >> $XDEBUG_CONFIG ;\
fi;

ARG PCOV_CONFIG=/etc/php/$PHP_VERSION/mods-available/pcov.ini
RUN echo "pcov.directory=./app" >> $PCOV_CONFIG \
    && echo "pcov.enabled=0" >> $PCOV_CONFIG

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
