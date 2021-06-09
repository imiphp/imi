#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

"$__DIR__/swoole" swoole/stop
