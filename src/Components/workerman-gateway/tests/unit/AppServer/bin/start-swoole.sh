#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop-swoole.sh

nohup /usr/bin/env php "$__DIR__/swoole" server/start > "$__DIR__/../logs/cli.log" 2>&1 &
