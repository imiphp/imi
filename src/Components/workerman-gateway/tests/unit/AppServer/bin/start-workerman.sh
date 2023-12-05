#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop-workerman.sh

php --ri xdebug > /dev/null
if [ $? -eq 0 ]; then
    paramsXdebug=""
else
    php -dzend_extension=xdebug --ri xdebug > /dev/null 2&>1
    if [ $? -eq 0 ]; then
        paramsXdebug="-dzend_extension=xdebug -dswoole.enable_fiber_mock -dxdebug.mode=coverage"
    fi
fi

nohup /usr/bin/env php $paramsXdebug "$__DIR__/workerman" workerman/start $* > "$__DIR__/../logs/cli.log" 2>&1 &
