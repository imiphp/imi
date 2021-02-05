<?php

echo '[Base Component]', \PHP_EOL;
$fileName = dirname(__DIR__) . '/tests/unit/Component/logs/' . date('Y-m-d') . '.log';
if (is_file($fileName))
{
    echo file_get_contents($fileName), \PHP_EOL;
}
else
{
    echo 'Not found!', \PHP_EOL;
}

echo '[Swoole Component]', \PHP_EOL;
$fileName = dirname(__DIR__) . '/tests/unit/Component/logs/' . date('Y-m-d') . '.log';
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
    'WebSocketServer',
    'TCPServer',
    'UDPServer',
] as $name)
{
    echo '[Swoole ', $name, ']', \PHP_EOL;
    $fileName = dirname(__DIR__) . '/src/Components/Swoole/tests/unit/' . $name . '/logs/cli.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        echo 'Not found!', \PHP_EOL;
    }
}

foreach ([
    'HttpServer',
] as $name)
{
    echo '[Workerman ', $name, ']', \PHP_EOL;
    $fileName = dirname(__DIR__) . '/src/Components/Workerman/tests/unit/' . $name . '/logs/cli.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        echo 'Not found!', \PHP_EOL;
    }
}

echo '[FPM]', \PHP_EOL;
$fileName = dirname(__DIR__) . '/src/Components/Fpm/tests/HttpServer/logs/cli.log';
if (is_file($fileName))
{
    echo file_get_contents($fileName), \PHP_EOL;
}
else
{
    echo 'Not found!', \PHP_EOL;
}
