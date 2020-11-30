#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

$__DIR__/../../bin/imi generate/table -appNamespace "Imi\Test\Component"
