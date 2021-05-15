#!/bin/bash

swooleVersion=$1

swooleDir="/tmp/swoole-src-${swooleVersion}"

mkdir -p $swooleDir
wget -O swoole.tar.gz https://github.com/swoole/swoole-src/archive/v$swooleVersion.tar.gz
tar -xzf swoole.tar.gz -C ${swooleDir} --strip-components=1
cd $swooleDir

phpize
./configure --enable-openssl \
    --enable-http2 \
    --enable-mysqlnd \
    --with-openssl-dir=$(brew --prefix openssl)
make -j
make install

PHP_INI_FILE="$(php-config --ini-dir)/swoole.ini"
echo $PHP_INI_FILE
echo "extension = swoole.so" >> $PHP_INI_FILE

php --ri swoole
