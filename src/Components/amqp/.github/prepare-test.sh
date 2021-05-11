#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__

containerName=$1
disableDb=$2

docker-compose up -d $containerName \
&& docker exec $containerName php -v \
&& docker exec $containerName php -m \
&& docker exec $containerName php --ri swoole \
&& docker exec $containerName composer -V \
&& docker ps -a \
&& docker exec $containerName composer update
