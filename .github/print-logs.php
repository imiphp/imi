<?php

foreach ([
    'Component',
    'HttpServer',
    'RedisSessionServer',
    'WebSocketServer',
    'TCPServer',
    'UDPServer',
] as $name)
{
    echo '[', $name, ']', \PHP_EOL;
    $fileName = dirname(__DIR__) . '/tests/unit/' . $name . '/logs/' . date('Y-m-d') . '.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        echo 'Not found!', \PHP_EOL;
    }
}
