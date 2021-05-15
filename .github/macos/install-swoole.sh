#!/bin/bash

swooleVersion=$1
# swooleVersion="4.6.7"
#

swooleDir="/tmp/swoole-src-${swooleVersion}"

mkdir -p $swooleDir
cd /tmp
wget -O swoole.tar.gz https://github.com/swoole/swoole-src/archive/v$swooleVersion.tar.gz
tar -xzf swoole.tar.gz
cd $swooleDir
rm swoole.tar.gz

phpize
./configure --enable-openssl \
    --enable-http2 \
    --enable-mysqlnd \
    --with-openssl-dir=$(brew --prefix openssl)
make -j
make install

PHP_INI_FILE="$(php-config --ini-dir)/swoole.sh"
echo "extension = swoole.so" >> $PHP_INI_FILE

php --ri swoole
