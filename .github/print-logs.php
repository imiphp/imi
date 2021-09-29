<?php

declare(strict_types=1);

$date = date('Y-m-d');

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
    'WebSocketServerWithRedisServerUtil',
] as $name)
{
    echo '[Swoole ', $name, ']', \PHP_EOL;
    $fileName = dirname(__DIR__) . '/src/Components/swoole/tests/unit/' . $name . '/logs/cli.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        $fileName = dirname(__DIR__) . '/src/Components/swoole/tests/unit/' . $name . '/logs/log-' . $date . '.log';
        if (is_file($fileName))
        {
            echo file_get_contents($fileName), \PHP_EOL;
        }
        else
        {
            echo 'Not found!', \PHP_EOL;
        }
    }
}

foreach ([
    'AppServer',
    'ChannelServerUtilServer',
] as $name)
{
    echo '[Workerman ', $name, ']', \PHP_EOL;
    $fileName = dirname(__DIR__) . '/src/Components/workerman/tests/unit/' . $name . '/logs/cli.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        $fileName = dirname(__DIR__) . '/src/Components/workerman/tests/unit/' . $name . '/logs/log-' . $date . '.log';
        if (is_file($fileName))
        {
            echo file_get_contents($fileName), \PHP_EOL;
        }
        else
        {
            echo 'Not found!', \PHP_EOL;
        }
    }
}

echo '[FPM]', \PHP_EOL;
$fileName = dirname(__DIR__) . '/src/Components/fpm/tests/HttpServer/logs/cli.log';
if (is_file($fileName))
{
    echo file_get_contents($fileName), \PHP_EOL;
}
else
{
    $fileName = dirname(__DIR__) . '/src/Components/fpm/tests/HttpServer/logs/log-' . $date . '.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        echo 'Not found!', \PHP_EOL;
    }
}

echo '[WorkermanGateway]', \PHP_EOL;
$fileName = dirname(__DIR__) . '/src/Components/workerman-gateway/tests/unit/AppServer/logs/cli.log';
if (is_file($fileName))
{
    echo file_get_contents($fileName), \PHP_EOL;
}
else
{
    $fileName = dirname(__DIR__) . '/src/Components/workerman-gateway/tests/unit/AppServer/logs/log-' . $date . '.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        echo 'Not found!', \PHP_EOL;
    }
}

echo '[RoadRunner]', \PHP_EOL;
$fileName = dirname(__DIR__) . '/src/Components/roadrunner/tests/unit/HttpServer/logs/cli.log';
if (is_file($fileName))
{
    echo file_get_contents($fileName), \PHP_EOL;
}
else
{
    $fileName = dirname(__DIR__) . '/src/Components/roadrunner/tests/unit/HttpServer/logs/log-' . $date . '.log';
    if (is_file($fileName))
    {
        echo file_get_contents($fileName), \PHP_EOL;
    }
    else
    {
        echo 'Not found!', \PHP_EOL;
    }
}

$dir = dirname(__DIR__) . '/src/Components/';
foreach ([
    'amqp',
    'grpc',
    'kafka',
    'mqtt',
    'smarty',
    'pgsql',
] as $component)
{
    $filename = $dir . $component . '/example/.runtime/logs/cli.log';
    echo '[components.', $component, '] ', \PHP_EOL;
    if (is_file($filename))
    {
        echo 'File: ', $filename, \PHP_EOL;
        echo file_get_contents($filename), \PHP_EOL;
    }
    elseif (is_file($fileName = $dir . $component . '/example/.runtime/logs/log-' . $date . '.log'))
    {
        echo 'File: ', $filename, \PHP_EOL;
        echo file_get_contents($filename), \PHP_EOL;
    }
    else
    {
        echo 'File not found', \PHP_EOL;
    }
}
