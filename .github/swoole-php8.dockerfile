ARG SWOOLE_DOCKER_VERSION
FROM phpswoole/swoole:${SWOOLE_DOCKER_VERSION}

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN echo "opcache.jit=1205" >> "$PHP_INI_DIR/php.ini" && \
echo "opcache.jit_buffer_size=64M" >> "$PHP_INI_DIR/php.ini" && \
echo "opcache.enable_cli=1" >> "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install bcmath mysqli pdo_mysql > /dev/null

RUN pecl install redis > /dev/null && docker-php-ext-enable redis
