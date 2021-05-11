ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN apt update && apt install -y unzip git

RUN docker-php-ext-install bcmath mysqli pdo_mysql > /dev/null

RUN pecl install redis > /dev/null && docker-php-ext-enable redis
