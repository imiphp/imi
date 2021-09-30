<?php

declare(strict_types=1);

return [
    'configs'    => [
    ],
    // bean扫描目录
    'beanScan'    => [
        'Imi\Swoole\Test\WebSocketServerWithAmqpServerUtil\MainServer\Controller',
        'Imi\Swoole\Test\WebSocketServerWithAmqpServerUtil\MainServer\Listener',
        'Imi\Swoole\Test\WebSocketServerWithAmqpServerUtil\MainServer\Error',
    ],
    'beans'    => [
        'WebSocketDispatcher'    => [
            'middlewares'    => [
                \Imi\Server\WebSocket\Middleware\RouteMiddleware::class,
                \Imi\Swoole\Test\WebSocketServerWithAmqpServerUtil\MainServer\Middleware\Test::class,
            ],
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupLocal',
        ],
        'HttpDispatcher'    => [
            'middlewares'    => [
                \Imi\Swoole\Server\WebSocket\Middleware\HandShakeMiddleware::class,
                \Imi\Server\Http\Middleware\RouteMiddleware::class,
            ],
        ],
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextLocal',
            'ttl'           => 600,
        ],
        'WSRouteNotFoundHandler'    => [
            'handler'   => 'RouteNotFound',
        ],
        'AmqpServerUtil' => [
            'exchangeConfig' => [
                'name' => 'imi_server_util_test',
                'type' => \PhpAmqpLib\Exchange\AMQPExchangeType::FANOUT,
            ],
            'queueConfig' => [
                'name'    => 'imi_server_util_',
                'durable' => false,
            ],
        ],
    ],
];
