ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN docker-php-ext-install bcmath mysqli pdo_mysql > /dev/null

RUN pecl install redis > /dev/null && docker-php-ext-enable redis

RUN pecl install hprose > /dev/null && docker-php-ext-enable hprose
