<?php

declare(strict_types=1);

require dirname(__DIR__, 4) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/autoload.php';

function checkHttpServerStatus(): bool
{
    for ($i = 0; $i < 60; ++$i)
    {
        sleep(1);
        $context = stream_context_create(['http' => ['timeout' => 1]]);
        $body = @file_get_contents('http://127.0.0.1:13456/ping', false, $context);
        if ('pong' === $body)
        {
            return true;
        }
    }

    return false;
}

/**
 * 开启服务器.
 */
function startServer(): void
{
    $servers = [
        'HttpServer'    => [
            'start'         => dirname(__DIR__) . '/example/bin/start.sh',
            'stop'          => dirname(__DIR__) . '/example/bin/stop.sh',
            'checkStatus'   => 'checkHttpServerStatus',
        ],
    ];

    foreach ($servers as $name => $options)
    {
        // start server
        $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
        echo "Starting {$name}...", \PHP_EOL;
        echo shell_exec("{$cmd}"), \PHP_EOL;

        register_shutdown_function(function () use ($name, $options) {
            // stop server
            $cmd = $options['stop'];
            echo "Stoping {$name}...", \PHP_EOL;
            echo shell_exec("{$cmd}"), \PHP_EOL;
            echo "{$name} stoped!", \PHP_EOL;
        });

        if (($options['checkStatus'])())
        {
            echo "{$name} started!", \PHP_EOL;
        }
        else
        {
            throw new \RuntimeException("{$name} start failed");
        }
    }
}

startServer();
