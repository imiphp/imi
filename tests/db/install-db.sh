#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

mysql -e 'CREATE DATABASE IF NOT EXISTS db_imi_test;'

$__DIR__/../../bin/imi generate/table -appNamespace "Imi\Test\Component"
