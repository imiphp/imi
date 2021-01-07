#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__

containerName=$1

docker-compose up -d $containerName \
&& docker exec $containerName php -v \
&& docker exec $containerName php -m \
&& docker exec $containerName php --ri swoole \
&& docker exec $containerName composer -V \
&& docker ps -a \
&& docker exec $containerName composer update \
&& docker exec $containerName bash -c "cd tests && composer update";

n=0
until [ $n -ge 5 ]
do
  docker exec $containerName bash tests/db/install-db.sh && break
  n=$[$n+1]
  sleep 1
done
