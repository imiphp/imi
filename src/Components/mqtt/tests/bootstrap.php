<?php

use Imi\App;
use Imi\Event\EventParam;
use function Yurun\Swoole\Coroutine\batch;

require dirname(__DIR__, 4) . '/vendor/autoload.php';

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
 *
 * @return void
 */
function startServer()
{
    $dirname = dirname(__DIR__);
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

App::initFramework('MQTTApp');

\Imi\Event\Event::on('IMI.INIT_TOOL', function (EventParam $param) {
    $data = $param->getData();
    $data['skip'] = true;
    \Imi\Tool\Tool::init();
});
\Imi\Event\Event::on('IMI.INITED', function (EventParam $param) {
    App::initWorker();
    $param->stopPropagation();
}, 1);
App::run('MQTTApp');
