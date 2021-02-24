#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

/usr/bin/env php "$__DIR__/../../../../../Cli/bin/imi-cli" imi/buildRuntime  --app-namespace "Imi\Fpm\Test\Web" --runtimeMode=Fpm