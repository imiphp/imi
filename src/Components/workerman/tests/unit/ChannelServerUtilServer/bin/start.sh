#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

nohup /usr/bin/env php -dzend_extension=xdebug -dxdebug.mode=coverage "$__DIR__/imi" workerman/start > "$__DIR__/../logs/cli.log" 2>&1 &
