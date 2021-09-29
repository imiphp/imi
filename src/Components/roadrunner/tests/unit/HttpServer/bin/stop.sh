#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

php "$__DIR__/cli" rr/stop --app-namespace "Imi\RoadRunner\Test\HttpServer" -w "$__DIR__/../"
