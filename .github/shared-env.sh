#!/bin/env sh

set -ex

echo "init shared"

chmod -R 777 /tmp/host-run
chmod -R 777 /tmp/docker

rm -f /tmp/docker/shared || true
rm -rf /tmp/host-run/* || true

ln -s /tmp/host-run /tmp/docker/shared

chmod -R 777 /tmp/docker/shared

tail -f /dev/null