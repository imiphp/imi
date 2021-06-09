#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop-server.sh

nohup $__DIR__/imi swoole/start > "$__DIR__/../.runtime/logs/cli.log" 2>&1 & echo $! > "$__DIR__/server.pid"
