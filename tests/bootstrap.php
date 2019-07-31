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
            if('imi' === @file_get_contents('http://127.0.0.1:13000/'))
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
            if('imi' === @file_get_contents('http://127.0.0.1:13001/'))
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
            @file_get_contents('http://127.0.0.1:13002/');
            if(isset($http_response_header) && 'HTTP/1.1 400 Bad Request' === $http_response_header[0])
            {
                $serverStarted = true;
                break;
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
            'start'         => __DIR__ . '/unit/RedisSessionServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/RedisSessionServer/bin/stop.sh',
            'checkStatus'   => 'checkRedisSessionServerStatus',
        ],
        'WebSocketServer'    =>  [
            'start'         => __DIR__ . '/unit/WebSocketServer/bin/start.sh',
            'stop'          => __DIR__ . '/unit/WebSocketServer/bin/stop.sh',
            'checkStatus'   => 'checkWebSocketServerStatus',
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
