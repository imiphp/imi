<?php

$dir = dirname(__DIR__) . '/tests/unit/';
$date = date('Y-m-d');

echo '[Component]', \PHP_EOL;
$fileName = $dir . 'Component/logs/' . $date . '.log';
if (is_file($fileName))
{
    echo file_get_contents($fileName), \PHP_EOL;
}
else
{
    echo 'Not found!', \PHP_EOL;
}

foreach ([
    'HttpServer',
    'RedisSessionServer',
    'TCPServer',
    'UDPServer',
    'WebSocketServer',
] as $server)
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
