<?php

declare(strict_types=1);

use function Yurun\Swoole\Coroutine\batch;

require \dirname(__DIR__) . '/vendor/autoload.php';

\Swoole\Runtime::enableCoroutine();

/**
 * @return bool
 */
function checkMQTTServerStatus()
{
    $serverStarted = false;
    for ($i = 0; $i < 60; ++$i)
    {
        sleep(1);
        $socket = @stream_socket_client(
            'tcp://127.0.0.1:8081',
            $errno,
            $errstr,
            1,
            \STREAM_CLIENT_CONNECT
        );
        if ($socket)
        {
            $serverStarted = true;
            break;
        }
    }

    return $serverStarted;
}

/**
 * 开启服务器.
 */
function startServer(): void
{
    $dirname = \dirname(__DIR__);
    $servers = [
        'MQTTServer'    => [
            'start'         => $dirname . '/example/bin/start-server.sh',
            'stop'          => $dirname . '/example/bin/stop-server.sh',
            'checkStatus'   => 'checkMQTTServerStatus',
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
        checkPorts([8081, 8082]);
    });
}

startServer();
