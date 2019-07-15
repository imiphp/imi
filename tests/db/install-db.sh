#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

mysql -e 'CREATE DATABASE IF NOT EXISTS db_imi_test;'
mysql -u root db_imi_test < $__DIR__/db.sql
