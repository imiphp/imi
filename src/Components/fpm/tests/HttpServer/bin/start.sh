#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

php --ri xdebug > /dev/null
if [ $? -eq 0 ]; then
    paramsXdebug=""
else
    php -dzend_extension=xdebug --ri xdebug > /dev/null
    if [ $? -eq 0 ]; then
        paramsXdebug="-dzend_extension=xdebug"
    fi
fi

if [[ "$1" = "-d" ]]; then
    nohup /usr/bin/env php $paramsXdebug -dxdebug.mode=coverage -d request_order=CGP -t "$__DIR__/../../Web/public" -S 127.0.0.1:13000 > "$__DIR__/../logs/cli.log" 2>&1 & echo $! > "$__DIR__/server.pid"
else
    /usr/bin/env php $paramsXdebug -dxdebug.mode=coverage -d request_order=CGP -t "$__DIR__/../../Web/public" -S 127.0.0.1:13000
fi
