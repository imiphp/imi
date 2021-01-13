#!/bin/bash

swooleVersion="4.6.1"

wget -O swoole.tar.gz https://github.com/swoole/swoole-src/archive/v$swooleVersion.tar.gz

swooleDir="swoole-src-${swooleVersion}"

tar -xzf swoole.tar.gz
rm swoole.tar.gz

cd $swooleDir

phpize && ./configure --enable-openssl --enable-http2 --enable-mysqlnd --with-openssl-dir=$(brew --prefix openssl) && make -j && make install

PHP_INI_FILE=$(php -r "echo php_ini_loaded_file();")
if [[ $PHP_INI_FILE == "" ]]; then
    PHP_INI_FILE="/usr/local/etc/php/$(php -r "echo (double)PHP_VERSION;")/php.ini";
fi
echo "extension = swoole.so" >> $PHP_INI_FILE

cd ../

rm -rf $swooleDir

php --ri swoole
