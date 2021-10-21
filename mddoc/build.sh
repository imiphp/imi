#!/usr/bin/env bash

set -eu

cd "$(dirname $0)"

if [ ! -d "./vendor" ]; then
  composer update
fi

if [ -d './output' ]; then
  rm -r ./output
fi

php ./vendor/bin/build.php -markdownPath ../doc -htmlPath ./output

echo "open file ($PWD/output/index.html) preview"