<?php

foreach ([
    'HttpServer',
    'RedisSessionServer',
    'WebSocketServer',
    'TCPServer',
    'UDPServer',
] as $name)
{
    echo '[', $name, ']', \PHP_EOL;
    echo file_get_contents(dirname(__DIR__) . '/tests/unit/' . $name . '/logs/' . date('Y-m-d') . '.log'), \PHP_EOL;
}
