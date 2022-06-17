#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

rm -rf ${__DIR__}/../.runtime/*runtime

${__DIR__}/stop-server.sh $1

nohup $__DIR__/imi-$1 $1/start > "$__DIR__/../.runtime/logs/$1.log" 2>&1 & echo $! > "$__DIR__/server.pid"
