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
    paramsXdebug="$paramsXdebug -dxdebug.mode=coverage"
fi

rm -rf "$__DIR__/../../Web/.runtime/fpm"

if [[ "$1" = "-d" ]]; then
    nohup /usr/bin/env php $paramsXdebug -d request_order=CGP -t "$__DIR__/../../Web/public" -S 127.0.0.1:13000 > "$__DIR__/../logs/cli.log" 2>&1 & echo $! > "$__DIR__/server.pid"
else
    /usr/bin/env php $paramsXdebug -d request_order=CGP -t "$__DIR__/../../Web/public" -S 127.0.0.1:13000
fi
