FROM ubuntu:21.04

ARG NODE_VERSION=17

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

WORKDIR /var/www/html

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && apt-get install -y gnupg gosu curl ca-certificates zip unzip git sqlite3 libcap2-bin libpng-dev python2 \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN apt-get update && apt-get install -y software-properties-common \
    && LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y php8.1-cli php8.1-dev \
        # Required by Laravel
        php8.1-intl php8.1-readline php8.1-ldap php8.1-gd php8.1-curl php8.1-mbstring php8.1-imap \
        php8.1-xml php8.1-zip php8.1-soap php8.1-msgpack php8.1-igbinary \
        # Unused, but just in case
        php8.1-pgsql php8.1-sqlite3 php8.1-memcached \
        # Application-specific
        php8.1-mysql php8.1-redis php8.1-bcmath \
        # Dev-only
        php8.1-xdebug php8.1-pcov \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \
    && apt-get update && apt-get install -y \
        nodejs \
        mysql-client \
    && apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

COPY php.ini /etc/php/8.1/cli/conf.d/99-docker.ini

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
