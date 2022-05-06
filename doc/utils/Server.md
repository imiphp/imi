# 服务器工具类

[toc]

**类名:** `Imi\Server\Server`

服务器工具类，支持向客户端进行消息推送，部分工具类实现还支持了分布式消息推送。

## 多种服务器工具类实现

配置：

```php
'imi' => [
    'beans' => [
        'ServerUtil' => '类名或Bean名称',
    ],
],
```

### Swoole

#### LocalServerUtil

基于 Swoole Server 的 `sendMessage()` 方法实现，支持跨 Worker 进程中的连接，推送数据到客户端。

适用于单实例部署的场景。

> Swoole 模式下默认使用该类

#### RedisServerUtil

使用 Redis 发布订阅实现的，分布式服务器工具类，支持分布式消息推送。

适用于分布式多实例部署的场景。

支持注入的 beans 参数：

```php
[
    'beans' => [
        'RedisServerUtil' => [
            'redisName' => null, // 配置的 Redis 连接名称，为 null 则使用默认
            'channel' => 'imi:RedisServerUtil:channel', // 发布订阅的频道名，不同服务请设为不同的，以防冲突
        ],
    ],
]
```

使用 `RedisServerUtil` 请在 redis 连接池的 `resource.options` 中加入：`\Redis::OPT_READ_TIMEOUT => -1`，防止 `read error on connection to xxx` 错误刷屏

#### SwooleGatewayServerUtil

在 Swoole 模式下使用 Workerman 网关

### Workerman

#### LocalServerUtil

仅支持单个进程中，连接的消息推送，建议仅用于开发环境。

#### ChannelServerUtil

基于 Workerman 的 Channel 组件实现。

适用于分布式多实例部署的场景。

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

#### WorkermanGatewayServerUtil

在 Workerman 模式下使用 Workerman 网关

### Fpm

无

## 方法

### getServer

获取服务器

```php
$server = Server::getServer(); // 获取当前服务器
$server = Server::getServer('serverName'); // 获取指定名称的服务器
```

### sendMessage

发送消息给 Worker 进程，使用框架内置格式

返回成功发送消息数量

```php
// 发送给所有 Worker
Server::sendMessage('动作', [
    // 数据
]);

// 发送给指定 Worker
Server::sendMessage('动作', [
    // 数据
], 0);

// 发送给指定多个 Worker
Server::sendMessage('动作', [
    // 数据
], [0, 1, 2]);
```

### sendMessageRaw

发送消息给 Worker 进程

返回成功发送消息数量

```php
// 发送给所有 Worker
Server::sendMessageRaw('数据');

// 发送给指定 Worker
Server::sendMessageRaw('数据', 0);

// 发送给指定多个 Worker
Server::sendMessageRaw('数据', [0, 1, 2]);
```

### send

发送数据给指定客户端，支持一个或多个（数组）

> 数据将会通过处理器编码

```php
// 发送给当前连接
Server::send([
    // 数据
]);

// 发送给指定连接
Server::send([
    // 数据
], 1);

// 发送给指定多个连接
Server::send([
    // 数据
], [1, 2, 3]);

// 指定服务名，支持监听多个子服务器的情况
Server::send([
    // 数据
], 1, 'myServer');

// SWOOLE_BASE 模式下只发送给当前 worker 进程中的连接
Server::send([
    // 数据
], 1, 'myServer', false);
```

### sendByFlag

发送数据给指定标记的客户端，支持一个或多个（数组）

> 数据将会通过处理器编码

```php
// 需要有绑定过的连接，才可以使用此方法
\Imi\ConnectionContext::bind('user1');

// 发送给当前连接
Server::sendByFlag([
    // 数据
]);

// 发送给指定连接
Server::sendByFlag([
    // 数据
], 'user1');

// 发送给指定多个连接
Server::sendByFlag([
    // 数据
], ['user1', 'user2', 'user3']);

// 指定服务名，支持监听多个子服务器的情况
Server::sendByFlag([
    // 数据
], 'user1', 'myServer');

// SWOOLE_BASE 模式下只发送给当前 worker 进程中的连接
Server::sendByFlag([
    // 数据
], 'user1', 'myServer', false);
```

### sendRaw

发送数据给指定客户端，支持一个或多个（数组）

```php
// 发送给当前连接
Server::sendRaw('数据');

// 发送给指定连接
Server::sendRaw('数据', 1);

// 发送给指定多个连接
Server::sendRaw('数据', [1, 2, 3]);

// 指定服务名，支持监听多个子服务器的情况
Server::sendRaw('数据', 1, 'myServer');

// SWOOLE_BASE 模式下只发送给当前 worker 进程中的连接
Server::sendRaw('数据', 1, 'myServer', false);
```

