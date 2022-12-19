<?php

declare(strict_types=1);

return [
    'configs'     => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\WebSocketServerWithAmqpRouteServerUtil\MainServer\Controller',
        'Imi\Swoole\Test\WebSocketServerWithAmqpRouteServerUtil\MainServer\Listener',
        'Imi\Swoole\Test\WebSocketServerWithAmqpRouteServerUtil\MainServer\Error',
    ],
    'beans'       => [
        'WebSocketDispatcher'       => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Swoole\Test\WebSocketServerWithAmqpRouteServerUtil\MainServer\Middleware\Test::class,
            ],
        ],
        'ServerGroup'               => [
            'groupHandler' => 'GroupAmqp',
        ],
        'HttpDispatcher'            => [
            'middlewares'    => [
                \Imi\Swoole\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectionContextStore'    => [
            'handlerClass'  => 'ConnectionContextAmqp',
            'ttl'           => 600,
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'RouteNotFound',
        ],
        'AmqpServerUtil'            => [
            'exchangeConfig' => [
                'name' => 'imi_server_util_route_test',
                'type' => \PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT,
            ],
            'queueConfig'    => [
                'name'    => 'imi_server_util_route_',
                'durable' => false,
            ],
        ],
    ],
];
