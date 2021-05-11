#!/bin/bash

__DIR__=$(cd `dirname $0`; pwd)

cd $__DIR__/../grpc

protoc --php_out=./ grpc.proto
