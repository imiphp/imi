#!/bin/bash

set -ex

echo "init kafka"
(docker exec kafka1 /opt/kafka/bin/kafka-topics.sh --zookeeper zookeeper:2181 --create --partitions 3 --replication-factor 1 --topic queue-imi-1 \
&& docker exec kafka1 /opt/kafka/bin/kafka-topics.sh --zookeeper zookeeper:2181 --create --partitions 3 --replication-factor 1 --topic QueueTest1 \
&& docker exec kafka1 /opt/kafka/bin/kafka-topics.sh --zookeeper zookeeper:2181 --create --partitions 3 --replication-factor 1 --topic imi-kafka-queue-test) || echo "no kafka"