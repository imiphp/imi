ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN apt-get update && apt-get -y install procps

RUN docker-php-ext-install bcmath mysqli pdo_mysql pcntl > /dev/null

RUN pecl install redis > /dev/null && docker-php-ext-enable redis

COPY install-env.sh install-env.sh

RUN ["/bin/bash", "install-env.sh"] 
