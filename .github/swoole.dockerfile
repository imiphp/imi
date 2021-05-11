ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN docker-php-ext-install bcmath mysqli pdo_mysql > /dev/null

RUN pecl install redis > /dev/null && docker-php-ext-enable redis

COPY install-env.sh install-env.sh

RUN ["/bin/bash", "-c", "install-env.sh"] 
