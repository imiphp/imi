<?php
require __DIR__ . '/Args.php';

Args::init();
$nproc = Args::get('nproc');
$versionName = Args::get('version-name');

define('OTHER_SWOOLE_VERSION', 'v4.4.0-beta');

if(version_compare(PHP_VERSION, '7.1', '>='))
{
    $version = OTHER_SWOOLE_VERSION;

    `wget https://github.com/swoole/swoole-src/archive/{$version}.tar.gz -O swoole.tar.gz && mkdir -p swoole && tar -xf swoole.tar.gz -C swoole --strip-components=1 && rm swoole.tar.gz && cd swoole && phpize && ./configure && make -j{$nproc} && make install && cd -`;

    `echo "extension = swoole.so" >> ~/.phpenv/versions/{$versionName}/etc/php.ini`;
}
else
{
    echo 'Skip Swoole', PHP_EOL;
}
