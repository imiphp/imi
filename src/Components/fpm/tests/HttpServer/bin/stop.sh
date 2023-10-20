#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

pidFile="$__DIR__/server.pid"

if [ -f $pidFile ];then
    kill -15 $(cat $pidFile)
fi