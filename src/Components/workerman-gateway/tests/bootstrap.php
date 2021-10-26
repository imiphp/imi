<?php

declare(strict_types=1);

use Imi\Cli\ImiCommand;
use function Imi\ttyExec;

require \dirname(__DIR__, 4) . '/vendor/autoload.php';
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
            if ('imi' === @file_get_contents(imiGetEnv('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/'), false, $context))
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
            'WorkermanServer'    => [
                'start'         => __DIR__ . '/unit/AppServer/bin/start-workerman.ps1',
                'stop'          => __DIR__ . '/unit/AppServer/bin/stop-workerman.ps1',
                'checkStatus'   => 'checkHttpServerStatus',
            ],
        ];
    }
    else
    {
        $servers = [
            'WorkermanServer'    => [
                'start'         => __DIR__ . '/unit/AppServer/bin/start-workerman.sh',
                'checkStatus'   => 'checkHttpServerStatus',
            ],
            'WorkermanRegisterServer'    => [
                'start'         => __DIR__ . '/unit/AppServer/bin/start-workerman.sh --name register',
            ],
            'WorkermanGatewayServer'    => [
                'start'         => __DIR__ . '/unit/AppServer/bin/start-workerman.sh --name gateway',
            ],
            'SwooleServer' => [
                'start'         => __DIR__ . '/unit/AppServer/bin/start-swoole.sh',
                'stop'          => __DIR__ . '/unit/AppServer/bin/stop-swoole.sh',
                'checkStatus'   => 'checkHttpServerStatus',
            ],
        ];
    }

    $input = ImiCommand::getInput();
    switch ($input->getParameterOption('--testsuite'))
    {
        case 'swoole':
            runTestServer('WorkermanRegisterServer', $servers['WorkermanRegisterServer']);
            runTestServer('WorkermanGatewayServer', $servers['WorkermanGatewayServer']);
            runTestServer('SwooleServer', $servers['SwooleServer']);
            break;
        case 'workerman':
            runTestServer('WorkermanServer', $servers['WorkermanServer']);
            break;
        default:
            throw new \RuntimeException(sprintf('Unknown --testsuite %s', $input->getParameterOption('--testsuite')));
    }

    if ('/' === \DIRECTORY_SEPARATOR)
    {
        register_shutdown_function(function () {
            echo 'Stoping WorkermanServers...', \PHP_EOL;
            if ('Darwin' === \PHP_OS)
            {
                $keyword = 'workerman/start';
            }
            else
            {
                $keyword = 'WorkerMan: master process';
            }
            ttyExec(<<<CMD
            kill `ps -ef|grep "{$keyword}"|grep -v grep|awk '{print $2}'`
            CMD);
            echo 'WorkermanServers stoped!', \PHP_EOL, \PHP_EOL;
        });
    }
}

function runTestServer(string $name, array $options): void
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

    if (isset($options['stop']))
    {
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
    }

    if (isset($options['checkStatus']))
    {
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
    foreach ([13000, 13002, 13004, 12900] as $port)
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
