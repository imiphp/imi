#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)
cd $__DIR__

echo "core" && ./vendor/bin/phpstan analyse --memory-limit 1G

echo "access-control" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/access-control/vendor/autoload.php src/Components/access-control

echo "amqp" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/amqp/vendor/autoload.php src/Components/amqp

echo "apidoc" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/apidoc/vendor/autoload.php src/Components/apidoc

echo "fpm" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/fpm/vendor/autoload.php src/Components/fpm

echo "grpc" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/grpc/vendor/autoload.php src/Components/grpc

echo "jwt" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/jwt/vendor/autoload.php src/Components/jwt

echo "kafka" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/kafka/vendor/autoload.php src/Components/kafka

echo "mqtt" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/mqtt/vendor/autoload.php src/Components/mqtt

echo "pgsql" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/pgsql/vendor/autoload.php src/Components/pgsql

echo "queue" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/queue/vendor/autoload.php src/Components/queue

echo "rate-limit" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/rate-limit/vendor/autoload.php src/Components/rate-limit

echo "roadrunner" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/roadrunner/vendor/autoload.php src/Components/roadrunner

echo "rpc" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/rpc/vendor/autoload.php src/Components/rpc

echo "shared-memory" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/shared-memory/vendor/autoload.php src/Components/shared-memory

echo "smarty" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/smarty/vendor/autoload.php src/Components/smarty

echo "snowflake" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/snowflake/vendor/autoload.php src/Components/snowflake

echo "swoole" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/swoole/vendor/autoload.php src/Components/swoole

echo "swoole-tracker" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/swoole-tracker/vendor/autoload.php src/Components/swoole-tracker

echo "workerman" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/workerman/vendor/autoload.php src/Components/workerman

echo "workerman-gateway" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/workerman-gateway/vendor/autoload.php src/Components/workerman-gateway

echo "macro" && ./vendor/bin/phpstan analyse --memory-limit 1G --configuration=phpstan-components.neon --autoload-file=src/Components/macro/vendor/autoload.php src/Components/macro
