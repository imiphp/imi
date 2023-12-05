#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

php --ri xdebug > /dev/null
if [ $? -eq 0 ]; then
    paramsXdebug=""
else
    php -dzend_extension=xdebug --ri xdebug > /dev/null 2&>1
    if [ $? -eq 0 ]; then
        paramsXdebug="-dzend_extension=xdebug -dswoole.enable_fiber_mock -dxdebug.mode=coverage"
    fi
fi

php $paramsXdebug "$__DIR__/imi" workerman/stop
