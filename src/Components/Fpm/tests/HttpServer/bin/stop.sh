#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

pidFile="$__DIR__/server.pid"

if [ -f $pidFile ];then
    cat $pidFile | while read LINE
    do
        ret=$(ps --no-heading ${LINE} | wc -l)
        if [[ "$ret" = "1" ]]; then
            echo "PID: ${LINE}"
            kill -9 ${LINE}
        fi
        break
    done
fi