#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop-workerman.sh

nohup /usr/bin/env php -dzend_extension=xdebug -dxdebug.mode=coverage "$__DIR__/workerman" workerman/start $* > "$__DIR__/../logs/cli.log" 2>&1 &
