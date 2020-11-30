#!/bin/bash
PHP_VERSION="7.4"
brew install php@$PHP_VERSION;
brew link --force --overwrite php@7.4

php -v
php -m
php-config

curl -o composer.phar https://getcomposer.org/composer-stable.phar && chmod +x composer.phar && sudo mv -f composer.phar /usr/local/bin/composer && composer -V;
