<?php

$date = date('Y-m-d');

$filename = dirname(__DIR__).'/example/.runtime/logs/cli.log';
echo '[testServer] ', PHP_EOL, 'File: ', $filename, PHP_EOL;
if (is_file($filename)) {
    echo file_get_contents($filename), PHP_EOL;
} else {
    echo 'File not found', PHP_EOL;
}
