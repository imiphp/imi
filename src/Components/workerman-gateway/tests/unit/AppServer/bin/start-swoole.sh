#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop-swoole.sh

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

nohup /usr/bin/env php $paramsXdebug "$__DIR__/swoole" swoole/start > "$__DIR__/../logs/cli.log" 2>&1 &
