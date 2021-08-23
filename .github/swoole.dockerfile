ARG SWOOLE_DOCKER_VERSION

FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

ARG PHP_JIT="0"

RUN set -eux \
    && apt-get update && apt-get -y install procps libpq-dev unzip \
    && docker-php-ext-install bcmath mysqli pdo_mysql pdo_pgsql pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && curl https://github.com/swoole/ext-postgresql/archive/refs/tags/v4.7.0.tar.gz -o ext-postgresql.tar.gz && tar -xzvf ext-postgresql.tar.gz && cd ext-postgresql && phpize && ./configure && make -j$(sysctl -n hw.ncpu) && make install && docker-php-ext-enable swoole_postgresql && php --ri swoole_postgresql\
    && ( \
        [ $(php -r "echo PHP_VERSION_ID < 80000 ? 1 : 0;") = "0" ] \
        || (pecl install hprose && docker-php-ext-enable hprose) \
    ) \
    && ( \
        [ "${PHP_JIT}" = "0" ] \
        || ( \
            echo "zend_extension=opcache.so" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
            && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
            && echo "opcache.jit_buffer_size=64M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
            && echo ">> enable opcache" \
        ) \
    )
