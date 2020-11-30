ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN docker-php-ext-install pdo_mysql > /dev/null

RUN pecl install redis > /dev/null && \
    docker-php-ext-enable redis
