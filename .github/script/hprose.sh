#!/bin/bash

set -ex

if [ "$(php -r 'echo PHP_VERSION_ID < 80000 ? 1 : 0;')" = "1" ]; then
  pecl install hprose
  docker-php-ext-enable hprose
fi