#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

$__DIR__/../../src/Components/Swoole/bin/imi-swoole generate/table --app-namespace "Imi\Test\Component"
