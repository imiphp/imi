<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Controller',
        'Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Listener',
        'Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Error',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Swoole\Test\WebSocketServerWithRedisServerUtil\MainServer\Middleware\Test::class,
            ],
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Swoole\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectionContextStore'   => [
            'handlerClass'  => \Imi\Server\ConnectionContext\StoreHandler\Redis::class,
            'ttl'           => 600,
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'RouteNotFound',
        ],
    ],
];
