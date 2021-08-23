#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)/../../../

$__DIR__/Cli/bin/imi-cli generate/pgModel "Imi\Pgsql\Test\Model" --app-namespace "Imi\Pgsql\Test" --prefix=tb_ --override=base --lengthCheck=1
