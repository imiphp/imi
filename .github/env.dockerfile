
ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN set -eux \
    && docker-php-ext-install bcmath mysqli pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && ( \
        [ $(php -r "echo version_compare(PHP_VERSION, '8.0', '<') ? 1 : 0;") = "1" ] \
        && pecl install hprose \
        && docker-php-ext-enable hprose \
    )
