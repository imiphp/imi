ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN docker-php-ext-install pdo_mysql > /dev/null

RUN docker-php-ext-install mysqli > /dev/null && docker-php-ext-enable mysqli

RUN docker-php-ext-install redis > /dev/null && docker-php-ext-enable redis
