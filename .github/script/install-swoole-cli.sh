#!/bin/bash

set -ex

version=$1

if [ -z "${version}" ]; then
  exit 0;
fi

mkdir -p /tmp/swoole-cli

cd /tmp/swoole-cli

curl -L -o swoole-cli.tar.xz https://github.com/swoole/swoole-src/releases/download/$version/swoole-cli-$version-linux-x64.tar.xz

xz -d swoole-cli.tar.xz

tar -xvf swoole-cli-$version-linux-x64.tar -C /usr/bin

cp /usr/bin/swoole-cli /usr/bin/php
