<?php

declare(strict_types=1);

$dir = dirname(__DIR__) . '/tests/unit/';

$servers = [
    'HttpServer',
    'RedisSessionServer',
    'TCPServer',
    'UDPServer',
    'WebSocketServer',
];

$date = date('Y-m-d');

foreach ($servers as $server)
{
    $filename = $dir . $server . '/logs/cli.log';
    echo '[', $server, '] ', \PHP_EOL, 'File: ', $filename, \PHP_EOL;
    if (is_file($filename))
    {
        echo file_get_contents($filename), \PHP_EOL;
    }
    else
    {
        echo 'File not found', \PHP_EOL;
    }
}
