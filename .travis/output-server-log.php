<?php
$dir = dirname(__DIR__) . '/tests/unit/';

$servers = [
    'HttpServer',
    'RedisSessionServer',
    'TCPServer',
    'UDPServer',
];

$date = date('Y-m-d');

foreach($servers as $server)
{
    $filename = $dir . $server . '/logs/' . $date . '.log';
    echo '[', $server, '] ', PHP_EOL, 'File: ', $filename, PHP_EOL;
    if(is_file($filename))
    {
        echo file_get_contents($filename), PHP_EOL;
    }
    else
    {
        echo 'File not found', PHP_EOL;
    }
}
