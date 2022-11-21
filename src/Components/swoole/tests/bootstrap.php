<?php

declare(strict_types=1);

use function Imi\env;
use function Imi\ttyExec;
use function Yurun\Swoole\Coroutine\batch;

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
        for ($i = 0; $i < 20; ++$i)
        {
            sleep(1);
            $context = stream_context_create(['http' => ['timeout' => 3]]);
            if ('imi' === @file_get_contents(env('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/'), false, $context))
            {
                $serverStarted = true;
                break;
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkRedisSessionServerStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 20; ++$i)
        {
            sleep(1);
            try
            {
                $context = stream_context_create(['http' => ['timeout' => 3]]);
                if ('imi' === @file_get_contents('http://127.0.0.1:13001/', false, $context))
                {
                    $serverStarted = true;
                    break;
                }
            }
            catch (ErrorException $e)
            {
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkWebSocketServerStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 20; ++$i)
        {
            sleep(1);
            try
            {
                $context = stream_context_create(['http' => ['timeout' => 3]]);
                @file_get_contents('http://127.0.0.1:13002/', false, $context);
                if (isset($http_response_header[0]) && 'HTTP/1.1 400 Bad Request' === $http_response_header[0])
                {
                    $serverStarted = true;
                    break;
                }
            }
            catch (ErrorException $e)
            {
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkTCPServerStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            try
            {
                $sock = socket_create(\AF_INET, \SOCK_STREAM, \SOL_TCP);
                if ($sock && socket_set_option($sock, \SOL_SOCKET, \SO_RCVTIMEO, ['sec' => 1, 'usec' => 0]) && @socket_connect($sock, '127.0.0.1', 13003))
                {
                    $serverStarted = true;
                    break;
                }
            }
            finally
            {
                if (isset($sock))
                {
                    socket_close($sock);
                }
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkUDPServerStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            try
            {
                $handle = @stream_socket_client('udp://127.0.0.1:13004', $errno, $errstr);
                if (
                    $handle
                    && stream_set_timeout($handle, 1)
                    && fwrite($handle, json_encode([
                        'action'    => 'hello',
                        'format'    => 'Y',
                        'time'      => time(),
                    ])) > 0
                    && '{' === fread($handle, 1)
                ) {
                    $serverStarted = true;
                    break;
                }
            }
            finally
            {
                if (isset($handle))
                {
                    fclose($handle);
                }
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkWebSocketServerWithRedisServerUtilStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 20; ++$i)
        {
            sleep(1);
            try
            {
                $context = stream_context_create(['http' => ['timeout' => 3]]);
                @file_get_contents('http://127.0.0.1:13008/', false, $context);
                if (isset($http_response_header[0]) && 'HTTP/1.1 400 Bad Request' === $http_response_header[0])
                {
                    $serverStarted = true;
                    break;
                }
            }
            catch (ErrorException $e)
            {
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkWebSocketServerWithAmqpServerUtilStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 20; ++$i)
        {
            sleep(1);
            try
            {
                $context = stream_context_create(['http' => ['timeout' => 3]]);
                @file_get_contents('http://127.0.0.1:13009/', false, $context);
                if (isset($http_response_header[0]) && 'HTTP/1.1 400 Bad Request' === $http_response_header[0])
                {
                    $serverStarted = true;
                    break;
                }
            }
            catch (ErrorException $e)
            {
            }
        }

        return $serverStarted;
    }

    // @phpstan-ignore-next-line
    function checkWebSocketServerWithAmqpRouteServerUtilStatus(): bool
    {
        $serverStarted = false;
        for ($i = 0; $i < 20; ++$i)
        {
            sleep(1);
            try
            {
                $context = stream_context_create(['http' => ['timeout' => 3]]);
                @file_get_contents('http://127.0.0.1:13010/', false, $context);
                if (isset($http_response_header[0]) && 'HTTP/1.1 400 Bad Request' === $http_response_header[0])
                {
                    $serverStarted = true;
                    break;
                }
            }
            catch (ErrorException $e)
            {
            }
        }

        return $serverStarted;
    }

    $servers = [
        'HttpServer'    => [
            'start'         => __DIR__ . '/unit/HttpServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/HttpServer/bin/stop.sh',
            'checkStatus'   => 'checkHttpServerStatus',
        ],
        'RedisSessionServer'    => [
            'start'         => __DIR__ . '/unit/RedisSessionServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/RedisSessionServer/bin/stop.sh',
            'checkStatus'   => 'checkRedisSessionServerStatus',
        ],
        'WebSocketServer'    => [
            'start'         => __DIR__ . '/unit/WebSocketServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/WebSocketServer/bin/stop.sh',
            'checkStatus'   => 'checkWebSocketServerStatus',
        ],
        'TCPServer'    => [
            'start'         => __DIR__ . '/unit/TCPServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/TCPServer/bin/stop.sh',
            'checkStatus'   => 'checkTCPServerStatus',
        ],
        'UDPServer'    => [
            'start'         => __DIR__ . '/unit/UDPServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/UDPServer/bin/stop.sh',
            'checkStatus'   => 'checkUDPServerStatus',
        ],
        'WebSocketServerWithRedisServerUtil'    => [
            'start'         => __DIR__ . '/unit/WebSocketServerWithRedisServerUtil/bin/start.sh',
            'stop'          => __DIR__ . '/unit/WebSocketServerWithRedisServerUtil/bin/stop.sh',
            'checkStatus'   => 'checkWebSocketServerWithRedisServerUtilStatus',
        ],
    ];

    if (env('IMI_TEST_AMQP_SERVER_UTIL', true))
    {
        $servers['WebSocketServerWithAmqpServerUtil'] = [
            'start'         => __DIR__ . '/unit/WebSocketServerWithAmqpServerUtil/bin/start.sh',
            'stop'          => __DIR__ . '/unit/WebSocketServerWithAmqpServerUtil/bin/stop.sh',
            'checkStatus'   => 'checkWebSocketServerWithAmqpServerUtilStatus',
        ];
        $servers['WebSocketServerWithAmqpRouteServerUtil'] = [
            'start'         => __DIR__ . '/unit/WebSocketServerWithAmqpRouteServerUtil/bin/start.sh',
            'stop'          => __DIR__ . '/unit/WebSocketServerWithAmqpRouteServerUtil/bin/stop.sh',
            'checkStatus'   => 'checkWebSocketServerWithAmqpRouteServerUtilStatus',
        ];
    }

    $callbacks = [];
    foreach ($servers as $name => $options)
    {
        $callbacks[] = static function () use ($options, $name) {
            // start server
            $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
            echo "Starting {$name}...", \PHP_EOL;
            $code = ttyExec($cmd);
            if (0 === $code)
            {
                register_shutdown_function(static function () use ($name, $options) {
                    \Swoole\Runtime::enableCoroutine(false);
                    // stop server
                    $cmd = $options['stop'];
                    echo "Stoping {$name}...", \PHP_EOL;
                    ttyExec($cmd);
                    echo "{$name} stoped!", \PHP_EOL, \PHP_EOL;
                });

                if (($options['checkStatus'])())
                {
                    echo "{$name} started!", \PHP_EOL;
                }
                else
                {
                    throw new \RuntimeException("{$name} check status failed");
                }
            }
            else
            {
                throw new \RuntimeException("{$name} start failed, code={$code}");
            }
        };
    }

    batch($callbacks, 120, max(swoole_cpu_num() - 1, 1));

    register_shutdown_function(static function () {
        \Swoole\Runtime::enableCoroutine(false);
        echo 'check ports...', \PHP_EOL;
        ttyExec(\PHP_BINARY . ' ' . __DIR__ . '/bin/checkPorts.php');
    });
}

startServer();
