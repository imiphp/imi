<?php
$loader = require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

use Imi\App;

App::setLoader($loader);

/**
 * 开启服务器
 *
 * @return void
 */
function startServer()
{
    function checkHttpServerStatus()
    {
        $serverStarted = false;
        for($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            $context = stream_context_create(['http'=>['timeout'=>1]]);
            if('imi' === @file_get_contents(imiGetEnv('HTTP_SERVER_HOST', 'http://127.0.0.1:13000/'), false, $context))
            {
                $serverStarted = true;
                break;
            }
        }
        return $serverStarted;
    }
    
    function checkRedisSessionServerStatus()
    {
        $serverStarted = false;
        for($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            $context = stream_context_create(['http'=>['timeout'=>1]]);
            if('imi' === @file_get_contents('http://127.0.0.1:13001/', false, $context))
            {
                $serverStarted = true;
                break;
            }
        }
        return $serverStarted;
    }

    function checkWebSocketServerStatus()
    {
        $serverStarted = false;
        for($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            $context = stream_context_create(['http'=>['timeout'=>1]]);
            @file_get_contents('http://127.0.0.1:13002/', false, $context);
            if(isset($http_response_header[0]) && 'HTTP/1.1 400 Bad Request' === $http_response_header[0])
            {
                $serverStarted = true;
                break;
            }
        }
        return $serverStarted;
    }
    
    function checkTCPServerStatus()
    {
        $serverStarted = false;
        for($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            try {
                $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
                if($sock && socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array('sec' => 1, 'usec' => 0)) && @socket_connect($sock, '127.0.0.1', 13003))
                {
                    $serverStarted = true;
                    break;
                }
            } catch(\Throwable $th) {
                throw $th;
            } finally {
                socket_close($sock);
            }
        }
        return $serverStarted;
    }

    function checkUDPServerStatus()
    {
        $serverStarted = false;
        for($i = 0; $i < 60; ++$i)
        {
            sleep(1);
            try {
                $handle = @stream_socket_client("udp://127.0.0.1:13004", $errno, $errstr);
                if(
                    $handle
                    && stream_set_timeout($handle, 1)
                    && fwrite($handle, json_encode([
                        'action'    =>  'hello',
                        'format'    =>  'Y',
                        'time'      =>  time(),
                    ])) > 0
                    && '{' === fread($handle, 1)
                )
                {
                    $serverStarted = true;
                    break;
                }
            } catch(\Throwable $th) {
                throw $th;
            } finally {
                fclose($handle);
            }
        }
        return $serverStarted;
    }
    
    $servers = [
        'HttpServer'    =>  [
            'start'         => __DIR__ . '/unit/HttpServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/HttpServer/bin/stop.sh',
            'checkStatus'   => 'checkHttpServerStatus',
        ],
        'RedisSessionServer'    =>  [
            'start'         => __DIR__ . '/unit/RedisSessionServer/bin/' . (version_compare(SWOOLE_VERSION, '4.4', '>=') ? 'start.sh' : 'start-sw4.3.sh'),
            'stop'          => __DIR__ . '/unit/RedisSessionServer/bin/stop.sh',
            'checkStatus'   => 'checkRedisSessionServerStatus',
        ],
        'WebSocketServer'    =>  [
            'start'         => __DIR__ . '/unit/WebSocketServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/WebSocketServer/bin/stop.sh',
            'checkStatus'   => 'checkWebSocketServerStatus',
        ],
        'TCPServer'    =>  [
            'start'         => __DIR__ . '/unit/TCPServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/TCPServer/bin/stop.sh',
            'checkStatus'   => 'checkTCPServerStatus',
        ],
        'UDPServer'    =>  [
            'start'         => __DIR__ . '/unit/UDPServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/UDPServer/bin/stop.sh',
            'checkStatus'   => 'checkUDPServerStatus',
        ],
    ];
    
    foreach($servers as $name => $options)
    {
        // start server
        $cmd = 'nohup ' . $options['start'] . ' > /dev/null 2>&1';
        echo "Starting {$name}...", PHP_EOL;
        echo `{$cmd}`, PHP_EOL;
    
        register_shutdown_function(function() use($name, $options){
            // stop server
            $cmd = $options['stop'];
            echo "Stoping {$name}...", PHP_EOL;
            echo `{$cmd}`, PHP_EOL;
            echo "{$name} stoped!", PHP_EOL;
        });
    
        if(($options['checkStatus'])())
        {
            echo "{$name} started!", PHP_EOL;
        }
        else
        {
            throw new \RuntimeException("{$name} start failed");
        }
    }
}

startServer();
