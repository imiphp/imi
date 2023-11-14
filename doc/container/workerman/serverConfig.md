# 服务器配置

[toc]

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
            // $request->getAppUri() 参数替换，每个参数都是可选项
            // 下面例子最终获取到的 Uri 为：https://root:123@imi-test:1234/test?id=666#test
            'appUri' => [
                'host'     => 'imi-test',   // 主机名
                'port'     => 1234,         // 端口
                'scheme'   => 'https',      // 协议
                'user'     => 'root',       // 用户名
                'pass'     => '123',        // 密码
                'path'     => '/test',      // 路径
                'query'    => 'id=666',     // 查询参数
                'fragment' => 'test',       // 锚点
            ],
            // 也支持回调
            'appUri' => function(\Imi\Util\Uri $uri) {
                return $uri->withHost('imi-test');
            },
        ],
        // 下面可以继续加入其它协议其它端口的服务
        'websocket' => [
            'namespace'   => 'Imi\Workerman\Test\AppServer\WebSocketServer',
            'type'        => Imi\Workerman\Server\Type::WEBSOCKET,
            'host'        => '0.0.0.0',
            'port'        => 13002,
            'shareWorker' => 'http', // 与名字叫 http 的服务，共享 worker 进程
            'nonControlFrameType' => \Imi\Server\WebSocket\Enum\NonControlFrameType::Text, // 配置 WebSocket 纯文本通信协议
            // 'nonControlFrameType' => \Imi\Server\WebSocket\Enum\NonControlFrameType::Binary, // 配置 WebSocket 二进制通信协议
            'configs'     => [
            ],
        ],
    ],
    'workerman' => [
        // 设置给 Worker 类静态属性的配置
        'worker' => [
            
        ],
    ],
];
```
