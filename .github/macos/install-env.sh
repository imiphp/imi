#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

#brew install php@$PHP_VERSION && brew link --force --overwrite php@7.4
#
#php -v
#php -m
#php-config

brew install mysql redis && brew services start mysql && brew services start redis &

#$__DIR__/install-php.sh &
#
#$__DIR__/install-swoole.sh 4.7.0 &

wait
