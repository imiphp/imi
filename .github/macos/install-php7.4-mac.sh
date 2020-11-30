#!/bin/bash
PHP_VERSION="7.4"
brew install php@$PHP_VERSION;
brew link --force --overwrite php@7.4

php -v
php -m
php-config

curl -o composer.phar https://getcomposer.org/composer-stable.phar && chmod +x composer.phar && sudo mv -f composer.phar /usr/local/bin/composer && composer -V;

# redis
echo "no" | pecl install -f redis;

PHP_INI_FILE=$(php -r "echo php_ini_loaded_file();")
if [[ $PHP_INI_FILE == "" ]]; then
    PHP_INI_FILE="/usr/local/etc/php/$(php -r "echo (double)PHP_VERSION;")/php.ini";
fi
echo "extension = redis.so" >> $PHP_INI_FILE
