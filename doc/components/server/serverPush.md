# 向客户端推送数据

长连接服务（TCP、WebSocket）支持向客户端推送数据，具体用法<https://doc.imiphp.com/utils/Server.md>

## Workerman

### Channel 模式

基于 Workerman 的 Channel 组件实现。

首先你需要配置一个 Channel 服务：

```php
// Workerman 服务器配置
'workermanServer' => [
    // channel 是名称可以改成你自己的
    'channel' => [
        'namespace'   => '',
        'type'        => Imi\Workerman\Server\Type::CHANNEL,
        'host'        => '0.0.0.0',
        'port'        => 13005,
        'configs'     => [
        ],
    ],
]
```

然后配置 Worker 进程连接 Channel：

```php
'workerman' => [
    // 多进程通讯组件配置
    'channel' => [
        'host' => '127.0.0.1',
        'port' => 13005,
    ],
],
```
