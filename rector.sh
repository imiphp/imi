#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__ && echo "core" && ./vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/access-control && echo "access-control" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/amqp && echo "amqp" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/apidoc && echo "apidoc" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/fpm && echo "fpm" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/grpc && echo "grpc" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/jwt && echo "jwt" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/kafka && echo "kafka" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/mqtt && echo "mqtt" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/pgsql && echo "pgsql" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/queue && echo "queue" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/rate-limit && echo "rate-limit" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/roadrunner && echo "roadrunner" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/rpc && echo "rpc" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/shared-memory && echo "shared-memory" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/smarty && echo "smarty" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/snowflake && echo "snowflake" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/swoole && echo "swoole" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/swoole-tracker && echo "swoole-tracker" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/workerman && echo "workerman" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/workerman-gateway && echo "workerman-gateway" && ../../../vendor/bin/rector process --dry-run
cd $__DIR__/src/Components/macro && echo "macro" && ../../../vendor/bin/rector process --dry-run