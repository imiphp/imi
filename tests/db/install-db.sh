#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

$__DIR__/../../src/Cli/bin/imi-cli generate/table -appNamespace "Imi\Test\Component"
