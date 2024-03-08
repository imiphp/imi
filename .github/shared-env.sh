#!/bin/env sh

echo "init shared"

chmod -R 777 /tmp/docker

ln -s /tmp/docker /tmp/host-docker/dir

tail -f /dev/zero