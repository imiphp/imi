#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

php --ri xdebug > /dev/null
if [ $? -eq 0 ]; then
    paramsXdebug=""
else
    php -dzend_extension=xdebug --ri xdebug > /dev/null
    if [ $? -eq 0 ]; then
        paramsXdebug="-dzend_extension=xdebug"
    fi
fi

php $paramsXdebug -dxdebug.mode=coverage "$__DIR__/imi" swoole/stop
