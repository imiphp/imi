#!/bin/bash

set -ex

if [ -n "${PHP_JIT}" ]; then
  echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
  echo "opcache.jit_buffer_size=64M" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
  echo ">> php8 jit enable"
fi