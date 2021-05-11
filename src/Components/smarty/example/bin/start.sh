#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

nohup $__DIR__/imi server/start > "$__DIR__/../logs/cli.log" 2>&1 & echo $! > "$__DIR__/server.pid"
