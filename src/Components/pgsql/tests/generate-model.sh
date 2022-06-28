#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__/../

$__DIR__/../../../Cli/bin/imi-cli generate/pgModel "Imi\Pgsql\Test\Model" --app-namespace "Imi\Pgsql\Test" --prefix=tb_ --override=base --lengthCheck=1

cd $__DIR__/../../../../

vendor/bin/php-cs-fixer fix $__DIR__/../
