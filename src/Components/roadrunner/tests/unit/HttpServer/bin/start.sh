#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

php --ri xdebug > /dev/null
if [ $? -eq 0 ]; then
    paramsXdebug=""
else
    php -dzend_extension=xdebug --ri xdebug > /dev/null 2&>1
    if [ $? -eq 0 ]; then
        paramsXdebug="-dzend_extension=xdebug"
    fi
fi

if [[ "$1" = "-d" ]]; then
    nohup /usr/bin/env php $paramsXdebug -dxdebug.mode=coverage "$__DIR__/cli" rr/start --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__/../" 2>&1 &
else
    /usr/bin/env php $paramsXdebug -dxdebug.mode=coverage "$__DIR__/cli" rr/start --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__/../"
fi
