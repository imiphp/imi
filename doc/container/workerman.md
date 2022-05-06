# Workerman

[toc]

imi v2.0 版本开始，支持运行在 Workerman 环境中。

组件引入：`composer require imiphp/imi-workerman`

## 核心特性

| 特性 | 是否支持 |
|-|-
| Http | ✔ |
| Http2 | ✔ |
| WebSocket | ✔ |
| TCP | ✔ |
| UDP | ✔ |
| MQTT |  |

## 命令

启动服务: `vendor/bin/imi-workerman workerman/start`

停止服务: `vendor/bin/imi-workerman workerman/stop`

## 注意事项

### send buffer full and drop package

这个问题一般出现在，服务器循环发送数据给客户端，导致塞满发送缓冲区。

**解决办法：**

如果你是 Workerman，可以在入口文件中加上：

```php
// 发送缓冲区大小，单位：字节。可以根据需要调整，不建议设置得很大，会浪费内存
\Workerman\Connection\TcpConnection::$defaultMaxSendBufferSize = 2 * 1024 * 1024;
```

如果你是 Workerman Gateway，可以在服务器配置中的 `configs` 加上相应配置：

```php
[
    'gateway' => [
        'namespace'   => 'Imi\WorkermanGateway\Test\AppServer\Gateway',
        'type'        => Imi\WorkermanGateway\Workerman\Server\Type::GATEWAY,
        'socketName'  => 'websocket://0.0.0.0:8081', // 网关监听的地址
        'configs'     => [
            'lanIp'           => '127.0.0.1',
            'startPort'       => 12900,
            'registerAddress' => '127.0.0.1:13004',
            // 发送缓冲区大小，单位：字节。可以根据需要调整，不建议设置得很大，会浪费内存
            'sendToClientBufferSize' => 2 * 1024 * 1024,
        ],
    ],
]
```