### sendRawByFlag

发送数据给指定标记的客户端，支持一个或多个（数组）

```php
// 需要有绑定过的连接，才可以使用此方法
\Imi\ConnectionContext::bind('user1');

// 发送给当前连接
Server::sendRawByFlag('数据');

// 发送给指定连接
Server::sendRawByFlag('数据', 'user1');

// 发送给指定多个连接
Server::sendRawByFlag('数据', ['user1', 'user2', 'user3']);

// 指定服务名，支持监听多个子服务器的情况
Server::sendRawByFlag('数据', 'user1', 'myServer');

// SWOOLE_BASE 模式下只发送给当前 worker 进程中的连接
Server::sendRawByFlag('数据', 'user1', 'myServer', false);
```

### sendToAll

发送数据给所有客户端

> 数据将会通过处理器编码

```php

// 发送给所有连接
Server::sendToAll([
    // 数据
]);

// 指定服务名，支持监听多个子服务器的情况
Server::sendToAll([
    // 数据
], 'myServer');

// BASE模式下，只发送给当前 worker 中的所有连接（默认发给所有进程的连接）
Server::sendToAll([
    // 数据
], 'myServer', false);
```

### sendRawToAll

发送数据给所有客户端

数据原样发送

```php
// 发送给所有连接
Server::sendRawToAll('数据');

// 指定服务名，支持监听多个子服务器的情况
Server::sendRawToAll('数据', 'myServer');

// BASE模式下，只发送给当前 worker 中的所有连接（默认发给所有进程的连接）
Server::sendRawToAll('数据', 'myServer', false);
```

### sendToGroup

发送数据给分组中的所有客户端，支持一个或多个（数组）

> 数据将会通过处理器编码

```php
// 发送给单个分组
Server::sendToGroup('myGroupName', [
    // 数据
]);

// 发送给多个分组
Server::sendToGroup(['myGroupName1', 'myGroupName2'], [
    // 数据
]);

// 指定服务名，支持监听多个子服务器的情况
Server::sendToGroup('myGroupName', [
    // 数据
], 'myServer');

// BASE模式下，只发送给当前 worker 中的所有连接（默认发给所有进程的连接）
Server::sendToGroup('myGroupName', [
    // 数据
], 'myServer', false);
```

### sendRawToGroup

发送数据给分组中的所有客户端，支持一个或多个（数组）

数据原样发送

```php
// 发送
Server::sendRawToGroup('myGroupName', '数据');


// 发送给多个分组
Server::sendRawToGroup(['myGroupName1', 'myGroupName2'], '数据');

// 指定服务名，支持监听多个子服务器的情况
Server::sendRawToGroup('myGroupName', '数据', 'myServer');

// BASE模式下，只发送给当前 worker 中的所有连接（默认发给所有进程的连接）
Server::sendRawToGroup('myGroupName', '数据', 'myServer', false);
```

### close

关闭一个或多个连接

```php
Server::close(1); // 关闭 clientId 1
Server::close([1, 2, 3]); // 关闭 clientId 1、2、3
Server::close(1, 'myServer'); // 指定服务器名

// BASE模式下，只关闭当前 worker 中的连接（默认关闭所有进程的指定连接）
Server::close(1, 'myServer', false);
```

### closeByFlag

关闭一个或多个指定标记的连接

```php
// 需要有绑定过的连接，才可以使用此方法
\Imi\ConnectionContext::bind('user1');

Server::closeByFlag('user1'); // 关闭 user1
Server::closeByFlag(['user1', 'user2']); // 关闭 user1、user2
Server::closeByFlag('user1', 'myServer'); // 指定服务器名

// BASE模式下，只关闭当前 worker 中的连接（默认关闭所有进程的指定连接）
Server::closeByFlag('user1', 'myServer', false);
```

### exists

连接是否存在

> 即便连接存在，也不代表连接 100% 联通，请勿过于依赖此方法。

> 推荐仅在 Local、Gateway 模式使用。如在 Redis、Channel 模式下使用可能获取到的结果不准确。

```php
var_dump(Server::exists(123));
```

### flagExists

指定标记的连接是否存在

> 即便连接存在，也不代表连接 100% 联通，请勿过于依赖此方法。

> 推荐仅在 Local、Gateway 模式使用。如在 Redis、Channel 模式下使用可能获取到的结果不准确。

```php
var_dump(Server::flagExists('user-123'));
```

### getConnections

获取连接数组。有可能返回的是当前进程管理的连接。

```php
$clientIds = Server::getConnections();
```

### getConnectionCount

获取当前连接数量

```php
$count = Server::getConnectionCount();
```

### 其它

> 连接上下文的使用：<https://doc.imiphp.com/v2.1/components/websocketServer/session.html>
