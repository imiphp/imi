<?php

declare(strict_types=1);

use function Imi\env;

require \dirname(__DIR__) . '/vendor/autoload.php';

/**
 * 开启服务器.
 */
function startServer(): void
{
    // @phpstan-ignore-next-line
    function checkHttpServerStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            $context = stream_context_create(['http' => ['timeout' => 1]]);
            if ('imi' === @file_get_contents(env('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/'), false, $context))
            {
                $serverStarted = true;
                break;
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkChannelServerUtilServerStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            $context = stream_context_create(['http' => ['timeout' => 1]]);
            if ('imi' === @file_get_contents(env('HTTP_SERVER_HOST', 'http://127.0.0.1:13006/'), false, $context))
            {
                $serverStarted = true;
                break;
            }
        }

        return $serverStarted;
    }

    if ('\\' === \DIRECTORY_SEPARATOR)
    {
        $servers = [
            'AppServer'    => [
                'start'         => __DIR__ . '/unit/AppServer/bin/start.ps1',
                'stop'          => __DIR__ . '/unit/AppServer/bin/stop.ps1',
                'checkStatus'   => 'checkHttpServerStatus',
            ],
            'ChannelServerUtilServer'    => [
                'start'         => __DIR__ . '/unit/ChannelServerUtilServer/bin/start.ps1',
                'stop'          => __DIR__ . '/unit/ChannelServerUtilServer/bin/stop.ps1',
                'checkStatus'   => 'checkChannelServerUtilServerStatus',
            ],
        ];
    }
    else
    {
        $servers = [
            'AppServer'    => [
                'start'         => __DIR__ . '/unit/AppServer/bin/start.sh',
                'stop'          => __DIR__ . '/unit/AppServer/bin/stop.sh',
                'checkStatus'   => 'checkHttpServerStatus',
            ],
            'ChannelServerUtilServer'    => [
                'start'         => __DIR__ . '/unit/ChannelServerUtilServer/bin/start.sh',
                'stop'          => __DIR__ . '/unit/ChannelServerUtilServer/bin/stop.sh',
                'checkStatus'   => 'checkChannelServerUtilServerStatus',
            ],
        ];
    }

    foreach ($servers as $name => $options)
    {
        // start server
        if ('\\' === \DIRECTORY_SEPARATOR)
        {
            $cmd = 'powershell ' . $options['start'];
        }
        else
        {
            $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
        }
        echo "Starting {$name}...", \PHP_EOL;
        shell_exec("{$cmd}");

        register_shutdown_function(function () use ($name, $options) {
            // stop server
            $cmd = $options['stop'];
            if ('\\' === \DIRECTORY_SEPARATOR)
            {
                $cmd = 'powershell ' . $cmd;
            }
            echo "Stoping {$name}...", \PHP_EOL;
            shell_exec("{$cmd}");
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
    }
}

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

startServer();

register_shutdown_function(function () {
    echo 'check ports...', \PHP_EOL;
    foreach ([13000, 13002, 13003, 13004, 13005, 13006, 13007] as $port)
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
});
