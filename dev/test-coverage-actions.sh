#!/bin/bash

test() {
    local name=$1
    local cmd=$2
    echo "test $name...";
    echo $cmd
    time $cmd
}

__DIR__=$(cd `dirname $0`; pwd) && \

cd $__DIR__/../ && \

rm -rf dev/cover/*

export IMI_CODE_COVERAGE=1

php --ri xdebug > /dev/null
if [ $? = 0 ]; then
    paramsXdebug=""
else
    php -dzend_extension=xdebug --ri xdebug > /dev/null 2>&1
    if [ $? = 0 ]; then
        paramsXdebug="-dzend_extension=xdebug"
    fi
fi
paramsXdebug="$paramsXdebug -dxdebug.mode=coverage"

testType=$2

phpUnitCommands=()
swoolePhpUnitCommands=()

if [[ $testType = "core" ]]; then
    # core test
    test "core" "php $paramsXdebug -dapc.enable_cli=1 vendor/bin/phpunit -c ./tests/phpunit.xml --coverage-php=./dev/cover/core-coverage.php"
elif [[ $testType = "swoole" ]]; then
    swoolePhpUnitCommands=(
        "swoole"
    )
elif [[ $testType = "components" ]]; then
    swoolePhpUnitCommands=(
        "queue"
        "grpc"
        "mqtt"
        "smarty"
        "pgsql"
    )
    phpUnitCommands=(
        "workerman"
        "roadrunner"
        "fpm"
        "jwt"
        "snowflake"
    )

    test "workerman-gateway-workerman" "php $paramsXdebug vendor/bin/phpunit -c ./src/Components/workerman-gateway/tests/phpunit.xml --testsuite workerman --coverage-php=./dev/cover/workerman-gateway-coverage.php"

    test "workerman-gateway-swoole" "php $paramsXdebug vendor/bin/phpunit -c ./src/Components/workerman-gateway/tests/phpunit.xml --testsuite swoole --coverage-php=./dev/cover/workerman-gateway-swoole-coverage.php"

    export AMQP_TEST_MODE=swoole
    test "amqp-swoole" "php $paramsXdebug src/Components/swoole/bin/swoole-phpunit -c ./src/Components/amqp/tests/phpunit.xml --coverage-php=./dev/cover/amqp-swoole-coverage.php"
    
    export AMQP_TEST_MODE=workerman
    test "amqp-workerman" "php $paramsXdebug src/Components/swoole/bin/swoole-phpunit -c ./src/Components/amqp/tests/phpunit.xml --coverage-php=./dev/cover/amqp-workerman-coverage.php"

    export KAFKA_TEST_MODE=swoole
    test "kafka-swoole" "php $paramsXdebug src/Components/swoole/bin/swoole-phpunit -c ./src/Components/kafka/tests/phpunit.xml --coverage-php=./dev/cover/kafka-swoole-coverage.php"

    export KAFKA_TEST_MODE=workerman
    test "kafka-workerman" "php $paramsXdebug src/Components/swoole/bin/swoole-phpunit -c ./src/Components/kafka/tests/phpunit.xml --coverage-php=./dev/cover/kafka-workerman-coverage.php"
else
    echo "未知的测试类型：$testType"
    exit 1
fi

for name in "${phpUnitCommands[@]}"
do
    cmd="php $paramsXdebug vendor/bin/phpunit -c ./src/Components/$name/tests/phpunit.xml --coverage-php=./dev/cover/$name-coverage.php"
    test "$name" "$cmd"
done

for name in "${swoolePhpUnitCommands[@]}"
do
    cmd="php $paramsXdebug src/Components/swoole/bin/swoole-phpunit -c ./src/Components/$name/tests/phpunit.xml --coverage-php=./dev/cover/$name-coverage.php"
    test "$name" "$cmd"
done

php dev/merge-coverage.php $1
