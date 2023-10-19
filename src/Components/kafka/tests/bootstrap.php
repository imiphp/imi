<?php

declare(strict_types=1);

use function Imi\env;
use function Yurun\Swoole\Coroutine\batch;

require \dirname(__DIR__) . '/vendor/autoload.php';

/**
 * @return bool
 */
function checkHttpServerStatus()
{
    for ($i = 0; $i < 60; ++$i)
    {
        sleep(1);
        try
        {
            $context = stream_context_create(['http' => ['timeout' => 20]]);
            $body = @file_get_contents('http://127.0.0.1:8080/', false, $context);
            if ('imi' === $body)
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
    $mode = env('KAFKA_TEST_MODE');
    if (!$mode)
    {
        throw new InvalidArgumentException('Invalid env KAFKA_TEST_MODE');
    }
    $servers = [
        'HttpServer'    => [
            'start'         => $dirname . '/example/bin/start-server.sh ' . $mode,
            'stop'          => $dirname . '/example/bin/stop-server.sh ' . $mode,
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
        checkPorts([8080]);
    });
}

startServer();
\Swoole\Coroutine::defer(static function (): void {
    \Imi\Event\Event::trigger('IMI.MAIN_SERVER.WORKER.EXIT', [], null, \Imi\Swoole\Server\Event\Param\WorkerExitEventParam::class);
});
