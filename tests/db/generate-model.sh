#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)/../../

$__DIR__/src/Cli/bin/imi-cli generate/model "Imi\Test\Component\Model" --app-namespace "Imi\Test\Component" --prefix=tb_ --override=base --lengthCheck --sqlSingleLine --exclude tb_prefix

$__DIR__/src/Cli/bin/imi-cli generate/model "Imi\Test\Component\Model" --app-namespace "Imi\Test\Component" --poolName=dbPrefix --override=base --lengthCheck --sqlSingleLine --include tb_prefix

$__DIR__/vendor/bin/php-cs-fixer fix
