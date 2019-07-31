#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

${__DIR__}/stop.sh

nohup /usr/bin/env php "$__DIR__/imi" server/start > /dev/null 2>&1 &
