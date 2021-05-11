#!/bin/bash

if [[ `php -r "echo version_compare(PHP_VERSION, '8.0', '<') ? 1 : 0;"` == "1" ]]; then
    pecl install hprose > /dev/null && docker-php-ext-enable hprose;
fi
