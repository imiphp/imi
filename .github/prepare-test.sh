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
&& docker exec $containerName composer update \
&& docker exec $containerName bash -c "cd split-repository && composer update" \
&& docker exec kafka1 /opt/kafka/bin/kafka-topics.sh --zookeeper zookeeper:2181 --create --partitions 3 --replication-factor 1 --topic queue-imi-1 \
&& docker exec kafka1 /opt/kafka/bin/kafka-topics.sh --zookeeper zookeeper:2181 --create --partitions 3 --replication-factor 1 --topic QueueTest1
;

if [[ $disableDb == "" ]]; then
  n=0
  until [ $n -ge 5 ]
  do
    docker exec $containerName bash tests/db/install-db.sh && break
    n=$[$n+1]
    sleep 1
  done
fi
