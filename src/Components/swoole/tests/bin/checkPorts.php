<?php

declare(strict_types=1);

/**
 * 检查端口是否可以被绑定.
 */
function checkPort(string $host, int $port, ?int &$errno = null, ?string &$errstr = null): bool
{
    $socket = @stream_socket_client('tcp://' . $host . ':' . $port, $errno, $errstr, 3);
    if (!$socket)
    {
        return false;
    }
    fclose($socket);

    return true;
}

foreach ([13000, 13001, 13002, 13003, 13004, 13005, 13006, 13007, 13008, 13009, 13010] as $port)
{
    echo "checking port {$port}...";
    $count = 0;
    while (checkPort('127.0.0.1', $port))
    {
        if ($count >= 10)
        {
            echo 'failed', \PHP_EOL;
            continue 2;
        }
        ++$count;
        sleep(1);
    }
    echo 'OK', \PHP_EOL;
}
