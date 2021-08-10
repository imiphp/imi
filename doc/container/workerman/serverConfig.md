# 服务器配置

```php
return [
    // Workerman 服务器配置
    'workermanServer' => [
        // 服务器名
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
    ],
];
```
