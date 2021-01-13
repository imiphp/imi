# 服务器工具类

**类名:** `Imi\Swoole\Server\Server`

服务器工具类

## 方法

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

### sendToAllRaw

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
```
