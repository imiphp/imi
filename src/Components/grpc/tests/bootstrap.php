<?php

declare(strict_types=1);

use function Yurun\Swoole\Coroutine\batch;

require \dirname(__DIR__) . '/vendor/autoload.php';

function checkHttpServerStatus(): bool
{
    for ($i = 0; $i < 60; ++$i)
    {
        sleep(1);
        try
        {
            $context = stream_context_create(['http' => ['timeout' => 20]]);
            if ('' === @file_get_contents('http://127.0.0.1:8081/', false, $context))
            {
                return true;
            }
        }
        catch (ErrorException $e)
        {
        }
    }

    return false;
}

/**
 * 开启服务器.
 */
function startServer(): void
{
    $dirname = \dirname(__DIR__);
    $servers = [
        'HttpServer'    => [
            'start'         => $dirname . '/example/bin/start-server.sh',
            'stop'          => $dirname . '/example/bin/stop-server.sh',
            'checkStatus'   => 'checkHttpServerStatus',
        ],
    ];

    $callbacks = [];
    foreach ($servers as $name => $options)
    {
        $callbacks[] = static function () use ($options, $name): void {
            // start server
            $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
            echo "Starting {$name}...", \PHP_EOL;
            shell_exec($cmd);

            register_shutdown_function(static function () use ($name, $options): void {
                \Swoole\Runtime::enableCoroutine(false);
                // stop server
                $cmd = $options['stop'];
                echo "Stoping {$name}...", \PHP_EOL;
                shell_exec($cmd);
                echo "{$name} stoped!", \PHP_EOL, \PHP_EOL;
            });

            if (($options['checkStatus'])())
            {
                echo "{$name} started!", \PHP_EOL;
            }
            else
            {
                throw new \RuntimeException("{$name} start failed");
            }
        };
    }

    batch($callbacks, 120, max(swoole_cpu_num() - 1, 1));
    register_shutdown_function(static function (): void {
        checkPorts([8081]);
    });
}

startServer();
