# 连接分组

imi 支持对 WebSocket 的连接（fd）进行逻辑分组，你可以方便地对用户进行分组消息推送。

所有的分组针对服务器，所以需要 imi 的服务器对象才可以调用下面这些方法，比如在`WebSocketController`中可以使用`$this->server`。

## 分组的使用

### 分组创建

```php
// 创建一个分组
$this->server->createGroup('分组名称');

// 创建一个分组，最多允许加入10个连接，-1为不限制
$this->server->createGroup('分组名称', 10);
```

### 加入分组

```php
$this->server->joinGroup('分组名称', $this->frame->getFd());
```

### 离开分组

```php
$this->server->leaveGroup('分组名称', $this->frame->getFd());
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
// 遍历g1分组中所有fd，调用服务器的push方法，发送message
$this->server->groupCall('g1', 'push', 'message');
```

### 获取所有分组

```php
$this->server->getGroups();
```

## 分组对象详解

类`\Imi\Server\Group\Group`

### 加入组

```php
$group->join($fd);
```

### 离开组

```php
$group->leave($fd);
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
// 发送给组里所有的连接，其它服务器方法一样调用，省去fd参数即可
$group->push('message');
```