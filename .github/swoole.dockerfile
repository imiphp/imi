ARG SWOOLE_DOCKER_VERSION

FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

ARG POSTGRESQL_VERSION=""

COPY script/ /tmp/script

RUN set -eux \
    && apt-get update && apt-get -y install procps libpq-dev unzip git libevent-dev libssl-dev libicu-dev \
    && docker-php-ext-install -j$(nproc) bcmath mysqli pdo_mysql pdo_pgsql pcntl sockets intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && pecl install inotify \
    && docker-php-ext-enable inotify \
    && pecl install event \
    && docker-php-ext-enable --ini-name z-event.ini event \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && bash /tmp/script/swoole_postgresql.sh ${POSTGRESQL_VERSION} \
    && bash /tmp/script/hprose.sh \
    && echo "zend_extension=opcache.so" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
