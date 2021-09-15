#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

$__DIR__/imi-$1 $1/stop
