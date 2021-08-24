#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

# MySQL
$__DIR__/../../src/Cli/bin/imi-cli generate/table --app-namespace "Imi\Test\Component"
