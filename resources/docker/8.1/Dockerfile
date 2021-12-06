# WARNING! This doesn't work. For some reason. Maybe try the stock sail config, Dockerfile and docker-compose dile for PHP 8.1.



FROM ubuntu:21.04

ARG WWWGROUP
ARG NODE_VERSION=17

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && apt-key adv --homedir ~/.gnupg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys E5267A6C \
    && apt-key adv --homedir ~/.gnupg --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C300EE8C \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu hirsute main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y \
        php8.1-cli php8.1-dev \
        php8.0-pgsql php8.0-sqlite3 php8.0-gd \
        php8.0-curl php8.0-memcached \
        php8.0-imap php8.0-mysql php8.0-mbstring \
        php8.0-xml php8.0-zip php8.0-bcmath php8.0-soap \
        php8.0-intl php8.0-readline \
        php8.0-msgpack php8.0-igbinary php8.0-ldap \
        php8.0-swoole \
        php8.0-redis \
        php8.0-imagick \
        php8.0-xdebug \
        php8.0-pcov \
        php8.0-ds \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get install -y nodejs \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y yarn \
    && apt-get install -y mysql-client \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ARG XDEBUG=true
ARG XDEBUG_PORT=9003
ARG XDEBUG_CONFIG=/etc/php/8.1/mods-available/xdebug.ini
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

ARG PCOV_CONFIG=/etc/php/8.1/mods-available/pcov.ini
RUN echo "pcov.directory=./app" >> $PCOV_CONFIG \
    && echo "pcov.enabled=0" >> $PCOV_CONFIG

RUN echo "max_execution_time = 600" >> /etc/php/8.1/cli/php.ini

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.1

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.1/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
