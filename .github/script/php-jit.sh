#!/bin/bash

set -ex

if [ "${PHP_JIT}" == 'yes' ]; then
  echo "opcache.enable=1
opcache.enable_cli=1
opcache.jit => 1205
opcache.jit_buffer_size => 64m
opcache.validate_timestamps=0
opcache.enable_file_override=1
opcache.huge_code_pages=1
" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
  echo ">> php8 jit enable"
fi