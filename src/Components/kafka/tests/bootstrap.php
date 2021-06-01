<?php

use Imi\App;
use Imi\Event\Event;
use Imi\Event\EventParam;
use Imi\Swoole\Server\Event\Param\WorkerExitEventParam;
use Swoole\Coroutine;
use Swoole\Runtime;
use function Yurun\Swoole\Coroutine\batch;

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

startServer();

\Imi\Event\Event::on('IMI.INIT_TOOL', function (EventParam $param) {
    $data = $param->getData();
    $data['skip'] = true;
    \Imi\Tool\Tool::init();
});
\Imi\Event\Event::on('IMI.INITED', function (EventParam $param) {
    Runtime::enableCoroutine();
    App::initWorker();
    $param->stopPropagation();
}, 1);
App::run('KafkaApp');

Coroutine::defer(function () {
    Event::trigger('IMI.MAIN_SERVER.WORKER.EXIT', [], null, WorkerExitEventParam::class);
});
