# 服务器配置

```php
return [
    // Workerman 服务器配置
    'workermanServer' => [
        // 服务器名，http 也可以改成 abc 等等，完全自定义
        'http' => [
            // 指定服务器命名空间
            'namespace' => 'Imi\Workerman\Test\HttpServer\ApiServer',
            // 服务器类型
            'type'      => Imi\Workerman\Server\Type::HTTP, // HTTP、WEBSOCKET、TCP、UDP
            'host'      => '0.0.0.0',
            'port'      => 8080,
            // socket的上下文选项，参考：http://doc3.workerman.net/315128
            'context'   => [],
            'configs'   => [
                // 支持设置 Workerman 参数
            ],
            // 服务器容器绑定
            'beans' => [
                'aaa' => XXX::class,
            ],
        ],
        // 下面可以继续加入其它协议其它端口的服务
        'websocket' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\WebSocketServer',
            'type'        => Imi\Workerman\Server\Type::WEBSOCKET,
            'host'        => '0.0.0.0',
            'port'        => 13002,
            'shareWorker' => 'http', // 与名字叫 http 的服务，共享 worker 进程
            'configs'     => [
            ],
        ],
    ],
];
```
