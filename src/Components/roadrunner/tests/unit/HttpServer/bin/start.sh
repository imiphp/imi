#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

if [[ "$1" = "-d" ]]; then
    nohup /usr/bin/env php "$__DIR__/cli" rr/start --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__/../" 2>&1 &
else
    /usr/bin/env php "$__DIR__/cli" rr/start --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__/../"
fi
