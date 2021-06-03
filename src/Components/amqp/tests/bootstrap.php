<?php

use function Yurun\Swoole\Coroutine\batch;

require dirname(__DIR__, 4) . '/vendor/autoload.php';
require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * @return bool
 */
function checkHttpServerStatus()
{
    for ($i = 0; $i < 60; ++$i)
    {
        sleep(1);
        $context = stream_context_create(['http' => ['timeout' => 1]]);
        $body = @file_get_contents('http://127.0.0.1:8080/', false, $context);
        if ('imi' === $body)
        {
            return true;
        }
    }

    return false;
}

/**
 * 开启服务器.
 *
 * @return void
 */
function startServer()
{
    $dirname = dirname(__DIR__);
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
        $callbacks[] = function () use ($options, $name) {
            // start server
            $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
            echo "Starting {$name}...", \PHP_EOL;
            shell_exec($cmd);

            register_shutdown_function(function () use ($name, $options) {
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
}

(function () {
    $redis = new \Redis();
    if (!$redis->connect(getenv('REDIS_SERVER_HOST') ?: '127.0.0.1', 6379))
    {
        exit('Redis connect failed');
    }
    $redis->del($redis->keys('imi-amqp:*'));
    $redis->close();
})();

startServer();
