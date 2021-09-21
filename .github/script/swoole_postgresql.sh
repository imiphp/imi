#!/bin/bash

set -ex

postgresqlVer=$1
postgresqlDir=/tmp/ext-postgresql

if [ -z "${postgresqlVer}" ]; then
  exit 0;
fi

mkdir -p ${postgresqlDir}

curl -L -o /tmp/ext-postgresql.tar.gz https://github.com/swoole/ext-postgresql/archive/${postgresqlVer}.tar.gz

tar -zxvf /tmp/ext-postgresql.tar.gz -C ${postgresqlDir} --strip-components=1
cd ${postgresqlDir}
phpize
./configure
make -j
make install
docker-php-ext-enable swoole_postgresql
php --ri swoole_postgresql