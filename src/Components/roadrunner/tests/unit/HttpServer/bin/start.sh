#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

if [ "$IMI_CODE_COVERAGE" = 1 ]; then
    php --ri xdebug > /dev/null
    if [ $? = 0 ]; then
        paramsXdebug=""
    else
        php -dzend_extension=xdebug --ri xdebug > /dev/null 2>&1
        if [ $? = 0 ]; then
            paramsXdebug="-dzend_extension=xdebug"
        fi
    fi
    paramsXdebug="$paramsXdebug -dswoole.enable_fiber_mock -dxdebug.mode=coverage"
fi

if [[ "$1" = "-d" ]]; then
    nohup /usr/bin/env php $paramsXdebug "$__DIR__/cli" rr/start --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__/../" 2>&1 &
else
    /usr/bin/env php $paramsXdebug "$__DIR__/cli" rr/start --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__/../"
fi
