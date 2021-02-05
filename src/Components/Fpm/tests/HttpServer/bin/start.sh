#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

if [[ "$1" = "-d" ]]; then
    nohup /usr/bin/env php -d request_order=CGP -t "$__DIR__/../../Web/public" -S 127.0.0.1:13000 > "$__DIR__/../logs/cli.log" 2>&1 & echo $! > "$__DIR__/server.pid"
else
    /usr/bin/env php -d request_order=CGP -t "$__DIR__/../../Web/public" -S 127.0.0.1:13000
fi

