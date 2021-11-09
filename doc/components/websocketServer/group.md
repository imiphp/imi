# 连接分组

imi 支持对 WebSocket 的连接（clientId）进行逻辑分组，你可以方便地对用户进行分组消息推送。

所有的分组针对服务器，所以需要 imi 的服务器对象才可以调用下面这些方法，比如在`WebSocketController`中可以使用`RequestContext::getServer()`。

## 分组的使用

### 配置

服务器配置文件：

```php
[
    'beans' => [
        // 分组
        'ServerGroup' => [
            'status'       => true , // 启用
            'groupHandler' => 'GroupRedis', // 分组处理器，目前仅支持 Redis
        ],
        // 分组 Redis 驱动
        'GroupRedis' => [
            'redisPool' => '连接池名',
            'redisDb' => null, // redis中第几个库，为null或不配置则使用连接池中的设置
            'heartbeatTimespan' => 5, // 心跳时间，单位：秒.
            'heartbeatTtl' => 8, // 心跳数据过期时间，单位：秒.
            'key' => '', // 该服务的分组键，默认为 imi:命名空间:connect_group
        ],
        // 分组本地驱动，仅支持当前 Worker 进程
        'GroupLocal' => [
            // 无配置项
        ],
    ],
]
```

### 分组创建

```php
// 创建一个分组
$this->server->createGroup('分组名称');

// 创建一个分组，最多允许加入10个连接，-1为不限制
$this->server->createGroup('分组名称', 10);
```

### 加入分组

```php
$this->server->joinGroup('分组名称', $this->frame->getClientId());
```

### 离开分组

```php
$this->server->leaveGroup('分组名称', $this->frame->getClientId());
```

### 连接是否存在于组里

```php
$this->server->getGroup('分组名称')->isInGroup($this->frame->getClientId());
```

### 获取所有连接

```php
$this->server->getGroup('分组名称')->getClientIds();
```

### 获取在组中的连接总数

```php
$this->server->getGroup('分组名称')->count();
```

## 获取分组对象

```php
// 不存在则为null，否则为\Imi\Server\Group\Group对象
$this->server->getGroup('分组名称');
```

### 组是否存在

```php
$this->server->hasGroup('分组名称');
```

### 调用组方法

```php
// 遍历分组中所有clientId，调用服务器的push方法，发送message
$this->server->groupCall('分组名称', 'push', 'message');
```

### 获取所有分组

```php
$this->server->getGroups();
```

## 分组对象详解

类`\Imi\Server\Group\Group`

### 加入组

```php
$group->join($clientId);
```

### 离开组

```php
$group->leave($clientId);
```

### 获取服务器对象

当前分组属于哪个服务器，这个是Swoole的服务器对象。

```php
$group->getServer();
```

### 获取最大客户端数量

```php
$group->getMaxClients();
```

### 调用组方法

```php
// 发送给组里所有的连接，其它服务器方法一样调用，省去clientId参数即可
$group->push('message');
```
