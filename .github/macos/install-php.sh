#!/bin/bash

curl -o composer.phar https://getcomposer.org/composer-stable.phar && chmod +x composer.phar && sudo mv -f composer.phar /usr/local/bin/composer && composer -V;

# redis
echo "no" | pecl install -f redis;
